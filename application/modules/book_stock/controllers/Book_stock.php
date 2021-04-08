<?php defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Book_stock extends MY_Controller
{
    public $per_page = 10;

    public function __construct()
    {
        parent::__construct();
        $this->pages = "book_stock";
        $this->load->model('book_stock/book_stock_model', 'book_stock');
        $this->load->model('book/book_model', 'book');
    }

    public function index($page = NULL){
        //all filter
        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'published_year'    => $this->input->get('published_year', true),
            'warehouse_present' => $this->input->get('warehouse_present', true),
            'excel'             => $this->input->get('excel', true)
        ];
        //custom per page
        $this->book_stock->per_page = $this->input->get('per_page', true) ?? 10;
        $get_data   = $this->book_stock->filter_book_stock($filters, $page);
        $book_stocks= $get_data['book_stocks'];
        $total      = $get_data['total'];
        $pagination = $this->book_stock->make_pagination(site_url('book_stock'), 2, $total);
        $pages      = $this->pages;
        $main_view  = 'book_stock/index_bookstock';
        $this->load->view('template', compact('pages', 'main_view', 'book_stocks', 'pagination', 'total'));

        if ($filters['excel'] == 1) {
            $this->generate_excel($filters);
        }
    }

    public function view($book_stock_id){
        $book_stock = $this->book_stock->get_book_stock($book_stock_id);
        if (!$book_stock) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        $input = (object) $book_stock;
        $book_stock->revision      = $this->book_stock->get_stock_revision($book_stock->book_id);
        $pages                      = $this->pages;
        $main_view                  = 'book_stock/view_bookstock';
        $this->load->view('template', compact('pages', 'main_view', 'input', 'book_stock'));
        return;
    }

    public function edit($book_stock_id){
        if(!$this->_is_warehouse_admin()){
            redirect($this->pages);
        }

        $book_stock = $this->book_stock->get_book_stock($book_stock_id);
        if(!$book_stock){
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        if(!$_POST){
            $input = (object) $book_stock;
        }
        else{
            $input = (object) $this->input->post(null, true);
            // catat orang yang menginput stok buku
            $input->user_id = $_SESSION['user_id'];
            
        }

        $pages = $this->pages;
        $main_view = 'book_stock/edit_bookstock';
        $form_action = "book_stock/edit/$book_stock_id";
        $this->load->view('template', compact('pages','main_view', 'input'));   
    }

    public function edit_book_stock(){
        if($this->_is_warehouse_admin() == TRUE && $this->input->method()=='post'){
            $operator = $this->input->post('warehouse_operator');
            $book_id = $this->input->post('book_id');
            $quantity = $this->input->post('warehouse_modifier');
            $notes = $this->input->post('notes');
            $book_stock = $this->book_stock->where('book_id', $book_id)->get();
            $book_stock_revision = (object) [
                'book_id'            => $book_id,
                'warehouse_past'     => $book_stock->warehouse_present,
                'warehouse_present'  => 0,
                'warehouse_revision' => $quantity,
                'operator'           => $operator,
                'notes'              => $notes,
                'revision_date'      => now()
            ];
            if (!$book_stock) {
                $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            }
            else {
                if ($operator=="+") $book_stock->warehouse_present += $quantity;
                else $book_stock->warehouse_present -= $quantity;
                $book_stock_revision->warehouse_present = $book_stock->warehouse_present;
                if ($this->book_stock->where('book_id', $book_id)->update($book_stock) && $this->db->insert('book_stock_revision',$book_stock_revision)) {
                    $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
                } else {
                    $this->session->set_flashdata('success', $this->lang->line('toast_edit_fail'));
                }
            }
        }
        else {
            $this->session->set_flashdata('warning', $this->lang->line('toast_edit_fail'));
        }
        redirect('book_stock/view/'.$book_stock->book_stock_id);
    }
    
    public function delete($book_stock_id = null)
    {
        if (!$this->_is_warehouse_admin()) {
            redirect($this->pages);
        }

        $book_stock = $this->book_stock->where('book_stock_id', $book_stock_id)->get();
        if (!$book_stock) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        // memastikan konsistensi data
        $this->db->trans_begin();

        $this->book_stock->where('book_stock_id', $book_stock_id)->delete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_delete_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_delete_success'));
        }

        redirect($this->pages);
    }

    // generate excel stok buku di index_bookstock
    public function generate_excel($filters)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $filename = 'STOK BUKU GUDANG';

        // Column Title
        $sheet->setCellValue('A1', 'STOK BUKU GUDANG');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A1')
                    ->getFont()
                    ->setBold(true);
        $sheet->setCellValue('A3', 'No');
        $sheet->setCellValue('B3', 'Judul');
        $sheet->setCellValue('C3', 'Stok Gudang');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A3:C3')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('A6A6A6');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A3:C3')
                    ->getFont()
                    ->setBold(true);

        // Auto width
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        $get_data = $this->book_stock->filter_excel($filters);
        $no = 1;
        $i = 4;
        // Column Content
        foreach ($get_data as $data) {
            foreach (range('A', 'C') as $v) {
                switch ($v) {
                    case 'A': {
                            $value = $no++;
                            break;
                        }
                    case 'B': {
                            $value = $data->book_title;
                            break;
                        }
                    case 'C': {
                            $value = $data->warehouse_present;
                            if($value <=50){
                                $spreadsheet->getActiveSheet()
                                ->getStyle('C'.$i)
                                ->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setARGB('FFC000');            
                            }
                            break;
                        }
                }
                $sheet->setCellValue($v . $i, $value);
            }
            $i++;
        }
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        die();
    }

    private function _is_warehouse_admin()
    {
        if ($this->level == 'superadmin' || $this->level == 'admin_gudang') {
            return true;
        } else {
            $this->session->set_flashdata('error', 'Hanya admin gudang dan superadmin yang dapat mengakses.');
            return false;
        }
    }
}
