<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_stock_model extends MY_Model
{
    public $per_page = 10;

    public function filter_book_stock($filters, $page)
    {
        $book_stocks = $this->select([
            'author.author_name', 'draft.draft_id',
            'book_stock_id', 'book.book_id',
            'book.book_title', 'book.published_date',
            'book_stock.*'])
            ->join_table('book', 'book_stock', 'book')
            ->join_table('draft', 'book', 'draft')
            ->join_table('category', 'draft', 'category')
            ->join_table('draft_author', 'draft', 'draft')
            ->join_table('author', 'draft_author', 'author')
            ->when('keyword', $filters['keyword'])
            ->when('published_year', $filters['published_year'])
            ->when('stock_moreeq', $filters['stock_moreeq'])
            ->when('stock_lesseq', $filters['stock_lesseq'])
            ->order_by('warehouse_present')
            ->group_by('draft.draft_id')
            ->paginate($page)
            ->get_all();

        $total = $this->select('book.book_id')
            ->when('keyword', $filters['keyword'])
            ->when('published_year', $filters['published_year'])
            ->when('stock_moreeq', $filters['stock_moreeq'])
            ->when('stock_lesseq', $filters['stock_lesseq'])
            ->join_table('book', 'book_stock', 'book')
            ->join_table('draft', 'book', 'draft')
            ->join_table('category', 'draft', 'category')
            ->join_table('draft_author', 'draft', 'draft')
            ->join_table('author', 'draft_author', 'author')
            ->group_by('draft.draft_id')
            ->order_by('warehouse_present')
            ->count();
        foreach ($book_stocks as $b) {
            if ($b->draft_id) {
                $b->authors = $this->get_id_and_name('author', 'draft_author', $b->draft_id, 'draft');
            } else {
                $b->authors = [];
            }
        }
    
        return [
            'book_stocks' => $book_stocks,
            'total' => $total
        ];
    }

    public function filter_excel_stock($filters)
    {
        $book_stocks =  $this->select([
            'author.author_name', 'draft.draft_id',
            'book_stock_id', 'book.book_id',
            'book.book_title', 'book.published_date',
            'book_stock.*'])
            ->join_table('book', 'book_stock', 'book')
            ->join_table('draft', 'book', 'draft')
            ->join_table('category', 'draft', 'category')
            ->join_table('draft_author', 'draft', 'draft')
            ->join_table('author', 'draft_author', 'author')
            ->when('keyword', $filters['keyword'])
            ->when('published_year', $filters['published_year'])
            ->when('stock_moreeq', $filters['stock_moreeq'])
            ->when('stock_lesseq', $filters['stock_lesseq'])
            ->order_by('book.book_title')
            ->group_by('draft.draft_id')
            ->get_all();
        return $book_stocks;
    }

    public function filter_book_asset($filters, $page)
    {
        $book_assets = $this->select([
            'author.author_name', 'draft.draft_id',
            'book_stock_id', 'book.book_id',
            'book.book_title', 'book.published_date','book.harga',
            'book_stock.*'])
            ->join_table('book', 'book_stock', 'book')
            ->join_table('draft', 'book', 'draft')
            ->join_table('category', 'draft', 'category')
            ->join_table('draft_author', 'draft', 'draft')
            ->join_table('author', 'draft_author', 'author')
            ->when('keyword', $filters['keyword'])
            ->order_by('warehouse_present')
            ->group_by('draft.draft_id')
            ->paginate($page)
            ->get_all();

        $book_assets_price = $this->select([
            'author.author_name', 'draft.draft_id',
            'book_stock_id', 'book.book_id',
            'book.book_title', 'book.published_date','book.harga',
            'book_stock.*'])
            ->join_table('book', 'book_stock', 'book')
            ->join_table('draft', 'book', 'draft')
            ->join_table('category', 'draft', 'category')
            ->join_table('draft_author', 'draft', 'draft')
            ->join_table('author', 'draft_author', 'author')
            ->when('keyword', $filters['keyword'])
            ->order_by('warehouse_present')
            ->group_by('draft.draft_id')
            ->get_all();

        $total = $this->select('book.book_id')
            ->when('keyword', $filters['keyword'])
            ->join_table('book', 'book_stock', 'book')
            ->join_table('draft', 'book', 'draft')
            ->join_table('category', 'draft', 'category')
            ->join_table('draft_author', 'draft', 'draft')
            ->join_table('author', 'draft_author', 'author')
            ->group_by('draft.draft_id')
            ->order_by('warehouse_present')
            ->count();
        foreach ($book_assets as $b) {
            if ($b->draft_id) {
                $b->authors = $this->get_id_and_name('author', 'draft_author', $b->draft_id, 'draft');
            } else {
                $b->authors = [];
            }
        }
    
        return [
            'book_assets' => $book_assets,
            'book_assets_price' => $book_assets_price,
            'total' => $total
        ];
    }

    public function filter_excel_asset($filters)
    {
        $book_assets =  $this->select([
            'author.author_name', 'draft.draft_id',
            'book_stock_id', 'book.book_id',
            'book.book_title', 'book.published_date','book.harga',
            'book_stock.*'])
            ->join_table('book', 'book_stock', 'book')
            ->join_table('draft', 'book', 'draft')
            ->join_table('category', 'draft', 'category')
            ->join_table('draft_author', 'draft', 'draft')
            ->join_table('author', 'draft_author', 'author')
            ->when('keyword', $filters['keyword'])
            ->order_by('book.book_title')
            ->group_by('draft.draft_id')
            ->get_all();
        return $book_assets;
    }

    public function when($params, $data)
    {
        //jika data null, maka skip
        if ($data) {
            if ($params == 'keyword') {
                $this->group_start();
                $this->or_like('book_title', $data);
                $this->or_like('author_name', $data);
                $this->group_end();
            }
            else if ($params == 'published_year') {
                $this->where('year(published_date)', $data);
            }
            else if ($params == 'stock_lesseq') {
                $this->where('warehouse_present <=', $data);
            }
            else if ($params == 'stock_moreeq'){
                $this->where('warehouse_present >=', $data);
            }
        }
        return $this;
    }

    public function get_book_stock($book_stock_id){
        return $this->select(['book.book_title', 'author.author_name',
        'book_stock.*'])
        ->join_table('book', 'book_stock', 'book')
        ->join_table('draft', 'book', 'draft')
        ->join_table('draft_author', 'draft', 'draft')
        ->join_table('author', 'draft_author', 'author')
        ->group_by('draft.draft_id')
        ->order_by('book.book_title')
        ->where('book_stock_id', $book_stock_id)
        ->get();
    }

    public function get_book_stock_by_book_id($book_id){
        return $this->select(['book.book_title','book_stock.*', 'author.author_name', 'book.harga'])
        ->where('book_stock.book_id', $book_id)
        ->join_table('book','book_stock','book')
        ->join_table('draft', 'book', 'draft')
        ->join_table('draft_author', 'draft', 'draft')
        ->join_table('author', 'draft_author', 'author')
        ->get();
    }

    public function get_book($book_id)
    {
        return $this->select('book.*')
        ->where('book_id', $book_id)
        ->join_table('book','book_stock','book')
        ->get('book');
    }

    public function get_stock_by_id($book_stock_id)
    {
        return $this->db->select('*')
        ->from('book_stock')
        ->where('book_stock_id', $book_stock_id)
        ->get()
        ->row();
    }

    public function get_stock_revision($book_id){
        return $this->db->select('*')
        ->from('book_stock_revision')
        ->where('book_stock_revision.book_id', $book_id)
        ->where('book_stock_revision.type', 'revision')
        ->order_by('book_stock_revision.book_stock_revision_id', 'DESC')
        ->get()
        ->result();
    }

    public function get_library($library_id){
        return $this->db->select('*')
        ->from('library')
        ->where('library.library_id', $library_id)
        ->get()
        ->row();
    }

    public function get_library_stock($book_stock_id){
        return $this->db->select('*','library.library_name')
        ->from('library_stock_detail')
        ->join('library', "library.library_id = library_stock_detail.library_id", 'left')
        ->where('library_stock_detail.book_stock_id', $book_stock_id)
        ->get()
        ->result();
    }

    public function get_one_library_stock($book_stock_id,$library_id){
        return $this->db->select('*')
        ->from('library_stock_detail')
        ->where('book_stock_id', $book_stock_id)
        ->where('library_id', $library_id)
        ->get()
        ->row();
    }

    public function retur_stock()
    {
        return $this->select(['book.book_title', 'book.book_id', 'author.author_name',
            'book_stock.book_stock_id', 'book_stock.book_id', 'book_stock.retur_stock'])
            ->join_table('book', 'book_stock', 'book')
            ->join_table('draft', 'book', 'draft')
            ->join_table('draft_author', 'draft', 'draft')
            ->join_table('author', 'draft_author', 'author')
            ->where_not('retur_stock', NULL)
            ->where_not('retur_stock', 0)
            ->order_by('book.book_title')
            ->get_all();
    }

    public function log_add_retur()
    {
        return $this->db->select(['book.book_title', 'book_stock_revision.*'])
            ->from('book_stock_revision')
            ->join('book', 'book.book_id = book_stock_revision.book_id')
            ->where('book_stock_revision.type', 'return')
            ->where('book_stock_revision.revision_type', 'sub')
            ->order_by('book_stock_revision_id', 'DESC')
            ->get()
            ->result();
    }

    public function log_delete_retur()
    {
        return $this->db->select(['book.book_title', 'book_stock_revision.*'])
            ->from('book_stock_revision')
            ->join('book', 'book.book_id = book_stock_revision.book_id')
            ->where('book_stock_revision.type', 'return')
            ->where('book_stock_revision.revision_type', 'del')
            ->order_by('book_stock_revision_id', 'DESC')
            ->get()
            ->result();
    }
}
