<?php defined('BASEPATH') or exit('No direct script access allowed');
class Library extends Warehouse_Sales_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'library';
 
        $this->load->model('Library_model', 'library');
    }
 
    public function index($page = null)
    {
        $libraries = $this->library->order_by('library_name')->get_all();
        $total     = count($libraries);
        $pages     = $this->pages;
        $main_view = 'library/index_library';
        $this->load->view('template', compact('pages', 'main_view', 'libraries', 'total'));
    }
 
    public function add()
    {
        if (!$_POST) {
            $input = (object) $this->library->get_default_values();
        } else {
            $input = (object) $this->input->post(null, true);
        }
        if (!$this->library->validate()) {
            $pages       = $this->pages;
            $main_view   = 'library/form_library';
            $form_action = 'library/add';
            $this->load->view('template', compact('pages', 'main_view', 'form_action', 'input'));
            return;
        }
        if ($this->library->insert($input)) {
            $this->session->set_flashdata('success', $this->lang->line('toast_add_success'));
        } else {
            $this->session->set_flashdata('error', $this->lang->line('toast_add_fail'));
        }
 
        redirect($this->pages);
    }
 
    public function edit($id = null)
    {
        $library = $this->library->where('library_id', $id)->get();
        if (!$this) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }
        if (!$_POST) {
            $input = (object) $library;
        } else {
            $input = (object) $this->input->post(null, true);
        }
        if (!$this->library->validate()) {
            $pages       = $this->pages;
            $main_view   = 'library/form_library';
            $form_action = "library/edit/$id";
            $this->load->view('template', compact('pages', 'main_view', 'form_action', 'input'));
            return;
        }
        if ($this->library->where('library_id', $id)->update($input)) {
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        } else {
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        }
 
        redirect($this->pages);
    }
 
    public function delete($id = null)
    {
        $library = $this->library->where('library_id', $id)->get();
        if (!$library) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }
        if ($this->library->where('library_id', $id)->delete()) {
            $this->session->set_flashdata('success', $this->lang->line('toast_delete_success'));
        } else {
            $this->session->set_flashdata('error', $this->lang->line('toast_delete_fail'));
        }
 
        redirect($this->pages);
    }
 
    public function unique_library_name($library_name)
    {
        $library_id = $this->input->post('library_id');
        $this->library->where('library_name', $library_name);
        !$library_id || $this->library->where_not('library_id', $library_id);
        $library = $this->library->get();
        if ($library) {
            $this->form_validation->set_message('unique_library_name', $this->lang->line('toast_data_duplicate'));
            return false;
        }
        return true;
    }
 
}
