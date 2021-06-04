<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_transaction_model extends MY_Model{
    public $per_page = 10;
    public function filter_book_transaction($filters, $page)
    {
        $book_transactions = $this->select([ 
            'print_order.print_order_id','print_order.order_number',
            'book_receive.book_receive_id',
            'invoice.invoice_id', 'invoice.number as invoice_number', 
            'book_stock_revision.book_stock_revision_id', 'book_stock_revision.revision_type', 'book_stock_revision.type',
            'book_stock_revision.warehouse_revision as revision_qty',
            'book_non_sales.book_non_sales_id', 'book_non_sales.number as book_non_sales_number',
            'book_transfer.book_transfer_id', 'book_transfer.transfer_number',         
            'book.book_title', 'book_transaction.*'])
            ->where_not('date', NULL)
            ->join_table('book', 'book_transaction', 'book')
            ->join_table('book_receive', 'book_transaction', 'book_receive')
            ->join_table('print_order', 'book_receive', 'print_order')
            ->join_table('invoice', 'book_transaction', 'invoice')
            ->join_table('book_transfer', 'book_transaction', 'book_transfer')
            ->join_table('book_non_sales', 'book_transaction', 'book_non_sales')
            ->join_table('book_stock_revision', 'book_transaction', 'book_stock_revision')
            ->when('keyword', $filters['keyword'])
            ->when('start_date', $filters['start_date'])
            ->when('end_date', $filters['end_date'])
            ->when('transaction_type', $filters['transaction_type'])
            ->order_by('book_transaction_id', 'DESC')
            ->paginate($page)
            ->get_all();

        $total = $this->select(['book.book_title', 'book_transaction.*'])
            ->where_not('date', NULL)
            ->join_table('book', 'book_transaction', 'book')
            ->when('keyword', $filters['keyword'])
            ->when('start_date', $filters['start_date'])
            ->when('end_date', $filters['end_date'])
            ->when('transaction_type', $filters['transaction_type'])
            ->count();
        return [
            'book_transactions' => $book_transactions,
            'total' => $total
        ];
    }
    public function get_transaction_data($book_stock_id,$year){
        return $this->select(['book_transaction.*'])
            ->when('book_stock_id',$book_stock_id)
            ->when('start_date', $year.'-01-01')
            ->when('end_date', $year.'-12-31')
            ->get_all();
    }
    public function when($params, $data)
    {
        //jika data null, maka skip
        if ($data) {
            if ($params == 'keyword') {
                $this->group_start();
                $this->or_like('book_title', $data);
                $this->group_end();
            }
            else if ($params == 'book_stock_id') {
                $this->where('book_stock_id', $data);
            }
            else if ($params == 'start_date') {
                $this->where('date >=', $data.' 00:00:00');
            }
            else if ($params == 'end_date') {
                $this->where('date <=', $data.' 23:59:59');
            }
            else if ($params == 'transaction_type'){
                if ($data == 'print'){
                    $this->where_not('book_transaction.book_receive_id', null);
                }
                else if ($data == 'invoice'){
                    $this->where_not('book_transaction.invoice_id', null);
                }
                else if ($data == 'transfer'){
                    $this->where_not('book_transaction.book_transfer_id', null);
                }
                else if ($data == 'non_sales'){
                    $this->where_not('book_transaction.book_non_sales_id', null);
                }
                else if ($data == 'revision'){
                    $this->where_not('book_transaction.book_stock_revision_id', null);
                }
            }
        }
        return $this;
    }
    public function filter_excel($filters)
    {
        return $this->select([ 
            'print_order.print_order_id','print_order.order_number',
            'book_receive.book_receive_id',
            'invoice.invoice_id', 'invoice.number as invoice_number', 
            'book_stock_revision.book_stock_revision_id', 'book_stock_revision.revision_type', 'book_stock_revision.type',
            'book_non_sales.book_non_sales_id', 'book_non_sales.number as book_non_sales_number',
            'book_transfer.book_transfer_id', 'book_transfer.transfer_number',         
            'book.book_title', 'book_transaction.*'])
            ->where_not('date', NULL)
            ->join_table('book', 'book_transaction', 'book')
            ->join_table('book_receive', 'book_transaction', 'book_receive')
            ->join_table('print_order', 'book_receive', 'print_order')
            ->join_table('invoice', 'book_transaction', 'invoice')
            ->join_table('book_transfer', 'book_transaction', 'book_transfer')
            ->join_table('book_non_sales', 'book_transaction', 'book_non_sales')
            ->join_table('book_stock_revision', 'book_transaction', 'book_stock_revision')
            ->when('keyword', $filters['keyword'])
            ->when('start_date', $filters['start_date'])
            ->when('end_date', $filters['end_date'])
            ->when('transaction_type', $filters['transaction_type'])
            ->order_by('book_transaction_id', 'DESC')
            ->get_all();
    }
}