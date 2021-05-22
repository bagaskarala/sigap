<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_receive extends Warehouse_Controller
{
    public $per_page = 10;

    public function __construct()
    {
        parent::__construct();
        $this->pages = "book_receive";
        $this->load->model('book_receive/book_receive_model', 'book_receive');
        $this->load->model('book_stock/book_stock_model', 'book_stock');
        $this->load->model('book_transaction/book_transaction_model', 'book_transaction');
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
            $this->db->set($input)->where('book_receive_id', $book_receive_id)->update('book_receive');
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        } else {
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        }
        redirect('book_receive');
    }

    //view details of book receive
    public function view($book_receive_id = null)
    {
        if (!$this->_is_warehouse_admin()) {
            redirect($this->pages);
        }

        if ($book_receive_id == null) {
            redirect($this->pages);
        }

        $book_receive = $this->book_receive->get_book_receive($book_receive_id);
        $filename = $filename = strtolower($book_receive->order_number . '_serah_terima_acc');

        if (!$book_receive) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        $is_handover = $book_receive->is_handover;
        $is_wrapping = $book_receive->is_wrapping;
        $is_final    = $book_receive->book_receive_status == 'finish';
        $uploaded_file = $this->book_receive->find_file_ext($filename);

        $pages       = $this->pages;
        $main_view   = 'book_receive/view/overview';
        $form_action = "book_receive/edit/$book_receive_id";
        $this->load->view('template', compact('form_action', 'main_view', 'pages', 'book_receive', 'is_final', 'is_handover', 'is_wrapping', 'uploaded_file'));
    }

    // masukkan deadline sebelum proses dimulai
    public function add_deadline($book_receive_id)
    {
        if ($this->_is_warehouse_admin() == TRUE && $this->input->method() == 'post') {
            $deadline = $this->input->post('deadline');
            $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
            $this->form_validation->set_rules('deadline', 'Deadline Penerimaan Buku', 'required');
            if (!$book_receive) {
                $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            } else {
                if ($this->form_validation->run() == true) {
                    $book_receive->deadline = $deadline;
                    $this->book_receive->where('book_receive_id', $book_receive_id)->update($book_receive);
                    $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
                } else {
                    $this->session->set_flashdata('error', 'Gagal menambahkan data deadline.');
                    redirect($_SERVER['HTTP_REFERER'], 'refresh');
                }
            }
        } else {
            $this->session->set_flashdata('warning', $this->lang->line('toast_edit_fail'));
        }
        redirect('book_receive/view/' . $book_receive_id);
    }

    // generate pdf berita acara serah terima
    public function generate_pdf_handover($book_receive_id, $progress)
    {
        $book_receive        = $this->book_receive->get_book_receive($book_receive_id);
        // PDF
        $this->load->library('pdf');

        // FORMAT DATA
        $data_format['jobtype'] = 'Serah Terima';
        $data_format['title'] = $book_receive->book_title ?? '';
        $data_format['ordernumber'] = $book_receive->order_number ?? '';
        $data_format['total'] = $book_receive->total ?? '';
        $data_format['total_postprint'] = $book_receive->total_postprint ?? '';
        $data_format['handover_end_date'] = date('d/m/Y', strtotime($book_receive->handover_end_date)) ?? '';
        $data_format['notes'] = $book_receive->{"{$progress}_notes"} ?? '';
        $format = $this->load->view('book_receive/format_pdf_handover', $data_format, true);
        $this->pdf->loadHtml($format);

        // (Optional) Setup the paper size and orientation
        $this->pdf->set_paper('A4', 'landscape');

        // Render the HTML as PDF
        $this->pdf->render();
        $this->pdf->stream(strtolower($data_format['ordernumber'] . '_' . $data_format['jobtype']));
    }

    // upload pdf berita acara serah terima
    public function upload_handover()
    {
        if ($this->_is_warehouse_admin() && $this->input->method() == 'post') {
            $book_receive_id = $this->input->post('receive_id');
            $book_receive = $this->book_receive->get_book_receive($book_receive_id);
            if (!empty($_FILES) && $book_receive) {
                $filename = strtolower($book_receive->order_number . '_serah_terima_acc');
                $upload   = $this->book_receive->upload_handover('handover_file', $filename);
                if ($upload) {
                    $this->session->set_flashdata('success', 'Upload file sukses');
                }
            } else {
                $this->session->set_flashdata('error', 'Upload file gagal');
            }
        } else {
            $this->session->set_flashdata('error', 'Upload file gagal');
        }
        redirect($this->pages . "/view/$book_receive_id");
    }

    // generate pdf lembar antrian wrapping
    public function generate_pdf_wrapping($book_receive_id, $progress)
    {
        $book_receive        = $this->book_receive->get_book_receive($book_receive_id);
        $staff_gudang        = $this->book_receive->get_staff_gudang_by_progress($progress, $book_receive_id);
        $staff = '';
        foreach ($staff_gudang as $val) {
            $staff .= $val->username . ", ";
        }
        // PDF
        $this->load->library('pdf');

        // FORMAT DATA
        $data_format['jobtype'] = 'Wrapping';
        $data_format['title'] = $book_receive->book_title ?? '';
        $data_format['ordernumber'] = $book_receive->order_number ?? '';
        $data_format['total'] = $book_receive->total ?? '';
        $data_format['total_postprint'] = $book_receive->total_postprint ?? '';
        $data_format['wrapping_start_date'] = date('d/m/Y', strtotime($book_receive->wrapping_start_date)) ?? '';
        $data_format['wrapping_end_date'] = date('d/m/Y', strtotime($book_receive->wrapping_end_date)) ?? '';
        $data_format['wrapping_deadline'] = date('d/m/Y', strtotime($book_receive->wrapping_deadline)) ?? '';
        $data_format['staff'] = $staff;
        $data_format['notes'] = $book_receive->{"{$progress}_notes"} ?? '';
        $format = $this->load->view('book_receive/format_pdf_wrapping', $data_format, true);
        $this->pdf->loadHtml($format);

        // (Optional) Setup the paper size and orientation
        $this->pdf->set_paper('A4', 'portrait');

        // Render the HTML as PDF
        $this->pdf->render();
        $this->pdf->stream(strtolower($data_format['ordernumber'] . '_' . $data_format['jobtype']));
    }

    // ambil staff gudang
    public function api_get_staff_gudang()
    {
        $staff_gudang = $this->book_receive->get_staff_gudang();
        return $this->send_json_output(true, $staff_gudang);
    }

    // tambah staff gudang untuk staff bertugas sesuai progress
    public function api_add_staff_gudang()
    {
        $input = (object) $this->input->post(null, true);

        if (!$input->book_receive_id || !$input->user_id || !$input->progress) {
            return $this->send_json_output(false, $this->lang->line('toast_data_not_available'));
        }

        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        if ($this->book_receive->check_row_staff_gudang($input->book_receive_id, $input->user_id, $input->progress) > 0) {
            return $this->send_json_output(false, $this->lang->line('toast_data_duplicate'), 422);
        }

        if ($this->db->insert('book_receive_user', $input)) {
            return $this->send_json_output(true, $this->lang->line('toast_add_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_add_fail'));
        }
    }

    // hapus staff gudang untuk staff bertugas sesuai progress
    public function api_delete_staff_gudang($id = null)
    {
        $staff_gudang = $this->db->where('book_receive_user_id', $id)->get('book_receive_user')->result();
        if (!$staff_gudang) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        if ($this->db->delete('book_receive_user', ['book_receive_user_id' => $id])) {
            return $this->send_json_output(true, $this->lang->line('toast_delete_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_delete_fail'));
        }
    }

    // mulai progress handover (serah terima) atau wrapping
    public function api_start_progress($book_receive_id)
    {
        // apakah book_receive tersedia
        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        if (!$book_receive) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // berisi 'progress' untuk conditional dibawah
        $input = (object) $this->input->post(null, false);

        // hanya untuk user yang berkaitan dengan book_receive ini
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        $is_start_progress = $this->book_receive->start_progress($book_receive_id, $input->progress);

        if ($is_start_progress) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    // update book_receive, kirim update via post
    public function api_update($book_receive_id = null)
    {
        // cek data
        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        if (!$book_receive) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        $input = (object) $this->input->post(null, false);

        // hanya untuk user yang berkaitan dengan book_receive ini
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        // untuk reset deadline
        if (isset($input->handover_deadline)) {
            $input->handover_deadline = empty_to_null($input->handover_deadline);
        }
        if (isset($input->wrapping_deadline)) {
            $input->wrapping_deadline = empty_to_null($input->wrapping_deadline);
        }

        // hilangkan property pembantu yang tidak ada di db
        unset($input->progress);

        if ($this->book_receive->where('book_receive_id', $book_receive_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    // selesai progress handover/wrapping
    public function api_finish_progress($book_receive_id)
    {
        // apakah order cetak tersedia
        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        if (!$book_receive) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // berisi 'progress' untuk conditional dibawah
        $input = (object) $this->input->post(null, false);

        // hanya untuk user yang berkaitan dengan book_receive ini
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        $is_finish_progress = $this->book_receive->finish_progress($book_receive_id, $input->progress);

        if ($is_finish_progress) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    // update book_receive untuk aksi progress, kirim update via post
    public function api_action_progress($book_receive_id)
    {
        // cek data
        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        if (!$book_receive) {
            $message = $this->lang->line('toast_data_not_available');
            return $this->send_json_output(false, $message, 404);
        }

        // hanya untuk superadmin
        if (!$this->_is_warehouse_admin()) {
            $message = $this->lang->line('toast_error_not_authorized');
            return $this->send_json_output(false, $message);
        }

        $input = (object) $this->input->post(null, false);

        // cek status apakah akan direvert
        if ($input->revert) {
            $input->{"is_$input->progress"} = 0;

            // kembali ke status 'sedang diproses'
            if ($input->progress == 'handover') {
                $input->book_receive_status = 'handover';
            } elseif ($input->progress == 'wrapping') {
                $input->book_receive_status = 'wrapping';
            }
        } 
        else {
            $input->{"is_$input->progress"} = $input->accept;

            // update book_receive status ketika selesai progress
            if ($input->progress == 'handover') {
                $input->book_receive_status = 'handover_finish';
            } elseif ($input->progress == 'wrapping') {
                $input->book_receive_status = 'wrapping_finish';
            }
        }

        // jika end date kosong, maka isikan nilai now
        if (!$book_receive->{"{$input->progress}_end_date"}) {
            $input->{"{$input->progress}_end_date"} = now();
        }

        // hilangkan property pembantu yang tidak ada di db
        unset($input->progress);
        unset($input->accept);
        unset($input->revert);

        if ($this->book_receive->where('book_receive_id', $book_receive_id)->update($input)) {
            return $this->send_json_output(true, $this->lang->line('toast_edit_success'));
        } else {
            return $this->send_json_output(false, $this->lang->line('toast_edit_fail'));
        }
    }

    // finalisasi book receive, update stok buku
    public function final($book_receive_id = null)
    {
        if (!$book_receive_id) {
            $this->session->set_flashdata('error', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        if (!$this->_is_warehouse_admin()) {
            redirect($_SERVER['HTTP_REFERER']);
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        // apakah book receive tersedia
        $book_receive = $this->book_receive->where('book_receive_id', $book_receive_id)->get();
        if (!$book_receive) {
            $this->session->set_flashdata('error', $this->lang->line('toast_data_not_available'));
        }

        // update data book_receive
        $this->book_receive->where('book_receive_id', $book_receive_id)->update([
            'book_receive_status' => 'finish',
            'finish_date' => now()
        ]);

        $book_stock = $this->book_stock->where('book_id', $book_receive->book_id)->get();
        $book_stock_print = $this->book_receive->get_print_order($book_receive->print_order_id);

        // update book stock
        if ($book_stock) {
            $book_stock->warehouse_present += $book_stock_print->total_postprint;
            $this->book_stock->where('book_id', $book_stock->book_id)->update($book_stock);
        } else {
            $this->book_stock->insert([
                'book_id'            => $book_receive->book_id,
                'warehouse_present'  => $book_stock_print->total_postprint
            ]);
        }
        
        //insert to book transaction
        $book_stock = $this->book_stock->where('book_id', $book_receive->book_id)->get();
        $this->book_transaction->insert([
            'book_id'            => $book_receive->book_id,
            'book_receive_id'    => $book_receive->book_receive_id,
            'book_stock_id'      => $book_stock->book_stock_id,
            'stock_initial'      => $book_stock->warehouse_present-$book_stock_print->total_postprint,
            'stock_mutation'     => $book_stock_print->total_postprint,
            'stock_last'         => $book_stock->warehouse_present,
            'date'               => now()
        ]);
        
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }

        redirect($this->pages . "/view/$book_receive_id");
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
