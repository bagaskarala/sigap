<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_stock_log extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index() 
    {
        $book_stock_logs = $this->db->select('*')->from('book_stock_log')->group_by('date')->get()->result();
        header('Content-Type: application/json');
        echo json_encode($book_stock_logs, JSON_PRETTY_PRINT);
    }

    public function save()
    {
        date_default_timezone_set('Asia/Jakarta');
        $now = date("His");
        $start = 000000;
        $end = 20000;
        if ($now >= $start && $now <= $end) {
            $book_stocks = $this->db->select('*')->from('book_stock')->get()->result();
            $logs = [];
            $existing = [];
            foreach ($book_stocks as $stock) {        
                $today_log = $this->db->select('*')->from('book_stock_log')->where('book_id', $stock->book_id)->where('date BETWEEN 
                addtime(CURDATE(), "00:00:00") AND addtime(CURDATE(), "23:59:59")')->order_by('date', 'DESC')->get()->row();
                if (!$today_log) {
                    $data = [
                        'date' => now(),
                        'book_id' => $stock->book_id,
                        'warehouse_stock' => $stock->warehouse_present ? $stock->warehouse_present : 0,
                        'showroom_stock' => $stock->showroom_present ? $stock->showroom_present : 0,
                        'library_stock' => $stock->library_present ? $stock->library_present : 0,
                        'retur_stock' => $stock->retur_stock ? $stock->retur_stock : 0,
                    ];
                    array_push($logs, $data);
                }
                else {
                    array_push($existing, $today_log);
                }
            }
            if ($logs != NULL) {
                $this->db->insert_batch('book_stock_log', $logs);
            }
            header('Content-Type: application/json');
            echo "added:";
            echo json_encode($logs, JSON_PRETTY_PRINT);
            echo "\r\n";
            echo "existing:";
            echo json_encode($existing, JSON_PRETTY_PRINT);
        }
        else {
            echo('Outside Logging Time!');
        }
        return ;
    }
}
