<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_non_sales_model extends MY_Model
{
    public $per_page = 10;

    public function get_validation_rules()
    {
        $validation_rules = [
            [
                'field' => 'name',
                'label' => 'Nama',
                'rules' => 'required',
            ],
            [
                'field' => 'address',
                'label' => 'Alamat',
                'rules' => 'required',
            ],
            [
                'field' => 'book_id',
                'label' => 'Buku',
                'rules' => 'required',
            ],
            [
                'field' => 'qty',
                'label' => 'Jumlah Buku',
                'rules' => 'required',
            ],
            [
                'field' => 'type',
                'label' => 'Tipe Non Penjualan',
                'rules' => 'required',
            ],
        ];

        return $validation_rules;
    }

    public function get_default_values()
    {
        return [
            'book_id'           => '',
            'type'              => '',
            'status'            => '',
            'number'            => '',
            'name'              => '',
            'address'           => '',
        ];
    }

    public function filter_book_non_sales($filters, $page){
        $book_non_sales = $this->select(['book_non_sales.*'])
        ->when('keyword',$filters['keyword'])
        ->when('type',$filters['type'])
        ->when('status',$filters['status'])
        ->order_by('book_non_sales_id','DESC')
        ->paginate($page)
        ->get_all();

        $total = $this->select('book_non_sales.id')
        ->when('keyword',$filters['keyword'])
        ->when('type',$filters['type'])
        ->when('status',$filters['status'])
        ->count();

        return [
            'book_non_sales'  => $book_non_sales,
            'total'           => $total,
        ];
    }

    public function when($params, $data)
    {
        // jika data null, maka skip
        if ($data != '') {
            if($params == 'keyword'){
                $this->group_start();
                $this->like('number',$data);
                $this->group_end();
            }
            if($params == 'type'){
                $this->where('type', $data);
            }
            if($params == 'status'){
                $this->where('status', $data);
            }
        }
        return $this;
    }

    public function fetch_book_non_sales($book_non_sales_id){
        return $this->select('book_non_sales.*')
        ->where('book_non_sales_id', $book_non_sales_id)
        ->get();
    }

    public function fetch_book_non_sales_list($book_non_sales_id)
    {
        return $this->db
            ->select('book_non_sales_list.*, book.book_id, book.book_title')
            ->from('book_non_sales_list')
            ->join('book', 'book.book_id = book_non_sales_list.book_id')
            ->where('book_non_sales_id', $book_non_sales_id)
            ->get()
            ->result();
    }

    // cari permintaan tahun ini
    public function get_book_non_sales_year($year){
        return $this->select('book_non_sales.number')
        ->like('number', $year)
        ->get();
    }

    // ambil number di id paling akhir
    public function get_latest_number(){
        return $this->db->select('number')
        ->from('book_non_sales')
        ->limit(1)
        ->order_by('book_non_sales_id', 'DESC')
        ->get()
        ->row();
    }
    public function get_ready_book_list()
    {
        $books = $this->db
            ->select('book_id, book_title')
            ->order_by('book_title', 'ASC')
            ->from('book')
            ->get()
            ->result();
        foreach ($books as $book) {
            // Tambahkan data stock ke buku
            $stock = $this->fetch_warehouse_stock($book->book_id);
            if ($stock == NULL)
                $book->stock = 0;
            else
                $book->stock = $stock->warehouse_present;
        }

        // Buku stock 0 tidak ditampilkan
        foreach ($books as $key => $book) {
            if ($book->stock == 0) {
                unset($books[$key]);
            }
        }

        // Input buku ke array untuk dropdown
        $options = ['' => '-- Pilih --'];
        foreach ($books as $book) {
            $options += [$book->book_id => $book->book_title];
        }

        return $options;
    }
    public function fetch_warehouse_stock($book_id)
    {
        $stock = $this->db->select('warehouse_present')
            ->from('book_stock')
            ->where('book_id', $book_id)
            ->order_by("book_stock_id", "DESC")
            ->limit(1)
            ->get()
            ->row();
        return $stock;
    }
}
