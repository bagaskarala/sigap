<?php defined('BASEPATH') or exit('No direct script access allowed');

class Invoice_model extends MY_Model
{
    public $per_page = 10;

    public function validate_invoice()
    {
        $data = array();
        $data['input_error'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('due-date') == '') {
            $data['input_error'][] = 'error-due-date';
            $data['status'] = FALSE;
        }

        if ($this->input->post('type') == '') {
            $data['input_error'][] = 'error-type';
            $data['status'] = FALSE;
        } else if ($this->input->post('type') == 'cash') {
            if ($this->input->post('source') == '') {
                $data['input_error'][] = 'error-source';
                $data['status'] = FALSE;
            } 
        }

        if ($this->input->post('source') == 'library') {
            if ($this->input->post('source-library-id') == '') {
                $data['input_error'][] = 'error-source-library';
                $data['status'] = FALSE;
            }
        }

        if ($this->input->post('customer-id') == '') {
            if ($this->input->post('new-customer-name') == '' && $this->input->post('new-customer-phone-number') == '') {
                $data['input_error'][] = 'error-customer-info';
                $data['status'] = FALSE;
            } else {
                if ($this->input->post('new-customer-name') == '') {
                    $data['input_error'][] = 'error-new-customer-name';
                    $data['status'] = FALSE;
                }
                if ($this->input->post('new-customer-phone-number') == '') {
                    $data['input_error'][] = 'error-new-customer-phone-number';
                    $data['status'] = FALSE;
                }
                if ($this->input->post('new-customer-type') == '') {
                    $data['input_error'][] = 'error-new-customer-type';
                    $data['status'] = FALSE;
                }
            }
        }

        if (empty($this->input->post('invoice_book_id'))) {
            $data['input_error'][] = 'error-no-book';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function fetch_invoice_id($invoice_id)
    {
        return $this->db
            ->select('*')
            ->from('invoice')
            ->where('invoice_id', $invoice_id)
            ->get()
            ->row();
    }

    public function fetch_invoice_book($invoice_id)
    {
        return $this->db
            ->select('invoice_book.*, book.book_title, book.harga')
            ->from('invoice_book')
            ->join('book', 'book.book_id = invoice_book.book_id')
            ->where('invoice_id', $invoice_id)
            ->get()
            ->result();
    }

    public function fetch_book_info($book_id)
    {
        return $this->db
            ->select('book_title')
            ->from('book')
            ->where('book_id', $book_id)
            ->get()
            ->row();
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

    public function get_book($book_id)
    {
        $book = $this->select('book.*')
            ->where('book_id', $book_id)
            ->get('book');

        $stock = $this->fetch_warehouse_stock($book_id);

        if ($stock == NULL) {
            $book->stock = 0;
        } else {
            $book->stock = $stock->warehouse_present;
        }
        return $book;
    }

    public function get_discount($type)
    {
        return $this->select('discount')->where('membership', $type)->get('discount');
    }

    public function get_customer($customer_id)
    {
        $this->db->select('customer_id, name, address, phone_number, type, discount');
        $this->db->from('customer');
        $this->db->join('discount', 'customer.type = discount.membership', 'left');
        $this->db->where('customer.customer_id', $customer_id);
        return $this->db->get()->row();
    }

    public function get_last_invoice_number($type)
    {
        $initial = '';
        switch ($type) {
            case 'credit':
                $initial = 'K';
                break;
            case 'cash':
                $initial = 'T';
                break;
            case 'online':
                $initial = 'O';
                break;
            case 'showroom':
                $initial = 'S';
                break;
        }
        $date_created       = substr(date('Ymd'), 2);
        $data = $this->db->select('*')->where('type', $type)->count_all_results('invoice') + 1;
        $invoiceNumber = $initial . $date_created . '-' . str_pad($data, 6, 0, STR_PAD_LEFT);
        return $invoiceNumber;
    }

    public function filter_invoice($filters, $page)
    {
        $invoice = $this->select(['invoice_id', 'number', 'issued_date', 'due_date', 'invoice.customer_id', 'name as customer_name', 'customer.type as customer_type', 'status', 'invoice.type as invoice_type'])
            ->join('customer', 'invoice.customer_id = customer.customer_id', 'left')
            ->when('keyword', $filters['keyword'])
            ->when('invoice_type', $filters['invoice_type'])
            ->when('customer_type', $filters['customer_type'])
            ->when('status', $filters['status'])
            ->order_by('invoice_id', 'DESC')
            ->paginate($page)
            ->get_all();

        $total = $this->select(['invoice_id', 'number', 'name'])
            ->join('customer', 'invoice.customer_id = customer.customer_id', 'left')
            ->when('keyword', $filters['keyword'])
            ->when('invoice_type', $filters['invoice_type'])
            ->when('customer_type', $filters['customer_type'])
            ->when('status', $filters['status'])
            ->order_by('invoice_id')
            ->count();

        return [
            'invoice'  => $invoice,
            'total' => $total
        ];
    }

    public function when($params, $data)
    {
        // jika data null, maka skip
        if ($data != '') {
            if ($params == 'keyword') {
                $this->group_start();
                $this->or_like('number', $data);
                $this->or_like('name', $data);
                $this->group_end();
            } else {
                $this->group_start();
                $this->or_like('invoice.type', $data);
                $this->or_like('customer.type', $data);
                $this->or_like('status', $data);
                $this->group_end();
            }
        }
        return $this;
    }

    // BOOK REQUEST BUAT DI GUDANG
    // filter untuk book request gudang
    public function filter_book_request($filters, $page)
    {
        $book_request = $this->select(['invoice_id', 'number', 'issued_date', 'due_date', 'status', 'type', 'source'])
            ->where('status', 'confirm')
            ->or_where('status', 'preparing')
            ->or_where('status', 'preparing_finish')
            ->when_request('keyword', $filters['keyword'])
            ->when_request('type', $filters['type'])
            ->order_by('invoice_id', 'DESC')
            ->paginate($page)
            ->get_all();

        $total = $this->select('invoice_id')
            ->where('status', 'confirm')
            ->or_where('status', 'preparing')
            ->or_where('status', 'preparing_finish')
            ->when_request('keyword', $filters['keyword'])
            ->when_request('type', $filters['type'])
            ->order_by('invoice_id')
            ->count();
        return [
            'book_request'  => $book_request,
            'total' => $total
        ];
    }

    public function when_request($params, $data)
    {
        // jika data null, maka skip
        if ($data != '') {
            if ($params == 'keyword') {
                $this->group_start();
                $this->or_like('number', $data);
                $this->group_end();
            }
            else if ($params == 'type') {
                $this->where('type', $data);
            }
            else if ($params == 'status') {
                $this->where('status', $data);
            }
        }
        return $this;
    }
    public function start_progress($invoice_id)
    {
        // transaction data agar konsisten
        $this->db->trans_begin();

        $input = [
            'status' => 'preparing',
            'preparing_start_date' => date('Y-m-d H:i:s')
        ];

        $this->invoice->where('invoice_id', $invoice_id)->update($input);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function finish_progress($invoice_id)
    {
        $input = [
            'status' => "preparing_finish",
            "preparing_end_date" => date('Y-m-d H:i:s')
        ];


        $update_state = $this->invoice->where('invoice_id', $invoice_id)->update($input);

        if ($update_state) {
            return true;
        } else {
            return false;
        }
    }
}
