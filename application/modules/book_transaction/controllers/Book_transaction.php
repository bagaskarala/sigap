<?php defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Book_transaction extends Warehouse_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = "book_transaction";
        $this->load->model('book_transaction/book_transaction_model', 'book_transaction');
        $this->load->model('book/book_model', 'book');
    }

    public function index($page = NULL){
        //all filter
        if($this->_is_book_transaction_user() == TRUE):
            $filters = [
                'keyword'           => $this->input->get('keyword', true),
                'published_year'    => $this->input->get('published_year', true),
                'start_date'        => $this->input->get('start_date', true),
                'end_date'          => $this->input->get('end_date', true),
                'transaction_type'  => $this->input->get('transaction_type', true),
                'excel'             => $this->input->get('excel', true)
            ];
            //custom per page
            $this->book_transaction->per_page = $this->input->get('per_page', true) ?? 10;
            $get_data = $this->book_transaction->filter_book_transaction($filters, $page);
        
            $book_transactions= $get_data['book_transactions'];
            $total = $get_data['total'];
            $pagination = $this->book_transaction->make_pagination(site_url('book_transaction'), 2, $total);
            $pages      = $this->pages;
            $main_view  = 'book_transaction/index_booktransaction';
            $this->load->view('template', compact('pages', 'main_view', 'book_transactions', 'pagination', 'total'));
        
            if ($filters['excel'] == 1) {
                $this->generate_excel($filters);
            }
        endif;
    }

    public function api_all_chart_data($year)
    {
        $book_transaction = $this->book_transaction->get_all_transaction_data($year);
        for ($i = 1; $i <= 12; $i++) {
            $chart_data['stock_in']['month_' . $i] = 0;
            $chart_data['stock_out']['month_' . $i] = 0;
        }
        foreach ($book_transaction as $data) {
            for ($i = 1; $i <= 12; $i++) {
                if (substr($data->date, 5, 2) == $i) {
                    if ($data->stock_initial < $data->stock_last) {
                        $chart_data['stock_in']['month_' . $i] += $data->stock_mutation;
                    }
                    if ($data->stock_initial > $data->stock_last) {
                        $chart_data['stock_out']['month_' . $i] += $data->stock_mutation;
                    }
                }
            }
        }
        return $this->send_json_output(true, (object) $chart_data);
    }

    public function generate_excel($filters)
    {
        // $get_data = $this->book_transaction->filter_excel($filters);
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $filename = 'Transaksi Buku';

        // Column Title
        $sheet->setCellValue('A1', 'Transaksi Buku');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A1')
                    ->getFont()
                    ->setBold(true);
        $sheet->setCellValue('A3', 'No');
        $sheet->setCellValue('B3', 'Judul Buku');
        $sheet->setCellValue('C3', 'Stok Awal');
        $sheet->setCellValue('D3', 'Perubahan');
        $sheet->setCellValue('E3', 'Stok Akhir');
        $sheet->setCellValue('F3', 'Jenis Transaksi');
        $sheet->setCellValue('G3', 'Tanggal Transaksi');
        $sheet->setCellValue('H3', 'Keterangan');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A3:F3')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('A6A6A6');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A3:H3')
                    ->getFont()
                    ->setBold(true);

        // Auto width
        // $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);

        // $get_data = $this->book_transaction->filter_excel($filters);
        $get_data = $this->book_transaction->filter_excel($filters);
        $no = 1;
        $i = 4;
        // Column Content
        foreach ($get_data as $data) {
            foreach (range('A', 'H') as $v) {
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
                            $value = $data->stock_initial;
                            break;
                    }
                    case 'D': {
                            $value = $data->stock_mutation;
                            break;
                    }
                    case 'E': {
                            $value = $data->stock_last;
                            break;
                    }
                    case 'F': {
                            if($data->book_receive_id){
                                $value = 'Masuk';
                            }
                            else if($data->book_stock_revision_id){
                                if($data->revision_type=='add'){
                                    $value = 'Masuk';
                                }
                                else{
                                    $value = 'Keluar';
                                }
                            }
                            else{
                                $value = 'Keluar';
                            }
                            break;
                    }
                    case 'G': {
                            $value = $data->date;
                        break;
                    }
                    case 'H':{
                        if($data->book_receive_id){
                            $value = "Penerimaan Buku";
                        }
                        else if($data->invoice_id){
                            $value = "Pesanan Buku";
                        }
                        else if($data->book_transfer_id){
                            $value = "Pemindahan Buku";
                        }
                        else if($data->book_non_sales_id){
                            $value = "Buku Non Penjualan";
                        }
                        else if($data->book_stock_revision_id){
                            if($data->type=="revision"){
                                $value = "Revisi";
                            }
                            else{
                                $value = "Retur";
                            }
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

    private function _is_book_transaction_user()
    {
        if ($this->level == 'superadmin' || $this->level == 'admin_gudang' || $this->level == 'staff_gudang') {
            return true;
        } else {
            $this->session->set_flashdata('error', 'Hanya admin gudang, superadmin, dan staff gudang yang dapat mengakses.');
            return false;
        }
    }

    public function chart(){
        $pages                      = 'book_transaction/chart';
        $main_view                  = 'book_transaction/chart';
        $this->load->view('template', compact('pages', 'main_view'));
        return;
    }
}
