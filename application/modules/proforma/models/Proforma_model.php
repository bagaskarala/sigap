<?php defined('BASEPATH') or exit('No direct script access allowed');

class Proforma_model extends MY_Model
{
    public $per_page = 10;

    public function validate_proforma()
    {
        $data = array();
        $data['input_error'] = array();
        $data['status'] = TRUE;

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

        if (empty($this->input->post('proforma_book_id'))) {
            $data['input_error'][] = 'error-no-book';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function fetch_proforma_id($proforma_id)
    {
        return $this->db
            ->select('*')
            ->from('proforma')
            ->where('proforma_id', $proforma_id)
            ->get()
            ->row();
    }

    public function fetch_proforma_book($proforma_id)
    {
        return $this->db
            ->select('proforma_book.*, book.book_title, book.harga')
            ->from('proforma_book')
            ->join('book', 'book.book_id = proforma_book.book_id')
            ->where('proforma_id', $proforma_id)
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

    public function get_last_proforma_number($convert = false)
    {
        $date_created       = substr(date('Ymd'), 2);
        if ($convert == true) {
            $initial = 'T';
            $data = $this->db->select('*')->where('type', 'cash')->count_all_results('invoice') + 1;
        } else {
            $initial = 'P';
            $last_id = $this->db
                ->select('proforma_id, number')
                ->from('proforma')
                ->order_by('proforma_id', 'DESC')
                ->get()
                ->row();
            if (!$last_id) {
                $data = 1;
            } else {
                [, $number] = explode("-", $last_id->number);
                $data = $number + 1;
            }
        }
        $number = $initial . $date_created . '-' . str_pad($data, 6, 0, STR_PAD_LEFT);
        return $number;
    }

    public function filter_proforma($filters, $page)
    {
        $proforma = $this->select(['proforma_id', 'number', 'issued_date', 'due_date', 'proforma.customer_id', 'name as customer_name', 'customer.type as customer_type'])
            ->join('customer', 'proforma.customer_id = customer.customer_id', 'left')
            ->when('keyword', $filters['keyword'])
            ->when('customer_type', $filters['customer_type'])
            ->order_by('proforma_id', 'DESC')
            ->paginate($page)
            ->get_all();

        $total = $this->select(['proforma_id'])
            ->join('customer', 'proforma.customer_id = customer.customer_id', 'left')
            ->when('keyword', $filters['keyword'])
            ->when('customer_type', $filters['customer_type'])
            ->order_by('proforma_id')
            ->count();

        return [
            'proforma' => $proforma,
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
                $this->or_like('customer.type', $data);
                $this->group_end();
            }
        }
        return $this;
    }
}
