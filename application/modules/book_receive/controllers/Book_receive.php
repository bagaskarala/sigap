<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_receive extends MY_Controller
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
        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        $book_receive_id = $this->input->post('book_receive_id');
        $entry_date = $this->input->post('entry_date');
        $deadline = $this->input->post('deadline');
        $finish_date = $this->input->post('finish_date');
        $is_handover = $this->input->post('is_handover');
        $handover_start_date = $this->input->post('handover_start_date');
        $handover_end_date = $this->input->post('handover_end_date');
        $handover_staff = $this->input->post('handover_staff');
        $handover_deadline = $this->input->post('handover_deadline');
        $is_wrapping = $this->input->post('is_wrapping');
        $wrapping_start_date = $this->input->post('wrapping_start_date');
        $wrapping_end_date = $this->input->post('wrapping_end_date');
        $wrapping_staff = $this->input->post('wrapping_staff');
        $wrapping_deadline = $this->input->post('wrapping_deadline');

        $this->form_validation->set_rules('deadline', 'Deadline Penerimaan Buku', 'required');

        // untuk mengubah status saat edit
        $status_waiting           = ['book_receive_status' => 'waiting'];
        $status_handover          = ['book_receive_status' => 'handover'];
        $status_handover_approval = ['book_receive_status' => 'handover_approval'];
        $status_handover_finish   = ['book_receive_status' => 'handover_finish'];
        $status_wrapping          = ['book_receive_status' => 'wrapping'];
        $status_wrapping_approval = ['book_receive_status' => 'wrapping_approval'];
        $status_wrapping_finish   = ['book_receive_status' => 'wrapping_finish'];
        $status_finish            = ['book_receive_status' => 'finish'];

        if (empty($entry_date)) {
            $entry_date = empty_to_null($entry_date);
        }
        if (empty($finish_date)) {
            $finish_date = empty_to_null($finish_date);
        }
        if (empty($handover_start_date)) {
            $handover_start_date = empty_to_null($handover_start_date);
        }
        if (empty($handover_end_date)) {
            $handover_end_date = empty_to_null($handover_end_date);
        }
        if (empty($handover_deadline)) {
            $handover_deadline = empty_to_null($handover_deadline);
        }
        if (empty($wrapping_start_date)) {
            $wrapping_start_date = empty_to_null($wrapping_start_date);
        }
        if (empty($wrapping_end_date)) {
            $wrapping_end_date = empty_to_null($wrapping_end_date);
        }
        if (empty($wrapping_deadline)) {
            $wrapping_deadline = empty_to_null($wrapping_deadline);
        }

        $data = [
            'entry_date' => $entry_date,
            'deadline' => $deadline,
            'finish_date' => $finish_date,
            'is_handover' => $is_handover,
            'handover_start_date' => $handover_start_date,
            'handover_end_date' => $handover_end_date,
            // 'handover_staff' => $handover_staff,
            'handover_deadline' => $handover_deadline,
            'is_wrapping' => $is_wrapping,
            'wrapping_start_date' => $wrapping_start_date,
            'wrapping_end_date' => $wrapping_end_date,
            // 'wrapping_staff' => $wrapping_staff,
            'wrapping_deadline' => $wrapping_deadline
        ];

        // untuk mengubah status berdasarkan data-data yang dihilangkan
        if ($this->form_validation->run() == true) {
            $this->db->set($data)->where('book_receive_id', $book_receive_id)->update('book_receive');
            if ($finish_date == null) {
                if ($is_handover == 0 && $is_wrapping == 0) {
                    if (
                        $handover_start_date == null && $handover_deadline == null && $handover_end_date == null
                        && $wrapping_start_date == null && $wrapping_deadline == null && $wrapping_end_date == null
                    ) {
                        $this->db->set($status_waiting)->where('book_receive_id', $book_receive_id)->update('book_receive');
                    }
                    if (!$handover_start_date == null && !$handover_deadline == null) {
                        if ($handover_end_date == null) {
                            $this->db->set($status_handover)->where('book_receive_id', $book_receive_id)->update('book_receive');
                        } else if (!$handover_end_date == null) {
                            $this->db->set($status_handover_approval)->where('book_receive_id', $book_receive_id)->update('book_receive');
                        }
                    }
                }
                if ($is_handover == 1 && $is_wrapping == 0) {
                    if ($wrapping_start_date == null && $wrapping_deadline == null && $wrapping_end_date == null) {
                        $this->db->set($status_handover_finish)->where('book_receive_id', $book_receive_id)->update('book_receive');
                    }
                    if (!$wrapping_start_date == null && !$wrapping_deadline == null) {
                        if ($wrapping_end_date == null) {
                            $this->db->set($status_wrapping)->where('book_receive_id', $book_receive_id)->update('book_receive');
                        }
                        if (!$wrapping_end_date == null) {
                            $this->db->set($status_wrapping_approval)->where('book_receive_id', $book_receive_id)->update('book_receive');
                        }
                    }
                }
                if ($is_handover == 1 && $is_wrapping == 1) {
                    $this->db->set($status_wrapping_finish)->where('book_receive_id', $book_receive_id)->update('book_receive');
                }
            }
            if (!$finish_date == null && $is_handover == 1 && $is_wrapping == 1) {
                $this->db->set($status_finish)->where('book_receive_id', $book_receive_id)->update('book_receive');
            }
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        } else {
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        }
        redirect('book_receive');
        // redirect('book_receive/view/' . $book_receive->book_receive_id);
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
