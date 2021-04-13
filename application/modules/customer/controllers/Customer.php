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
            'distributor'   => 'Distributor',
            'reseller'      => 'Reseller',
            'author'        => 'Penulis',
            'member'        => 'Member',
            'general'       => 'Umum'
        );

        // custom per page
        $this->customer->per_page = $this->input->get('per_page', true) ?? 10;

        $get_data = $this->customer->filter_data($filters, $page);
        $discount = $this->customer->get_discount();

        $customers  = $get_data['customers'];
        $total      = $get_data['total'];
        $pages      = $this->pages;
        $main_view  = 'customer/index_customer';
        $pagination = $this->customer->make_pagination(site_url('customer'), 2, $total);
        $this->load->view('template', compact('pagination', 'pages', 'main_view', 'customers', 'total', 'customer_type', 'discount'));
    }

    public function api_customer_info($id)
    {
        $customer = $this->customer->where('customer_id', $id)->get();
        echo json_encode($customer);
    }

    public function add()
    {
        $this->customer->validate_modal_add();
        $add = [
            'name'          => $this->input->post('name'),
            'address'       => $this->input->post('address'),
            'phone_number'  => $this->input->post('phone-number'),
            'type'          => $this->input->post('type')
        ];
        $this->db->insert('customer', $add);
        echo json_encode(['status' => TRUE]);

        $this->session->set_flashdata('success', $this->lang->line('toast_add_success'));
    }

    public function edit($id = null)
    {
        $this->customer->validate_modal_edit();
        $id = $this->input->post('edit-id');
        $update = [
            'name'          => $this->input->post('edit-name'),
            'address'       => $this->input->post('edit-address'),
            'phone_number'  => $this->input->post('edit-phone-number'),
            'type'          => $this->input->post('edit-type')
        ];
        $this->db->set($update);
        $this->db->where('customer_id', $id);
        $this->db->update('customer');

        echo json_encode(['status' => TRUE]);

        $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
    }

    public function delete($id = null)
    {
        $this->db->where('customer_id', $id);
        $this->db->delete('customer');
        $this->session->set_flashdata('success', $this->lang->line('toast_delete_success'));
        redirect('customer');
    }

    public function edit_discount()
    {
        $this->customer->validate_edit_discount();
        $data = array(
            array(
                'membership' => 'distributor',
                'discount'   => $this->input->post('discount-distributor')
            ),
            array(
                'membership' => 'reseller',
                'discount'   => $this->input->post('discount-reseller')
            ),
            array(
                'membership' => 'author',
                'discount'   => $this->input->post('discount-author')
            ),
            array(
                'membership' => 'member',
                'discount'   => $this->input->post('discount-member')
            ),
            array(
                'membership' => 'general',
                'discount'   => $this->input->post('discount-general')
            )
        );
        $this->db->update_batch('discount', $data, 'membership');
        echo json_encode(['status' => TRUE]);

        $this->session->set_flashdata('success', 'Diskon berhasil diupdate.');
    }

}
