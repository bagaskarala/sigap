<?php defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Earning extends Sales_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'earning';
        $this->load->model('earning_model', 'earning');
        $this->load->helper('sales_helper');
    }

    public function index()
    {
        $filters = [
            'date_year'     => $this->input->get('date_year', true),
            'invoice_type'  => $this->input->get('invoice_type', true),
            'excel'         => $this->input->get('excel', true)
        ];
        if ($filters['date_year'] == NULL && $filters['invoice_type'] == NULL) {
            $filters['date_year'] = date("Y");
        }

        if ($filters['invoice_type'] == NULL) {
            $filter_invoice_type = false;
            $invoice_type = ['cash', 'showroom', 'credit', 'online'];
            for ($i = 0; $i < 4; $i++) {
                $filters['invoice_type'] = $invoice_type[$i];
                for ($month = 1; $month <= 12; $month++) {
                    $filters['date_month'] = $month;
                    $monthly[$filters['invoice_type']][$month] = $this->earning->filter_total($filters);

                    $total_earning[$filters['invoice_type']][$month] = 0;
                    foreach ($monthly[$filters['invoice_type']][$month] as $value) {
                        $total_earning[$filters['invoice_type']][$month] += $value->earning;
                    }
                }
            }
        } else {
            $filter_invoice_type = $filters['invoice_type'];
            for ($month = 1; $month <= 12; $month++) {
                $filters['date_month'] = $month;
                $monthly[$filter_invoice_type][$month] = $this->earning->filter_total($filters);

                $total_earning[$filter_invoice_type][$month] = 0;
                foreach ($monthly[$filter_invoice_type][$month] as $value) {
                    $total_earning[$filter_invoice_type][$month] += $value->earning;
                }
            }
        }
        $year = $filters['date_year'];
        $pages      = $this->pages;
        $main_view  = 'earning/index_earning';
        $this->load->view('template', compact('main_view', 'pages', 'filter_invoice_type', 'total_earning', 'year'));

        //generate excel
        if ($filters['excel'] == 1) {
            $this->generate_excel($filters, 'index');
        }
    }
    public function detail()
    {
        $filters = [
            'date_year'     => $this->input->get('date_year', true),
            'date_month'    => $this->input->get('date_month', true),
            'invoice_type'  => $this->input->get('invoice_type', true),
            'excel'         => $this->input->get('excel', true)
        ];
        if ($filters['date_year'] == NULL && $filters['invoice_type'] == NULL) {
            $filters['date_year'] = '2021';
        }

        $invoice_type = ['cash', 'showroom', 'credit', 'online'];
        for ($i = 0; $i < 4; $i++) {
            $filters['invoice_type'] = $invoice_type[$i];
            $details[$filters['invoice_type']] = $this->earning->filter_detail($filters)->earning;
        }
        $pages      = $this->pages;
        $main_view  = 'earning/detail_earning';
        $this->load->view('template', compact('main_view', 'pages', 'details'));

        //generate excel
        if ($filters['excel'] == 1) {
            $this->generate_excel($filters, 'detail');
        }
    }

    public function api_get_invoice($year, $month, $invoice_type)
    {
        $filters = [
            'date_year'     => $year,
            'date_month'    => $month + 1,
            'invoice_type'  => $invoice_type
        ];
        $result = $this->earning->get_invoice($filters);
        return $this->send_json_output(true, $result);
    }

    public function generate_excel($filters, $menu)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        if ($menu == 'index') {
            $filename = 'Laporan_Pendapatan_Tahun_' . $filters['date_year'];
            $get_data = $this->earning->filter_excel_total($filters);
        } else {
            $filename = 'Laporan_Pendapatan_Bulan_' . $filters['date_month'] . '_Tahun_' . $filters['date_year'];
            $get_data = $this->earning->filter_excel_detail($filters);
        }
        $i = 2;
        $no = 1;
        // Column Content
        foreach ($get_data as $data) {
            foreach (range('A', 'F') as $v) {
                switch ($v) {
                    case 'A': {
                            $value = $no++;
                            break;
                        }
                    case 'B': {
                            $value = $data->number;
                            break;
                        }
                    case 'C': {
                            $value = date('d F Y', strtotime($data->issued_date));
                            break;
                        }
                    case 'D': {
                            $value = get_invoice_type()[$data->type];
                            break;
                        }
                    case 'E': {
                            $value = $data->status;
                            break;
                        }
                    case 'F': {
                            $value = $data->earning;
                            break;
                        }
                }
                $sheet->setCellValue($v . $i, $value);
            }
            $i++;
        }
        // Column Title
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nomor Faktur');
        $sheet->setCellValue('C1', 'Tanggal Dikeluarkan');
        $sheet->setCellValue('D1', 'Jenis Faktur');
        $sheet->setCellValue('E1', 'Status');
        $sheet->setCellValue('F1', 'Pendapatan');
        // Auto width
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        die();
    }
}
