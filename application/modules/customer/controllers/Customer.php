<?php defined('BASEPATH') or exit('No direct script access allowed');

class Customer extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'customer';
        $this->load->model('customer_model', 'customer');
    }

    public function index($page = null)
    {
        $filters = [
            'keyword' => $this->input->get('keyword', true),
            'type'  => $this->input->get('type', true)
        ];

        $customer_type = array(
            'Distributor'      => 'Distributor',
            'Reseller'      => 'Reseller',
            'Penulis'        => 'Penulis',
            'Member'        => 'Member',
            'Biasa'        => ' - '
        );

        // custom per page
        $this->customer->per_page = $this->input->get('per_page', true) ?? 10;

        $get_data = $this->customer->filter_data($filters, $page);

        $customers  = $get_data['customers'];
        $total      = $get_data['total'];
        $pages      = $this->pages;
        $main_view  = 'customer/index_customer';
        $pagination = $this->customer->make_pagination(site_url('customer'), 2, $total);
        $this->load->view('template', compact('pagination', 'pages', 'main_view', 'customers', 'total', 'customer_type'));
    }

    public function api_get_staffs($level)
    {
        $staffs = $this->customer->get_all_staffs($level);
        return $this->send_json_output(true, $staffs);
    }

    public function add()
    {
        // if (!$_POST) {
        //     $input = (object) $this->customer->get_default_values();
        // } else {
        //     $input = (object) $this->input->post(null, true);
        // }

        $customer_type = array(
            'Distributor'      => 'Distributor',
            'Reseller'      => 'Reseller',
            'Penulis'        => 'Penulis',
            'Member'        => 'Member',
            'Biasa'        => ' - '
        );

        $pages       = $this->pages;
        $main_view   = 'customer/form_customer';
        $form_action = 'customer/add_customer';
        $this->load->view('template', compact('pages', 'main_view', 'form_action', 'customer_type'));
    }

    public function add_customer()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Nama Customer', 'required');
        $this->form_validation->set_rules('type', 'Tipe Customer', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Customer gagal ditambah.');
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        } else {
            $check = $this->customer->add_customer();
            if ($check   ==  TRUE) {
                $this->session->set_flashdata('success', 'Customer berhasil ditambah.');
                redirect('customer');
            } else {
                $this->session->set_flashdata('error', 'Customer gagal ditambah 2.');
                redirect($_SERVER['HTTP_REFERER'], 'refresh');
            }
        }
    }

    public function delete($id = null)
    {
        $this->customer->delete_customer($id);
        redirect('customer');
    }

    public function update($id = null)
    {
        $customer_type = array(
            'Distributor'      => 'Distributor',
            'Reseller'      => 'Reseller',
            'Penulis'        => 'Penulis',
            'Member'        => 'Member',
            'Biasa'        => ' - '
        );

        $customer = $this->customer->getCustomerInfo($id);
        $pages       = $this->pages;
        $main_view   = 'customer/form_customer_edit';
        $this->load->view('template', compact('customer', 'pages', 'main_view', 'customer_type'));
        // $user = $this->user->where('user_id', $id)->get();
        // if (!$user) {
        //     $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
        //     redirect($this->pages);
        // }

        // // prevent delete superadmin
        // if ($user->level == 'superadmin' && $user->username == 'superadmin') {
        //     $this->session->set_flashdata('error', $this->lang->line('toast_error_not_authorized'));
        //     redirect($this->pages);
        // }

        // if (!$_POST) {
        //     $input = (object) $user;
        // } else {
        //     $input = (object) $this->input->post(null, true);
        // }

        // if (!$this->user->validate()) {
        //     $pages       = $this->pages;
        //     $main_view   = 'user/form_user';
        //     $form_action = "user/edit/$id";
        //     $this->load->view('template', compact('pages', 'main_view', 'form_action', 'input'));
        //     return;
        // }

        // if ($this->user->update_data($input, $id)) {
        //     $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        // } else {
        //     $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        // }

        // redirect($this->pages);
    }

    public function update_customer($id = null)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Nama Customer', 'required');
        $this->form_validation->set_rules('type', 'Tipe Customer', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Customer gagal diupdate.');
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        } else {
            $check = $this->customer->update_customer($id);
            if ($check   ==  TRUE) {
                $this->session->set_flashdata('success', 'Customer berhasil diupdate.');
                redirect('customer');
            } else {
                $this->session->set_flashdata('error', 'Customer gagal diupdate.');
                redirect($_SERVER['HTTP_REFERER'], 'refresh');
            }
        }
        $this->customer->delete_customer($id);
        redirect('customer');
    }
    public function required_on($str, $condition)
    {
        if ($this->uri->segment(2) != $condition) {
            return true;
        }

        if (!$str) {
            $this->form_validation->set_message('required_on', 'Bidang %s dibutuhkan');
            return false;
        }

        return true;
    }

    public function unique_data($str, $data_key)
    {
        $author_id = $this->input->post('user_id');
        if (!$str) {
            return true;
        }
        $this->user->where($data_key, $str);
        !$author_id || $this->user->where_not('user_id', $author_id);
        $user = $this->user->get();
        if ($user) {
            $this->form_validation->set_message('unique_data', $this->lang->line('toast_data_duplicate'));
            return false;
        }
        return true;
    }
}
