<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_request_model extends MY_Model{
    public $per_page = 10;

    public function get_staff_gudang()
    {
        return $this->select(['user_id', 'username', 'level', 'email'])
            ->where('level', 'staff_gudang')
            ->where('is_blocked', 'n')
            ->order_by('username', 'ASC')
            ->get_all('user');
    }

    public function get_staff_gudang_by_invoice($invoice_id)
    {
        return $this->db->select(['book_request_user_id', 'book_request_user.user_id', 'invoice_id', 'username', 'email'])
            ->from('user')
            ->join('book_request_user', 'user.user_id = book_request_user.user_id')
            ->where('invoice_id', $invoice_id)
            ->get()->result();
    }

    public function check_row_staff_gudang($invoice_id, $user_id)
    {
        return $this->db
            ->where(['invoice_id' => $invoice_id, 'user_id' => $user_id])
            ->get('book_request_user')
            ->num_rows();
    }

}