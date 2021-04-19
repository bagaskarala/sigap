<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_receive extends Warehouse_Controller
{
    public $per_page = 10;

    public function __construct()
    {
        parent::__construct();
        $this->pages = "book_receive";
        $this->load->model('book_receive/book_receive_model', 'book_receive');
        // $this->load->model('book_stock/book_stock_model', 'book_stock');
        // $this->load->model('book_transaction/book_transaction_model', 'book_transaction');
    }

    //index book receive
    public function index($page = NULL)
    {
        //all filters
        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'book_receive_status' => $this->input->get('book_receive_status', true)
        ];

        // custom per page
        $this->book_receive->per_page = $this->input->get('per_page', true) ?? 10;
        $get_data = $this->book_receive->filter_book_receive($filters, $page);

        $book_receives = $get_data['book_receives'];
        $total = $get_data['total'];
        $pagination = $this->book_receive->make_pagination(site_url('book_receives'), 2, $total);

        $pages      = $this->pages;
        $main_view  = 'book_receive/index_bookreceive';
        $this->load->view('template', compact('pages', 'main_view', 'book_receives', 'pagination', 'total'));
    }

    //edit book receive
    public function edit($book_receive_id)
    {
        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        if (!$book_receive) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        } else {
            $pages       = $this->pages;
            $main_view   = 'book_receive/edit_bookreceive';
            $this->load->view('template', compact('pages', 'main_view', 'book_receive'));
        }
    }

    public function update($book_receive_id)
    {
        $input = (object) $this->input->post(null, true);
        $this->form_validation->set_rules('deadline', 'Deadline Penerimaan Buku', 'required');

        $input->finish_date = empty_to_null($input->finish_date);
        $input->handover_start_date = empty_to_null($input->handover_start_date);
        $input->handover_end_date = empty_to_null($input->handover_end_date);
        $input->handover_deadline = empty_to_null($input->handover_deadline);
        $input->wrapping_start_date = empty_to_null($input->wrapping_start_date);
        $input->wrapping_end_date = empty_to_null($input->wrapping_end_date);
        $input->wrapping_deadline = empty_to_null($input->wrapping_deadline);

        if ($this->form_validation->run() == true) {
            if ($input->is_handover == 0 || $input->is_wrapping == 0) {
                $input->finish_date = null;
            }
            if ($input->is_handover == 0){
                $input->wrapping_start_date = null;
                $input->wrapping_end_date = null;
                $input->is_wrapping = 0;
                if ($input->handover_start_date == null){
                    $input->handover_end_date = null; 
                    $input->book_receive_status = 'waiting';
                }
                else if ($input->handover_end_date == null){
                    $input->book_receive_status = 'handover';
                }
                else if ($input->handover_end_date != null){
                    $input->book_receive_status = 'handover_approval';
                }
            }
            else if ($input->is_wrapping == 0){
                if ($input->wrapping_start_date == null){
                    $input->wrapping_end_date = null; 
                    $input->book_receive_status = 'handover_finish';
                }
                else if ($input->wrapping_end_date == null){
                    $input->book_receive_status = 'wrapping';
                }
                else if ($input->wrapping_end_date != null){
                    $input->book_receive_status = 'wrapping_approval';
                }
            }
            else {
                if ($input->finish_date == null){
                    $input->book_receive_status = 'wrapping_finish';
                }
                else {
                    $input->book_receive_status = 'finish';
                }
            }
            $this->db->set($input)->where('book_receive_id', $book_receive_id)->update('book_receive');
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        } else {
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        }
        redirect('book_receive');
    }
    public function delete($book_receive_id = null)
    {
        if (!$this->_is_warehouse_admin()) {
            redirect($this->pages);
        }

        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        if (!$book_receive) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        $this->book_receive->where('book_receive_id', $book_receive_id)->delete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_delete_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_delete_success'));
        }

        redirect($this->pages);
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
