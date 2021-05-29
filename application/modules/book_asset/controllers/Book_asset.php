<?php defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Book_asset extends Warehouse_Sales_Controller
{
    public $per_page = 10;

    public function __construct()
    {
        parent::__construct();
        $this->pages = "book_asset";
        $this->load->model('book_stock/book_stock_model', 'book_stock');
        $this->load->model('book/book_model', 'book');
    }

    public function index($page = NULL)
    {
        //all filter
        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'excel'             => $this->input->get('excel', true)
        ];
        //custom per page
        $this->book_stock->per_page = $this->input->get('per_page', true) ?? 10;
        $get_data = $this->book_stock->filter_book_asset($filters, $page);

        $book_assets = $get_data['book_assets'];
        $total = $get_data['total'];

        $count = array (
            'warehouse' => 0,
            'showroom' => 0,
            'library' => 0
        );
        foreach ($book_assets as $book_asset){
            $count['warehouse'] += $book_asset->harga*$book_asset->warehouse_present;
            $count['showroom'] += $book_asset->harga*$book_asset->showroom_present;
            $count['library'] += $book_asset->harga*$book_asset->library_present;
        }
        $count['all'] = $count['warehouse']+$count['showroom']+$count['library'];
        
        $pagination = $this->book_stock->make_pagination(site_url('book_asset'), 2, $total);
        $pages      = $this->pages;
        $main_view  = 'book_asset/index_bookasset';
        $this->load->view('template', compact('pages', 'main_view', 'book_assets', 'pagination', 'total', 'count'));

        if ($filters['excel'] == 1) {
            $this->generate_excel($filters);
        }
    }

    public function api_get_by_book_id($book_id)
    {
        $book_asset = $this->book_stock->get_book_asset_by_book_id($book_id);
        return $this->send_json_output(true, $book_asset);
    }

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
        $sheet->setCellValue('C3', 'Penulis');
        $sheet->setCellValue('D3', 'Stok Gudang');
        $spreadsheet->getActiveSheet()
            ->getStyle('A3:D3')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('A6A6A6');
        $spreadsheet->getActiveSheet()
            ->getStyle('A3:D3')
            ->getFont()
            ->setBold(true);

        // Auto width
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);

        $get_data = $this->book_stock->filter_excel_asset($filters);
        $no = 1;
        $i = 4;
        // Column Content
        foreach ($get_data as $data) {
            foreach (range('A', 'D') as $v) {
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
                            $value = $data->author_name;
                            break;
                        }
                    case 'D': {
                            $value = $data->warehouse_present;
                            if ($value <= 50) {
                                $spreadsheet->getActiveSheet()
                                ->getStyle('D' . $i)
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

    public function generate_retur()
    {
        $spreadsheet = new Spreadsheet;
        $sheet_1 = $spreadsheet->getActiveSheet()->setTitle('stok retur');
        $filename = 'STOK RETUR_' . date('Y m d');

        // Column Title
        $sheet_1->setCellValue('A1', 'STOK RETUR');
        $spreadsheet->getActiveSheet()
            ->getStyle('A1')
            ->getFont()
            ->setBold(true);
        $sheet_1->setCellValue('A3', 'No');
        $sheet_1->setCellValue('B3', 'Judul');
        $sheet_1->setCellValue('C3', 'Stok Retur');
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
        $sheet_1->getColumnDimension('B')->setAutoSize(true);
        $sheet_1->getColumnDimension('C')->setAutoSize(true);

        $get_data = $this->book_stock->retur_asset();
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
                            $value = $data->retur_asset;
                            break;
                        }
                }
                $sheet_1->setCellValue($v . $i, $value);
            }
            $i++;
        }

        // Create new sheet
        $spreadsheet->createSheet();
        // Zero based, so set the second tab as active sheet
        $spreadsheet->setActiveSheetIndex(1);
        $sheet_2 = $spreadsheet->getActiveSheet()->setTitle('log tambah retur');
        // Column Title
        $sheet_2->setCellValue('A1', 'LOG PENAMBAHAN RETUR');
        $spreadsheet->getActiveSheet()
            ->getStyle('A1')
            ->getFont()
            ->setBold(true);
        $sheet_2->setCellValue('A3', 'No');
        $sheet_2->setCellValue('B3', 'Judul Buku');
        $sheet_2->setCellValue('C3', 'Jumlah Retur');
        $sheet_2->setCellValue('D3', 'Tanggal');
        $spreadsheet->getActiveSheet()
            ->getStyle('A3:D3')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('A6A6A6');
        $spreadsheet->getActiveSheet()
            ->getStyle('A3:D3')
            ->getFont()
            ->setBold(true);

        // Auto width
        $sheet_2->getColumnDimension('B')->setAutoSize(true);
        $sheet_2->getColumnDimension('C')->setAutoSize(true);
        $sheet_2->getColumnDimension('D')->setAutoSize(true);

        $get_data = $this->book_stock->log_add_retur();
        $no = 1;
        $i = 4;
        // Column Content
        foreach ($get_data as $data) {
            foreach (range('A', 'D') as $v) {
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
                            $value = $data->warehouse_revision;
                            break;
                        }
                    case 'D': {
                            $value = date("d/m/Y", strtotime($data->revision_date));
                            break;
                        }
                }
                $sheet_2->setCellValue($v . $i, $value);
            }
            $i++;
        }

        // Create new sheet
        $spreadsheet->createSheet();
        // Zero based, so set the second tab as active sheet
        $spreadsheet->setActiveSheetIndex(2);
        $sheet_3 = $spreadsheet->getActiveSheet()->setTitle('log hapus retur');
        // Column Title
        $sheet_3->setCellValue('A1', 'LOG PENGHAPUSAN RETUR');
        $spreadsheet->getActiveSheet()
            ->getStyle('A1')
            ->getFont()
            ->setBold(true);
        $sheet_3->setCellValue('A3', 'No');
        $sheet_3->setCellValue('B3', 'Judul Buku');
        $sheet_3->setCellValue('C3', 'Jumlah Hapus');
        $sheet_3->setCellValue('D3', 'Tanggal Hapus Retur');
        $spreadsheet->getActiveSheet()
            ->getStyle('A3:D3')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('A6A6A6');
        $spreadsheet->getActiveSheet()
            ->getStyle('A3:D3')
            ->getFont()
            ->setBold(true);

        // Auto width
        $sheet_3->getColumnDimension('B')->setAutoSize(true);
        $sheet_3->getColumnDimension('C')->setAutoSize(true);
        $sheet_3->getColumnDimension('D')->setAutoSize(true);

        $get_data = $this->book_stock->log_delete_retur();
        $no = 1;
        $i = 4;
        // Column Content
        foreach ($get_data as $data) {
            foreach (range('A', 'D') as $v) {
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
                            $value = $data->warehouse_revision;
                            break;
                        }
                    case 'D': {
                            $value = date("d/m/Y", strtotime($data->revision_date));
                            break;
                        }
                }
                $sheet_3->setCellValue($v . $i, $value);
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
