<?php defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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
        $this->load->model('library/library_model', 'library');
    }

    public function index($page = NULL)
    {
        $filters = [
            'keyword'           => $this->input->get('keyword', true),
            'published_year'    => $this->input->get('published_year', true),
            'stock_moreeq'      => $this->input->get('stock_moreeq', true),
            'stock_lesseq'      => $this->input->get('stock_lesseq', true),
        ];

        if ($this->input->get('excel', true) == 1) {
            $this->generate_excel($filters);
            return;
        }

        //custom per page
        $this->book_stock->per_page = $this->input->get('per_page', true) ?? 10;
        $get_data = $this->book_stock->filter_book_stock($filters, $page);

        $book_stocks = $get_data['book_stocks'];
        $total = $get_data['total'];
        if ($book_stocks) {
            $max_stock = $get_data['book_stocks_max']->warehouse_present;;
        } else {
            $max_stock = 0;
        }
        $pagination = $this->book_stock->make_pagination(site_url('book_stock'), 2, $total);
        $pages      = $this->pages;
        $main_view  = 'book_stock/index_bookstock';
        $this->load->view('template', compact('pages', 'main_view', 'book_stocks', 'pagination', 'total', 'max_stock'));
    }

    public function add()
    {
        if (!$_POST) {
            $input = (object) $this->book_stock->get_default_values();
        } else {
            $input = (object) $this->input->post(null, true);
        }

        if (!$this->book_stock->validate()) {
            $pages       = $this->pages;
            $main_view   = 'book_stock/form_bookstock';
            $form_action = 'book_stock/add';

            $this->load->view('template', compact('pages', 'main_view', 'form_action', 'input'));
            return;
        }

        // apakah book id sudah tersedia
        $book_stock = $this->book_stock->where('book_id', $input->book_id)->get();
        if ($book_stock) {
            $this->session->set_flashdata('error', $this->lang->line('toast_data_duplicate'));
            redirect("{$this->pages}/view/{$book_stock->book_stock_id}");
        }

        if ($this->book_stock->insert($input)) {
            $this->session->set_flashdata('success', $this->lang->line('toast_add_success'));
        } else {
            $this->session->set_flashdata('error', $this->lang->line('toast_add_fail'));
        }

        redirect($this->pages);
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

    public function edit($book_stock_id)
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
            // catat orang yang menginput stok buku
            $input->user_id = $_SESSION['user_id'];
        }

        $pages = $this->pages;
        $main_view = 'book_stock/edit_bookstock';
        $form_action = "book_stock/edit/$book_stock_id";
        $this->load->view('template', compact('pages', 'main_view', 'input'));
    }

    public function edit_book_stock()
    {
        if (!$this->_is_warehouse_admin() && !$_POST) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_edit_fail'));
            redirect($this->pages);
        }

        $revision_type = $this->input->post('revision_type');
        $book_id = $this->input->post('book_id');
        $quantity = $this->input->post('warehouse_modifier');
        $notes = $this->input->post('notes');
        $book_stock = $this->book_stock->where('book_id', $book_id)->get();

        if (!$book_stock) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        $new_stock_qty = $revision_type == "add" ?  $book_stock->warehouse_present + $quantity : $book_stock->warehouse_present - $quantity;

        $this->db->trans_begin();

        $this->book_stock->where('book_id', $book_id)->update(['warehouse_present' => $new_stock_qty]);
        $this->db->insert('book_stock_revision', [
            'book_id'            => $book_id,
            'warehouse_past'     => $book_stock->warehouse_present,
            'warehouse_present'  => $new_stock_qty,
            'warehouse_revision' => $quantity,
            'revision_type'      => $revision_type,
            'notes'              => $notes,
            'revision_date'      => now(),
            'type'               => "revision"
        ]);
        $book_stock_revision_id = $this->db->insert_id();
        $this->book_transaction->insert([
            'book_id'                => $book_id,
            'book_stock_revision_id' => $book_stock_revision_id,
            'book_stock_id'          => $book_stock->book_stock_id,
            'stock_initial'          => $book_stock->warehouse_present,
            'stock_mutation'         => $quantity,
            'stock_last'             => $new_stock_qty,
            'date'                   => now()
        ]);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }

        redirect('book_stock/view/' . $book_stock->book_stock_id);
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
        if (!$this->_is_warehouse_admin() && !$_POST) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_edit_fail'));
            redirect($this->pages);
        }

        $input = (object) $this->input->post(null, true);
        $book_stock = $this->book_stock->where('book_id', $input->book_id)->get();
        if (!$book_stock) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }

        $new_stock_qty = $input->revision_type == "sub" ? $book_stock->warehouse_present - $input->warehouse_modifier : $book_stock->warehouse_present;
        $new_retur_qty = $input->revision_type == "sub" ? $book_stock->retur_stock + $input->warehouse_modifier : $book_stock->retur_stock - $input->warehouse_modifier;

        $this->db->trans_begin();

        $this->book_stock->where('book_id', $input->book_id)->update([
            'warehouse_present' => $new_stock_qty,
            'retur_stock' => $new_retur_qty
        ]);

        $this->db->insert('book_stock_revision', [
            'book_id'            => $input->book_id,
            'warehouse_past'     => $book_stock->warehouse_present,
            'type'               => "return",
            'warehouse_present'  => $new_stock_qty,
            'warehouse_revision' => $input->warehouse_modifier,
            'revision_type'      => $input->revision_type,
            'notes'              => $input->notes,
            'revision_date'      => $input->revision_date
        ]);
        $book_stock_revision_id = $this->db->insert_id();

        // masukkan book transaksi jika terjadi retur (stok retur bertambah, stok buku berkurang)
        if ($input->revision_type == "sub") {
            $this->book_transaction->insert([
                'book_id'                => $input->book_id,
                'book_stock_revision_id' => $book_stock_revision_id,
                'book_stock_id'          => $book_stock->book_stock_id,
                'stock_initial'          => $book_stock->warehouse_present,
                'stock_mutation'         => $input->warehouse_modifier,
                'stock_last'             => $new_stock_qty,
                'date'                   => now()
            ]);
        }
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_fail'));
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        }
        redirect('book_stock/view/' . $book_stock->book_stock_id);
    }

    public function edit_book_location()
    {
        if (!$this->_is_warehouse_admin() && !$_POST) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_edit_fail'));
            redirect($this->pages);
        }

        $input = (object) $this->input->post(null, true);
        $book_stock = $this->book_stock->where('book_stock_id', $input->book_stock_id)->get();
        if (!$book_stock) {
            $this->session->set_flashdata('warning', $this->lang->line('toast_data_not_available'));
            redirect($this->pages);
        }
        if ($this->book_stock->where('book_stock_id', $input->book_stock_id)->update(['book_location' => $input->book_location])) {
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_success'));
        } else {
            $this->session->set_flashdata('success', $this->lang->line('toast_edit_fail'));
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
        $library = $this->library->get_all();
        $get_data = $this->book_stock->filter_excel_stock($filters);
        $header_row_number = 3;
        $content_start_row = $header_row_number + 1;

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $filename = 'STOK BUKU - ' . date('d-m-Y H:i:s');

        $libraries = array_map(function ($lib) {
            return  "Stok " . $lib->library_name;
        }, $library);
        $headers =  array_merge(['No', 'Judul', 'Penulis', 'Stok Gudang', 'Stok Showroom'], $libraries);
        $max_column_string = Coordinate::stringFromColumnIndex(count($headers));

        // set title
        $sheet->setCellValue('A1', $filename);
        $sheet->mergeCells("A1:{$max_column_string}1");
        $spreadsheet->getActiveSheet()
            ->getStyle('A1')
            ->getFont()
            ->setBold(true);

        // set value
        $content = [
            $headers,
        ];
        foreach ($get_data as $index => $item) {
            $library_stocks = array_map(function ($lib_column) use ($item) {
                foreach ($item->libraries as $item_lib) {
                    if ($item_lib->library_id === $lib_column->library_id) {
                        return $item_lib->library_stock;
                    }
                }
                return '0';
            }, $library);

            array_push($content, array_merge([$index + 1, $item->book_title, $item->author_name, $item->warehouse_present ?? '0', $item->showroom_present ?? '0'], $library_stocks));
        }

        $spreadsheet->getActiveSheet()->fromArray(
            $content,
            NULL,
            "A{$header_row_number}"
        );

        // set header style
        $spreadsheet->getActiveSheet()
            ->getStyle("A3:{$max_column_string}3")
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('A6A6A6');
        $spreadsheet->getActiveSheet()
            ->getStyle("A3:{$max_column_string}3")
            ->getFont()
            ->setBold(true);

        // set auto width
        $startColumn = 'A';
        for ($i = 0; $i < count($headers); $i++) {
            if ($startColumn == 'B') {
                $sheet->getColumnDimension('B')->setWidth(50);
            } else {
                $sheet->getColumnDimension($startColumn)->setAutoSize(true);
            }
            $startColumn++;
        }

        // set conditional column style
        $index = $content_start_row;
        foreach ($get_data as $data) {
            $value = $data->warehouse_present;
            if ($value <= 50) {
                $spreadsheet->getActiveSheet()
                    ->getStyle('D' . $index)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFC000');
            }
            $index++;
        }

        $spreadsheet->getDefaultStyle()->getAlignment()->setWrapText(true);

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
