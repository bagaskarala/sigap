<?php defined('BASEPATH') or exit('No direct script access allowed');

class Royalty_model extends MY_Model
{
    public $per_page = 10;

    public function validate_royalty()
    {
        $data = array();
        $data['input_error'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('start_date') == '') {
            $data['input_error'][] = 'null-start-date';
            $data['status'] = FALSE;
        } else if ($this->input->post('start_date') > $this->input->post('end_date')) {
            $data['input_error'][] = 'invalid-range';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function get_dropdown_author_history()
    {
        $authors =  $this->db
            ->select('author_name')
            ->from('author')
            ->join('royalty', 'author.author_id = royalty.author_id')
            ->group_by('author.author_id')
            ->get()
            ->result();
        $options = [];
        foreach ($authors as $author) {
            $options += [$author->author_name => $author->author_name];
        }
        return $options;
    }

    public function get_authors()
    {
        return $this->db->select('author_name')
            ->from('author')
            ->get()
            ->result();
    }

    public function get_book($author_id)
    {
        return $this->db->select('book_id, book_title, book.draft_id')
            ->from('book')
            ->join('draft_author', 'draft_author.draft_id = book.draft_id')
            ->where('author_id', $author_id)
            ->get()
            ->result();
    }

    public function fetch_royalty_history($author_id)
    {
        return $this->db->select('*')
            ->from('royalty')
            ->where('author_id', $author_id)
            ->order_by('royalty_id', 'DESC')
            ->get()
            ->result();
    }

    public function fetch_latest_royalty($author_id)
    {
        return $this->db->select('*')
            ->from('royalty')
            ->where('author_id', $author_id)
            ->order_by('royalty_id', 'DESC')
            ->limit(1)
            ->get()
            ->row();
    }

    public function fetch_all_royalty_history($filters, $page)
    {
        $this->db->start_cache();
        $this->db->select('royalty_id, author.author_id, author_name, start_date, end_date, status, paid_date, receipt')
            ->from('royalty')
            ->join('author', 'royalty.author_id = author.author_id')
            ->order_by('royalty_id', 'DESC')
            //->group_by('author.author_name')
            ->limit($this->per_page, $this->calculate_real_offset($page));
        if ($filters['keyword'] != '') {
            $this->db->like('author_name', $filters['keyword']);
        }
        if ($filters['start_date'] != null) {
            $this->db->where('start_date >=', $filters['start_date']);
        }
        if ($filters['period_end'] != null) {
            $this->db->where('end_date <=', $filters['period_end']);
        }

        $this->db->stop_cache();
        $royalty = $this->db->get()->result();
        $total = $this->db->count_all_results();
        $this->db->flush_cache();
        return [
            'royalty' => $royalty,
            'total'   => $total
        ];

        return $this->db->get()->result();
    }

    public function author_earning($filters, $page)
    {
        $this->db->start_cache();
        $this->db->select('author.author_id, author_name, royalty.start_date as start_date, royalty.end_date as end_date, royalty.status as status, SUM(qty*price) AS total_sales, SUM(qty*price*book.royalty/100) as earned_royalty')
            ->from('book')
            ->join('draft_author', 'draft_author.draft_id = book.draft_id', 'right')
            ->join('author', 'draft_author.author_id = author.author_id')
            ->join('royalty', 'author.author_id = royalty.author_id AND royalty_id = (SELECT royalty_id from royalty where royalty.author_id = author.author_id order by royalty_id DESC limit 1)', 'left')
            ->join('invoice_book', 'book.book_id = invoice_book.book_id')
            ->join('invoice', 'invoice_book.invoice_id = invoice.invoice_id')
            ->group_by('author.author_id')
            ->order_by('author.author_name');
        if ($filters['keyword'] != '') {
            $this->db->like('author_name', $filters['keyword']);
        }
        if ($filters['period_end'] != null) {
            //if author.last_paid_date == null
            $this->db->where('issued_date BETWEEN IFNULL((SELECT IF(royalty.status = "paid", end_date, start_date - INTERVAL 1 SECOND)), "2000/01/01") and "' . $filters['period_end'] . ' 23:59:59"');
        } else {
            $this->db->where('issued_date BETWEEN IFNULL((SELECT IF(royalty.status = "paid", end_date, start_date - INTERVAL 1 SECOND)), "2000/01/01") and addtime(CURDATE(), "23:59:59") - INTERVAL 1 DAY');
        }
        $this->db->where('invoice.status', 'finish')->limit($this->per_page, $this->calculate_real_offset($page));

        $this->db->stop_cache();
        $royalty = $this->db->get()->result();
        $total = $this->db->count_all_results();
        $this->db->flush_cache();
        return [
            'royalty' => $royalty,
            'total'   => $total
        ];
    }

    public function author_details($author_id, $filters)
    {
        $this->db->select('book.book_id, book.book_title, price, SUM(invoice_book.qty) AS count, SUM(invoice_book.qty*invoice_book.price) AS total_sales, SUM(invoice_book.qty*invoice_book.price*invoice_book.royalty/100) as earned_royalty, invoice_book.royalty as royalty')
            ->from('book')
            ->join('draft_author', 'draft_author.draft_id = book.draft_id', 'right')
            ->join('invoice_book', 'book.book_id = invoice_book.book_id')
            ->join('invoice', 'invoice_book.invoice_id = invoice.invoice_id')
            ->group_by('book.book_id')
            ->where('invoice.status', 'finish')
            ->where('draft_author.author_id', $author_id);
        if ($filters['period_end'] != null) {
            //if author.last_paid_date == null
            $this->db->where('issued_date BETWEEN "' . $filters['last_paid_date'] .  '" and "' . $filters['period_end'] . ' 23:59:59"');
        } else {
            $this->db->where('issued_date BETWEEN "' . $filters['last_paid_date'] .  '" and addtime(CURDATE(), "23:59:59") - INTERVAL 1 DAY');
        }

        return $this->db->get()->result();
    }

    public function stocks_info($author_id, $filters)
    {
        $this->db->select('book.book_id, IFNULL(warehouse_present, 0) as WP, IFNULL(showroom_present, 0) as SP, IFNULL(library_present, 0) as LP, SUM(qty) AS count, invoice_book.royalty')
            ->from('book')
            ->join('book_stock', 'book_stock.book_id = book.book_id', 'left')
            ->join('draft_author', 'draft_author.draft_id = book.draft_id', 'right')
            ->join('invoice_book', 'book.book_id = invoice_book.book_id')
            ->join('invoice', 'invoice_book.invoice_id = invoice.invoice_id')
            ->where('invoice.status', 'finish')
            ->where('draft_author.author_id', $author_id)
            ->where('issued_date BETWEEN "' . $filters['last_paid_date'] .  '" and now()')
            ->group_by('book.book_id');
        return $this->db->get()->result();
    }

    public function get_non_sales_book($book_id, $filters, $type)
    {
        $this->db->select('sum(qty) as qty_non_sales')
            ->from('book_non_sales_list')
            ->join('book_non_sales', 'book_non_sales_list.book_non_sales_id = book_non_sales.book_non_sales_id', 'left')
            ->where('book_id', $book_id);
        if ($type == 'now') $this->db->where('issued_date BETWEEN "' . $filters['last_paid_date'] .  '" and now()');
        else if ($type == 'last') $this->db->where('issued_date BETWEEN "' . $filters['last_paid_date'] .  '" and "' . $filters['period_end'] . '"');
        return $this->db->get()->row();
    }
}
