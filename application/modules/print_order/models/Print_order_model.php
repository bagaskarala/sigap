<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Print_order_model extends MY_Model
{
    public $per_page = 10;

    public function get_validation_rules()
    {
        $validation_rules = [
            [
                'field' => 'book_id',
                'label' => $this->lang->line('form_book_title'),
                'rules' => 'trim|required',
            ],
            [
                'field' => 'order_number',
                'label' => $this->lang->line('form_print_order_number'),
                'rules' => 'trim|required',
            ],
            [
                'field' => 'type',
                'label' => $this->lang->line('form_print_order_type'),
                'rules' => 'trim|required',
            ],
            [
                'field' => 'priority',
                'label' => $this->lang->line('form_print_order_priority'),
                'rules' => 'trim|required|integer',
            ],
            [
                'field' => 'total',
                'label' => $this->lang->line('form_print_order_total'),
                'rules' => 'trim|required|integer',
            ],
            [
                'field' => 'paper_content',
                'label' => $this->lang->line('form_print_order_paper_content'),
                'rules' => 'trim|required',
            ],
            [
                'field' => 'paper_cover',
                'label' => $this->lang->line('form_print_order_paper_cover'),
                'rules' => 'trim|required',
            ],
            [
                'field' => 'paper_size',
                'label' => $this->lang->line('form_print_order_paper_size'),
                'rules' => 'trim|required',
            ],
        ];

        return $validation_rules;
    }

    public function get_default_values()
    {
        return [
            'book_id'       => '',
            'order_number'  => '',
            'total'         => '',
            'print_number'  => '',
            'paper_content' => '',
            'paper_cover'  => '',
            'paper_size'    => '',
            'type'          => 'pod',
            'priority'      => '',
        ];
    }

    public function get_print_order($print_order_id)
    {
        return $this->select(['print_order_id', 'print_order.book_id', 'book.draft_id', 'book_title', 'order_number', 'total', 'type', 'priority', 'print_order.entry_date', 'print_order.finish_date', 'print_order.input_by', 'book_file', 'book_file_link', 'cover_file', 'cover_file_link', 'book_notes', 'is_preprint', 'preprint_start_date', 'preprint_end_date', 'preprint_deadline', 'preprint_notes', 'print_order.is_print', 'print_order.print_start_date', 'print_order.print_end_date', 'print_order.print_deadline', 'print_order.print_notes', 'is_postprint', 'postprint_start_date', 'postprint_end_date', 'postprint_deadline', 'postprint_notes', 'print_order_status', 'is_reprint', 'book_edition'])
            ->join('book')
            ->join_table('draft', 'book', 'draft')
            ->where('print_order_id', $print_order_id)
            ->get();
    }

    public function filter_print_order($filters, $page)
    {
        $print_orders = $this->select(['print_order_id', 'print_order.book_id', 'book.draft_id', 'book_title', 'category_name', 'order_number', 'total', 'type', 'priority', 'print_order.entry_date', 'print_order_status'])
            ->when('keyword', $filters['keyword'])
            ->join_table('book', 'print_order', 'book')
            ->join_table('draft', 'book', 'draft')
            ->join_table('category', 'draft', 'category')
            ->order_by('status_hak_cipta')
            ->order_by('published_date')
            ->order_by('book_title')
            ->order_by('UNIX_TIMESTAMP(print_order.entry_date)', 'DESC')
            ->paginate($page)
            ->get_all();

        $total = $this->select('draft.draft_id')
            ->when('keyword', $filters['keyword'])
            ->join_table('book', 'print_order', 'book')
            ->join_table('draft', 'book', 'draft')
            ->join_table('category', 'draft', 'category')
            ->order_by('status_hak_cipta')
            ->order_by('published_date')
            ->order_by('book_title')
            ->count();

        // get authors
        // foreach ($print_orders as $b) {
        //     if ($b->draft_id) {
        //         $b->authors = $this->get_id_and_name('author', 'draft_author', $b->draft_id, 'draft');
        //     } else {
        //         $b->authors = [];
        //     }
        // }

        return [
            'print_orders' => $print_orders,
            'total'        => $total,
        ];
    }

    public function when($params, $data)
    {
        // jika data null, maka skip
        if ($data) {
            if ($params == 'reprint') {
                $this->where('is_reprint', $data);
            }

            if ($params == 'category') {
                $this->where('draft.category_id', $data);
            }

            if ($params == 'status') {
                $this->where('book.status_hak_cipta', $data == 'done' ? 2 : 1);
            }

            if ($params == 'published_year') {
                $this->where('year(published_date)', $data);
            }

            if ($params == 'keyword') {
                $this->group_start();
                // $this->like('draft_title', $data);
                $this->or_like('book_title', $data);
                // if ($this->session->userdata('level') != 'reviewer') {
                $this->or_like('author_name', $data);
                // }
                $this->group_end();
            }
        }
        return $this;
    }
}

/* End of file Print_order_model.php */
