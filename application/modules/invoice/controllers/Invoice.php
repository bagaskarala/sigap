<?php defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Invoice extends Sales_Controller
{
    public $invoice_type_options = array(
        'credit'      => 'Kredit',
        'online'      => 'Online',
        'cash'        => 'Tunai',
    );

    public $source_options = array(
        'warehouse' => 'Gudang',
        'library'   => 'Perpustakaan',
    );

    public function __construct()
    {
        parent::__construct();
        $this->pages = 'invoice';
        $this->load->model('invoice_model', 'invoice');
        $this->load->model('book/book_model', 'book');
        $this->load->model('book_stock/book_stock_model', 'book_stock');
        $this->load->model('book_transaction/book_transaction_model', 'book_transaction');
        $this->load->helper('sales_helper');
    }

    public function index($page = NULL)
    {
        // SIDE EFFECT
        // run command to set expired invoice which exceed due date
        $this->set_expired();


        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'invoice_type'      => $this->input->get('invoice_type', true),
            'status'            => $this->input->get('status', true),
            'receipt'           => $this->input->get('receipt', true),
            'customer_type'     => $this->input->get('customer_type', true),
            'excel'             => $this->input->get('excel', true)
        ];

        $this->invoice->per_page = $this->input->get('per_page', true) ?? 10;

        $get_data = $this->invoice->filter_invoice($filters, $page);
        //data invoice
        $invoice    = $get_data['invoice'];
        foreach ($invoice as $each_invoice) {
            if ($each_invoice->customer_name == NULL) $each_invoice->customer_name = '';
            if ($each_invoice->customer_type == NULL) $each_invoice->customer_type = 'general';
        }
        $total      = $get_data['total'];
        $pagination = $this->invoice->make_pagination(site_url('invoice'), 2, $total);

        foreach ($invoice as $key => $value) {
            $invoice[$key]->is_expired = $this->_check_if_expired($invoice[$key]->due_date);
        }

        $pages      = $this->pages;
        $main_view  = 'invoice/index_invoice';
        $this->load->view('template', compact('pages', 'main_view', 'invoice', 'pagination', 'total'));
        if ($filters['excel'] == 1) {
            $this->generate_excel($filters);
        }
    }

    public function view($invoice_id = null)
    {
        if (!isset($invoice_id)) {
            redirect('invoice');
        }
        $pages          = $this->pages;
        $main_view      = 'invoice/view_invoice';
        $invoice        = $this->invoice->fetch_invoice_id($invoice_id);
        $invoice_books  = $this->invoice->fetch_invoice_book($invoice_id);
        $invoice->customer = $this->invoice->get_customer($invoice->customer_id);
        $invoice->is_expired = $this->_check_if_expired($invoice->due_date);

        $this->load->view('template', compact('pages', 'main_view', 'invoice', 'invoice_books'));
    }

    public function add()
    {
        //post add invoice
        if ($_POST) {
            //validasi input
            $this->invoice->validate_invoice();
            $date_created       = date('Y-m-d H:i:s');

            $this->db->trans_begin();

            //Nentuin customer id jika customer diambil dari database
            if (!empty($this->input->post('customer-id'))) {
                $customer_id = $this->input->post('customer-id');
            }
            //Nentuin customer id jika customer dibuat baru
            else if (!empty($this->input->post('new-customer-name'))) {
                $add = [
                    'name'          => $this->input->post('new-customer-name'),
                    'address'       => $this->input->post('new-customer-address'),
                    'phone_number'  => $this->input->post('new-customer-phone-number'),
                    'email'         => $this->input->post('new-customer-email'),
                    'type'          => $this->input->post('new-customer-type')
                ];
                $this->db->insert('customer', $add);
                $customer_id = $this->db->insert_id();
            }
            //Customer null (showroom)
            else {
                $customer_id = null;
            }

            $type = $this->input->post('type');
            $status = 'waiting';
            $source = $this->input->post('source') ?? 'warehouse';

            if ($type == 'showroom') {
                $status = 'finish';
                $source = 'showroom';
            }
            $library_id = $this->input->post('source-library-id');
            $add = [
                'number'            => $this->invoice->get_last_invoice_number($type),
                'customer_id'       => $customer_id,
                'due_date'          => $this->input->post('due-date'),
                'type'              => $type,
                'source'            => $source,
                'source_library_id' => $library_id,
                'status'            => $status,
                'issued_date'       => $date_created,
                'delivery_fee'       => $this->input->post('delivery-fee')
            ];
            $this->db->insert('invoice', $add);
            // ID faktur terbaru untuk diisi buku
            $invoice_id = $this->db->insert_id();

            // Jumlah Buku di Faktur
            $countsize = count($this->input->post('invoice_book_id'));
            // Total berat buku
            $total_weight = 0;
            // Masukkan buku di form faktur ke database
            for ($i = 0; $i < $countsize; $i++) {
                $book_id = $this->input->post('invoice_book_id')[$i];
                $book = [
                    'invoice_id'    => $invoice_id,
                    'book_id'       => $book_id,
                    'qty'           => $this->input->post('invoice_book_qty')[$i],
                    'price'         => $this->input->post('invoice_book_price')[$i],
                    'discount'      => $this->input->post('invoice_book_discount')[$i],
                    'royalty'       => $this->invoice->get_book_royalty($book_id)
                ];
                $this->db->insert('invoice_book', $book);
                // Hitung berat buku
                $book_weight = $this->invoice->get_book($book['book_id'])->weight;
                $total_weight +=  $book_weight * $book['qty'];

                // Kurangi Stock Buku
                $book_stock = $this->book_stock->where('book_id', $book['book_id'])->get();
                if ($type == 'showroom') {
                    $book_stock->showroom_present -= $book['qty'];
                } else
                if ($source == 'warehouse') {
                    $book_stock->warehouse_present -= $book['qty'];
                } else
                if ($source == 'library') {
                    // kurangi stock detail perpus
                    $library_stock = ($this->book_stock->get_one_library_stock($book_stock->book_stock_id, $library_id))->library_stock;
                    $book_stock->library_present -= $book['qty'];
                    $library_stock -= $book['qty'];

                    $this->db->set('library_stock', $library_stock)
                        ->where('book_stock_id', $book_stock->book_stock_id)
                        ->where('library_id', $library_id)
                        ->update('library_stock_detail');
                }
                $this->book_stock->where('book_id', $book['book_id'])->update($book_stock);

                // Faktur Showroom tidak mencatat transaksi (karena sumber buku bukan dari gudang)
                if ($type != 'showroom') {
                    // Masukkan transaksi buku
                    $this->book_transaction->insert([
                        'book_id'            => $book['book_id'],
                        'invoice_id'         => $invoice_id,
                        'book_stock_id'      => $book_stock->book_stock_id,
                        'stock_initial'      => $book_stock->warehouse_present + $book['qty'],
                        'stock_mutation'     => $book['qty'],
                        'stock_last'         => $book_stock->warehouse_present,
                        'date'               => $date_created
                    ]);
                }
            }
            $this->db->set('total_weight', $total_weight)->where('invoice_id', $invoice_id)->update('invoice');
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('success', $this->lang->line('toast_add_fail'));
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('success', $this->lang->line('toast_add_success'));
                echo json_encode(['status' => TRUE, 'invoice_id' => $invoice_id]);
            }
        }
        //View add invoice
        else {
            $invoice = (object)[
                'invoice_id'   => '',
                'due_date'   => '',
                'customer_id'   => '',
                'delivery_fee'   => 0,
            ];

            $customer = (object)[
                'name'   => '',
                'address'   => '',
                'phone_number'   => '',
                'email'   => '',
                'type'   => '',
            ];

            $discount = 0;

            $invoice_type_options = $this->invoice_type_options;

            $source_options = $this->source_options;

            $customer_type = get_customer_type();

            $invoice_book = [];

            $dropdown_book_options = $this->invoice->get_available_book_list('warehouse', '');

            $form_type = 'add';

            $pages       = $this->pages;
            $main_view   = 'invoice/form_invoice';
            $this->load->view('template', compact('pages', 'main_view', 'invoice_type_options', 'source_options', 'customer_type', 'dropdown_book_options', 'form_type', 'invoice', 'invoice_book', 'customer', 'discount'));
        }
    }

    public function add_showroom()
    {
        $customer_type = get_customer_type();

        $dropdown_book_options = $this->invoice->get_available_book_list('showroom', '');

        $pages       = 'invoice/add_showroom';
        $main_view   = 'invoice/add_showroom';
        $this->load->view('template', compact('pages', 'main_view', 'customer_type', 'dropdown_book_options'));
    }

    public function edit($invoice_id = null)
    {
        if (!isset($invoice_id)) {
            redirect('invoice');
        }
        //post edit invoice
        if ($_POST) {
            //validasi input edit
            $this->invoice->validate_invoice();

            $this->db->trans_begin();

            //Nentuin customer id jika customer diambil dari database
            if (!empty($this->input->post('customer-id'))) {
                $customer_id = $this->input->post('customer-id');
            }
            //Nentuin customer id jika customer dibuat baru
            else {
                $add = [
                    'name'          => $this->input->post('new-customer-name'),
                    'address'       => $this->input->post('new-customer-address'),
                    'phone_number'  => $this->input->post('new-customer-phone-number'),
                    'email'         => $this->input->post('new-customer-email'),
                    'type'          => $this->input->post('new-customer-type')
                ];
                $this->db->insert('customer', $add);
                $customer_id = $this->db->insert_id();
            }

            $edit = [
                'customer_id'       => $customer_id,
                'due_date'          => $this->input->post('due-date'),
                'status'            => 'waiting',
                'delivery_fee'      => $this->input->post('delivery-fee')
            ];

            $this->db->set($edit)->where('invoice_id', $invoice_id)->update('invoice');

            $invoice = $this->invoice->fetch_invoice_id($invoice_id);
            // Kembalikan stock buku
            $invoice_books  = $this->invoice->fetch_invoice_book($invoice_id);
            foreach ($invoice_books as $invoice_book) {
                if ($invoice->source == 'warehouse') {
                    $book_stock = $this->book_stock->where('book_id', $invoice_book->book_id)->get();
                    $book_stock->warehouse_present += $invoice_book->qty;
                    $this->book_stock->where('book_id', $invoice_book->book_id)->update($book_stock);
                } else
                if ($invoice->source == 'library') {
                    $book_stock = $this->book_stock->where('book_id', $invoice_book->book_id)->get();
                    $library_id = $invoice->source_library_id;
                    $library_stock = ($this->book_stock->get_one_library_stock($book_stock->book_stock_id, $library_id))->library_stock;
                    $book_stock->library_present += $invoice_book->qty;
                    $library_stock += $invoice_book->qty;
                    $this->db->set('library_stock', $library_stock)
                        ->where('book_stock_id', $book_stock->book_stock_id)
                        ->where('library_id', $library_id)
                        ->update('library_stock_detail');
                }
                $this->book_stock->where('book_id', $invoice_book->book_id)->update($book_stock);
            }

            // Hapus invoice_book yang sudah ada
            $this->db->where('invoice_id', $invoice_id)->delete('invoice_book');

            // Update stock_initial dan stock_last di transaksi yang lebih baru dengan stock setelah dikembalikan
            $book_transactions = $this->db->select('*')->from('book_transaction')->where('invoice_id', $invoice_id)->get()->result();
            foreach ($book_transactions as $book_transaction) {
                $mutation = $book_transaction->stock_mutation;
                $newer_transactions = $this->db->select('*')
                    ->from('book_transaction')
                    ->where('book_transaction_id >', $book_transaction->book_transaction_id)
                    ->where('book_id', $book_transaction->book_id)
                    ->get()->result();
                foreach ($newer_transactions as $newer_transaction) {
                    $newer_transaction->stock_initial += $mutation;
                    $newer_transaction->stock_last += $mutation;
                    $this->book_transaction->where('book_transaction_id', $newer_transaction->book_transaction_id)->update($newer_transaction);
                }
            }
            // Hapus Transaction yang sudah ada
            $this->db->where('invoice_id', $invoice_id)->delete('book_transaction');

            // Jumlah Buku di Faktur
            $countsize = count($this->input->post('invoice_book_id'));

            $total_weight = 0;
            // Masukkan invoice_book yang baru (hasil edit) ke database
            for ($i = 0; $i < $countsize; $i++) {
                $book_id = $this->input->post('invoice_book_id')[$i];
                $book = [
                    'invoice_id'    => $invoice_id,
                    'book_id'       => $book_id,
                    'qty'           => $this->input->post('invoice_book_qty')[$i],
                    'price'         => $this->input->post('invoice_book_price')[$i],
                    'discount'      => $this->input->post('invoice_book_discount')[$i],
                    'royalty'       => $this->invoice->get_book_royalty($book_id)
                ];
                $this->db->insert('invoice_book', $book);
                $book_weight = $this->invoice->get_book($book['book_id'])->weight;
                $total_weight +=  $book_weight * $book['qty'];

                $book_stock = $this->book_stock->where('book_id', $book['book_id'])->get();
                if ($invoice->source == 'warehouse') {
                    $book_stock->warehouse_present -= $book['qty'];
                } else
                if ($invoice->source == 'library') {
                    // kurangi stock detail perpus
                    $library_stock = ($this->book_stock->get_one_library_stock($book_stock->book_stock_id, $library_id))->library_stock;
                    $book_stock->library_present -= $book['qty'];
                    $library_stock -= $book['qty'];

                    $this->db->set('library_stock', $library_stock)
                        ->where('book_stock_id', $book_stock->book_stock_id)
                        ->where('library_id', $library_id)
                        ->update('library_stock_detail');
                }
                $this->book_stock->where('book_id', $book['book_id'])->update($book_stock);

                // Masukkan transaksi buku
                $this->book_transaction->insert([
                    'book_id'            => $book['book_id'],
                    'invoice_id'         => $invoice_id,
                    'book_stock_id'      => $book_stock->book_stock_id,
                    'stock_initial'      => $book_stock->warehouse_present + $book['qty'],
                    'stock_mutation'     => $book['qty'],
                    'stock_last'         => $book_stock->warehouse_present,
                    'date'               => date('Y-m-d H:i:s')
                ]);
            }
            $this->db->set('total_weight', $total_weight)->where('invoice_id', $invoice_id)->update('invoice');


            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('success', $this->lang->line('toast_edit_fail'));
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
                echo json_encode(['status' => TRUE, 'invoice_id' => $invoice_id]);
            }
        }
        //view edit invoice
        else {
            $invoice        = $this->invoice->fetch_invoice_id($invoice_id);
            if ($this->_check_if_expired($invoice->due_date)) {
                $this->session->set_flashdata('warning', 'Faktur sudah melewati tangal jatuh tempo');
                redirect($this->pages);
            }

            //info customer dan diskon
            $customer = $this->db->select('*')->from('customer')->where('customer_id', $invoice->customer_id)->get()->row();
            $discount_data = $this->db->select('discount')->from('discount')->where('membership', $customer->type)->get()->row();
            $discount = $discount_data->discount;

            $invoice_type_options = $this->invoice_type_options;

            $source_options = $this->source_options;

            $customer_type = get_customer_type();

            $invoice_book = $this->invoice->fetch_invoice_book($invoice->invoice_id);

            $dropdown_book_options = $this->invoice->get_available_book_list($invoice->source, $invoice->source_library_id);

            $form_type = 'edit';

            $pages       = $this->pages;
            $main_view   = 'invoice/form_invoice';
            $this->load->view('template', compact('pages', 'invoice', 'invoice_book', 'customer', 'discount', 'main_view', 'invoice_type_options', 'source_options', 'customer_type', 'dropdown_book_options', 'form_type'));
        }
    }

    // internal_use: skip redirection and toast flash data
    public function action($id, $invoice_status, $internal_use = false)
    {
        $invoice = $this->invoice->where('invoice_id', $id)->get();
        if (!$invoice) {
            if (!$internal_use) {
                $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
                redirect($this->pages);
            } else {
                return;
            }
        }

        $this->db->trans_begin();

        if ($invoice->status == 'waiting') {
            // Confirm Faktur
            if ($invoice_status == 'confirm') {
                // M T W T F S S
                // 1 2 3 4 5 6 7
                if (date('N') < 5) {
                    $preparing_deadline = date("Y-m-d H:i:s", strtotime("+ 1 day"));
                } else {
                    $add_day = 8 - date('N');
                    $preparing_deadline = date("Y-m-d H:i:s", strtotime("+ " . $add_day . "day"));
                }
                if ($invoice->source != 'warehouse') {
                    $invoice_status = 'finish';
                }
                $this->invoice->where('invoice_id', $id)->update([
                    'status' => $invoice_status,
                    'confirm_date' => now(),
                    'preparing_deadline' => $preparing_deadline
                ]);
            } elseif ($invoice_status == 'cancel') {
                // Cancel Faktur
                $this->invoice->where('invoice_id', $id)->update([
                    'status' => $invoice_status,
                    'cancel_date' => now(),
                ]);
                $invoice_books  = $this->invoice->fetch_invoice_book($id);
                // Harga dibuat 0 (Untuk bagian pendapatan)
                foreach ($invoice_books as $invoice_book) {
                    $this->db->set('price', 0)->where('invoice_id', $id)->update('invoice_book');
                }


                if ($invoice->type != 'showroom') {
                    // Kembalikan stock buku
                    $invoice_books  = $this->invoice->fetch_invoice_book($id);
                    foreach ($invoice_books as $invoice_book) {
                        if ($invoice->source == 'warehouse') {
                            $book_stock = $this->book_stock->where('book_id', $invoice_book->book_id)->get();
                            $book_stock->warehouse_present += $invoice_book->qty;
                            $this->book_stock->where('book_id', $invoice_book->book_id)->update($book_stock);
                        } else
                                if ($invoice->source == 'library') {
                            $book_stock = $this->book_stock->where('book_id', $invoice_book->book_id)->get();
                            $library_id = $invoice->source_library_id;
                            $library_stock = ($this->book_stock->get_one_library_stock($book_stock->book_stock_id, $library_id))->library_stock;
                            $book_stock->library_present += $invoice_book->qty;
                            $library_stock += $invoice_book->qty;
                            $this->db->set('library_stock', $library_stock)
                                ->where('book_stock_id', $book_stock->book_stock_id)
                                ->where('library_id', $library_id)
                                ->update('library_stock_detail');
                        }
                        $this->book_stock->where('book_id', $invoice_book->book_id)->update($book_stock);
                    }
                    if ($invoice->source == 'warehouse') {
                        // Update stock_initial dan stock_last di transaksi yang lebih baru dengan stock setelah dikembalikan
                        $book_transactions = $this->db->select('*')->from('book_transaction')->where('invoice_id', $id)->get()->result();
                        foreach ($book_transactions as $book_transaction) {
                            $mutation = $book_transaction->stock_mutation;
                            $newer_transactions = $this->db->select('*')
                                ->from('book_transaction')
                                ->where('book_transaction_id >', $book_transaction->book_transaction_id)
                                ->where('book_id', $book_transaction->book_id)
                                ->get()->result();
                            foreach ($newer_transactions as $newer_transaction) {
                                $newer_transaction->stock_initial += $mutation;
                                $newer_transaction->stock_last += $mutation;
                                $this->book_transaction->where('book_transaction_id', $newer_transaction->book_transaction_id)->update($newer_transaction);
                            }
                        }
                    }
                    // Hapus Transaction yang sudah ada
                    $this->db->where('invoice_id', $id)->delete('book_transaction');
                }
            }
        } elseif ($invoice->status == 'preparing_finish') {
            // Finish Faktur
            if ($invoice_status == 'finish') {
                $this->invoice->where('invoice_id', $id)->update([
                    'status' => $invoice_status,
                    'finish_date' => now(),
                ]);
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            if (!$internal_use) {
                $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
            }
        } elseif ($this->db->trans_status() != false && $invoice_status == 'confirm') {
            $this->db->trans_commit();
            if (!$internal_use) {
                $this->session->set_flashdata('confirm_invoice', 'Permintaan diteruskan ke gudang');
            }
        } else {
            $this->db->trans_commit();
            if (!$internal_use) {
                $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
            }
        }

        if (!$internal_use) {
            redirect("{$this->pages}/view/{$invoice->invoice_id}");
        }
    }

    public function update_delivery_fee($invoice_id)
    {
        $edit = [
            'delivery_fee' => $this->input->post('delivery_fee'),
            'receipt' => $this->input->post('receipt')
        ];
        $this->db->set($edit)->where('invoice_id', $invoice_id)->update('invoice');
        echo json_encode(['status' => TRUE]);
    }

    public function generate_pdf($invoice_type, $invoice_id)
    {
        $invoice        = $this->invoice->fetch_invoice_id($invoice_id);
        if (!$invoice) {
            echo 'invoice tidak ditemukan';
            return;
        }
        if ($invoice->status != 'waiting' && $invoice->status != 'cancel') {
            $invoice_books  = $this->invoice->fetch_invoice_book($invoice_id);
            $customer       = $this->invoice->get_customer($invoice->customer_id);

            // PDF
            $this->load->library('pdf');
            $data_format['invoice'] = $invoice ?? '';
            $data_format['invoice_books'] = $invoice_books ?? '';
            $data_format['customer'] = $customer ?? '';
            $data_format['pay_cash'] = $this->input->get('pay_cash') ?? 0;

            $invoicePath = '';
            if ($invoice_type === 'showroom') {
                $invoicePath = 'invoice/view_showroom_receipt_pdf';
            } else {
                $invoicePath = 'invoice/view_invoice_pdf';
            }
            $html = $this->load->view($invoicePath, $data_format, true);

            $file_name = $invoice->number . '_Invoice';

            if ($invoice_type === 'showroom') {
                $this->pdf->generate_pdf_thermal($html, $file_name);
            } else {
                $this->pdf->generate_pdf_a4_portrait($html, $file_name);
            }
        }
    }

    public function generate_excel($filters)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $filename = 'Data_Faktur';

        // get all invoice without pagination
        $invoice_data = $this->invoice->filter_invoice($filters, -1);
        $invoices = $invoice_data['invoice'];

        $i = 2;
        $no = 1;
        // Column Content
        foreach ($invoices as $data) {
            $invoice_books = $this->invoice->fetch_invoice_book($data->invoice_id);
            $total = 0;
            foreach ($invoice_books as $invoice_book) {
                $total += $invoice_book->price * $invoice_book->qty * (1 - $invoice_book->discount / 100);
            }

            foreach (range('A', 'K') as $v) {
                $receipt = explode("-", $data->receipt);
                switch ($v) {
                    case 'A': {
                            $value = $no++;
                            break;
                        }
                    case 'B': {
                            $value = $data->number;
                            break;
                        }
                    case 'C': {
                            $value = date('d F Y', strtotime($data->issued_date));
                            break;
                        }
                    case 'D': {
                            $value = get_invoice_type()[$data->invoice_type];
                            break;
                        }
                    case 'E': {
                            $value = $data->customer_name;
                            break;
                        }
                    case 'F': {
                            $value = $data->customer_type;
                            break;
                        }
                    case 'G': {
                            $value = get_invoice_status()[$data->status];
                            break;
                        }
                    case 'H': {
                            $value = $data->due_date;
                            break;
                        }
                    case 'I': {
                            $value = $receipt[0];
                            break;
                        }
                    case 'J': {
                            $value = $receipt[1] ?? '';
                            break;
                        }
                    case 'K': {
                            $value = $total;
                            break;
                        }
                }
                $sheet->setCellValue($v . $i, $value);
            }
            $i++;
        }
        // Column Title
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nomor Faktur');
        $sheet->setCellValue('C1', 'Tanggal Dikeluarkan');
        $sheet->setCellValue('D1', 'Jenis Faktur');
        $sheet->setCellValue('E1', 'Nama Customer');
        $sheet->setCellValue('F1', 'Jenis Customer');
        $sheet->setCellValue('G1', 'Status');
        $sheet->setCellValue('H1', 'Jatuh Tempo');
        $sheet->setCellValue('I1', 'Bukti Bayar');
        $sheet->setCellValue('J1', 'Marketplace');
        $sheet->setCellValue('K1', 'Total Harga');
        // Auto width
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        die();
    }

    public function api_get_book($book_id)
    {
        $book = $this->invoice->get_book($book_id);
        return $this->send_json_output(true, $book);
    }

    public function api_get_book_dynamic_stock($book_id, $source, $library_id = null)
    {
        $book = $this->invoice->get_book_dynamic_stock($book_id, $source, $library_id);
        return $this->send_json_output(true, $book);
    }

    public function api_get_customer($customer_id)
    {
        $customer =  $this->invoice->get_customer($customer_id);
        return $this->send_json_output(true, $customer);
    }

    // Auto fill diskon berdasar jenis customer
    public function api_get_discount($customerType)
    {
        $discount = $this->invoice->get_discount($customerType);
        return $this->send_json_output(true, $discount);
    }

    public function api_get_book_dropdown($type, $library_id = '')
    {
        $data = $this->invoice->get_available_book_list($type, $library_id);
        return $this->send_json_output(true, $data);
    }

    // check for expired then action cancel if due_date exceed now
    public function set_expired()
    {
        $expired_invoices = $this->invoice->get_all_expired_invoice();
        foreach ($expired_invoices as $invoice) {
            $this->action($invoice->invoice_id, 'cancel', true);
        }
    }

    private function _check_if_expired($date)
    {
        $now = (new Datetime())->format('Y-m-d');
        $due_date = (new Datetime($date))->format('Y-m-d');
        return $due_date < $now;
    }
}
