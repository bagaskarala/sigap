<?php defined('BASEPATH') or exit('No direct script access allowed');

class Royalty extends Sales_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'royalty';
        $this->load->model('royalty_model', 'royalty');
        $this->load->helper('sales_helper');
    }

    public function index($page = NULL)
    {
        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'period_end'        => $this->input->get('end_date', true)
        ];

        //validasi max date
        $today = date('Y-m-d', time());
        if (strtotime($filters['period_end']) >= strtotime($today)) {
            redirect($this->pages);
        }
        $this->royalty->per_page = $this->input->get('per_page', true) ?? 10;

        $get_data = $this->royalty->author_earning($filters, $page);

        $royalty = $get_data['royalty'];

        // Hilangkan author yang tidak dapat royalti periode ini
        $royalty = $this->filter_author($royalty, $filters);

        //set dropdown filter penulis
        $filter_dropdown = [
            'keyword'           => '',
            'period_end'        => NULL
        ];
        $dropdown_list = $this->royalty->author_earning($filter_dropdown, NULL)['royalty'];
        $dropdown_list = $this->filter_author($dropdown_list, $filters);
        $dropdown_author = [];
        foreach ($dropdown_list as $each_author) {
            $dropdown_author += [$each_author->author_name => $each_author->author_name];
        }

        $total = $get_data['total'];
        $total_penjualan = 0;
        $total_royalty = 0;
        foreach ($royalty as $royalty_each) {
            $total_royalty += $royalty_each->earned_royalty;
        }

        $pagination = $this->royalty->make_pagination(site_url('royalty'), 2, $total);

        $pages      = $this->pages;
        $main_view  = 'royalty/index_royalty';
        $this->load->view('template', compact('pages', 'main_view', 'royalty', 'pagination', 'total', 'total_royalty', 'dropdown_author'));
    }

    public function history($page = NULL)
    {
        $start_date = $this->input->get('start_date', true);
        $period_end = $this->input->get('end_date', true);
        //validasi
        if ($start_date > $period_end) {
            redirect('royalty/history');
        }

        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'start_date'        => $start_date,
            'period_end'        => $period_end
        ];

        $this->royalty->per_page = $this->input->get('per_page', true) ?? 10;
        $get_data = $this->royalty->fetch_all_royalty_history($filters, $page);
        $royalty_history = $get_data['royalty'];
        foreach ($royalty_history as $history) {
            $author_id = $history->author_id;
            $history_filter = [
                'last_paid_date'    => $history->start_date,
                'period_end'        => $history->end_date
            ];
            if ($this->royalty->author_details($author_id, $history_filter) != null) {
                $history->details = $this->royalty->author_details($author_id, $history_filter)[0];
            }
        }

        $total = $get_data['total'];
        $dropdown_author = $this->royalty->get_dropdown_author_history();

        $pagination = $this->royalty->make_pagination(site_url('royalty/history'), 3, $total);
        $pages      = $this->pages;
        $main_view  = 'royalty/history_royalty';
        $this->load->view('template', compact('pages', 'main_view', 'pagination', 'royalty_history', 'total', 'dropdown_author'));
    }

    public function view($author_id)
    {
        //validasi max date
        $today = date('Y-m-d', time());
        $period_end = $this->input->get('due-date');
        if (strtotime($period_end) >= strtotime($today)) {
            redirect($this->pages . '/view/' . $author_id);
        }

        $author = $this->db->select('author_id, author_name')->from('author')->where('author_id', $author_id)->get()->row();

        $latest_royalty = $this->royalty->fetch_latest_royalty($author_id);
        if ($latest_royalty != NULL) {
            $latest_filters = [
                'last_paid_date'    => $latest_royalty->start_date,
                'period_end'        => $latest_royalty->end_date
            ];
            $latest_royalty->details = $this->royalty->author_details($author_id, $latest_filters)[0];
        }

        if ($latest_royalty != NULL) {
            // Sudah pernah bayar
            $last_paid_date = $latest_royalty->end_date;
            $current_start_date = date('Y-m-d H:i:s', strtotime($latest_royalty->end_date) + 1);
        } else {
            // Baru pertama kali
            $last_paid_date = $this->input->get('start-date');
            $current_start_date = NULL;
        }


        $filters = [
            'period_end'     => $period_end,
            'last_paid_date' => $last_paid_date
        ];
        $royalty_details = $this->royalty->author_details($author_id, $filters);

        $royalty_history = $this->royalty->fetch_royalty_history($author_id);
        foreach ($royalty_history as $history) {
            $history_filter = [
                'last_paid_date'    => $history->start_date,
                'period_end'        => $history->end_date
            ];
            $history->details = $this->royalty->author_details($author_id, $history_filter)[0];
        }
        $pages          = $this->pages;
        $main_view      = 'royalty/view_royalty';
        $this->load->view('template', compact('pages', 'main_view', 'author', 'last_paid_date', 'current_start_date', 'latest_royalty', 'royalty_details', 'royalty_history', 'period_end'));
    }

    public function view_detail($royalty_id)
    {
        // $royalty = $this->db->select('*')->from('royalty')->where('royalty_id', $royalty_id)->get()->row();
        $royalty =  $this->royalty->where('royalty_id', $royalty_id)->get();
        $filters = [
            'last_paid_date'        => $royalty->start_date,
            'period_end'            => $royalty->end_date,
        ];
        $author = $this->db->select('author_id, author_name')->from('author')->where('author_id', $royalty->author_id)->get()->row();
        $royalty_details = $this->royalty->author_details($royalty->author_id, $filters);
        $pages          = $this->pages;
        $main_view      = 'royalty/view_royalty_detail';
        $this->load->view('template', compact('pages', 'main_view', 'author', 'royalty', 'royalty_details'));
    }

    public function generate_pdf($royalty_id, $type = '')
    {
        $royalty = $this->db->select('*')->from('royalty')->where('royalty_id', $royalty_id)->get()->row();
        $author = $this->db->select('author_id, author_name')->from('author')->where('author_id', $royalty->author_id)->get()->row();
        $filters = [
            'last_paid_date'    => $royalty->start_date,
            'period_end'        => $royalty->end_date,
        ];
        $royalty_details = $this->royalty->author_details($royalty->author_id, $filters);
        $book_details = $this->royalty->get_sold_books($royalty->author_id, $filters);
        foreach ($book_details as $book_detail) {
            // log pada hari mulai royalti
            $start_stock =  $this->db->select('*')
                ->from('book_stock_log')
                ->where('book_id', $book_detail->book_id)
                ->where('date BETWEEN "' . $royalty->start_date . '" AND ADDTIME("' . $royalty->start_date . '", "23:59:59")')
                ->order_by('date', 'DESC')
                ->get()->row();
            if ($start_stock != NULL) {
                $book_detail->warehouse_start = $start_stock->warehouse_stock;
                $book_detail->showroom_start = $start_stock->showroom_stock;
                $book_detail->library_start = $start_stock->library_stock;
            }
            // Backup jika pada hari mulai royalti data stock tidak ada
            // Ambil data stock 1 hari sebelumnya
            else {
                // stock 1 hari sebelum
                $start_stock =  $this->db->select('*')
                    ->from('book_stock_log')
                    ->where('book_id', $book_detail->book_id)
                    ->where('date BETWEEN SUBTIME("' . $royalty->start_date . '", "1 00:00:00") AND "' . $royalty->start_date . '"')
                    ->order_by('date', 'DESC')
                    ->get()->row();
                if ($start_stock != NULL) {
                    // buku non penjualan 1 hari sebelum
                    $non_sales_one_day_ago =  $this->db->select('sum(qty) as qty_non_sales')
                        ->from('book_non_sales_list')
                        ->join('book_non_sales', 'book_non_sales_list.book_non_sales_id = book_non_sales.book_non_sales_id', 'left')
                        ->where('book_id', $book_detail->book_id)
                        ->where('issued_date BETWEEN SUBTIME("' . $royalty->start_date . '", "1 00:00:00") AND "' . $royalty->start_date . '"')
                        ->get()->row();
                    // buku terjual 1 hari sebelum
                    $sold_one_day_ago = $this->db->select('SUM(qty) AS sold_books')
                        ->from('invoice_book')
                        ->join('invoice', 'invoice_book.invoice_id = invoice.invoice_id')
                        ->where('invoice.status', 'finish')
                        ->where('invoice_book.book_id', $book_detail->book_id)
                        ->where('issued_date BETWEEN SUBTIME("' . $royalty->start_date . '", "1 00:00:00") AND "' . $royalty->start_date . '"')
                        ->group_by('invoice_book.book_id')
                        ->get()->row();
                    // echo $this->db->last_query();
                    // var_dump($sold_one_day_ago);

                    // stock 1 hari sebelum - buku terjual 1 hari sebelum - non penjualan 1 hari sebelum
                    $backup_stock = $start_stock->warehouse_stock - ($non_sales_one_day_ago ? $non_sales_one_day_ago->qty_non_sales : 0) - ($sold_one_day_ago ? $sold_one_day_ago->sold_books : 0);
                    $book_detail->warehouse_start = $backup_stock;
                    $book_detail->showroom_start = $start_stock->showroom_stock;
                    $book_detail->library_start = $start_stock->library_stock;
                }
                // fallback jika 2 hari data log tidak tercatat
                else {
                    $book_detail->warehouse_start = 0;
                    $book_detail->showroom_start = 0;
                    $book_detail->library_start = 0;
                }
            }

            $book_detail->non_sales_last = $this->royalty->get_non_sales_book($book_detail->book_id, $filters, 'last')->qty_non_sales;
        }
        // PDF
        $this->load->library('pdf');

        $data = array(
            'author' => $author,
            'royalty_details' => $royalty_details,
            'start_date' => $royalty->start_date,
            'period_end' => $royalty->end_date,
            'book_details' => $book_details,
            'pdf_type' => $type
        );
        $html = $this->load->view('royalty/view_royalty_pdf', $data, true);
        if ($type == 'author') {
            $file_name = 'Royalti_Penulis_' . $data['author']->author_name;
        } else {
            $file_name = 'Royalti_' . $data['author']->author_name;
        }


        ob_end_clean();
        $this->pdf->generate_pdf_a4_landscape($html, $file_name);
    }

    public function pay()
    {
        $unfinished_invoice = NULL;
        $author_id = $this->input->post('author_id');
        $latest_royalty = $this->royalty->fetch_latest_royalty($author_id);
        //jika belum ada data royalti
        if ($latest_royalty == NULL) {
            $this->royalty->validate_royalty();
            $end_date = $this->input->post('end_date');
            $start_date = $this->input->post('start_date');
            $unfinished_invoice = $this->royalty->get_unfinished_invoice($author_id, $start_date, $end_date);
            // ada invoice yang tidak finish / cancel
            if ($unfinished_invoice != NULL) {
                $this->session->set_flashdata('error', $this->lang->line('toast_royalty_unfinished_invoice'));
            } else {
                //tambahkan data royalti author
                $data = [
                    'author_id' => $author_id,
                    'start_date' => $start_date . ' 00:00:00',
                    'end_date' =>  $end_date . ' 23:59:59',
                    'status' => 'requested'
                ];
                $this->db->insert('royalty', $data);
            }
        } else {
            //jika sudah ada dan sedang diajukan
            if ($latest_royalty->status == 'requested') {
                $data = [
                    'paid_date' => now(),
                    'status' => 'paid',
                    'receipt' => $this->input->post('receipt')
                ];
                $this->db->set($data)->where('author_id', $author_id)->where('status', 'requested')->update('royalty');
            }
            //jika sudah ada dan belum diajukan
            else if ($latest_royalty->status == 'paid') {

                $this->royalty->validate_royalty();
                $last_paid_date = strtotime($latest_royalty->end_date) + 1;
                $start_date = date('Y-m-d H:i:s', $last_paid_date);
                $end_date = $this->input->post('end_date');
                $unfinished_invoice = $this->royalty->get_unfinished_invoice($author_id, $start_date, $end_date);
                // ada invoice yang tidak finish / cancel
                if ($unfinished_invoice != NULL) {
                    $this->session->set_flashdata('error', $this->lang->line('toast_royalty_unfinished_invoice'));
                } else {
                    $data = [
                        'author_id' => $author_id,
                        'start_date' => $start_date,
                        'end_date' => $end_date . ' 23:59:59',
                        'status' => 'requested'
                    ];
                    $this->db->insert('royalty', $data);
                }
            }
        }
        if ($unfinished_invoice == NULL) {
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }
        echo json_encode(['status' => TRUE]);
        return;
    }

    public function cancel($royalty_id)
    {
        $royalty = $this->db->select('*')->from('royalty')->where('royalty_id', $royalty_id)->get()->row();
        if ($royalty->status == 'requested') {
            $this->session->set_flashdata('success', $this->lang->line('toast_royalty_cancel_success'));
            $this->db->where('royalty_id', $royalty_id);
            $this->db->delete('royalty');
        }
        redirect('royalty');
    }

    public function filter_author($royalty, $filters)
    {
        foreach ($royalty as $key => $each_royalty) {
            if ($each_royalty->status == 'paid') {
                $filters_next_royalty = [
                    'last_paid_date'    => $each_royalty->end_date,
                    'period_end'        => $filters['period_end']
                ];
                $next_royalty = $this->royalty->author_details($each_royalty->author_id, $filters_next_royalty);
                // Buku penulis tidak ada yg terjual selama periode ini
                if ($next_royalty == NULL) {
                    unset($royalty[$key]);
                }
            }
        }
        return $royalty;
    }
}
