<?php
$level              = check_level();
$per_page           = $this->input->get('per_page') ?? 10;
$start_date         = $this->input->get('start_date');
$end_date           = $this->input->get('end_date');
$transaction_type   = $this->input->get('transaction_type');
$keyword            = $this->input->get('keyword');
$page               = $this->uri->segment(2);
$i                  = isset($page) ? $page * $per_page - $per_page : 0;

?>

<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item active">
                <a class="text-muted">Transaksi Buku</a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Transaksi Buku </h1>
            <span class="badge badge-info">Total:
                <?= $total; ?>
            </span>
        </div>
    </div>
</header>
<div class="page-section">
    <div class="row">
        <div class="col-12">
            <section class="card card-fluid">
                <div class="card-body p-0">
                    <div class="p-3">
                        <?= form_open($pages, ['method' => 'GET']); ?>
                        <div class="row">
                            <div class="col-12 col-md-3 mb-3">
                                <label for="per_page">Data per halaman</label>
                                <?= form_dropdown('per_page', get_per_page_options(), $per_page, 'id="per_page" class="form-control custom-select d-block" title="List per page"'); ?>
                            </div>
                            <div class="col-12 col-md-3 mb-3">
                                <label for="transaction_type">Jenis Transaksi</label>
                                <?= form_dropdown('transaction_type', get_book_transaction_type(), $transaction_type, 'id="transaction_type" class="form-control custom-select d-block" title="Filter Jenis Transaksi"');?>
                            </div>
                            <div class="col-12 col-md-3 mb-3">
                                <label for="start_date">Tanggal Mulai</label>
                                <?= form_input('start_date', $start_date, 'class="form-control dates"')?>
                            </div>
                            <div class="col-12 col-md-3 mb-3">
                                <label for="end_date">Tanggal Selesai</label>
                                <?= form_input('end_date', $end_date, 'class="form-control dates"')?>
                            </div>
                            <div class="col-12 col-md-8">
                                <label for="status">Pencarian</label>
                                <?= form_input('keyword', $keyword, 'placeholder="Cari berdasarkan Judul" class="form-control"'); ?>
                            </div>
                            <div class="col-12 col-lg-4">
                                <label>&nbsp;</label>
                                <div class="btn-group btn-block" role="group" aria-label="Filter button">
                                    <button class="btn btn-secondary" type="button"
                                        onclick="location.href = '<?= base_url($pages); ?>'"> Reset</button>
                                    <button class="btn btn-primary" type="submit" value="Submit"><i
                                            class="fa fa-filter"></i> Filter</button>
                                    <?php if ($level == "superadmin" || $level == "admin_gudang") : ?>
                                    <button class="btn btn-success" type="submit" id="excel" name="excel"
                                        value="1">Excel</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?= form_close(); ?>
                    </div>
                    <?php if ($book_transactions) : ?>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" class="pl-4 align-middle text-center">No</th>
                                    <th scope="col" style="min-width:350px;" class="align-middle text-center">
                                        Judul</th>
                                    <th scope="col" style="min-width:150px;" class="align-middle text-center">
                                        Nomor Order
                                    </th>
                                    <th scope="col" style="min-width:100px;" class="align-middle text-center">
                                        Stok Awal</th>
                                    <th scope="col" style="min-width:100px;" class="align-middle text-center">
                                        Perubahan</th>
                                        <th scope="col" style="min-width:100px;" class="align-middle text-center">
                                        Stok Akhir</th>
                                    <th scope="col" style="min-width:100px;" class="align-middle text-center">
                                        Tanggal</th>
                                    <th scope="col" style="min-width:150px;" class="align-middle text-center">
                                        Jenis Transaksi</th>
                                    <th scope="col" style="min-width:150px;" class="align-middle text-center">
                                        Keterangan</th>
                                </tr>  
                            </thead>
                            <tbody>
                                <?php foreach ($book_transactions as $book_transaction) : ?>
                                <?php 
                                if($book_transaction->book_receive_id){
                                    $stock_display = '+ '.$book_transaction->stock_mutation;
                                    $change_text_color = "green";
                                    $type= "Masuk";                                    
                                    $type_display = "Percetakan";
                                    $order_number = $book_transaction->order_number;
                                    $link = base_url('book_receive/view/' . $book_transaction->book_receive_id);
                                }
                                else if($book_transaction->invoice_id){
                                    $stock_display = '- '.$book_transaction->stock_mutation;
                                    $change_text_color = "red";
                                    $type= "Keluar";                                    
                                    $type_display = "Pesanan";
                                    $order_number = $book_transaction->invoice_number;
                                    $link = base_url('book_request/view/' . $book_transaction->invoice_id);
                                }
                                else if($book_transaction->book_stock_revision_id){
                                    $order_number = "-";
                                    $link = base_url('book_stock/view/' . $book_transaction->book_stock_id);
                                    if($book_transaction->revision_type=='add'){
                                        $change_text_color = "green";    
                                        $stock_display = '+ '.$book_transaction->stock_mutation;
                                        $type= "Masuk";      
                                        $type_display = "Revisi";                              
                                    }
                                    else{
                                        $change_text_color = "red";   
                                        $stock_display = '- '.$book_transaction->stock_mutation;
                                        $type= "Keluar";                                     
                                        if($book_transaction->type=='revision'){
                                            $type_display = "Revisi";
                                        }
                                        else{
                                            $type_display = "Retur";
                                        }
                                    }
                                }
                                else if ($book_transaction->book_non_sales_id){
                                    $stock_display = '- '.$book_transaction->stock_mutation; 
                                    $change_text_color = "red";
                                    $type= "Keluar";                                     
                                    $type_display = "Non Penjualan";
                                    $order_number = $book_transaction->book_non_sales_number;
                                    $link = base_url('book_non_sales/view/' . $book_transaction->book_non_sales_id);
                                }
                                else if ($book_transaction->transfer_number){
                                    $stock_display = '- '.$book_transaction->stock_mutation; 
                                    $change_text_color = "red";
                                    $type= "Keluar";                                     
                                    $type_display = "Pemindahan";
                                    $order_number = $book_transaction->transfer_number;
                                    $link = base_url('book_transfer/view/' . $book_transaction->book_transfer_id);
                                }
                                    ?>
                                <tr>
                                    <td class="align-middle text-center"><?= ++$i; ?></td>
                                    <td class="align-middle">
                                        <a href="<?= $link ?>"
                                            class="font-weight-bold">
                                            <?= highlight_keyword($book_transaction->book_title, $keyword); ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?= $order_number ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?= $book_transaction->stock_initial ?>
                                    </td>
                                    <td class="align-middle text-center" style=<?= "color:". $change_text_color?>>
                                        <?= $stock_display ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?= $book_transaction->stock_last ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?= format_datetime($book_transaction->date); ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?= $type ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?= $type_display ?>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else : ?>
                    <p class="text-center my-5">Data tidak tersedia</p>
                    <?php endif; ?>
                    <?= $pagination ?? null; ?>
                </div>
            </section>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.dates').flatpickr({
        altInput: true,
        altFormat: 'j F Y',
        dateFormat: 'Y-m-d'
    });
})
</script>