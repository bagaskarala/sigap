<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_transfer extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'book_transfer';
        $this->load->model('book_transfer_model', 'book_transfer');
        $this->load->model('book_transaction/book_transaction_model', 'book_transaction');
        $this->load->model('book_stock/book_stock_model', 'book_stock');
        $this->load->model('library/library_model', 'library');
        $this->load->helper('sales_helper');
    }

    public function index($page = NULL)
    {
        if ($this->_is_warehouse_sales_admin() == TRUE) :
            // all filter
            $filters = [
                'keyword'           => $this->input->get('keyword', true),
                'status'            => $this->input->get('status', true),
            ];

            // custom per page
            $this->book_transfer->per_page = $this->input->get('per_page', true) ?? 10;

            $get_data = $this->book_transfer->filter_book_transfer($filters, $page);

            $book_transfer   = $get_data['book_transfer'];
            $total          = $get_data['total'];
            $pagination     = $this->book_transfer->make_pagination(site_url('book_transfer'), 2, $total);
            $pages          = $this->pages;
            $main_view      = 'book_transfer/index_book_transfer';
            $this->load->view('template', compact('pages', 'main_view', 'book_transfer', 'pagination', 'total'));
        endif;
    }

    public function view($book_transfer_id)
    {
        if (!$this->_is_warehouse_admin()) {
            redirect($this->pages);
        }

        if ($book_transfer_id == null) {
            redirect($this->pages);
        }

        $book_transfer = $this->book_transfer->fetch_book_transfer($book_transfer_id);

        if (!$book_transfer) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        $book_transfer_list = $this->book_transfer->fetch_book_transfer_list($book_transfer_id);
        $pages       = $this->pages;
        $main_view   = 'book_transfer/view/view_book_transfer';
        $this->load->view('template', compact('main_view', 'pages', 'book_transfer', 'book_transfer_list'));
    }
    
    public function generate_pdf_bon($book_transfer_id)
    {
        if(!$this->_is_warehouse_sales_admin()){
            redirect($_SERVER['HTTP_REFERER']);
        }
        else{
            $book_transfer        = $this->book_transfer->fetch_book_transfer($book_transfer_id);
            $book_transfer_list   = $this->book_transfer->fetch_book_transfer_list($book_transfer_id);
    
            // PDF
            $this->load->library('pdf');
            // FORMAT DATA
            if($book_transfer->library_id){
                $data_format['destination'] = $book_transfer->library_name;
            }
            else{
                $data_format['destination'] = 'Showroom';
            }
            $data_format['number']        = $book_transfer->transfer_number ?? '';
            $data_format['transfer_date'] = $book_transfer->transfer_date ?? '';
            $data_format['book_list']     = $book_transfer_list ?? '';
            $html = $this->load->view('book_transfer/format_pdf_bon', $data_format, true);
            $file_name = $data_format['number'].'_Pemindahan Buku';    
            $this->pdf->generate_pdf_a4_landscape($html, $file_name);    
        }

    }

    public function api_get_staff_gudang()
    {
        $staff_gudang = $this->book_transfer->get_staff_gudang();
        return $this->send_json_output(true, $staff_gudang);
    }

    public function api_add_staff_gudang()
    {
        $input = (object) $this->input->post(null, true);

        if (!$input->book_transfer_id || !$input->user_id) {
            return $this->send_json_output(false, $this->lang->line('toast_data_not_available'));
        }

        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        if ($this->book_transfer->check_row_staff_gudang($input->book_transfer_id, $input->user_id) > 0) {
            return $this->send_json_output(false, $this->lang->line('toast_data_duplicate'), 422);
        }

        if ($this->db->insert('book_transfer_user', $input)) {
            return $this->send_json_output(true, $this->lang->line('toast_add_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_add_fail'));
        }
    }

    public function api_delete_staff_gudang($id = null)
    {
        $staff_gudang = $this->db->where('book_transfer_user_id', $id)->get('book_transfer_user')->result();
        if (!$staff_gudang) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        if ($this->db->delete('book_transfer_user', ['book_transfer_user_id' => $id])) {
            return $this->send_json_output(true, $this->lang->line('toast_delete_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_delete_fail'));
        }
    }

    public function api_start_preparing($book_transfer_id)
    {
        // apakah book_transfer tersedia
        $book_transfer = $this->book_transfer->where('book_transfer_id', $book_transfer_id)->get();
        if (!$book_transfer) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk user yang berkaitan dengan book_transfer ini
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }
        
        $is_start_preparing = $this->book_transfer->start_progress($book_transfer_id);

        if ($is_start_preparing) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    // update book_transfer, kirim update via post
    public function api_update($book_transfer_id = null)
    {
        // cek data
        $book_transfer = $this->book_transfer->where('book_transfer_id', $book_transfer_id)->get();
        if (!$book_transfer) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        $input = (object) $this->input->post(null, false);

        // hanya untuk user yang berkaitan dengan book_transfer ini
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        // untuk reset deadline
        if (isset($input->preparing_deadline)) {
            $input->preparing_deadline = empty_to_null($input->preparing_deadline);
        }

        // hilangkan property pembantu yang tidak ada di db
        unset($input->progress);

        if ($this->book_transfer->where('book_transfer_id', $book_transfer_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    public function api_finish_preparing($book_transfer_id)
    {
        // apakah book_transfer tersedia
        $book_transfer = $this->book_transfer->where('book_transfer_id', $book_transfer_id)->get();
        if (!$book_transfer) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk user yang berkaitan dengan book_transfer ini
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }
        
        //update status
        $is_finish_preparing = $this->book_transfer->finish_progress($book_transfer_id);
        
        if ($is_finish_preparing) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    public function final($book_transfer_id = null, $action = null){
        if (!$book_transfer_id || !$action) {
            $this->session->set_flashdata('error', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        if (!$this->_is_sales_admin()) {
            redirect($_SERVER['HTTP_REFERER']);
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        // apakah book transfer tersedia
        $book_transfer = $this->book_transfer->where('book_transfer_id', $book_transfer_id)->get();
        if (!$book_transfer) {
            $this->session->set_flashdata('error', $this->lang->line('toast_data_not_available'));
        }

        // update book stock tapi tabel list buku yg dipindahin blm ada
        $book_transfer_lists  = $this->book_transfer->fetch_book_transfer_list($book_transfer_id);

        // update stok perpustakaan
        if($book_transfer->library_id){
            foreach($book_transfer_lists as $book_transfer_list){
                $book_stock = $this->book_stock->where('book_id', $book_transfer_list->book_id)->get();
                $book_stock->library_present += $book_transfer_list->qty;
                $this->book_stock->where('book_id', $book_transfer_list->book_id)->update($book_stock);
                // stok detail perpustakaan
                $library_stock_detail = $this->book_stock->get_one_library_stock($book_stock->book_stock_id,$book_transfer->library_id);
                if($library_stock_detail){
                    $library_stock_detail->library_stock += $book_transfer_list->qty;
                    $this->db->set('library_stock', $library_stock_detail->library_stock);
                    $this->db->where('library_id', $library_stock_detail->library_id);
                    $this->db->where('book_stock_id', $book_stock->book_stock_id);
                    $this->db->update('library_stock_detail');
                }
                else{
                    $library_stock_insert = [
                        'library_id'    => $book_transfer->library_id,
                        'book_stock_id' => $book_stock->book_stock_id,
                        'library_stock' => $book_transfer_list->qty,
                    ];
                    $this->db->insert('library_stock_detail', $library_stock_insert);                
                }
            }
        }
        // update stok showroom
        else {
            foreach($book_transfer_lists as $book_transfer_list){
                $book_stock = $this->book_stock->where('book_id', $book_transfer_list->book_id)->get();
                $book_stock->showroom_present += $book_transfer_list->qty;
                $this->book_stock->where('book_id', $book_transfer_list->book_id)->update($book_stock);
            }
        }

        foreach($book_transfer_lists as $book_transfer_list){
            $book_stock = $this->book_stock->where('book_id', $book_transfer_list->book_id)->get();
            $book_stock->warehouse_present -= $book_transfer_list->qty;
            // update book stock
            $this->book_stock->where('book_id', $book_transfer_list->book_id)->update($book_stock);
            //insert to book transaction
            $this->book_transaction->insert([
                'book_id' => $book_transfer_list->book_id,
                'book_stock_id' => $book_stock->book_stock_id,
                'book_transfer_id' => $book_transfer_list->book_transfer_id,
                'stock_initial'=> $book_stock->warehouse_present+$book_transfer_list->qty,
                'stock_mutation' => $book_transfer_list->qty,
                'stock_last'=> $book_stock->warehouse_present,
                'date' => now()
            ]);
        }

        // update data book_transfer
        $this->book_transfer->where('book_transfer_id', $book_transfer_id)->update([
            'status' => $action,
            'finish_date' => now()
        ]);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }

        redirect($this->pages . "/view/$book_transfer_id");
    }

    public function add()
    {
        if (!$this->_is_sales_admin()==true) {
            redirect($this->pages);
        }

        if (!$_POST) {
            $input = (object) $this->book_transfer->get_default_values();
            if (!$this->book_transfer->validate() || $this->form_validation->error_array()) {

                $pages       = $this->pages;
                $main_view   = 'book_transfer/book_transfer_add';
                $form_action = 'book_transfer/add';
                $book_transfer_available = $this->book_transfer->get_ready_book_list();
                $this->load->view('template', compact('pages', 'main_view', 'form_action', 'book_transfer_available', 'input'));
                return;
            }
        } else {
            $input = (object) $this->input->post(null, true);
        }

        $input->library_id = empty_to_null($input->library_id);
        $transfer_number = $this->book_transfer->get_transfer_number();
        $book_transfer = (object) [
            'transfer_number' => $transfer_number,
            'status' => 'waiting',
            'transfer_date' => now(),
            'destination' => $input->destination,
            'library_id' => $input->library_id,
        ];
        $this->db->trans_begin();
        // insert book transfer
        $this->book_transfer->insert($book_transfer);
        $book_transfer_id = $this->db->insert_id();
        foreach ($input->book_list as $books){
            $book_transfer_list = (object)[
                'book_transfer_id' => $book_transfer_id,
                'book_id' => $books['book_id'],
                'qty' => $books['qty'],
            ];
            $this->db->insert('book_transfer_list',$book_transfer_list);
        }
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('success', $this->lang->line('toast_add_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_add_success'));
        }
        redirect('book_transfer/add');
    }

    public function api_get_book($book_id)
    {
        return $this->send_json_output(true, $this->book_transfer->get_book($book_id));
    }

    public function edit($book_transfer_id = null)
    {
        if (!$this->_is_warehouse_admin()) {
            redirect($this->pages);
        }

        $book_transfer = $this->book_transfer->fetch_book_transfer($book_transfer_id);
        if (!$book_transfer) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }
        else{
            $pages       = $this->pages;
            $main_view   = 'book_transfer/book_transfer_edit';
            $this->load->view('template', compact('pages', 'main_view', 'book_transfer'));
            return;
        }
    }


    public function update($book_transfer_id)
    {
        $input = (object) $this->input->post(null, true);

        $input->finish_date = empty_to_null($input->finish_date);
        $input->transfer_date = empty_to_null($input->transfer_date);
        $input->preparing_start_date = empty_to_null($input->preparing_start_date);
        $input->preparing_end_date = empty_to_null($input->preparing_end_date);
        $input->preparing_deadline = empty_to_null($input->preparing_deadline);

        $this->db->trans_begin();
        $this->book_transfer->where('book_transfer_id', $book_transfer_id)->update($input);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }

        redirect('book_transfer/view/' . $book_transfer_id);
    }

    public function delete_book_transfer($book_transfer_id = null)
    {
        if (!$this->_is_sales_admin()) {
            redirect($this->pages);
        }

        $book_transfer = $this->book_transfer->where('book_transfer_id', $book_transfer_id)->get();
        if (!$book_transfer) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        if($book_transfer->status=="finish"){
            $this->session->set_flashdata('warning', "Pemindahan buku telah selesai, tidak dapat menghapus data pemindahan.");
            redirect($this->pages);
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        $this->book_transfer->where('book_transfer_id', $book_transfer_id)->delete();
        
        // hapus book_transfer_list, book_transfer_user
        $this->db->where('book_transfer_id',$book_transfer_id)->delete('book_transfer_list');
        $this->db->where('book_transfer_id',$book_transfer_id)->delete('book_transfer_user');

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_delete_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_delete_success'));
        }

        redirect($this->pages);
    }

    public function _is_warehouse_sales_admin()
    {
        if ($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_gudang' || $_SESSION['level'] == 'admin_pemasaran') {
            return TRUE;
        } else {
            $this->session->set_flashdata('error', 'Hanya admin gudang, admin pemasaran, dan superadmin yang dapat mengakses.');
            redirect(base_url());
        }
    }

    public function _is_sales_admin()
    {
        if ($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_pemasaran') {
            return TRUE;
        } else {
            $this->session->set_flashdata('error', 'Hanya admin pemasaran dan superadmin yang dapat mengakses.');
            redirect(base_url());
        }
    }

    private function _is_warehouse_admin()
    {
        if ($this->level == 'superadmin' || $this->level == 'admin_gudang') {
            return true;
        } else {
            $this->session->set_flashdata('error', 'Hanya admin gudang dan superadmin yang dapat mengakses.');
            return false;
        }
    }
}
