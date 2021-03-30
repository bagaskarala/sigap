<?php defined('BASEPATH') or exit('No direct script access allowed');

class Invoice extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'invoice';
        $this->load->model('invoice_model', 'invoice');
        $this->load->model('book/Book_model', 'book');
    }

    public function index($page = NULL)
    {

        $filters = [
            'keyword'   => $this->input->get('keyword', true),
            'type'      => $this->input->get('type', true),
            'status'    => $this->input->get('status', true)
        ];

        $this->invoice->per_page = $this->input->get('per_page', true) ?? 10;

        $get_data = $this->invoice->filter_invoice($filters, $page);

        $invoice = $get_data['invoice'];
        $total      = $get_data['total'];
        $pagination = $this->invoice->make_pagination(site_url('invoice'), 2, $total);

        $pages      = $this->pages;
        $main_view  = 'invoice/index_invoice';
        $this->load->view('template', compact('pages', 'main_view', 'invoice', 'pagination', 'total'));
    }

    public function add()
    {
        //if($this->check_level_gudang() == TRUE):
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

        $customer_type = array(
            'distributor'      => 'Distributor',
            'reseller'      => 'Reseller',
            'penulis'        => 'Penulis',
            'member'        => 'Member',
            'biasa'        => ' - '
        );

        $dropdown_book_options = $this->invoice->get_ready_book_list();

        $pages       = $this->pages;
        $main_view   = 'invoice/add_invoice';
        $this->load->view('template', compact('pages', 'main_view', 'invoice_type', 'source', 'customer_type', 'dropdown_book_options'));
        //endif;
    }

    public function edit($invoice_id)
    {
        $invoice        = $this->invoice->fetch_invoice_id($invoice_id);
        //if($this->check_level_gudang() == TRUE):

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

        $customer_type = array(
            'distributor'      => 'Distributor',
            'reseller'      => 'Reseller',
            'penulis'        => 'Penulis',
            'member'        => 'Member',
            'biasa'        => ' - '
        );

        $invoice_book = $this->invoice->fetch_invoice_book($invoice->invoice_id);

        $dropdown_book_options = $this->invoice->get_ready_book_list();

        $pages       = $this->pages;
        $main_view   = 'invoice/edit_invoice';
        $this->load->view('template', compact('pages', 'invoice', 'invoice_book', 'main_view', 'invoice_type', 'source', 'customer_type', 'dropdown_book_options'));
        // endif;
    }

    public function debug()
    {
        $book = $this->invoice->get_ready_book_list();
        var_dump($book);
    }

    // public function edit($logistic_id){
    //     if($this->check_level_gudang() == TRUE):
    //     $pages       = $this->pages;
    //     $main_view   = 'logistic/logistic_edit';
    //     $lData       = $this->logistic->fetch_logistic_id($logistic_id);
    //     if(empty($lData) == FALSE):
    //     $this->load->view('template', compact('pages', 'main_view', 'lData'));
    //     else:
    //     $this->session->set_flashdata('error','Halaman tidak ditemukan.');
    //     redirect(base_url(), 'refresh');
    //     endif;
    //     endif;
    // }

    public function view($invoice_id)
    {
        // if($this->check_level() == TRUE):
        $pages          = $this->pages;
        $main_view      = 'invoice/view_invoice';
        $invoice        = $this->invoice->fetch_invoice_id($invoice_id);
        $invoice_books  = $this->invoice->fetch_invoice_book($invoice_id);
        //join invoice books + books untuk qty dan diskon

        // foreach($invoice_books as $invoice_book)
        // {
        //     $total = $invoice_book->harga * $invoice_book->qty * (1 - $invoice_book->discount);
        //     var_dump($invoice_book);
        // }


        // $get_stock      = $this->logistic->fetch_stock_by_id($logistic_id);
        // $stock_history  = $get_stock['stock_history'];
        // $stock_last     = $get_stock['stock_last'];
        // if(empty($lData) == FALSE):
        //var_dump($invoice_books);
        $this->load->view('template', compact('pages', 'main_view', 'invoice', 'invoice_books'));
        // else:
        // $this->session->set_flashdata('error','Halaman tidak ditemukan.');
        // redirect(base_url(), 'refresh');
        // endif;
    }

    public function add_invoice()
    {
        // if($this->check_level_gudang() == TRUE):
        $this->load->library('form_validation');
        // $this->load->helper(array('form', 'url'));

        $this->form_validation->set_rules('number', 'Nomor Faktur', 'required');
        $this->form_validation->set_rules('due-date', 'Jatuh Tempo', 'required');
        $this->form_validation->set_rules('type', 'Tipe Faktur', 'required');
        $this->form_validation->set_rules('invoice_book_id[]', 'Buku Invoice', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Faktur gagal ditambah.');
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        } else {
            $check = $this->invoice->add_invoice();
            if ($check   ==  TRUE) {
                $this->session->set_flashdata('success', 'Faktur berhasil ditambah.');
                redirect('invoice');
            } else {
                $this->session->set_flashdata('error', 'Faktur gagal ditambah 2.');
                redirect($_SERVER['HTTP_REFERER'], 'refresh');
            }
        }

        // endif;
    }

    public function update_status()
    {
        $invoice_id = $this->input->post('invoice_id');
        $status = $this->input->post('status');

        $this->invoice->update_status($invoice_id, $status);
    }

    public function edit_invoice($invoice_id)
    {
        // if($this->check_level_gudang() == TRUE):
        $this->form_validation->set_rules('number', 'Nomor Faktur', 'required');
        $this->form_validation->set_rules('due-date', 'Jatuh Tempo', 'required');
        $this->form_validation->set_rules('type', 'Tipe Faktur', 'required');
        $this->form_validation->set_rules('invoice_book_id[]', 'Buku Invoice', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Invoice gagal diubah.');
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        } else {
            $check = $this->invoice->edit_invoice($invoice_id);
            if ($check   ==  TRUE) {
                $this->session->set_flashdata('success', 'Invoice berhasil diubah.');
                redirect('invoice/view/' . $invoice_id);
            } else {
                $this->session->set_flashdata('error', 'Invoice gagal diubah.');
                redirect($_SERVER['HTTP_REFERER'], 'refresh');
            }
        }
        // endif;
    }

    // public function delete_logistic($logistic_id){
    //     if($this->check_level_gudang() == TRUE):
    //     $check = $this->logistic->delete_logistic($logistic_id);
    //     if($check   ==  TRUE){
    //         $this->session->set_flashdata('success','Logistik berhasil di hapus.');
    //         redirect('logistic');
    //     }else{
    //         $this->session->set_flashdata('error','Logistik gagal di hapus.');
    //         redirect('logistic');
    //     }
    //     endif;
    // }

    // public function add_logistic_stock(){
    //     if($this->check_level_gudang() == TRUE):
    //     $this->load->library('form_validation');
    //     $this->form_validation->set_rules('modifier_warehouse', 'Stok Gudang', 'max_length[10]');
    //     $this->form_validation->set_rules('modifier_production', 'Stok Produksi', 'max_length[10]');
    //     $this->form_validation->set_rules('modifier_other', 'Stok Lainnya', 'max_length[10]');
    //     $this->form_validation->set_rules('input_notes', 'Catatan', 'required|max_length[256]');

    //     if($this->form_validation->run() == FALSE){
    //         $this->session->set_flashdata('error','Gagal mengubah stok.');
    //         redirect($_SERVER['HTTP_REFERER'], 'refresh');
    //     }else{
    //         $check  =   $this->logistic->add_logistic_stock();
    //         if($check   ==  TRUE){
    //             $this->session->set_flashdata('success','Berhasil mengubah stok.');
    //             redirect($_SERVER['HTTP_REFERER'], 'refresh');
    //         }else{
    //             $this->session->set_flashdata('error','Gagal mengubah stok.');
    //             redirect($_SERVER['HTTP_REFERER'], 'refresh');
    //         }
    //     }
    //     endif;
    // }

    public function api_get_book($book_id)
    {
        return $this->send_json_output(true, $this->invoice->get_book($book_id));
    }

    public function api_get_customer($customer_id)
    {
        $customer =  $this->invoice->get_customer($customer_id);
        return $this->send_json_output(true, $customer);
    }

    public function api_get_last_invoice_number($type)
    {
        return $this->send_json_output(true, $this->invoice->get_last_invoice_number($type));
    }

    public function api_get_discount($customerType)
    {
        return $this->send_json_output(true, $this->invoice->get_discount($customerType));
    }

    public function pdf()
    {
        $this->load->library('dompdf_gen');

        $this->load->view('view_invoice_pdf');

        $paper_size = 'A4';
        $orientation = 'landscape';
        $html = $this->output->get_output();
        $this->dompdf->set_paper($paper_size, $orientation);

        $this->dompdf->load_html($html);
        $this->dompdf->render();
        $this->dompdf->stream('invoice.pdf', array('Attachment' => 0));
    }

    // public function delete_logistic_stock($logistic_stock_id){
    //     if($this->check_level_gudang() == TRUE):
    //     $isDeleted  = $this->logistic->delete_logistic_stock($logistic_stock_id);
    //     if($isDeleted   ==  TRUE){
    //         $this->session->set_flashdata('success','Berhasil menghapus data stok logistik.');
    //         redirect($_SERVER['HTTP_REFERER'], 'refresh');
    //     }else{
    //         $this->session->set_flashdata('error','Gagal menghapus data stok logistik.');
    //         redirect($_SERVER['HTTP_REFERER'], 'refresh');
    //     }
    //     endif;
    // }

    // public function check_level(){
    //     if($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_gudang' || $_SESSION['level'] == 'admin_keuangan' || $_SESSION['level'] == 'admin_penerbitan' || $_SESSION['level'] == 'admin_percetakan'){
    //         return TRUE;
    //     }else{
    //         $this->session->set_flashdata('error','Hanya admin gudang, admin keuangan, admin penerbitan dan superadmin yang dapat mengakses.');
    //         redirect(base_url(), 'refresh');
    //     }
    // }

    // public function check_level_gudang(){
    //     if($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_gudang'){
    //         return TRUE;
    //     }else{
    //         $this->session->set_flashdata('error','Hanya admin gudang dan superadmin yang dapat mengakses.');
    //         redirect(base_url(), 'refresh');
    //     }
    // }
}
