<?php defined('BASEPATH') or exit('No direct script access allowed');
class Notifikasi extends Operator_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Notifikasi_model');
        $this->load->library('session');
        $this->pages = 'notifikasi';
    }

    public function index($page = null)
    {
        $filters = [
            'id_user_pembuat' => $this->input->get('user_pembuat', true),
            'keyword'  => $this->input->get('keyword', true),
            'is_read'  => $this->input->get('is_read', true),
            'is_starred' => $this->input->get('is_starred', true),
            'id_draft'  => $this->input->get('id_draft', true),
        ];
        
        $this->Notifikasi_model->per_page = $this->input->get('per_page', true) ?? 10;

        $get_data = $this->Notifikasi_model->filter_notif($filters, $page, $this->user_id);

        //echo var_dump($this);
        // $this->user_id
        //$pages       = $this->pages;
        //$main_view   = 'book/form_book';
        //$form_action = 'book/add';
        //$this->load->view('template', compact('pages', 'main_view', 'form_action', 'input'));
        //return;
        //$this->load->view('template');
        $notif      = $get_data['notif'];
        $total      = $get_data['total'];
        $pagination = $this->Notifikasi_model->make_pagination(site_url('notifikasi/pagen'), 3, $total);
        //echo var_dump($pagination);
        $pages      = $this->pages;
        $main_view  = 'notif';
        $this->load->view('template', compact('pages', 'main_view', 'notif', 'pagination', 'total'));
    }

    public function pagen($page = null)
    {
        $this->index($page);
    }

    public function read_forward($id_notifikasi='', $id_draft='')
    {
        if(!empty($id_draft))
        {
            if(!empty($id_notifikasi))
            {
                $notif = $this->Notifikasi_model->get_notifById($id_notifikasi);
                if(!empty($notif))
                {
                    $data = array('is_read' => 1, 'read_at' => date('Y-m-d H:i:s'));
                    $this->Notifikasi_model->update_notif($data, $id_notifikasi);
                }
            }
            header("Location: " . base_url(). 'draft/view/'. $key->id_draft);
            exit;
        }
        header("Location: " . base_url(). 'notifikasi/index/');
        exit;
    }

    public function read($is_read=0, $id_notifikasi='')
    {
        if(!empty($id_notifikasi))
        {
            $notif = $this->Notifikasi_model->get_notifById($id_notifikasi);
            if(!empty($notif))
            {
                $data = array('is_read' => $is_read, 'read_at' => date('Y-m-d H:i:s'));
                $this->Notifikasi_model->update_notif($data, $id_notifikasi);
            }
        }
        header("Location: " . base_url() . "notifikasi/index");
        exit;
    }

    public function toggle_bintang($toggle=0, $id_notifikasi='')
    {
        if(!empty($id_notifikasi))
        {
            $notif = $this->Notifikasi_model->get_notifById($id_notifikasi);
            if(!empty($notif))
            {
                $data = array('is_starred' => $toggle);
                if(empty($notif[0]->starred_at))
                    $data['read_at'] = date('Y-m-d H:i:s');

                $this->Notifikasi_model->update_notif($data, $id_notifikasi);
            }
        }
        header("Location: " . base_url() . "notifikasi/index");
        exit;
    }

    public function cek_push()
    {
        $result = array();
        $get = $this->Notifikasi_model->get_notif_belum_pushByUserKepadaTgl($this->session->userdata('user_id'), date('Y-m-d'));
        if(!empty($get))
        {
            $result['message'] = $get->ket;
            $result['dari'] = $get->username;

            $data = array('is_pushed' => 1);
            $this->Notifikasi_model->update_notif($data, $get->id);
        }
        echo json_encode($result);
    }

    public function count_belum_read()
    {
        $result = array();
        $result['total'] = $this->Notifikasi_model->get_notif_belum_readByUserKepada($this->session->userdata('user_id'));
        echo json_encode($result);
    }

    public function notif_deadline()
    {
        //cek apakah sistem telah mengirim notifikasi deadline pada hari ini
        $check = $this->Notifikasi_model->get_notif_systemByTgl(date('Y-m-d'));
        //memastikan bahwa notifikasi deadline hanya dilakukan 1 hari sekali setiap jam 00.01
        if(!empty($check))
        {
            echo "notifikasi hari ini telah dijalankan.";
            exit;
        }

        //notif review H-3
        $date = Date('Y-m-d', strtotime('+3 days'));
        $hmin3 = Date('d-m-Y', strtotime('+3 days'));
        $get = $this->Notifikasi_model->get_draftByreview_deadline($date);
        foreach ($get as $key) {
            $data = array(
                'ket' => "Review draft dengan judul ".$key->draft_title." mendekati deadline. Segera selesaikan draft sebelum tanggal ".$hmin3.".",
                'id_draft' => $key->draft_id,
                'id_user_pembuat' => -1,
                'id_user_kepada' => $key->user_id
            );
            $this->Notifikasi_model->insert_notifikasi($data);
        }
        //notif editorial hari H
        $date = Date('Y-m-d');
        $get = $this->Notifikasi_model->get_draftByreview_deadline($date);
        foreach ($get as $key) {
            $data = array(
                'ket' => "Review draft dengan judul ".$key->draft_title." memasuki deadline. Segera selesaikan draft agar dapat dilanjutkan ke tahap berikutnya.",
                'id_draft' => $key->draft_id,
                'id_user_pembuat' => -1,
                'id_user_kepada' => $key->user_id
            );
            $this->Notifikasi_model->insert_notifikasi($data);
        }
        //notif editorial h+1
        $date = Date('Y-m-d', strtotime('-1 day'));
        $get = $this->Notifikasi_model->get_draftByreview_deadline($date);
        foreach ($get as $key) {
            $data = array(
                'ket' => "Review draft dengan judul ".$key->draft_title." melebihi deadline. Tingkatkan lagi kinerja Anda.",
                'id_draft' => $key->draft_id,
                'id_user_pembuat' => -1,
                'id_user_kepada' => $key->user_id
            );
            $this->Notifikasi_model->insert_notifikasi($data);
        }

        //notif editiorial H-3
        $date = Date('Y-m-d', strtotime('+3 days'));
        $hmin3 = Date('d-m-Y', strtotime('+3 days'));
        $get = $this->Notifikasi_model->get_draftByedit_deadline($date);
        foreach ($get as $key) {
            $data = array(
                'ket' => "Editorial draft dengan judul ".$key->draft_title." mendekati deadline. Segera selesaikan draft sebelum tanggal ".$hmin3.".",
                'id_draft' => $key->draft_id,
                'id_user_pembuat' => -1,
                'id_user_kepada' => $key->user_id
            );
            $this->Notifikasi_model->insert_notifikasi($data);
        }
        //notif editorial hari H
        $date = Date('Y-m-d');
        $get = $this->Notifikasi_model->get_draftByedit_deadline($date);
        foreach ($get as $key) {
            $data = array(
                'ket' => "Editorial draft dengan judul ".$key->draft_title." memasuki deadline. Segera selesaikan draft agar dapat dilanjutkan ke tahap berikutnya.",
                'id_draft' => $key->draft_id,
                'id_user_pembuat' => -1,
                'id_user_kepada' => $key->user_id
            );
            $this->Notifikasi_model->insert_notifikasi($data);
        }
        //notif editorial h+1
        $date = Date('Y-m-d', strtotime('-1 day'));
        $get = $this->Notifikasi_model->get_draftByedit_deadline($date);
        foreach ($get as $key) {
            $data = array(
                'ket' => "Editorial draft dengan judul ".$key->draft_title." melebihi deadline. Tingkatkan lagi kinerja Anda.",
                'id_draft' => $key->draft_id,
                'id_user_pembuat' => -1,
                'id_user_kepada' => $key->user_id
            );
            $this->Notifikasi_model->insert_notifikasi($data);
        }

        //notif layouting H-3
        $date = Date('Y-m-d', strtotime('+3 days'));
        $get = $this->Notifikasi_model->get_draftBylayout_deadline($date);
        foreach ($get as $key) {
            $data = array(
                'ket' => "Layouting draft dengan judul ".$key->draft_title." mendekati deadline. Segera selesaikan draft sebelum tanggal ".$hmin3.".",
                'id_draft' => $key->draft_id,
                'id_user_pembuat' => -1,
                'id_user_kepada' => $key->user_id
            );
            $this->Notifikasi_model->insert_notifikasi($data);
        }
        //notif layouting hari H
        $date = Date('Y-m-d');
        $get = $this->Notifikasi_model->get_draftBylayout_deadline($date);
        foreach ($get as $key) {
            $data = array(
                'ket' => "Layouting draft dengan judul ".$key->draft_title." memasuki deadline. Segera selesaikan draft agar dapat dilanjutkan ke tahap berikutnya.",
                'id_draft' => $key->draft_id,
                'id_user_pembuat' => -1,
                'id_user_kepada' => $key->user_id
            );
            $this->Notifikasi_model->insert_notifikasi($data);
        }
        //notif layouting h+1
        $date = Date('Y-m-d', strtotime('-1 day'));
        $get = $this->Notifikasi_model->get_draftBylayout_deadline($date);
        foreach ($get as $key) {
            $data = array(
                'ket' => "Layouting draft dengan judul ".$key->draft_title." melebihi deadline. Tingkatkan lagi kinerja Anda.",
                'id_draft' => $key->draft_id,
                'id_user_pembuat' => -1,
                'id_user_kepada' => $key->user_id
            );
            $this->Notifikasi_model->insert_notifikasi($data);
        }

        echo "notifikasi deadline berhasil dijalankan.";
    }
}
