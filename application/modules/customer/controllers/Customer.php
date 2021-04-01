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

    public function api_customer_info($customer_id)
    {
        $customer =  $this->customer->get_customer($customer_id);
        $data = json_encode($customer);
        echo $data;
    }

    public function add_customer()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Nama Customer', 'required');
        $this->form_validation->set_rules('type', 'Tipe Customer', 'required');
        $this->form_validation->set_rules('phone-number', 'Nomor HP Customer', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Customer gagal ditambah.');
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        } else {
            $check = $this->customer->add_customer();
            if ($check   ==  TRUE) {
                $this->session->set_flashdata('success', 'Customer berhasil ditambah.');
                redirect('customer');
            } else {
                $this->session->set_flashdata('error', 'Customer gagal ditambah.');
                redirect($_SERVER['HTTP_REFERER'], 'refresh');
            }
        }
    }

    public function update_customer()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('editName', 'Nama Customer', 'required');
        $this->form_validation->set_rules('editType', 'Tipe Customer', 'required');
        $this->form_validation->set_rules('editPhone-number', 'Nomor HP Customer', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Customer gagal diupdate.');
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        } else {
            $check = $this->customer->update_customer();
            if ($check   ==  TRUE) {
                $this->session->set_flashdata('success', 'Customer berhasil diupdate.');
                redirect('customer');
            } else {
                $this->session->set_flashdata('error', 'Customer gagal diupdate.');
                redirect($_SERVER['HTTP_REFERER'], 'refresh');
            }
        }
        redirect('customer');
    }

    public function delete($id = null)
    {
        $this->customer->delete_customer($id);
        redirect('customer');
    }
}
