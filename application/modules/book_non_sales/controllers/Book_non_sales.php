<?php defined('BASEPATH') or exit('No direct script access allowed');

class Book_non_sales extends Warehouse_sales_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'book_non_sales';
        $this->load->model('book_non_sales_model', 'book_non_sales');
        $this->load->model('book_transaction/book_transaction_model', 'book_transaction');
        $this->load->model('book_stock/book_stock_model', 'book_stock');
    }

    public function index($page = NULL)
    {
        if ($this->_is_warehouse_sales_admin() == TRUE) :
            // all filter
            $filters = [
                'keyword'           => $this->input->get('keyword', true),
                'type'              => $this->input->get('type', true),
                'status'            => $this->input->get('status', true),
            ];

            // custom per page
            $this->book_non_sales->per_page = $this->input->get('per_page', true) ?? 10;

            $get_data = $this->book_non_sales->filter_book_non_sales($filters, $page);

            $book_non_sales = $get_data['book_non_sales'];
            $total          = $get_data['total'];
            $pagination     = $this->book_non_sales->make_pagination(site_url('book_non_sales'), 2, $total);
            $pages          = $this->pages;
            $main_view      = 'book_non_sales/index_book_non_sales';
            $this->load->view('template', compact('pages', 'main_view', 'book_non_sales', 'pagination', 'total'));
        endif;
    }

    public function add(){
        if (!$this->_is_sales_admin()) {
            redirect($this->pages);
        }

        if (!$_POST) {
            $input = (object) $this->book_non_sales->get_default_values();
            if (!$this->book_non_sales->validate() || $this->form_validation->error_array()) {
                $pages       = $this->pages;
                $main_view   = 'book_non_sales/add_book_non_sales';
                $form_action = 'book_non_sales/add';
                $book_non_sales_available = $this->book_non_sales->get_ready_book_list();
                $this->load->view('template', compact('pages', 'main_view', 'book_non_sales_available', 'form_action', 'input'));
                return;
            }
        } else {
            $input = (object) $this->input->post(null, true);
        }

        // generate number
        $year_now = date('Y');
        // apakah ada permintaan non jual tahun ini
        $non_sales_now = $this->book_non_sales->get_book_non_sales_year($year_now);
        if($non_sales_now){
            // ambil nomor terbaru
            $latest = $this->book_non_sales->get_latest_number();
            $latest_number = (int) substr(++$latest->number, 5);
            $latest_number = str_pad((string) $latest_number, 5, '0', STR_PAD_LEFT);
            $input->number = $year_now.'-'.$latest_number;
        }
        // permintaan pertama di tahun ini
        else{
            $input->number = $year_now.'-00001';
        }
        $book_non_sales = (object) [
            'issued_date' => now(),
            'number' => $input->number,
            'status' => 'waiting',
            'type' => $input->type,
            'name' => $input->name,
            'address' => $input->address,
            'notes' => $input->notes
        ];
        // insert book non sales
        $book_non_sales_success = $this->book_non_sales->insert($book_non_sales);
        $book_non_sales_id = $this->db->insert_id();
        // insert book non sales list
        foreach ($input->book_list as $books){
            $book_non_sales_list = (object)[
                'book_non_sales_id' => $book_non_sales_id,
                'book_id' => $books['book_id'],
                'qty' => $books['qty']
            ];
            $book_non_sales_list_success = $this->db->insert('book_non_sales_list',$book_non_sales_list);
        }

        if ($book_non_sales_success && $book_non_sales_list_success) {
            $this->session->set_flashdata('success', $this->lang->line('toast_add_success'));
        } else {
            $this->session->set_flashdata('error', $this->lang->line('toast_add_fail'));
        }

        redirect('book_non_sales');
    }

    public function delete($book_non_sales_id = null)
    {
        if (!$this->_is_sales_admin()) {
            redirect($this->pages);
        }

        $book_non_sales = $this->book_non_sales->where('book_non_sales_id', $book_non_sales_id)->get();
        if (!$book_non_sales) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        $this->book_non_sales->where('book_non_sales_id', $book_non_sales_id)->delete();

        // hapus list buku
        $book_non_sales_books  = $this->book_non_sales->fetch_book_non_sales_list($book_non_sales_id);
        foreach($book_non_sales_books as $book){
            $this->db->where('book_id', $book->book_id)->delete('book_non_sales_list');
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_delete_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_delete_success'));
        }
        redirect($this->pages);
    }

    public function view($book_non_sales_id)
    {
        if(!$this->_is_warehouse_sales_admin()){
            redirect($this->pages);
        }
        $book_non_sales = $this->book_non_sales->fetch_book_non_sales($book_non_sales_id);
        if (!$book_non_sales) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }
        $book_non_sales_list = $this->book_non_sales->fetch_book_non_sales_list($book_non_sales_id);
        $pages                      = $this->pages;
        $main_view                  = 'book_non_sales/view_book_non_sales';
        $this->load->view('template', compact('pages', 'main_view', 'book_non_sales','book_non_sales_list'));
        return;
    }

    public function generate_pdf_bon($book_non_sales_id)
    {
        if(!$this->_is_warehouse_sales_admin()){
            redirect($_SERVER['HTTP_REFERER']);
        }
        else{
            $book_non_sales        = $this->book_non_sales->fetch_book_non_sales($book_non_sales_id);
            $book_non_sales_list   = $this->book_non_sales->fetch_book_non_sales_list($book_non_sales_id);
    
            // PDF
            $this->load->library('pdf');
            // FORMAT DATA
            $data_format['number']        = $book_non_sales->number ?? '';
            $data_format['date']          = $book_non_sales->issued_date ?? '';
            $data_format['type']          = $book_non_sales->type ?? '';
            $data_format['name']          = $book_non_sales->name ?? '';
            $data_format['address']       = $book_non_sales->address ?? '';
            $data_format['notes']         = $book_non_sales->notes ?? '';
            $data_format['book_list']     = $book_non_sales_list ?? '';
            $html = $this->load->view('book_non_sales/format_pdf_non_sales', $data_format, true);        $file_name = $data_format['number'].'_Pemindahan Buku';
            $file_name = $data_format['number'].'_Pemindahan Buku';
            $this->pdf->generate_pdf_a4_landscape($html, $file_name);
        }
    }

    public function finish($book_non_sales_id)
    {
        if (!$book_non_sales_id) {
            $this->session->set_flashdata('error', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        if (!$this->_is_warehouse_admin()) {
            redirect($_SERVER['HTTP_REFERER']);
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        // apakah book_non_sales tersedia
        $book_non_sales = $this->book_non_sales->where('book_non_sales_id', $book_non_sales_id)->get();
        if (!$book_non_sales) {
            $this->session->set_flashdata('error', $this->lang->line('toast_data_not_available'));
        }

        // update stok
        $book_non_sales_lists  = $this->book_non_sales->fetch_book_non_sales_list($book_non_sales_id);
        foreach($book_non_sales_lists as $book_non_sales_list){
            $book_stock = $this->book_stock->where('book_id', $book_non_sales_list->book_id)->get();
            $book_stock->warehouse_present -= $book_non_sales_list->qty;
            $this->book_stock->where('book_id', $book_non_sales_list->book_id)->update($book_stock);
            $this->book_transaction->insert([
                'book_id' => $book_non_sales_list->book_id,
                'book_stock_id' => $book_stock->book_stock_id,
                'book_non_sales_id' => $book_non_sales_id,
                'stock_initial'=> $book_stock->warehouse_present+$book_non_sales_list->qty,
                'stock_mutation' => $book_non_sales_list->qty,
                'stock_last'=> $book_stock->warehouse_present,
                'date' => now()
            ]);
        }
        
        // update data book_non_sales
        $this->db->set('status','finish')->where('book_non_sales_id', $book_non_sales_id)->update('book_non_sales');

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }

        redirect($this->pages . "/view/$book_non_sales_id");

    }

    private function _is_warehouse_sales_admin()
    {
        if ($this->level == 'superadmin' || $this->level == 'admin_gudang' || $this->level == 'admin_pemasaran') {
            return true;
        } else {
            $this->session->set_flashdata('error', 'Hanya admin gudang, admin pemasaran, dan superadmin yang dapat mengakses.');
            return false;
        }
    }

    private function _is_warehouse_admin(){
        if ($this->level == 'superadmin' || $this->level == 'admin_gudang') {
            return true;
        } else {
            $this->session->set_flashdata('error', 'Hanya admin gudang dan superadmin yang dapat mengakses.');
            return false;
        }
    }

    private function _is_sales_admin(){
        if ($this->level == 'superadmin' || $this->level == 'admin_pemasaran') {
            return true;
        } else {
            $this->session->set_flashdata('error', 'Hanya admin pemasaran dan superadmin yang dapat mengakses.');
            return false;
        }
    }

}
