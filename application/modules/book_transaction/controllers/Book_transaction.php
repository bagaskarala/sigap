<?php defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Book_transaction extends MY_Controller
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
        if($this->_is_warehouse_admin() == TRUE):
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


    public function view($book_transaction_id){
        // $book_transaction = $this->book_transaction->join('book')->where('book.book_id', $book_id)->get();
        $book_transaction = $this->book_transaction->get_book_transaction($book_transaction_id);
        if (!$book_transaction) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        $input = (object) $book_transaction;
        // $get_transaction      = $this->book_transaction->fetch_transaction_by_id($book_transaction_id);
        // $transaction_history  = $get_transaction['transaction_history'];
        // $transaction_last     = $get_transaction['transaction_last'];

        $pages       = $this->pages;
        $main_view   = 'book_transaction/view_booktransaction';
        $this->load->view('template', compact('pages', 'main_view', 'input'));
        return;
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
        $sheet->setCellValue('C3', 'Perubahan');
        $sheet->setCellValue('D3', 'Jenis Transaksi');
        $sheet->setCellValue('E3', 'Tanggal Transaksi');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A3:E3')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('A6A6A6');
        $spreadsheet->getActiveSheet()
                    ->getStyle('A3:E3')
                    ->getFont()
                    ->setBold(true);

        // Auto width
        // $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);

        // $get_data = $this->book_transaction->filter_excel($filters);
        $get_data = $this->book_transaction->filter_excel($filters);
        $no = 1;
        $i = 4;
        // Column Content
        // Menampilkan data (Nomor, judul buku, 
        // stok awal, perubahan (total), jenis transaksi ($book_receive_id==null: keluar. Sebaliknya gt), 
        // tanggal transaksi (finish_date faktur/book_receive))

        foreach ($get_data as $data) {
            foreach (range('A', 'E') as $v) {
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
                            if($data->book_receive_id){
                                $value = $data->stock_in;
                            }
                            else{
                                $value = $data->stock_out;
                            }
                            break;
                    }
                    case 'D': {
                            if($data->book_receive_id){
                                $value = 'Masuk';
                            }
                            else{
                                $value = 'Keluar';
                            }
                            break;
                    }
                    case 'E': {
                            $value = $data->date;
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
