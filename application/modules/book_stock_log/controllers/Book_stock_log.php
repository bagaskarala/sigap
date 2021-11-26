<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_stock_log extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($page = NULL)
    {
        $book_stocks = $this->db->select('*')->from('book_stock')->get()->result();
        // var_dump($book_stocks);
        foreach ($book_stocks as $stock) {        
            $today_log = $this->db->select('*')->from('book_stock_log')->where('book_id', $stock->book_id)->where('date BETWEEN 
            addtime(CURDATE(), "00:00:00") AND addtime(CURDATE(), "23:59:59")')->order_by('date', 'DESC')->get()->row();
            if (!$today_log) {
                $add = [
                    'date' => now(),
                    'book_id' => $stock->book_id,
                    'warehouse_stock' => $stock->warehouse_present ? $stock->warehouse_present : 0,
                    'showroom_stock' => $stock->showroom_present ? $stock->showroom_present : 0,
                    'library_stock' => $stock->library_present ? $stock->library_present : 0,
                    'retur_stock' => $stock->retur_stock ? $stock->retur_stock : 0,
                ];
                $this->db->insert('book_stock_log', $add);
            }
        }
        return;
    }
}
