<?php defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Book_asset extends Warehouse_Controller
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
        $book_assets_price = $get_data['book_assets_price'];
        $total = $get_data['total'];

        $count = array (
            'warehouse' => 0,
            'showroom' => 0,
            'library' => 0
        );
        foreach ($book_assets_price as $book_asset){
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

    public function view($book_id){
        $book_asset = $this->book_stock->get_book_stock_by_book_id($book_id);
        if (!$book_asset) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        $book_asset->library_stock = $this->book_stock->get_library_stock($book_asset->book_stock_id);
        $pages                      = $this->pages;
        $main_view                  = 'book_asset/view_bookasset';
        $this->load->view('template', compact('pages', 'main_view', 'book_asset'));
        return;
    }

    public function generate_excel($filters)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $filename = 'ASET BUKU UGM PRESS';
        $hpp_percent = $this->input->get('hpp_percent');
        $hpp = $hpp_percent/100;

        // Column Title
        $sheet->setCellValue('A1', 'ASET BUKU UGM PRESS');
        $sheet->getStyle('A1')
              ->getFont()
              ->setBold(true);
        $sheet->setCellValue('A2', 'HPP : '.$hpp_percent.'%');
        $sheet->getStyle('D')
              ->getNumberFormat()
              ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
        $sheet->getStyle('H:O')
              ->getNumberFormat()
              ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_ACCOUNTING_USD);
        $sheet->setCellValue('A3', 'No');
        $sheet->setCellValue('B3', 'Judul');
        $sheet->setCellValue('C3', 'Penulis');
        $sheet->setCellValue('D3', 'Harga');
        $sheet->setCellValue('E3', 'Stok Gudang');
        $sheet->setCellValue('F3', 'Stok Showroom');
        $sheet->setCellValue('G3', 'Stok Perpustakaan');
        $sheet->setCellValue('H3', 'Aset Gudang');
        $sheet->setCellValue('I3', 'Aset Showroom');
        $sheet->setCellValue('J3', 'Aset Perpustakaan');
        $sheet->setCellValue('K3', 'Total Aset');
        $sheet->setCellValue('L3', 'HPP Aset Gudang');
        $sheet->setCellValue('M3', 'HPP Aset Showroom');
        $sheet->setCellValue('N3', 'HPP Aset Perpustakaan');
        $sheet->setCellValue('O3', 'Total HPP Aset');
        $sheet->getStyle('A3:O3')
              ->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()
              ->setARGB('A6A6A6');
        $sheet->getStyle('A3:O3')
              ->getFont()
              ->setBold(true);

        // Auto width
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);

        $get_data = $this->book_stock->filter_excel_asset($filters);
        $no = 1;
        $i = 4;
        // Column Content
        foreach ($get_data as $data) {
            foreach (range('A', 'O') as $v) {
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
                        $value = $data->harga;
                        break;
                    }
                    case 'E': {
                        $value = $data->warehouse_present;
                        break;
                    }
                    case 'F': {
                        $value = $data->showroom_present;
                        break;
                    }
                    case 'G': {
                        $value = $data->library_present;
                        break;
                    }
                    case 'H': {
                        $value = $data->harga*$data->warehouse_present;
                        break;
                    }
                    case 'I': {
                        $value = $data->harga*$data->library_present;
                        break;
                    }
                    case 'J': {
                        $value = $data->harga*$data->library_present;
                        break;
                    }
                    case 'K': {
                        $value = ($data->harga*$data->warehouse_present) + 
                                 ($data->harga*$data->library_present) +
                                 ($data->harga*$data->library_present);
                        break;
                    }
                    case 'L': {
                        $value = $data->harga*$data->warehouse_present*$hpp;
                        break;
                    }
                    case 'M': {
                        $value = $data->harga*$data->library_present*$hpp;
                        break;
                    }
                    case 'N': {
                        $value = $data->harga*$data->library_present*$hpp;
                        break;
                    }
                    case 'O': {
                        $value = ($data->harga*$data->warehouse_present*$hpp) + 
                                 ($data->harga*$data->library_present*$hpp) +
                                 ($data->harga*$data->library_present*$hpp);
                        break;
                    }
                }
                $sheet->setCellValue($v . $i, $value);
            }
            $i++;
        }
        // total
        $sheet->setCellValue('G'. $i, 'TOTAL');
        $sheet->getStyle('G'.$i)
              ->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()
              ->setARGB('A6A6A6');
        $sheet->getStyle('G'.$i)
              ->getFont()
              ->setBold(true);
        $sum_warehouse_asset = 'H4:H'.$i;
        $sheet->setCellValue('H'.$i, '=SUM('.$sum_warehouse_asset.')');
        
        $sum_showroom_asset = 'I4:I'.$i;
        $sheet->setCellValue('I'.$i, '=SUM('.$sum_showroom_asset.')');
        
        $sum_library_asset = 'J4:J'.$i;
        $sheet->setCellValue('J'.$i, '=SUM('.$sum_library_asset.')');
        
        $sum_total_asset = 'K4:K'.$i;
        $sheet->setCellValue('K'.$i, '=SUM('.$sum_total_asset.')');
        
        $sum_warehouse_hpp_asset = 'L4:L'.$i;
        $sheet->setCellValue('L'.$i, '=SUM('.$sum_warehouse_hpp_asset.')');
        
        $sum_showroom_hpp_asset = 'M4:M'.$i;
        $sheet->setCellValue('M'.$i, '=SUM('.$sum_showroom_hpp_asset.')');
        
        $sum_library_hpp_asset = 'N4:N'.$i;
        $sheet->setCellValue('N'.$i, '=SUM('.$sum_library_hpp_asset.')');
        
        $sum_total_hpp_asset = 'O4:O'.$i;
        $sheet->setCellValue('O'.$i, '=SUM('.$sum_total_hpp_asset.')');

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
