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
}
