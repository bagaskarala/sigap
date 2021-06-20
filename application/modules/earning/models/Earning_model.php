<?php defined('BASEPATH') or exit('No direct script access allowed');

class Earning_model extends MY_Model
{
    //filter invoice
    public function filter_total($filters)
    {
        $this->db->select('(qty*price*(1-discount/100)) AS earning')
            ->from('invoice')
            ->join('invoice_book', 'invoice.invoice_id = invoice_book.invoice_id', 'right')
            ->order_by('invoice.invoice_id', 'ASC')
            ->group_start()
            ->where('invoice.status', 'finish')
            ->or_where('invoice.status', 'cancel')
            ->group_end()
            ->where('YEAR(invoice.issued_date)', $filters['date_year'])
            ->where('MONTH(invoice.issued_date)', $filters['date_month'])
            ->where('invoice.type', $filters['invoice_type']);
        return $this->db->get()->result();
        //select sum((qty*price*(1-discount/100))) as earning from invoice right join invoice_book on invoice.invoice_id = invoice_book.invoice_id;
    }

    public function filter_detail($filters)
    {
        $this->db->select('SUM(qty*price*(1-discount/100)) AS earning')
            ->from('invoice')
            ->join('invoice_book', 'invoice.invoice_id = invoice_book.invoice_id', 'right')
            ->order_by('invoice.invoice_id', 'ASC')
            ->group_start()
            ->where('invoice.status', 'finish')
            ->or_where('invoice.status', 'cancel')
            ->group_end()
            ->where('invoice.type', $filters['invoice_type'])
            ->where('YEAR(invoice.issued_date)', $filters['date_year']);
        if ($filters['date_month'] != '') {
            $this->db->where('MONTH(invoice.issued_date)', $filters['date_month']);
        }
        return $this->db->get()->row();
    }

    public function get_invoice($filters)
    {
        return $this->db->select('invoice.number, invoice.issued_date, invoice.type, status ,sum(price*(1-discount/100)*qty) as earning')
            ->from('invoice')
            ->join('invoice_book', 'invoice.invoice_id = invoice_book.invoice_id', 'right')
            ->order_by('invoice.invoice_id', 'ASC')
            ->group_by('invoice_book.invoice_id')
            ->group_start()
            ->where('invoice.status', 'finish')
            ->or_where('invoice.status', 'cancel')
            ->group_end()
            ->where('YEAR(invoice.issued_date)', $filters['date_year'])
            ->where('MONTH(invoice.issued_date)', $filters['date_month'])
            ->where('invoice.type', $filters['invoice_type'])
            ->get()->result();
    }

    public function filter_excel_total($filters)
    {
        return $this->db->select('number, issued_date, type, status, receipt, sum(price*(1-discount/100)*qty) as earning')
            ->from('invoice')
            ->join('invoice_book', 'invoice.invoice_id = invoice_book.invoice_id', 'right')
            ->order_by('invoice.invoice_id', 'ASC')
            ->group_by('invoice_book.invoice_id')
            ->group_start()
            ->where('invoice.status', 'finish')
            ->or_where('invoice.status', 'cancel')
            ->group_end()
            ->where('YEAR(invoice.issued_date)', $filters['date_year'])
            ->get()->result();
    }

    public function filter_excel_detail($filters)
    {
        $this->db->select('number, issued_date, type, status, receipt, sum(price*(1-discount/100)*qty) as earning')
            ->from('invoice')
            ->join('invoice_book', 'invoice.invoice_id = invoice_book.invoice_id', 'right')
            ->order_by('invoice.invoice_id', 'ASC')
            ->group_by('invoice_book.invoice_id')
            ->group_start()
            ->where('invoice.status', 'finish')
            ->or_where('invoice.status', 'cancel')
            ->group_end()
            ->where('YEAR(invoice.issued_date)', $filters['date_year']);
        if ($filters['date_month'] != '') {
            $this->db->where('MONTH(invoice.issued_date)', $filters['date_month']);
        }
        if ($filters['invoice_type'] != '') {
            $this->db->where('invoice.type', $filters['invoice_type']);
        }
        return $this->db->get()->result();
    }
}
