<?php defined('BASEPATH') or exit('No direct script access allowed');

class Invoice extends Sales_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'invoice';
        $this->load->model('invoice_model', 'invoice');
        $this->load->model('book/Book_model', 'book');
        $this->load->model('book_stock/Book_stock_model', 'book_stock');
        $this->load->model('book_transaction/book_transaction_model', 'book_transaction');
        $this->load->helper('sales_helper');
    }

    public function index($page = NULL)
    {
        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'invoice_type'      => $this->input->get('invoice_type', true),
            'status'            => $this->input->get('status', true),
            'customer_type'     => $this->input->get('customer_type', true)
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

        $pages      = $this->pages;
        $main_view  = 'invoice/index_invoice';
        $this->load->view('template', compact('pages', 'main_view', 'invoice', 'pagination', 'total'));
    }

    public function view($invoice_id)
    {
        $pages          = $this->pages;
        $main_view      = 'invoice/view_invoice';
        $invoice        = $this->invoice->fetch_invoice_id($invoice_id);
        $invoice_books  = $this->invoice->fetch_invoice_book($invoice_id);
        $invoice->customer = $this->invoice->get_customer($invoice->customer_id);

        $this->load->view('template', compact('pages', 'main_view', 'invoice', 'invoice_books'));
    }

    public function add()
    {
        //post add invoice
        if ($_POST) {
            //validasi input
            $this->invoice->validate_invoice();
            $date_created       = date('Y-m-d H:i:s');

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
            if ($type == 'showroom') {
                $status = 'finish';
            }
            $add = [
                'number'            => $this->invoice->get_last_invoice_number($type),
                'customer_id'       => $customer_id,
                'due_date'          => $this->input->post('due-date'),
                'type'              => $type,
                'source'            => $this->input->post('source'),
                'source_library_id' => $this->input->post('source-library-id'),
                'status'            => $status,
                'issued_date'       => $date_created
                // 'user_created'      => $user_created
            ];
            $this->db->insert('invoice', $add);
            // ID faktur terbaru untuk diisi buku
            $invoice_id = $this->db->insert_id();
            if ($type == 'credit' || $type == 'online') {
                $this->db->set('source', 'warehouse')->where('invoice_id', $invoice_id)->update('invoice');
            }

            // Jumlah Buku di Faktur
            $countsize = count($this->input->post('invoice_book_id'));
            // Total berat buku
            $total_weight = 0;
            // Masukkan buku di form faktur ke database
            for ($i = 0; $i < $countsize; $i++) {
                $book = [
                    'invoice_id'    => $invoice_id,
                    'book_id'       => $this->input->post('invoice_book_id')[$i],
                    'qty'           => $this->input->post('invoice_book_qty')[$i],
                    'price'         => $this->input->post('invoice_book_price')[$i],
                    'discount'      => $this->input->post('invoice_book_discount')[$i]
                ];
                $this->db->insert('invoice_book', $book);
                $book_weight = $this->invoice->get_book($book['book_id'])->weight;
                $total_weight +=  $book_weight * $book['qty'];

                // Kurangi Stock Buku
                $book_stock = $this->book_stock->where('book_id', $book['book_id'])->get();
                if ($type == 'showroom') {
                    $book_stock->showroom_present -= $book['qty'];
                } else {
                    $book_stock->warehouse_present -= $book['qty'];
                }
                $this->book_stock->where('book_id', $book['book_id'])->update($book_stock);

                // Faktur Showroom tidak mencatat transaksi (karena sumber buku bukan dari gudang)
                if ($type != 'showroom') {
                    // Masukkan transaksi buku
                    $this->book_transaction->insert([
                        'book_id'            => $book['book_id'],
                        'invoice_id'         => $invoice_id,
                        'book_stock_id'      => $book_stock->book_stock_id,
                        'stock_initial'      => $book_stock->warehouse_present+$book['qty'],
                        'stock_mutation'     => $book['qty'],
                        'stock_last'         => $book_stock->warehouse_present,
                        'date'               => $date_created
                    ]);        
                }
                
            }
            $this->db->set('total_weight', $total_weight)->where('invoice_id', $invoice_id)->update('invoice');

            echo json_encode(['status' => TRUE]);
            $this->session->set_flashdata('success', $this->lang->line('toast_add_success'));
        }

        //View add invoice
        else {
            $invoice_type = array(
                'credit'      => 'Kredit',
                'online'      => 'Online',
                'cash'        => 'Tunai',
            );

            $source = array(
                'library'   => 'Perpustakaan',
                'showroom'  => 'Showroom',
                'warehouse' => 'Gudang'
            );

            $customer_type = get_customer_type();

            $dropdown_book_options = $this->invoice->get_ready_book_list();

            $pages       = $this->pages;
            $main_view   = 'invoice/add_invoice';
            $this->load->view('template', compact('pages', 'main_view', 'invoice_type', 'source', 'customer_type', 'dropdown_book_options'));
        }
    }

    public function add_showroom()
    {
        $customer_type = get_customer_type();

        $dropdown_book_options = $this->invoice->get_ready_book_list_showroom();

        $pages       = 'invoice/add_showroom';
        $main_view   = 'invoice/add_showroom';
        $this->load->view('template', compact('pages', 'main_view', 'customer_type', 'dropdown_book_options'));
    }

    public function edit($invoice_id)
    {
        //post edit invoice
        if ($_POST) {
            //validasi input edit
            $this->invoice->validate_invoice();
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
                    'type'          => $this->input->post('new-customer-type')
                ];
                $this->db->insert('customer', $add);
                $customer_id = $this->db->insert_id();
            }

            $edit = [
                'customer_id'       => $customer_id,
                'due_date'          => $this->input->post('due-date'),
                'source'            => $this->input->post('source'),
                'source_library_id' => $this->input->post('source-library-id'),
                'status'            => 'waiting'
                // 'date_edited'   => date('Y-m-d H:i:s'),
                // 'user_edited'   => $_SESSION['username']
            ];

            $this->db->set($edit)->where('invoice_id', $invoice_id)->update('invoice');

            // Kembalikan stock buku
            $invoice_books  = $this->invoice->fetch_invoice_book($invoice_id);
            foreach ($invoice_books as $invoice_book) {
                $book_stock = $this->book_stock->where('book_id', $invoice_book->book_id)->get();
                $book_stock->warehouse_present += $invoice_book->qty;
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
            // Total berat buku
            $total_weight = 0;
            // Masukkan buku di form faktur ke database
            for ($i = 0; $i < $countsize; $i++) {
                $book = [
                    'invoice_id'    => $invoice_id,
                    'book_id'       => $this->input->post('invoice_book_id')[$i],
                    'qty'           => $this->input->post('invoice_book_qty')[$i],
                    'price'         => $this->input->post('invoice_book_price')[$i],
                    'discount'      => $this->input->post('invoice_book_discount')[$i]
                ];
                $this->db->insert('invoice_book', $book);
                $book_weight = $this->invoice->get_book($book['book_id'])->weight;
                $total_weight +=  $book_weight * $book['qty'];

                // Kurangi Stock Buku
                $book_stock = $this->book_stock->where('book_id', $book['book_id'])->get();
                $book_stock->warehouse_present -= $book['qty'];
                $this->book_stock->where('book_id', $book['book_id'])->update($book_stock);

                // Masukkan transaksi buku
                $this->book_transaction->insert([
                    'book_id'            => $book['book_id'],
                    'invoice_id'         => $invoice_id,
                    'book_stock_id'      => $book_stock->book_stock_id,
                    'stock_initial'      => $book_stock->warehouse_present+$book['qty'],
                    'stock_mutation'     => $book['qty'],
                    'stock_last'         => $book_stock->warehouse_present,
                    'date'               => date('Y-m-d H:i:s')
                ]); 
            }
            $this->db->set('total_weight', $total_weight)->where('invoice_id', $invoice_id)->update('invoice');

            echo json_encode(['status' => TRUE]);
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }
        //view edit invoice
        else {
            $invoice        = $this->invoice->fetch_invoice_id($invoice_id);

            //info customer dan diskon
            $customer = $this->db->select('*')->from('customer')->where('customer_id', $invoice->customer_id)->get()->row();
            $discount_data = $this->db->select('discount')->from('discount')->where('membership', $customer->type)->get()->row();
            $discount = $discount_data->discount;

            $invoice_type = array(
                'credit'      => 'Kredit',
                'online'      => 'Online',
                'cash'        => 'Tunai',
                'showroom'    => 'Showroom',
            );

            $source = array(
                'library'   => 'Perpustakaan',
                'showroom'  => 'Showroom',
                'warehouse' => 'Gudang'
            );

            $customer_type = get_customer_type();

            $invoice_book = $this->invoice->fetch_invoice_book($invoice->invoice_id);

            $dropdown_book_options = $this->invoice->get_ready_book_list();

            $pages       = $this->pages;
            $main_view   = 'invoice/edit_invoice';
            $this->load->view('template', compact('pages', 'invoice', 'invoice_book', 'customer', 'discount', 'main_view', 'invoice_type', 'source', 'customer_type', 'dropdown_book_options'));
        }
    }

    public function action($id, $invoice_status)
    {
        $invoice = $this->invoice->where('invoice_id', $id)->get();
        if (!$invoice) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        $this->db->trans_begin();

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
            $this->invoice->where('invoice_id', $id)->update([
                'status' => $invoice_status,
                'confirm_date' => now(),
                'preparing_deadline' => $preparing_deadline
            ]);
        } else
            // Cancel Faktur
            if ($invoice_status == 'cancel') {
                $this->invoice->where('invoice_id', $id)->update([
                    'status' => $invoice_status,
                    'cancel_date' => now(),
                ]);
                if($invoice->source == 'warehouse') {
                    // Kembalikan stock Gudang buku
                    $invoice_books  = $this->invoice->fetch_invoice_book($id);
                    foreach ($invoice_books as $invoice_book) {
                        $book_stock = $this->book_stock->where('book_id', $invoice_book->book_id)->get();
                        $book_stock->warehouse_present += $invoice_book->qty;
                        $this->book_stock->where('book_id', $invoice_book->book_id)->update($book_stock);
                    }

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
                    // Hapus Transaction yang sudah ada
                    $this->db->where('invoice_id', $id)->delete('book_transaction');
                }
            }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }

        redirect($this->pages);
    }

    public function update_delivery_fee($invoice_id)
    {
        $delivery_fee = $this->input->post('delivery_fee');
        $this->db->set('delivery_fee', $delivery_fee)->where('invoice_id', $invoice_id)->update('invoice');
        echo json_encode(['status' => TRUE]);
    }

    public function generate_pdf($invoice_id)
    {
        $invoice        = $this->invoice->fetch_invoice_id($invoice_id);
        if ($invoice->status != 'waiting' && $invoice->status != 'cancel') {
            $invoice        = $this->invoice->fetch_invoice_id($invoice_id);
            $invoice_books  = $this->invoice->fetch_invoice_book($invoice_id);
            $customer       = $this->invoice->get_customer($invoice->customer_id);

            // PDF
            $this->load->library('pdf');
            $data_format['invoice'] = $invoice ?? '';
            $data_format['invoice_books'] = $invoice_books ?? '';
            $data_format['customer'] = $customer ?? '';

            $html = $this->load->view('invoice/view_invoice_pdf', $data_format, true);

            $file_name = $invoice->number . '_Invoice';

            $this->pdf->generate_pdf_a4_portrait($html, $file_name);
        }
    }

    public function api_get_book($book_id)
    {
        $book = $this->invoice->get_book($book_id);
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
}
