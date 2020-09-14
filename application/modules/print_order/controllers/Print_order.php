<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Print_order extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->pages = 'print_order';

        // load model
        $this->load->model('print_order_model', 'print_order');
    }

    public function index($page = null)
    {
        // all filter
        $filters = [
            'keyword'            => $this->input->get('keyword', true),
            'category'            => $this->input->get('category', true),
            'type'               => $this->input->get('type', true),
            'priority'           => $this->input->get('priority', true),
            'mode'           => $this->input->get('mode', true),
            'print_order_status' => $this->input->get('print_order_status', true)
        ];

        // custom per page
        $this->print_order->per_page = $this->input->get('per_page', true) ?? 10;

        $get_data = $this->print_order->filter_print_order($filters, $page);

        // echo '<pre>';
        // print_r($get_data['print_orders']);
        // echo '</pre>';
        // die();

        $print_orders = $get_data['print_orders'];
        $total        = $get_data['total'];
        $pagination   = $this->print_order->make_pagination(site_url('print_order'), 2, $total);
        $pages        = $this->pages;
        $main_view    = 'print_order/index_print_order';
        $this->load->view('template', compact('pages', 'main_view', 'print_orders', 'pagination', 'total'));
    }

    public function add()
    {
        if (!$this->_is_printing_admin()) {
            redirect($this->pages);
        }

        if (!$_POST) {
            $input = (object) $this->print_order->get_default_values();
        } else {
            $input = (object) $this->input->post(null, true);
            // catat orang yang menginput order cetak
            $input->user_id = $_SESSION['user_id'];
            $input->category = 'new';

            // repopulate print_order_file ketika validasi form gagal
            if (!isset($input->print_order_file)) {
                $input->print_order_file = null;
                $this->session->set_flashdata('print_order_file_no_data', $this->lang->line('form_error_file_no_data'));
            }
        }



        // conditional rules, untuk book dan nonbook
        if ($input->print_mode == 'nonbook') {
            $input->category = 'nonbook';
            $this->form_validation->set_rules('name', $this->lang->line('form_print_order_name'), 'required');
        } elseif($input->print_mode == 'book') {
            $input->category = $this->_check_book($input->book_id);
            $this->form_validation->set_rules('book_id', $this->lang->line('form_book_title'), 'required');
        } elseif($input->print_mode == 'outsideprint') {
            $input->category = 'outsideprint';
            $this->form_validation->set_rules('book_id', $this->lang->line('form_book_title'), 'required');
        }

        if ($this->print_order->validate()) {
            if (!empty($_FILES) && $file_name = $_FILES['print_order_file']['name']) {
                $generated_name = $this->_generate_file_name($file_name);
                $upload          = $this->print_order->upload_print_order_file('print_order_file', $generated_name);
                if ($upload) {
                    $input->print_order_file = $generated_name;
                }
            }
        }

        if (!$this->print_order->validate() || $this->form_validation->error_array()) {

            $pages       = $this->pages;
            $main_view   = 'print_order/form_print_order_add';
            $form_action = 'print_order/add';
            $this->load->view('template', compact('pages', 'main_view', 'form_action', 'input'));
            return;
        }

        // handle book dan nonbook
        if ($input->category == 'nonbook') {
            $input->book_id = null;
        } else {
            $input->name = null;
        }

        // set status awal
        $input->print_order_status = 'waiting';

        unset($input->print_mode);

        // insert print order
        $print_order_id = $this->print_order->insert($input);

        if ($print_order_id) {
            $this->session->set_flashdata('success', $this->lang->line('toast_add_success'));
        } else {
            $this->session->set_flashdata('error', $this->lang->line('toast_add_fail'));
        }

        redirect('print_order/view/' . $print_order_id);
    }

    public function edit($print_order_id)
    {
        if (!$this->_is_printing_admin()) {
            redirect($this->pages);
        }

        $print_order = $this->print_order->get_print_order($print_order_id);
        if (!$print_order) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        if (!$_POST) {
            $input = (object)  $print_order;
        } else {
            $input = (object) $this->input->post(null, true);
            // catat orang yang menginput order cetak
            $input->user_id = $_SESSION['user_id'];

            // repopulate print_order_file ketika validasi form gagal
            if (!isset($input->print_order_file)) {
                $input->print_order_file = $print_order->print_order_file;
                $this->session->set_flashdata('print_order_file_no_data', $this->lang->line('form_error_file_no_data'));
            }
        }

        if ($input->category == 'nonbook') {
            $this->form_validation->set_rules('name', $this->lang->line('form_print_order_name'), 'required');
        } else {
            $this->form_validation->set_rules('book_id', $this->lang->line('form_book_title'), 'required');
        }


        if ($this->print_order->validate()) {
            if (!empty($_FILES) && $file_name = $_FILES['print_order_file']['name']) {
                $generated_name = $this->_generate_file_name($file_name);
                $upload          = $this->print_order->upload_print_order_file('print_order_file', $generated_name);
                if ($upload) {
                    $input->print_order_file = $generated_name;
                    if ($print_order->print_order_file) {
                        $this->print_order->delete_print_order_file($print_order->print_order_file);
                    }
                }
            }
        }

        if (!$this->print_order->validate() || $this->form_validation->error_array()) {
            $pages       = $this->pages;
            $main_view   = 'print_order/form_print_order_edit';
            $form_action = "print_order/edit/$print_order_id";
            $this->load->view('template', compact('pages', 'main_view', 'form_action', 'input'));
            return;
        }

        // handle book dan nonbook
        if ($input->category == 'nonbook') {
            $input->book_id = null;
        } elseif ($input->category == 'normal') {
            $input->name = null;
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        //  hapus order cetak jika check delete_file
        if (isset($input->delete_file) && $input->delete_file == 1) {
            $this->print_order->delete_print_order_file($print_order->print_order_file);
            $this->print_order->delete_print_order_file($input->print_order_file);
            $input->print_order_file = null;
            unset($input->delete_file);
        }

        // update print order
        $this->print_order->where('print_order_id', $print_order_id)->update($input);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }

        redirect('print_order/view/' . $print_order_id);
    }

    public function view($print_order_id = null)
    {
        if (!$this->_is_printing_admin()) {
            redirect($this->pages);
        }

        if ($print_order_id == null) {
            redirect($this->pages);
        }

        $print_order = $this->print_order->get_print_order($print_order_id);

        if (!$print_order) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        $is_final = $print_order->print_order_status == 'finish';

        $pages       = $this->pages;
        $main_view   = 'print_order/view/overview';
        $form_action = "print_order/edit/$print_order_id";
        $this->load->view('template', compact('form_action', 'main_view', 'pages', 'print_order', 'is_final'));
    }

    public function final($print_order_id = null, $action = null)
    {
        if (!$print_order_id || !$action) {
            $this->session->set_flashdata('error', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        // apakah order cetak tersedia
        $print_order = $this->print_order->where('print_order_id', $print_order_id)->get();
        if (!$print_order) {
            $this->session->set_flashdata('error', $this->lang->line('toast_data_not_available'));
        }

        // update data order cetak
        $this->print_order->where('print_order_id', $print_order_id)->update([
            'print_order_status' => $action,
            'finish_date' => $action == 'finish' ? now() : null
        ]);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }

        redirect($this->pages . "/view/$print_order_id");
    }

    public function delete($print_order_id = null)
    {
        if (!$this->_is_printing_admin()) {
            redirect($this->pages);
        }

        $print_order = $this->print_order->where('print_order_id', $print_order_id)->get();
        if (!$print_order) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        if ($this->print_order->where('print_order_id', $print_order_id)->delete()) {
            $this->print_order->delete_print_order_file($print_order->print_order_file);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_delete_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_delete_success'));
        }

        redirect($this->pages);
    }

    public function api_start_progress($print_order_id)
    {
        // apakah order cetak tersedia
        $print_order = $this->print_order->where('print_order_id', $print_order_id)->get();
        if (!$print_order) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk user yang berkaitan dengan print_order ini
        if (!$this->_is_printing_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        // berisi 'progress' untuk conditional dibawah
        $input = (object) $this->input->post(null, false);

        $is_start_progress = $this->print_order->start_progress($print_order_id, $input->progress);

        if ($is_start_progress) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    public function api_finish_progress($print_order_id)
    {
        // apakah order cetak tersedia
        $print_order = $this->print_order->where('print_order_id', $print_order_id)->get();
        if (!$print_order) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk admin
        if (!$this->_is_printing_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        // berisi 'progress' untuk conditional dibawah
        $input = (object) $this->input->post(null, false);

        $is_finish_progress = $this->print_order->finish_progress($print_order_id, $input->progress);

        if ($is_finish_progress) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    // update print_order, kirim update via post
    public function api_update($print_order_id = null)
    {
        // cek data
        $print_order = $this->print_order->where('print_order_id', $print_order_id)->get();
        if (!$print_order) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // // hanya untuk admin
        // if (!$this->_is_printing_admin()) {
        //     $message = $this->lang->line('toast_error_not_authorized');
        //     return $this->send_json_output(false, $message);
        // }


        $input = (object) $this->input->post(null, false);

        // untuk reset deadline
        if (isset($input->preprint_deadline)) {
            $input->preprint_deadline = empty_to_null($input->preprint_deadline);
        }
        if (isset($input->print_deadline)) {
            $input->print_deadline = empty_to_null($input->print_deadline);
        }
        if (isset($input->postprint_deadline)) {
            $input->postprint_deadline = empty_to_null($input->postprint_deadline);
        }

        if ($this->print_order->where('print_order_id', $print_order_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    // update print_order, kirim update via post
    public function api_action_progress($print_order_id)
    {
        // cek data
        $print_order = $this->print_order->where('print_order_id', $print_order_id)->get();
        if (!$print_order) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk admin
        if (!$this->_is_printing_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        $input = (object) $this->input->post(null, false);

        // cek status apakah akan direvert
        if ($input->revert) {
            $input->{"is_$input->progress"} = 0;

            // kembali ke status 'sedang diproses'
            if ($input->progress == 'preprint') {
                $input->print_order_status = 'preprint';
            } elseif ($input->progress == 'print') {
                $input->print_order_status = 'print';
            } elseif ($input->progress == 'postprint') {
                $input->print_order_status = 'posprint';
            }
        } else {
            $input->{"is_$input->progress"} = $input->accept;

            // update print_order status ketika selesai progress
            if ($input->progress == 'preprint') {
                $input->print_order_status = $input->accept ? 'preprint_finish' : 'reject';
            } elseif ($input->progress == 'print') {
                $input->print_order_status = $input->accept ? 'print_finish' : 'reject';
            } elseif ($input->progress == 'postprint') {
                $input->print_order_status = $input->accept ? 'postprint_finish' : 'reject';
            }
        }

        // jika end date kosong, maka isikan nilai now
        if (!$print_order->{"{$input->progress}_end_date"}) {
            $input->{"{$input->progress}_end_date"} = now();
        }

        // hilangkan property pembantu yang tidak ada di db
        unset($input->progress);
        unset($input->accept);
        unset($input->revert);

        if ($this->print_order->where('print_order_id', $print_order_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    private function _check_book($book_id)
    {
        $this->load->model('book/book_model', 'book');

        $book = $this->book->join('draft')->get_where(['book_id' => $book_id]);
        $is_reprint_book = $book->is_reprint ?? null;

        // cek buku apakah pernah order cetak
        $book_order = $this->print_order->get_where(['book_id' => $book_id]);

        if ($book_order) {
            return 'reprint';
        }

        if (!$book_order) {
            if ($is_reprint_book == 'n') {
                return 'new';
            }
            if ($is_reprint_book == 'y') {
                return 'revise';
            }
        }

        return null;
    }

    public function api_check_book($book_id)
    {
        return $this->send_json_output(true, $this->_check_book($book_id));
    }

    public function action_print_postprint($print_order_id){
        // cek data
        $print_order = $this->print_order->where('print_order_id', $print_order_id)->get();
        if (!$print_order) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk admin
        if (!$this->_is_printing_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        $input = (object) $this->input->post(null, false);

        // cek status apakah akan direvert
        if ($input->revert) {
            $input->is_print = 0;
            $input->is_postprint = 0;
            $input->print_order_status = 'print';
            // $input->{"is_$input->progress"} = 0;

            // kembali ke status 'sedang diproses'
            // if ($input->progress == 'preprint') {
            //     $input->print_order_status = 'preprint';
            // } elseif ($input->progress == 'print') {
            //     $input->print_order_status = 'print';
            // } elseif ($input->progress == 'postprint') {
            //     $input->print_order_status = 'posprint';
            // }
        } else {
            $input->is_print = $input->accept;
            $input->is_postprint = $input->accept;
            $input->print_order_status = $input->accept ? 'print_finish' : 'reject';
            // $input->{"is_$input->progress"} = $input->accept;

            // update print_order status ketika selesai progress
            // if ($input->progress == 'preprint') {
            //     $input->print_order_status = $input->accept ? 'preprint_finish' : 'reject';
            // } elseif ($input->progress == 'print') {
            //     $input->print_order_status = $input->accept ? 'print_finish' : 'reject';
            // } elseif ($input->progress == 'postprint') {
            //     $input->print_order_status = $input->accept ? 'postprint_finish' : 'reject';
            // }
        }

        // jika end date kosong, maka isikan nilai now
        if (empty($print_order->print_end_date)==TRUE || empty($print_order->postprint_end_date)==TRUE) {
            // $input->{"{$input->progress}_end_date"} = now();
            $input->print_end_date = now();
            $input->postprint_end_date = now();
        }

        // hilangkan property pembantu yang tidak ada di db
        unset($input->progress);
        unset($input->accept);
        unset($input->revert);

        if ($this->print_order->where('print_order_id', $print_order_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    public function finish_print_postprint($print_order_id){
        // apakah order cetak tersedia
        $print_order = $this->print_order->where('print_order_id', $print_order_id)->get();
        if (!$print_order) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk admin
        if (!$this->_is_printing_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        // berisi 'progress' untuk conditional dibawah
        $input = (object) $this->input->post(null, false);

        $is_finish_progress = $this->print_order->finish_print_postprint($print_order_id);

        if ($is_finish_progress) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    public function api_upload_preprint_file(){
        if (!$this->_is_printing_admin()) {
            redirect($this->pages);
        }

        if (!$_POST) {
            $input = (object) $this->print_order->get_default_values();
        } else {
            $input = (object) $this->input->post(null, true);

            // repopulate preprint_file ketika validasi form gagal
            if (!isset($input->preprint_file)) {
                $input->preprint_file = null;
                $this->session->set_flashdata('preprint_file_no_data', $this->lang->line('form_error_file_no_data'));
            }
        }

        if ($this->print_order->validate()) {
            if (!empty($_FILES) && $file_name = $_FILES['preprint_file']['name']) {
                $generated_name = $this->_generate_file_name($file_name);
                $upload          = $this->print_order->upload_preprint_file('preprint_file', $generated_name);
                if ($upload) {
                    $input->preprint_file = $generated_name;
                }
            }
        }

        // insert print order
        $print_order_id = $this->print_order->insert($input);

        if ($print_order_id) {
            $this->session->set_flashdata('success', $this->lang->line('toast_add_success'));
        } else {
            $this->session->set_flashdata('error', $this->lang->line('toast_add_fail'));
        }

        redirect('print_order/view/' . $print_order_id.'#preprint-progress');
    }

    public function api_upload_progress($print_order_id)
    {
        // apakah draft tersedia
        $print_order = $this->print_order->join('book')->where('print_order_id', $print_order_id)->get();
        if (!$print_order) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk author pertama
        // if ($this->level == 'author') {
        //     $draft_author_status = $this->_get_author_permission($this->role_id, $draft_id);
        //     if ($draft_author_status == 0) {
        //         $message = $this->lang->line('toast_error_not_authorized');
        //         return $this->send_json_output(false, $message);
        //     }
        // }

        $input = (object) $this->input->post(null, true);
        $progress = 'preprint';

        // tiap upload, update upload date
        $this->print_order->edit_print_order_date($print_order_id, $progress . '_upload_date');
        $upload_by_field         = $progress . '_upload_by';
        $input->$upload_by_field = $this->username;

        $column = "{$progress}_file";

        if (!empty($_FILES) && $file_name = $_FILES[$column]['name']) {
            $print_order_file_name = $this->_generate_print_order_file_name($file_name, $print_order->book_title, $column);
            $upload = $this->print_order->upload_file($column, $print_order_file_name);
            if ($upload) {
                $input->$column = $print_order_file_name;
                // Delete old draft file
                if ($print_order->$column) {
                    $this->print_order->delete_preprint_file($print_order->$column);
                }
            }

            // validasi jenis file sesuai model
            if ($this->upload->display_errors()) {
                return $this->send_json_output(false, $this->upload->display_errors(), 422);
            }
        }

        // unset unnecesary data
        unset($input->progress);

        if ($this->print_order->where('print_order_id', $print_order_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    // hapus progress draft
    public function api_delete_progress($print_order_id)
    {
        // apakah draft tersedia
        $print_order = $this->print_order->where('print_order_id', $print_order_id)->get();
        if (!$print_order) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        $input = (object) $this->input->post(null, true);
        $progress = $input->progress;
        $file_type = $input->file_type;
        if ($file_type == 'file') {
            if (!$this->print_order->delete_preprint_file($print_order->{$input->progress . "_file"})) {
                return $this->send_json_output(false, $this->lang->line('toast_delete_fail'));
            }

            // ketika hapus file, update upload date, update upload by, delete progress file
            $print_order->{$progress . '_file'} = '';
        } else  if ($file_type == 'link') {
            $print_order->{$progress . '_file_link'} = '';
        }
        $print_order->{$progress . '_upload_date'} = date('Y-m-d H:i:s');
        $upload_by_field         = $progress . '_upload_by';
        $print_order->$upload_by_field = $this->username;

        if ($this->print_order->where('print_order_id', $print_order_id)->update($print_order)) {
            return $this->send_json_output(true, $this->lang->line('toast_delete_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_delete_fail'));
        }
    }

    public function download_preprint_file($filename){
        $this->load->helper('download');
        force_download('./preprintfile/'.$filename, NULL);
    }

    public function api_set_stock($print_order_id){
        // cek data
        $print_order = $this->print_order->where('print_order_id', $print_order_id)->get();
        if (!$print_order) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        $input = (object) $this->input->post(null, false);

        if ($this->print_order->where('print_order_id', $print_order_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    private function _generate_print_order_file_name($print_order_file_name, $print_order_title, $progress = null)
    {
        $get_extension = explode(".", $print_order_file_name)[1];
        if ($progress) {
            return str_replace(" ", "_", $print_order_title . '_' . $progress . '_' . date('YmdHis') . '.' . $get_extension); // progress file name
        } else {
            return str_replace(" ", "_", $print_order_title . '_' . date('YmdHis') . '.' . $get_extension); // draft file name
        }
    }

    private function _is_printing_admin()
    {
        if ($this->level == 'superadmin' || $this->level == 'admin_percetakan') {
            return true;
        } else {
            $this->session->set_flashdata('error', 'Hanya admin percetakan dan superadmin yang dapat mengakses.');
            return false;
        }
    }

    private function _is_superadmin()
    {
        if ($this->level == 'superadmin') {
            return TRUE;
        } else {
            $this->session->set_flashdata('error', 'Hanya superadmin yang dapat mengakses.');
            redirect(base_url(), 'refresh');
        }
    }

    private function _generate_file_name($file_name)
    {
        $split_filename = explode(".", $file_name);
        return str_replace(" ", "_", 'Print_order_' . $split_filename[0] . '_' . date('YmdHis') . '.' . $split_filename[1]); // print_order file name
    }
}

/* End of file Print_order.php */
