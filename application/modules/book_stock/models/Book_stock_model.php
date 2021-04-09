<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_stock_model extends MY_Model
{
    public $per_page = 10;

    public function filter_book_stock($filters, $page)
    {
        $book_stocks = $this->select([
            'book_stock_id', 'book.book_id',
            'book.book_title', 'book.isbn', 'book.published_date',
            'book_stock.*'])
            ->when('keyword', $filters['keyword'])
            ->when('published_year', $filters['published_year'])
            ->when('warehouse_present', $filters['warehouse_present'])
            ->join_table('book', 'book_stock', 'book')
            ->order_by('warehouse_present')
            ->paginate($page)
            ->get_all();

        $total = $this->select('book.book_id')
            ->when('keyword', $filters['keyword'])
            ->when('published_year', $filters['published_year'])
            ->when('warehouse_present', $filters['warehouse_present'])
            ->join_table('book', 'book_stock', 'book')
            ->order_by('warehouse_present')
            ->count();
        return [
            'book_stocks' => $book_stocks,
            'total' => $total
        ];
    }

    public function filter_excel($filters)
    {
        return $this->select(['book.book_title', 'book.isbn', 'book.published_date', 'book_stock.*'])
            ->when('keyword', $filters['keyword'])
            ->when('published_year', $filters['published_year'])
            ->when('warehouse_present', $filters['warehouse_present'])
            ->join_table('book', 'book_stock', 'book')
            ->order_by('book.book_title')
            ->get_all();
    }

    public function when($params, $data)
    {
        //jika data null, maka skip
        if ($data) {
            if ($params == 'keyword') {
                $this->group_start();
                $this->or_like('book_title', $data);
                $this->or_like('isbn', $data);
                $this->group_end();
            }
            if ($params == 'published_year') {
                $this->where('year(published_date)', $data);
            }
            if ($params == 'warehouse_present') {
                if($data == 'up_to_50'){
                    $this->where('warehouse_present <=', 50);
                }
                else if($data == 'above_50'){
                    $this->where('warehouse_present >', 50);
                } 
                else{
                    $this->where('warehouse_present', $data);
                }
            }
        }
        return $this;
    }

    public function get_book_stock($book_stock_id){
        return $this->select(['book.book_title', 
        'book_stock.*'])
        ->join('book')
        ->where('book_stock_id', $book_stock_id)
        ->get();
    }

    public function get_stock_revision($book_id){
        return $this->db->select('*')
        ->from('book_stock_revision')
        ->where('book_stock_revision.book_id', $book_id)
        ->order_by('book_stock_revision.book_stock_revision_id', 'DESC')
        ->get()
        ->result();
    }

}
