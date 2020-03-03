<?php defined('BASEPATH') or exit('No direct script access allowed');
class Worksheet extends Operator_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'worksheet';
        //author dan reviewer tidak boleh akses halaman ini
        $ceklevel = $this->session->userdata('level');
        if ($ceklevel == 'author' || $ceklevel == 'reviewer') {
            redirect('home');
        }
    }
    public function index($page = null)
    {
        $worksheets = $this->worksheet->join('draft')->order_by('worksheet_status')->order_by('worksheet_id', 'desc')->order_by('worksheet_num')->paginate($page)->get_all();
        $total      = $this->worksheet->join('draft')->count();
        $pages      = $this->pages;
        $main_view  = 'worksheet/index_worksheet';
        $pagination = $this->worksheet->make_pagination(site_url('worksheet'), 2, $total);
        $this->load->view('template', compact('pages', 'main_view', 'worksheets', 'pagination', 'total'));
    }
    public function filter($page = null)
    {
        $filter = $this->input->get('filter', true);
        $this->db->group_by('worksheet.worksheet_id');
        if ($filter == 'waiting') {
            $worksheets = $this->worksheet->join('draft')->where('draft_status', 0)->order_by('worksheet_num')->paginate($page)->get_all();
            $total      = $this->worksheet->join('draft')->where('draft_status', 0)->count();
        } elseif ($filter == 'approved') {
            $worksheets = $this->worksheet->join('draft')->where_not('draft_status', 0)->where_not('draft_status', 2)->order_by('worksheet_num')->paginate($page)->get_all();
            $total      = $this->worksheet->join('draft')->where_not('draft_status', 0)->where_not('draft_status', 2)->count();
        } elseif ($filter == 'rejected') {
            $worksheets = $this->worksheet->join('draft')->where('draft_status', 2)->order_by('worksheet_num')->paginate($page)->get_all();
            $total      = $this->worksheet->join('draft')->where('draft_status', 2)->count();
        } else {
            redirect(base_url('worksheet'));
        }
        $pagination = $this->worksheet->make_pagination(site_url('worksheet/filter/'), 3, $total);
        if (!$worksheets) {
            $this->session->set_flashdata('warning', 'Data were not found');
            redirect($this->pages);
        }
        $pages     = $this->pages;
        $main_view = 'worksheet/index_worksheet';
        $this->load->view('template', compact('pages', 'main_view', 'worksheets', 'pagination', 'total'));
    }
    public function add()
    {
        if (!$_POST) {
            $input = (object) $this->worksheet->get_default_values();
        } else {
            $input = (object) $this->input->post(null, true);
        }
        if (!$this->worksheet->validate()) {
            $pages       = $this->pages;
            $main_view   = 'worksheet/form_worksheet';
            $form_action = 'worksheet/add';
            $this->load->view('template', compact('pages', 'main_view', 'form_action', 'input'));
            return;
        }
        if ($this->worksheet->insert($input)) {
            $this->session->set_flashdata('success', 'Data saved');
        } else {
            $this->session->set_flashdata('error', 'Data failed to save');
        }
        redirect('worksheet');
    }
    public function edit($id = null)
    {
        $worksheet                  = $this->worksheet->where('worksheet_id', $id)->get();
        $data                       = array('draft_id' => $worksheet->draft_id);
        $draft                      = $this->worksheet->get_where($data, 'draft');
        $worksheet->draft_title     = $draft->draft_title;
        $worksheet->draft_file      = $draft->draft_file;
        $worksheet->draft_file_link = $draft->draft_file_link;
        if (!$worksheet) {
            $this->session->set_flashdata('warning', 'Worksheet data were not available');
            redirect('worksheet');
        }
        if (!$_POST) {
            $input = (object) $worksheet;
        } else {
            $input = (object) $this->input->post(null, true);
        }
        if (!$this->worksheet->validate()) {
            $pages       = $this->pages;
            $main_view   = 'worksheet/form_worksheet';
            $form_action = "worksheet/edit/$id";
            $this->load->view('template', compact('pages', 'main_view', 'form_action', 'input'));
            return;
        }
        $input->worksheet_pic = $this->username;
        unset($input->files);
        unset($input->draft_file);
        unset($input->draft_file_link);
        if ($this->worksheet->where('worksheet_id', $id)->update($input)) {
            if ($input->worksheet_status == 1) {
                $status = array('draft_status' => 1);
            } elseif ($input->worksheet_status == 2) {
                $status = array('draft_status' => 2);
            } else {
                $status = array('draft_status' => 0);
            }
            $this->worksheet->update_draft_status($worksheet->draft_id, $status);
            $this->session->set_flashdata('success', 'Data updated');
        } else {
            $this->session->set_flashdata('error', 'Data failed to update');
        }
        redirect('worksheet');
    }
    public function action($id, $action)
    {
        $worksheet = $this->worksheet->where('worksheet_id', $id)->get();
        if (!$worksheet) {
            $this->session->set_flashdata('warning', 'Worksheet data were not available');
            redirect('worksheet');
        }
        $data = array('worksheet_status' => $action, 'worksheet_pic' => $this->username);
        if ($this->worksheet->where('worksheet_id', $id)->update($data)) {
            $status = array('draft_status' => $action);
            $this->worksheet->update_draft_status($worksheet->draft_id, $status);
            $affected_rows = $this->db->affected_rows();
            if ($affected_rows > 0) {
                $actionMessage = 'Approved';
                if ($action == '2') {
                    $actionMessage = 'Rejected';
                }
                $this->session->set_flashdata('success', "Worksheet $actionMessage");
            } else {
                $this->session->set_flashdata('warning', "Worksheet Failed Update");
            }
        } else {
            $this->session->set_flashdata('warning', 'Worksheet Failed Update');
        }
        redirect('worksheet');
    }
    //  penghapusan worksheet mengikuti draft
    //  public function delete($id = null)
    //  {
    //  $worksheet = $this->worksheet->where('worksheet_id', $id)->get();
    //        if (!$worksheet) {
    //            $this->session->set_flashdata('warning', 'Worksheet data were not available');
    //            redirect('worksheet');
    //        }
    //
    //        if ($this->worksheet->where('worksheet_id', $id)->delete()) {
    //  $this->session->set_flashdata('success', 'Data deleted');
    //      } else {
    //            $this->session->set_flashdata('error', 'Data failed to delete');
    //        }
    //
    //      redirect('worksheet');
    //  }
    public function search($page = null)
    {
        $keywords   = $this->input->get('keywords', true);
        $worksheets = $this->worksheet->like('worksheet_num', $keywords)->or_like('draft_title', $keywords)->join('draft')->order_by('draft.draft_title')->order_by('worksheet_num')->paginate($page)->get_all();
        $tot        = $this->worksheet->like('worksheet_num', $keywords)->or_like('draft_title', $keywords)->join('draft')->order_by('draft.draft_title')->order_by('worksheet_num')->get_all();
        $total      = count($tot);
        $pagination = $this->worksheet->make_pagination(site_url('worksheet/search/'), 3, $total);
        if (!$worksheets) {
            $this->session->set_flashdata('warning', 'Data were not found');
        }
        $pages     = $this->pages;
        $main_view = 'worksheet/index_worksheet';
        $this->load->view('template', compact('pages', 'main_view', 'worksheets', 'pagination', 'total'));
    }
    //validasi nomor lembar kerja
    public function unique_worksheet_num()
    {
        $worksheet_num = $this->input->post('worksheet_num');
        $worksheet_id  = $this->input->post('worksheet_id');
        $this->worksheet->where('worksheet_num', $worksheet_num);
        !$worksheet_id || $this->worksheet->where('worksheet_id !=', $worksheet_id);
        $worksheet = $this->worksheet->get();
        if (count($worksheet)) {
            $this->form_validation->set_message('unique_worksheet_num', '%s has been used');
            return false;
        }
        return true;
    }
    //validasi draft lembar kerja
    public function unique_worksheet_draft()
    {
        $draft_id     = $this->input->post('draft_id');
        $worksheet_id = $this->input->post('worksheet_id');
        $this->worksheet->where('draft_id', $draft_id);
        !$worksheet_id || $this->worksheet->where('worksheet_id !=', $worksheet_id);
        $worksheet = $this->worksheet->get();
        if (count($worksheet)) {
            $this->form_validation->set_message('unique_worksheet_draft', '%s has been used');
            return false;
        }
        return true;
    }
}