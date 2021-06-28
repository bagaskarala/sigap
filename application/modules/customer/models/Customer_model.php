<?php defined('BASEPATH') or exit('No direct script access allowed');

class Customer_model extends MY_Model
{
    // set public if want to override per_page
    public $per_page;

    public function validate_modal_add()
    {
        $data = array();
        $data['input_error'] = array();
        $data['status'] = TRUE;
 
        if($this->input->post('name') == '')
        {
            $data['input_error'][] = 'error-name';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('phone-number') == '')
        {
            $data['input_error'][] = 'error-phone-number';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('type') == '')
        {
            $data['input_error'][] = 'error-type';
            $data['status'] = FALSE;
        }
 
        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }
    }

    public function validate_modal_edit()
    {
        $data = array();
        $data['input_error'] = array();
        $data['status'] = TRUE;
 
        if($this->input->post('edit-name') == '')
        {
            $data['input_error'][] = 'error-edit-name';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('edit-phone-number') == '')
        {
            $data['input_error'][] = 'error-edit-phone-number';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('edit-type') == '')
        {
            $data['input_error'][] = 'error-edit-type';
            $data['status'] = FALSE;
        }
 
        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }
    }

    public function validate_edit_discount()
    {
        $data = array();
        $data['input_error'] = array();
        $data['status'] = TRUE;

        if($this->input->post('discount-distributor') == '' || $this->input->post('discount-distributor') < 0 || $this->input->post('discount-distributor') > 100)
        {
            $data['input_error'][] = 'error-discount-distributor';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('discount-reseller') == '' || $this->input->post('discount-reseller') < 0 || $this->input->post('discount-reseller') > 100)
        {
            $data['input_error'][] = 'error-discount-reseller';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('discount-author') == '' || $this->input->post('discount-author') < 0 || $this->input->post('discount-author') > 100)
        {
            $data['input_error'][] = 'error-discount-author';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('discount-member') == '' || $this->input->post('discount-member') < 0 || $this->input->post('discount-member') > 100)
        {
            $data['input_error'][] = 'error-discount-member';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('discount-general') == '' || $this->input->post('discount-general') < 0 || $this->input->post('discount-general') > 100)
        {
            $data['input_error'][] = 'error-discount-general';
            $data['status'] = FALSE;
        }

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }
    }

    public function get_discount()
    {
        return $this->db->select('*')->from('discount')->get()->result();
    }

    public function filter_data($filters, $page = null)
    {
        $customers = $this->select(['customer_id', 'name', 'address', 'phone_number', 'email', 'type'])
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
