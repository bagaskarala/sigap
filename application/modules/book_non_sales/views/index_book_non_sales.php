<?php
$per_page           = 10;
$keyword            = $this->input->get('keyword');
$type               = $this->input->get('type');
$status             = $this->input->get('status');
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
                <a class="text-muted">Buku Non Penjualan</a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Buku Non Penjualan </h1>
            <span class="badge badge-info">Total : <?= $total; ?></span>
        </div>
        <?php if ($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_pemasaran' ) : ?>
        <a href="<?= base_url("$pages/add"); ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus fa-fw"></i>
            Tambah</a>
        <?php endif?>
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
                            <div class="col-12 col-md-4 mb-3">
                                <label for="per_page">Data per halaman</label>
                                <?= form_dropdown('per_page', get_per_page_options(), $per_page, 'id="per_page" class="form-control custom-select d-block" title="List per page"'); ?>
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label for="category">Tipe</label>
                                <?= form_dropdown('type', get_book_non_sales_type(), $type, 'id="type" class="form-control custom-select d-block" title="Filter Kategori"'); ?>
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label for="status">Status</label>
                                <?= form_dropdown('status', get_book_non_sales_status(), $status, 'id="status" class="form-control custom-select d-block" title="Filter Status"'); ?>
                            </div>
                            <div class="col-12 col-md-8">
                                <label for="keyword">Pencarian</label>
                                <?= form_input('keyword', $keyword, 'placeholder="Cari berdasarkan nomor permintaan" class="form-control"'); ?>
                            </div>
                            <div class="col-12 col-lg-4">
                                <label>&nbsp;</label>
                                <div class="btn-group btn-block" role="group" aria-label="Filter button">
                                    <button class="btn btn-secondary" type="button"
                                        onclick="location.href = '<?= base_url($pages); ?>'"> Reset</button>
                                    <button class="btn btn-primary" type="submit" value="Submit"><i
                                            class="fa fa-filter"></i> Filter</button>
                                </div>
                            </div>
                        </div>
                        <?= form_close(); ?>
                    </div>
                    <?php if ($book_non_sales) : ?>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col" class="pl-4">No</th>
                                    <th scope="col" style="min-width:150px;">Nomor Permintaan</th>
                                    <th scope="col" style="min-width:200px;">Tanggal</th>
                                    <th scope="col" style="min-width:200px;">Tipe</th>
                                    <th scope="col" style="min-width:200px;">Status</th>
                                    <th style="min-width:100px;"> </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($book_non_sales as $book_non_sales) : ?>
                                <tr class="text-center">
                                    <td class="align-middle pl-4"><?= ++$i; ?></td>
                                    <td class="text-left align-middle">
                                        <a href="<?= base_url('book_non_sales/view/' . $book_non_sales->book_non_sales_id . ''); ?>"
                                            class="font-weight-bold">
                                            <?= highlight_keyword($book_non_sales->number, $keyword); ?>
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <?= format_datetime($book_non_sales->issued_date); ?>
                                    </td>
                                    <td class="align-middle">
                                        <?= get_book_non_sales_type()[$book_non_sales->type]?>
                                    </td>
                                    <td class="align-middle">
                                        <?= get_book_non_sales_status()[$book_non_sales->status]; ?>
                                    </td>
                                    <td class="align-middle text-right">
                                        <?php if($book_non_sales->status=='waiting') : ?>
                                            <?php if (($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_gudang')): ?>
                                            <button type="button" class="btn btn-sm btn-success" data-toggle="modal"
                                                data-target="#finish_modal" title="Selesai">
                                                <i class="fas fa-check"></i>
                                            </button>
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
                                            <?php if(($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_pemasaran')) :?>
                                            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal"
                                                data-target="#modal-hapus-<?= $book_non_sales->book_non_sales_id; ?>"><i
                                                    class="fa fa-trash-alt"></i><span class="sr-only">Delete</span></button>
                                            <div class="text-left">
                                                <div class="modal modal-alert fade"
                                                    id="modal-hapus-<?= $book_non_sales->book_non_sales_id; ?>"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="modal-hapus-<?= $book_non_sales->book_non_sales_id; ?>"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    <i class="fa fa-exclamation-triangle text-red mr-1"></i>
                                                                    Konfirmasi
                                                                    Hapus
                                                                </h5>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Apakah anda yakin akan menghapus permintaan buku non
                                                                    penjualan <span
                                                                        class="font-weight-bold"><?= $book_non_sales->number; ?></span>?
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-light"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-danger"
                                                                    onclick="location.href='<?= base_url('book_non_sales/delete/'.$book_non_sales->book_non_sales_id); ?>'"
                                                                    data-dismiss="modal">Hapus</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif?>
                                        <?php endif?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else : ?>
                    <p class="text-center">Data tidak tersedia</p>
                    <?php endif; ?>
                    <?= $pagination ?? null; ?>
                </div>
            </section>
        </div>
    </div>
</div>