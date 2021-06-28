<?php defined('BASEPATH') or exit('No direct script access allowed');

class Proforma extends Sales_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'proforma';
        $this->load->model('proforma_model', 'proforma');
        $this->load->model('book/book_model', 'book');
        $this->load->model('book_stock/book_stock_model', 'book_stock');
        $this->load->model('book_transaction/book_transaction_model', 'book_transaction');
        $this->load->helper('sales_helper');
    }

    public function index($page = NULL)
    {
        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'customer_type'     => $this->input->get('customer_type', true)
        ];

        $this->proforma->per_page = $this->input->get('per_page', true) ?? 10;

        $get_data = $this->proforma->filter_proforma($filters, $page);

        //data proforma
        $proforma    = $get_data['proforma'];
        $total      = $get_data['total'];
        $pagination = $this->proforma->make_pagination(site_url('proforma'), 2, $total);

        $pages      = $this->pages;
        $main_view  = 'proforma/index_proforma';
        $this->load->view('template', compact('pages', 'main_view', 'proforma', 'pagination', 'total'));
    }

    public function view($proforma_id)
    {
        $pages          = $this->pages;
        $main_view      = 'proforma/view_proforma';
        $proforma       = $this->proforma->fetch_proforma_id($proforma_id);
        $proforma_books  = $this->proforma->fetch_proforma_book($proforma_id);
        $proforma->customer = $this->proforma->get_customer($proforma->customer_id);

        $this->load->view('template', compact('pages', 'main_view', 'proforma', 'proforma_books'));
    }

    public function action($id, $proforma_status)
    {
        $proforma = $this->proforma->where('proforma_id', $id)->get();
        if (!$proforma) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }
        $this->db->trans_begin();
        $flag = true;
        $empty_books = array();

        // Confirm Proforma
        if ($proforma_status == 'confirm') {
            //cek stok gudang dengan proforma_book
            $books = $this->proforma->fetch_proforma_book($id);
            foreach ($books as $book) {
                $book->stock = $this->proforma->fetch_warehouse_stock($book->book_id);
                $qty = intval($book->qty);
                $stock = intval($book->stock->warehouse_present);
                if ($qty > $stock) {
                    $flag = false;
                    array_push($empty_books, $book);
                }
            }
            if ($flag) {
                $proforma       = $this->proforma->fetch_proforma_id($id);
                $invoice_number = $this->proforma->get_last_proforma_number(true);

                //pemindahan data dari proforma ke faktur
                $date_created       = date('Y-m-d H:i:s');
                $add = [
                    'number'            => $invoice_number,
                    'customer_id'       => $proforma->customer_id,
                    'due_date'          => $proforma->due_date,
                    'type'              => 'cash',
                    'source'            => 'warehouse',
                    'status'            => 'waiting',
                    'issued_date'       => $date_created
                    // 'user_created'      => $user_created
                ];
                $this->db->insert('invoice', $add);

                // ID faktur terbaru untuk diisi buku
                $invoice_id = $this->db->insert_id();

                $total_weight = 0;

                //pemindahan data dari proforma_book ke invoice_book
                foreach ($books as $book) {
                    $add_book = [
                        'invoice_id'    => $invoice_id,
                        'book_id'       => $book->book_id,
                        'qty'           => $book->qty,
                        'price'         => $book->price,
                        'discount'      => $book->discount
                    ];
                    $this->db->insert('invoice_book', $add_book);

                    $book_weight = $this->proforma->get_book($book->book_id)->weight;
                    $total_weight +=  $book_weight * $book->qty;

                    // Kurangi Stock Buku
                    $book_stock = $this->book_stock->where('book_id', $book->book_id)->get();
                    $book_stock->warehouse_present -= $book->qty;
                    $this->book_stock->where('book_id', $book->book_id)->update($book_stock);

                    // Masukkan transaksi buku
                    $this->book_transaction->insert([
                        'book_id'            => $book->book_id,
                        'invoice_id'         => $invoice_id,
                        'book_stock_id'      => $book_stock->book_stock_id,
                        'stock_initial'      => $book_stock->warehouse_present+$book->qty,
                        'stock_mutation'     => $book->qty,
                        'stock_last'         => $book_stock->warehouse_present,
                        'date'               => $date_created
                    ]);
                }

                //delete data proforma
                $this->db->where('proforma_id', $id)->delete('proforma');
                $this->db->where('proforma_id', $id)->delete('proforma_book');
                $redirect = true;
            }

        } else if ($proforma_status == 'cancel') {
            //delete data proforma
            $this->db->where('proforma_id', $id)->delete('proforma');
            $this->db->where('proforma_id', $id)->delete('proforma_book');
            $redirect = false;
        }

        if (!$flag) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_convert_empty'));
            $this->session->set_flashdata('empty_books', $empty_books);
            redirect('proforma/edit/' . $id);
        } else {
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
            }
        }

        if ($redirect) redirect('invoice/view/' . $invoice_id);
        else redirect($this->pages);
    }

    public function add()
    {
        //post add proforma
        if ($_POST) {
            //validasi input
            $this->proforma->validate_proforma();
            $date_created       = date('Y-m-d H:i:s');

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

            $add = [
                'number'            => $this->proforma->get_last_proforma_number(),
                'customer_id'       => $customer_id,
                'due_date'          => $this->input->post('due-date'),
                'issued_date'       => $date_created
                // 'user_created'      => $user_created
            ];
            $this->db->insert('proforma', $add);

            // ID faktur terbaru untuk diisi buku
            $proforma_id = $this->db->insert_id();

            // Jumlah Buku di Faktur
            $countsize = count($this->input->post('proforma_book_id'));

            // Masukkan buku di form proforma ke database
            for ($i = 0; $i < $countsize; $i++) {
                $book = [
                    'proforma_id'    => $proforma_id,
                    'book_id'       => $this->input->post('proforma_book_id')[$i],
                    'qty'           => $this->input->post('proforma_book_qty')[$i],
                    'price'         => $this->input->post('proforma_book_price')[$i],
                    'discount'      => $this->input->post('proforma_book_discount')[$i]
                ];
                $this->db->insert('proforma_book', $book);
            }
            echo json_encode(['status' => TRUE]);
            $this->session->set_flashdata('success', $this->lang->line('toast_add_success'));
        }

        //View add proforma
        else {
            $customer_type = get_customer_type();

            $dropdown_book_options = $this->proforma->get_ready_book_list();

            $form_action = "proforma/add";
            $form_type = "add";
            $pages       = $this->pages;
            $main_view   = 'proforma/form_proforma';
            $this->load->view('template', compact('pages', 'main_view', 'customer_type', 'dropdown_book_options', 'form_action', 'form_type'));
        }
    }

    public function edit($proforma_id)
    {
        //post edit proforma
        if ($_POST) {
            //validasi input edit
            $this->proforma->validate_proforma();
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
                // 'date_edited'   => date('Y-m-d H:i:s'),
                // 'user_edited'   => $_SESSION['username']
            ];

            $this->db->set($edit)->where('proforma_id', $proforma_id)->update('proforma');

            // Jumlah Buku di Proforma
            $countsize = count($this->input->post('proforma_book_id'));

            //hapus proforma_book yang sudah ada 
            $this->db->where('proforma_id', $proforma_id)->delete('proforma_book');

            // Masukkan buku di form proforma ke database
            for ($i = 0; $i < $countsize; $i++) {
                $book = [
                    'proforma_id'    => $proforma_id,
                    'book_id'       => $this->input->post('proforma_book_id')[$i],
                    'qty'           => $this->input->post('proforma_book_qty')[$i],
                    'price'         => $this->input->post('proforma_book_price')[$i],
                    'discount'      => $this->input->post('proforma_book_discount')[$i]
                ];
                $this->db->insert('proforma_book', $book);
            }
            echo json_encode(['status' => TRUE]);
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }
        //view edit proforma
        else {
            $proforma      = $this->proforma->fetch_proforma_id($proforma_id);

            //info customer dan diskon
            $customer = $this->db->select('*')->from('customer')->where('customer_id', $proforma->customer_id)->get()->row();
            $discount_data = $this->db->select('discount')->from('discount')->where('membership', $customer->type)->get()->row();
            $discount = $discount_data->discount;

            $customer_type = get_customer_type();

            $proforma_book = $this->proforma->fetch_proforma_book($proforma->proforma_id);

            $dropdown_book_options = $this->proforma->get_ready_book_list();

            $form_action = "proforma/edit/$proforma_id";
            $form_type = "edit";
            $pages       = $this->pages;
            $main_view   = 'proforma/form_proforma';
            $this->load->view('template', compact('pages', 'proforma', 'proforma_book', 'customer', 'discount', 'main_view', 'customer_type', 'dropdown_book_options', 'form_action', 'form_type'));
        }
    }

    public function generate_pdf($proforma_id)
    {
        $proforma      = $this->proforma->fetch_proforma_id($proforma_id);
        $proforma_books = $this->proforma->fetch_proforma_book($proforma->proforma_id);
        $proforma->customer = $this->proforma->get_customer($proforma->customer_id);

        // PDF
        $this->load->library('pdf');
        $data_format['proforma'] = $proforma ?? '';
        $data_format['proforma_books'] = $proforma_books ?? '';

        $html = $this->load->view('proforma/view_proforma_pdf', $data_format, true);

        $file_name = $proforma->number . '_Proforma';

        $this->pdf->generate_pdf_a4_portrait($html, $file_name);
    }

    public function api_get_book($book_id)
    {
        $book = $this->proforma->get_book($book_id);
        return $this->send_json_output(true, $book);
    }

    public function api_get_customer($customer_id)
    {
        $customer =  $this->proforma->get_customer($customer_id);
        return $this->send_json_output(true, $customer);
    }

    // Auto fill diskon berdasar jenis customer
    public function api_get_discount($customerType)
    {
        $discount = $this->proforma->get_discount($customerType);
        return $this->send_json_output(true, $discount);
    }

}
