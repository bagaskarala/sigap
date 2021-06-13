<?php
$level              = check_level();
?>
<!-- BREADCUMB,TITLE -->
<header class="page-title-bar mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('book_non_sales'); ?>">Buku Non Penjualan</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted"><?= $book_non_sales->number; ?></a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center my-3">
        <div class="page-title mb-0 pb-0 h1"> Buku Non Penjualan </div>
        <?php if($book_non_sales->status=='waiting' && ($level == 'superadmin' || $level == 'admin_gudang')) : ?>
        <button type="button" class="btn btn-sm btn-success" data-toggle="modal"
            data-target="#finish_modal"><i class="fa fa-check fa-fw"></i> Selesaikan Proses</button>
            <!-- Modal -->
        <div class="modal fade" id="finish_modal" tabindex="-1" role="dialog"
            aria-labelledby="finish_modaltitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="finish_modalLongTitle">Penyelesaian Proses
                        </h5>
                        <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-left">
                        <div class="alert alert-info">
                            <strong>Perhatian</strong>
                            <p class="mb-0">1. Stok gudang akan dikurangi ketika klik submit</p>
                            <p class="mb-0">2. Status akan berubah menjadi selesai</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-dismiss="modal">Close</button>
                        <a class="btn btn-primary" href="<?=base_url('book_non_sales/finish/'.$book_non_sales->book_non_sales_id)?>">Submit</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif?>
    </div>
</header>
<!-- BREADCUMB,TITLE -->

<!-- DETAIL -->
<section class="card" id="detail_book_non_sales">
    <header class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active show" data-toggle="tab" href="#book-request-data-wrapper"><i
                        class="fa fa-info-circle"></i> Detail Buku Non Penjualan</a>
            </li>
        </ul>
    </header>

    <div class="card-body">
        <div class="tab-content">
            <!-- DATA INFO -->
            <div id="book-request-data-wrapper" class="tab-pane fade active show">
                <div id="book-request">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0 nowrap">
                            <tbody>
                                <tr>
                                    <td width="200px"> Nomor Bon </td>
                                    <td>
                                        <?= $book_non_sales->number;?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Peminta </td>
                                    <td><?= $book_non_sales->requester?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Penerima </td>
                                    <td><?= $book_non_sales->receiver?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Jenis Bon Buku Non Penjualan </td>
                                    <td>
                                        <?= get_book_non_sales_type()[$book_non_sales->type] ?>
                                    </td>
                                </tr>
                                <?php if($book_non_sales->notes):?>
                                <tr>
                                    <td width="200px"> Keterangan Jenis </td>
                                    <td>
                                        <?= $book_non_sales->notes;?>
                                    </td>
                                </tr>
                                <?php endif?>
                                <tr>
                                    <td width="200px"> Tanggal Permintaan </td>
                                    <td><?= format_datetime($book_non_sales->issued_date)?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Status </td>
                                    <td>
                                        <?= get_book_non_sales_status()[$book_non_sales->status]?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200px"> File Bon </td>
                                    <td>
                                        <a class="btn btn-outline-danger" id="bon" name="bon" 
                                            href = "<?= base_url('/book_non_sales/generate_pdf_bon/'.$book_non_sales->book_non_sales_id); ?>">
                                            <i class="fas fa-file-pdf mr-3" aria-hidden="true"></i>Download file PDF bon
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Buku yang dipesan </td>
                                    <td>
                                        <table class="table table-striped mb-0 table-responsive">
                                            <tbody>
                                                <tr class="text-center">
                                                    <th scope="col">No</th>
                                                    <th scope="col" style="width:300px;">Judul Buku</th>
                                                    <th scope="col">Jumlah</th>
                                                </tr>
                                                <?php $i = 0; ?>
                                                <?php foreach ($book_non_sales_list as $book_list) : ?>
                                                <?php $i++; ?>
                                                <tr>
                                                    <td class="align-middle text-center pl-4">
                                                        <?= $i ?>
                                                    </td>
                                                    <td>
                                                        <?= $book_list->book_title ?>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <?= $book_list->qty ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- DATA INFO -->
        </div>
    </div>
</section>
<!-- DETAIL -->
<?php if($book_non_sales->status=="finish"){
    echo "Permintaan buku non penjualan telah selesai.";
}?>