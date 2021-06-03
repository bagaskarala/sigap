<?php defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Book_stock extends Warehouse_Sales_Controller
{
    public $per_page = 10;

    public function __construct()
    {
        parent::__construct();
        $this->pages = "book_stock";
        $this->load->model('book_stock/book_stock_model', 'book_stock');
        $this->load->model('book/book_model', 'book');
        $this->load->model('book_transaction/book_transaction_model', 'book_transaction');
    }

    public function index($page = NULL)
    {
        //all filter
        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'published_year'    => $this->input->get('published_year', true),
            'stock_moreeq'      => $this->input->get('stock_moreeq', true),
            'stock_lesseq'      => $this->input->get('stock_lesseq', true),
            'excel'             => $this->input->get('excel', true)
        ];
        //custom per page
        $this->book_stock->per_page = $this->input->get('per_page', true) ?? 10;
        $get_data = $this->book_stock->filter_book_stock($filters, $page);

        $book_stocks = $get_data['book_stocks'];
        $total = $get_data['total'];
        if ($book_stocks) {
            $max_stock = $book_stocks[count($book_stocks) - 1]->warehouse_present;
        } else {
            $max_stock = 0;
        }
        $pagination = $this->book_stock->make_pagination(site_url('book_stock'), 2, $total);
        $pages      = $this->pages;
        $main_view  = 'book_stock/index_bookstock';
        $this->load->view('template', compact('pages', 'main_view', 'book_stocks', 'pagination', 'total', 'max_stock'));

        if ($filters['excel'] == 1) {
            $this->generate_excel($filters);
        }
    }

    public function view($book_stock_id)
    {
        $book_stock = $this->book_stock->get_book_stock($book_stock_id);
        if (!$book_stock) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        $input = (object) $book_stock;
        $book_stock->revision      = $this->book_stock->get_stock_revision($book_stock->book_id);
        $book_stock->library_stock = $this->book_stock->get_library_stock($book_stock->book_stock_id);
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
            $revision_type = $this->input->post('revision_type');
            $book_id = $this->input->post('book_id');
            $quantity = $this->input->post('warehouse_modifier');
            $notes = $this->input->post('notes');
            $book_stock = $this->book_stock->where('book_id', $book_id)->get();
            $book_stock_revision = (object) [
                'book_id'            => $book_id,
                'warehouse_past'     => $book_stock->warehouse_present,
                'warehouse_present'  => 0,
                'warehouse_revision' => $quantity,
                'revision_type'      => $revision_type,
                'notes'              => $notes,
                'revision_date'      => now(),
                'type'               => "revision"
            ];
            if (!$book_stock) {
                $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            }
            else {
                if ($revision_type=="add") $book_stock->warehouse_present += $quantity;
                else $book_stock->warehouse_present -= $quantity;
                $book_stock_revision->warehouse_present = $book_stock->warehouse_present;
                if ($this->book_stock->where('book_id', $book_id)->update($book_stock) && $this->db->insert('book_stock_revision',$book_stock_revision)) {
                    $book_stock_revision_id = $this->db->insert_id();
                    //insert to book transaction
                    if($revision_type=="add"){
                        $this->book_transaction->insert([
                            'book_id'            => $book_id,
                            'book_stock_revision_id' => $book_stock_revision_id,
                            'book_stock_id'      => $book_stock->book_stock_id,
                            'stock_initial'      => $book_stock->warehouse_present-$quantity,
                            'stock_mutation'     => $quantity,
                            'stock_last'         => $book_stock->warehouse_present,
                            'date'               => now()
                        ]);    
                    }
                    else{
                        $this->book_transaction->insert([
                            'book_id'            => $book_id,
                            'book_stock_revision_id' => $book_stock_revision_id,
                            'book_stock_id'      => $book_stock->book_stock_id,
                            'stock_initial'      => $book_stock->warehouse_present+$quantity,
                            'stock_mutation'     => $quantity,
                            'stock_last'         => $book_stock->warehouse_present,
                            'date'               => now()
                        ]);    
                    }
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
    
    public function retur($book_stock_id)
    {
        if (!$this->_is_warehouse_admin()) {
            redirect($this->pages);
        }

        $book_stock = $this->book_stock->get_book_stock($book_stock_id);
        if (!$book_stock) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        if (!$_POST) {
            $input = (object) $book_stock;
        } else {
            $input = (object) $this->input->post(null, true);
        }
        $pages = $this->pages;
        $main_view = 'book_stock/retur_bookstock';
        $form_action = "book_stock/retur/$book_stock_id";
        $this->load->view('template', compact('pages', 'main_view', 'input'));
    }

    public function retur_book_stock()
    {
        if ($this->_is_warehouse_admin() == TRUE && $this->input->method() == 'post') {
            $input = (object) $this->input->post(null, true);
            $book_stock = $this->book_stock->where('book_id', $input->book_id)->get();
            if (!$book_stock) {
                $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            } else {
                $book_stock_revision = (object) [
                    'book_id'            => $input->book_id,
                    'warehouse_past'     => $book_stock->warehouse_present,
                    'type'               => "return",
                    'warehouse_present'  => 0,
                    'warehouse_revision' => $input->warehouse_modifier,
                    'revision_type'      => $input->revision_type,
                    'notes'              => $input->notes,
                    'revision_date'      => $input->revision_date
                ];
                if ($input->revision_type == "sub") {
                    $book_stock->warehouse_present -= $input->warehouse_modifier;
                    $book_stock->retur_stock += $input->warehouse_modifier;
                } else {
                    $book_stock->retur_stock -= $input->warehouse_modifier;
                }
                $book_stock_revision->warehouse_present = $book_stock->warehouse_present;
                $this->db->trans_begin();
                $this->book_stock->where('book_id', $input->book_id)->update($book_stock);
                $this->db->insert('book_stock_revision', $book_stock_revision);
                $book_stock_revision_id = $this->db->insert_id();
                if($input->revision_type=="sub"){
                    $this->book_transaction->insert([
                        'book_id'            => $input->book_id,
                        'book_stock_revision_id' => $book_stock_revision_id,
                        'book_stock_id'      => $book_stock->book_stock_id,
                        'stock_initial'      => $book_stock->warehouse_present+$input->warehouse_modifier,
                        'stock_mutation'     => $input->warehouse_modifier,
                        'stock_last'         => $book_stock->warehouse_present,
                        'date'               => now()
                    ]);    
                }
                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('success', $this->lang->line('toast_edit_fail'));
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
                }
            }
        } else {
            $this->session->set_flashdata('warning', $this->lang->line('toast_edit_fail'));
        }
        redirect('book_stock/view/' . $book_stock->book_stock_id);
    }

    public function edit_book_location()
    {
        if ($this->_is_warehouse_admin() == TRUE && $this->input->method() == 'post') {
            $input = (object) $this->input->post(null, true);
            $book_stock = $this->book_stock->where('book_stock_id', $input->book_stock_id)->get();
            if (!$book_stock) {
                $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            } else {
                $book_stock->book_location = $input->book_location;
                if ($this->book_stock->where('book_stock_id', $input->book_stock_id)->update($book_stock)) {
                    $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
                } else {
                    $this->session->set_flashdata('success', $this->lang->line('toast_edit_fail'));
                }
            }
        } else {
            $this->session->set_flashdata('warning', $this->lang->line('toast_edit_fail'));
        }
        redirect($this->pages);
    }

    public function api_chart_data($book_stock_id, $year)
    {
        $book_transaction = $this->book_transaction->get_transaction_data($book_stock_id, $year);
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

    public function api_get_by_book_id($book_id)
    {
        $book_stock = $this->book_stock->get_book_stock_by_book_id($book_id);
        return $this->send_json_output(true, $book_stock);
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

        $get_data = $this->book_stock->filter_excel_stock($filters);
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

        $get_data = $this->book_stock->retur_stock();
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
                            $value = $data->retur_stock;
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
