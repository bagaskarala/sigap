<?php defined('BASEPATH') or exit('No direct script access allowed');

class Customer_model extends MY_Model
{
    // set public if want to override per_page
    public $per_page;

    // public function get_validation_rules()
    // {
    //     $validation_rules = [
    //         [
    //             'field' => 'username',
    //             'label' => $this->lang->line('form_user_name'),
    //             'rules' => 'trim|required|min_length[4]|max_length[256]|callback_unique_data[username]',
    //         ],
    //         [
    //             'field' => 'password',
    //             'label' => $this->lang->line('form_user_password'),
    //             'rules' => 'trim|callback_required_on[add]|min_length[4]|max_length[30]',
    //         ],
    //         [
    //             'field' => 'email',
    //             'label' => $this->lang->line('form_user_email'),
    //             'rules' => 'trim|required|valid_email|callback_unique_data[email]',
    //         ],
    //         [
    //             'field' => 'level',
    //             'label' => $this->lang->line('form_user_level'),
    //             'rules' => 'trim|required',
    //         ],
    //         [
    //             'field' => 'is_blocked',
    //             'label' => $this->lang->line('form_user_is_blocked'),
    //             'rules' => 'trim|callback_required_on[edit]',
    //         ],
    //     ];

    //     return $validation_rules;
    // }

    // public function get_default_values()
    // {
    //     return [
    //         'name'   => null,
    //         'address'   => null,
    //         'phone_number'      => null,
    //         'type'      => null,
    //     ];
    // }

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

    public function delete_customer($id)
    {
        $this->db->where('customer_id', $id);
        $this->db->delete('customer');
    }

    public function update_customer($id)
    {
        $update = [
            'name'          => $this->input->post('name'),
            'address'       => $this->input->post('address'),
            'phone_number'  => $this->input->post('phone-number'),
            'type'          => $this->input->post('type')
        ];

        $this->db->set($update);
        $this->db->where('customer_id', $id);
        $this->db->update('customer');
        return TRUE;
    }

    public function getCustomerInfo($id){
        return $this->select('*')->where('customer_id', $id)->get('customer');
    }

    // public function insert_data($input)
    // {
    //     // clone data untuk dikirimkan via email
    //     $send_mail  = $input->send_mail;
    //     $email_data = clone $input;

    //     $input->password = md5($input->password);

    //     unset($input->send_mail);
    //     if ($this->insert($input)) {
    //         // jika sukses input data, kirim email ke user
    //         if ($send_mail) {
    //             $this->send_user_mail($email_data, 'email/create_user_email', 'Registrasi berhasil');
    //         }
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    // public function update_data($input, $user_id)
    // {
    //     // clone data untuk dikirimkan vie email
    //     $send_mail  = $input->send_mail;
    //     $email_data = clone $input;

    //     // jika update password
    //     if (!empty($input->password)) {
    //         $input->password = md5($input->password);
    //     } else {
    //         unset($input->password);
    //     }

    //     unset($input->send_mail);
    //     if ($this->where('user_id', $user_id)->update($input)) {
    //         if ($send_mail) {
    //             $this->send_user_mail($email_data, 'email/update_user_email', 'Update Data Akun');
    //         }
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    // public function send_user_mail($input, $email_content, $subject)
    // {
    //     $email_subject = $subject;
    //     $data          = [
    //         'preheader'     => null,
    //         'username'      => $input->username,
    //         'level'         => ucwords(str_replace('_', ' ', $input->level)),
    //         'password'      => $input->password,
    //         'status'        => $input->is_blocked == 'y' ? 'Nonaktif' : 'Aktif',
    //         'email_content' => $email_content,
    //     ];
    //     $email_message = $this->load->view('email/main_email_template', $data, true);

    //     $mail = $this->send_mail($input->email, $email_subject, $email_message);
    //     if (!$mail['status']) {
    //         return false;
    //     } else {
    //         return true;
    //     }
    // }

    // public function get_draft_staffs($draft_id, $staff_level)
    // {
    //     return $this->select(['username', 'level', 'responsibility_id', 'responsibility.user_id'])
    //         ->join_table('responsibility', 'user', 'user')
    //         ->join_table('draft', 'responsibility', 'draft')
    //         ->where('responsibility.draft_id', $draft_id)
    //         ->where('level', $staff_level)
    //         ->get_all();
    // }

    // public function get_all_staffs($level)
    // {
    //     return $this->select('user_id,username,level')
    //         ->where('level', $level)
    //         ->order_by('username')
    //         ->get_all();
    // }
}
