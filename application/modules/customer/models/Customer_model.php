<?php defined('BASEPATH') or exit('No direct script access allowed');

class Customer_model extends MY_Model
{
    // set public if want to override per_page
    public $per_page;

    public function add_customer()
    {
        $add = [
            'name'          => $this->input->post('name'),
            'address'       => $this->input->post('address'),
            'phone_number'  => $this->input->post('phone-number'),
            'type'          => $this->input->post('type')
        ];

        $this->db->insert('customer', $add);
        return TRUE;
    }

    public function get_customer($customer_id)
    {
        $this->db->select('*');
        $this->db->from('customer');
        $this->db->where('customer.customer_id', $customer_id);
        return $this->db->get()->row();
    }

    public function update_customer()
    {
        $id = $this->input->post('editId');
        $update = [
            'name'          => $this->input->post('editName'),
            'address'       => $this->input->post('editAddress'),
            'phone_number'  => $this->input->post('editPhone-number'),
            'type'          => $this->input->post('editType')
        ];
        $this->db->set($update);
        $this->db->where('customer_id', $id);
        $this->db->update('customer');
        return TRUE;
    }

    public function delete_customer($id)
    {
        $this->db->where('customer_id', $id);
        $this->db->delete('customer');
    }

    public function filter_data($filters, $page = null)
    {
        $customers = $this->select(['customer_id', 'name', 'address', 'phone_number', 'type'])
            ->when('keyword', $filters['keyword'])
            ->when('type', $filters['type'])
            ->order_by('name')
            ->paginate($page)
            ->get_all();

        $total = $this->select('customer_id', 'name')
            ->when('keyword', $filters['keyword'])
            ->when('type', $filters['type'])
            ->order_by('customer_id')
            ->count();

        return [
            'customers'  => $customers,
            'total' => $total
        ];
    }

    public function when($params, $data)
    {
        // jika data null, maka skip
        if ($data != '') {
            if ($params == 'keyword') {
                $this->group_start();
                $this->or_like('name', $data);
                $this->or_like('address', $data);
                $this->or_like('phone_number', $data);
                $this->group_end();
            } else {
                $this->group_start();
                $this->or_like('type', $data);
                $this->group_end();
            }
        }
        return $this;
    }
}
