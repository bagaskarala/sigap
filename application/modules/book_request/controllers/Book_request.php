<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_request extends Warehouse_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'book_request';
        $this->load->model('book_request_model', 'book_request');
        $this->load->model('invoice/invoice_model', 'invoice');
        $this->load->model('book_stock/book_stock_model', 'book_stock');
    }

    public function index($page = NULL){
        // all filter
        $filters = [
            'keyword'              => $this->input->get('keyword', true),
            'type'                 => $this->input->get('type', true),
            'status'               => $this->input->get('status', true),
            'source'               => $this->input->get('source', true),
        ];

        // custom per page
        $this->book_request->per_page = $this->input->get('per_page', true) ?? 10;
        $get_data = $this->invoice->filter_book_request($filters, $page);

        $book_request   = $get_data['book_request'];
        $total          = $get_data['total'];
        $pagination     = $this->book_request->make_pagination(site_url('book_request'), 2, $total);
        $pages          = $this->pages;
        $main_view      = 'book_request/index_book_request';
        $this->load->view('template', compact('pages', 'main_view', 'book_request', 'pagination','total'));
    }

    public function view($invoice_id){
        $book_request = $this->invoice->fetch_invoice_id($invoice_id);
        if (!$book_request) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }
        $invoice_books  = $this->invoice->fetch_invoice_book($invoice_id);

        $pages        = $this->pages;
        $main_view    = 'book_request/view/view_book_request';
        $this->load->view('template', compact('pages', 'main_view', 'book_request','invoice_books'));
        return;
    }

    public function api_get_staff_gudang()
    {
        $staff_gudang = $this->book_request->get_staff_gudang();
        return $this->send_json_output(true, $staff_gudang);
    }

    public function api_add_staff_gudang()
    {
        $input = (object) $this->input->post(null, true);

        if (!$input->invoice_id || !$input->user_id) {
            return $this->send_json_output(false, $this->lang->line('toast_data_not_available'));
        }

        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        if ($this->book_request->check_row_staff_gudang($input->invoice_id, $input->user_id) > 0) {
            return $this->send_json_output(false, $this->lang->line('toast_data_duplicate'), 422);
        }

        if ($this->db->insert('book_request_user', $input)) {
            return $this->send_json_output(true, $this->lang->line('toast_add_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_add_fail'));
        }
    }

    public function api_delete_staff_gudang($id = null)
    {
        $staff_gudang = $this->db->where('book_request_user_id', $id)->get('book_request_user')->result();
        if (!$staff_gudang) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        if ($this->db->delete('book_request_user', ['book_request_user_id' => $id])) {
            return $this->send_json_output(true, $this->lang->line('toast_delete_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_delete_fail'));
        }
    }

    public function api_start_preparing($invoice_id)
    {
        // apakah book_request tersedia
        $book_request = $this->invoice->where('invoice_id', $invoice_id)->get();
        if (!$book_request) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk user yang berkaitan dengan book_request ini
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }
        
        $is_start_preparing = $this->invoice->start_progress($invoice_id);

        if ($is_start_preparing) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    // update book_request, kirim update via post
    public function api_update($invoice_id = null)
    {
        // cek data
        $book_request = $this->invoice->where('invoice_id', $invoice_id)->get();
        if (!$book_request) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        $input = (object) $this->input->post(null, false);

        // hanya untuk user yang berkaitan dengan book_request ini
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        // untuk reset deadline
        if (isset($input->preparing_deadline)) {
            $input->preparing_deadline = empty_to_null($input->preparing_deadline);
        }


        if ($this->invoice->where('invoice_id', $invoice_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    public function api_finish_preparing($invoice_id)
    {
        // apakah book_request tersedia
        $book_request = $this->invoice->where('invoice_id', $invoice_id)->get();
        if (!$book_request) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // berisi 'progress' untuk conditional dibawah
        $input = (object) $this->input->post(null, false);

        // hanya untuk user yang berkaitan dengan book_request ini
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }
        
        //update status
        $is_finish_preparing = $this->invoice->finish_progress($invoice_id);
        
        if ($is_finish_preparing) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    public function _is_warehouse_admin(){
        if($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_gudang'){
            return TRUE;
        }else{
            $this->session->set_flashdata('error','Hanya admin gudang dan superadmin yang dapat mengakses.');
            redirect(base_url());
        }
    }    
}