<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_receive_model extends MY_Model
{

    public $per_page = 10;

    public function get_validation_rules()
    {
        $validation_rules = [
            [
                'field' => 'book_receive_status',
                'label' => $this->lang->line('form_book_receive_status'),
                'rules' => 'trim|required',
            ],
            [
                'field' => 'deadline',
                'label' => $this->lang->line('form_book_receive_deadline'),
                'rules' => 'trim|required',
            ],
        ];

        return $validation_rules;
    }

    public function get_default_values()
    {
        return [
            'book_id'           => '',
            'print_order_id'    => '',
            'order_number'      => '',
            'total'             => '',
            'total_postprint'   => '',
            'book_title'        => '',
            'deadline'          => ''
        ];
    }

    //get & filter data and total of data
    public function filter_book_receive($filters, $page)
    {
        $book_receives = $this->select(['print_order.print_order_id', 
        'print_order.order_number',
        'print_order.total', 'print_order.total_postprint', 
        'book.book_id', 
        'book.book_title',
        'book_receive.*'])
            ->when('keyword', $filters['keyword'])
            ->when('book_receive_status', $filters['book_receive_status'])
            ->join_table('print_order', 'book_receive', 'print_order')
            ->join_table('book', 'book_receive', 'book')
            ->order_by('entry_date', 'DESC')
            ->paginate($page)
            ->get_all();
        $total = $this->select('book_receive_id')
            ->when('keyword', $filters['keyword'])
            ->join_table('print_order', 'book_receive', 'print_order')
            ->join_table('book', 'book_receive', 'book')
            ->count();
        return [
            'book_receives' => $book_receives,
            'total' => $total
        ];
    }

    //get filtered
    public function when($params, $data)
    {
        if ($data) {
            if ($params == 'keyword') {
                $this->group_start();
                $this->like('book_title', $data);
                $this->or_like('order_number', $data);
                $this->group_end();
            }
            if ($params == 'book_receive_status'){
                $this->where('book_receive_status', $data);
                $this->or_where('book_receive_status', "{$data}_approval");
                $this->or_where('book_receive_status', "{$data}_finish");
            }
        }
        return $this;
    }

    //get book_id
    public function get_book($book_id)
    {
        return $this->select('book.*')
            ->where('book_id', $book_id)
            ->join_table('book', 'book_receive', 'book')
            ->get('book');
    }
    
    //get print_order_id
    public function get_print_order($print_order_id)
    {
        return $this->select('print_order.*')
            ->where('print_order_id', $print_order_id)
            ->get('print_order');
    }    

    //get book receive id
    public function get_book_receive($book_receive_id)
    {
        return $this->select([
        'print_order.print_order_id',
        'print_order.order_number', 
        'print_order.total', 'print_order.total_postprint', 
        'book.book_id', 
        'book.book_title', 
        'book_receive.*'])
            ->join_table('print_order', 'book_receive', 'print_order')
            ->join_table('book', 'book_receive', 'book')
            ->where('book_receive_id', $book_receive_id)
            ->get();
    }
}