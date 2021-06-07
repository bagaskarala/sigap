<?php defined('BASEPATH') or exit('No direct script access allowed');

class Royalty_model extends MY_Model
{
    public $per_page = 10;

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

    public function fetch_royalty_history ($author_id) {
        return $this->db->select('*')
                        ->from('royalty')
                        ->where('author_id', $author_id)
                        ->order_by('royalty_id', 'DESC')
                        ->get()
                        ->result();
    }

    public function fetch_latest_royalty ($author_id) {
        return $this->db->select('*')
                        ->from('royalty')
                        ->where('author_id', $author_id)
                        ->order_by('royalty_id', 'DESC')
                        ->limit(1)
                        ->get()
                        ->row();
    }

    public function author_earning($filters)
    {
        $this->db->select('author.author_id, author_name, last_paid_date, royalty_payment.status as status, SUM(qty*price) AS total_sales, SUM(qty*price*book.royalty/100) as earned_royalty')
            ->from('book')
            ->join('draft_author', 'draft_author.draft_id = book.draft_id', 'right')
            ->join('author', 'draft_author.author_id = author.author_id')
            ->join('royalty_payment', 'author.author_id = royalty_payment.author_id', 'left')
            ->join('invoice_book', 'book.book_id = invoice_book.book_id')
            ->join('invoice', 'invoice_book.invoice_id = invoice.invoice_id')
            ->group_by('author.author_id');
        if ($filters['keyword'] != '') {
            $this->db->like('author_name', $filters['keyword']);
        }
        if ($filters['period_end'] != null) {
            //if author.last_paid_date == null
            $this->db->where('issued_date BETWEEN IFNULL(royalty_payment.last_paid_date, "2000/01/01") and "' . $filters['period_end'] . ' 23:59:59"');
        } else {
            $this->db->where('issued_date BETWEEN IFNULL(royalty_payment.last_paid_date, "2000/01/01") and addtime(CURDATE(), "23:59:59") - INTERVAL 1 DAY');
        }
        $this->db->where('invoice.status', 'finish');
        return $this->db->get()->result();
    }

    public function author_details($author_id, $filters)
    {
        $last_paid_date = '';
        if ($filters['last_paid_date'] == '') $last_paid_date = "2021/01/01";
        else $last_paid_date = $filters['last_paid_date'];
        $this->db->select('book.book_id, book.book_title, harga, SUM(qty) AS count, SUM(qty*price) AS total_sales, SUM(qty*price*invoice_book.royalty/100) as earned_royalty')
            ->from('book')
            ->join('draft_author', 'draft_author.draft_id = book.draft_id', 'right')
            ->join('invoice_book', 'book.book_id = invoice_book.book_id')
            ->join('invoice', 'invoice_book.invoice_id = invoice.invoice_id')
            ->where('invoice.status', 'finish')
            ->where('draft_author.author_id', $author_id);
        if ($filters['period_end'] != null) {
            //if author.last_paid_date == null
            $this->db->where('issued_date BETWEEN "' . $last_paid_date .  '" and "' . $filters['period_end'] . ' 23:59:59"');
        } else {
            $this->db->where('issued_date BETWEEN "' . $last_paid_date .  '" and addtime(CURDATE(), "23:59:59") - INTERVAL 1 DAY');
        }
        
        return $this->db->get()->result();
    }

    public function stocks_info($author_id, $filters)
    {
        $last_paid_date = '';
        if ($filters['last_paid_date'] == '') $last_paid_date = "2021/01/01";
        else $last_paid_date = $filters['last_paid_date'];
        $this->db->select('IFNULL(warehouse_present, 0) as WP, IFNULL(showroom_present, 0) as SP, IFNULL(library_present, 0) as LP, SUM(qty) AS count')
            ->from('book')
            ->join('book_stock', 'book_stock.book_id = book.book_id', 'left')
            ->join('draft_author', 'draft_author.draft_id = book.draft_id', 'right')
            ->join('invoice_book', 'book.book_id = invoice_book.book_id')
            ->join('invoice', 'invoice_book.invoice_id = invoice.invoice_id')
            ->where('invoice.status', 'finish')
            ->where('draft_author.author_id', $author_id)
            ->where('issued_date BETWEEN "' . $last_paid_date .  '" and now()');
        return $this->db->get()->result();
    }
}
