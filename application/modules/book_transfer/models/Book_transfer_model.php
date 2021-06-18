<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_transfer_model extends MY_Model{
    public $per_page = 10;

    public function get_validation_rules()
    {
        $validation_rules = [
            [
                'field' => 'quantity',
                'label' => 'Jumlah Buku',
                'rules' => 'trim|required',
            ],
            [
                'field' => 'destination',
                'label' => 'Tujuan Pemindahan',
                'rules' => 'trim|required',
            ],
        ];

        return $validation_rules;
    }

    public function get_default_values()
    {
        return [
            'book_id'           => '',
            'destination'       => '',
            'library_id'        => '',
            'quantity'          => '',
        ];
    }

    public function fetch_book_transfer($book_transfer_id){
        return $this->select(['library.*', 'book_transfer.*'])
        ->join_table('library', 'book_transfer', 'library')
        ->where('book_transfer_id', $book_transfer_id)
        ->get();
    }

    public function get_book($book_id)
    {
        return $this->select('book.*')
            ->where('book_id', $book_id)
            ->get('book');
    }
    
    public function filter_book_transfer($filters, $page){
        $book_transfer = $this->select(['book_transfer.*','library.library_name'])
        ->when('keyword',$filters['keyword'])
        ->when('status',$filters['status'])
        ->join_table('library','book_transfer','library')
        ->order_by('book_transfer_id','DESC')
        ->paginate($page)
        ->get_all();

        $total = $this->select('book_transfer.*')
        ->when('keyword',$filters['keyword'])
        ->when('status',$filters['status'])
        ->count();

        return [
            'book_transfer'  => $book_transfer,
            'total'         => $total,
        ];
    }

    public function when($params, $data)
    {
        // jika data null, maka skip
        if ($data != '') {
            if($params == 'keyword'){
                $this->group_start();
                $this->like('transfer_number',$data);
                $this->group_end();
            }
            if($params == 'status'){
                $this->where('status', $data);
            }
        }
        return $this;
    }

    public function fetch_book_transfer_list($book_transfer_id)
    {
        return $this->db
            ->select('book_transfer_list.*, book.book_id, book.book_title')
            ->from('book_transfer_list')
            ->join('book', 'book.book_id = book_transfer_list.book_id')
            ->where('book_transfer_id', $book_transfer_id)
            ->get()
            ->result();
    }

    // ambil data buku yg ada di gudang
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

    // buat nampilin data buku gudang di form add
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

    public function get_transfer_number(){
        $data = $this->select(['transfer_number'])
        ->order_by('transfer_number', 'DESC')
        ->get('book_transfer');
        $current_year = date('Y');
        if ($data){
            $year = substr($data->transfer_number,0,4);
            $number = substr($data->transfer_number,5);
            if ($current_year == $year){
                $new_number = (int)++$number;
                $new_number = str_pad((string) $new_number, 5, '0', STR_PAD_LEFT);
            }
            else {
                $new_number = '00001';
            }
            $new_transfer_number = $current_year."-".$new_number;
        }
        else {
            $new_transfer_number = $current_year."-00001";
        }
        // return 00001
        return $new_transfer_number;
    }

    public function get_staff_gudang()
    {
        return $this->select(['user_id', 'username', 'level', 'email'])
            ->where('level', 'staff_gudang')
            ->where('is_blocked', 'n')
            ->order_by('username', 'ASC')
            ->get_all('user');
    }

    public function get_staff_gudang_by_id($book_transfer_id)
    {
        return $this->db->select(['book_transfer_user_id', 'book_transfer_user.user_id', 'book_transfer_id', 'username', 'email'])
            ->from('user')
            ->join('book_transfer_user', 'user.user_id = book_transfer_user.user_id')
            ->where('book_transfer_id', $book_transfer_id)
            ->get()->result();
    }

    public function check_row_staff_gudang($book_transfer_id, $user_id)
    {
        return $this->db
            ->where(['book_transfer_id' => $book_transfer_id, 'user_id' => $user_id])
            ->get('book_transfer_user')
            ->num_rows();
    }

    public function start_progress($book_transfer_id)
    {
        // transaction data agar konsisten
        $this->db->trans_begin();

        $input = [
            'status' => 'preparing',
            "preparing_start_date" => date('Y-m-d H:i:s')
        ];

        $this->book_transfer->where('book_transfer_id', $book_transfer_id)->update($input);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function finish_progress($book_transfer_id)
    {
        $input = [
            'status' => "preparing_finish",
            "preparing_end_date" => date('Y-m-d H:i:s')
        ];

        $update_state = $this->book_transfer->where('book_transfer_id', $book_transfer_id)->update($input);

        if ($update_state) {
            return true;
        } else {
            return false;
        }
    }


}
