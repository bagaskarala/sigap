<?php
$level              = check_level();
$per_page           = $this->input->get('per_page') ?? 10;
$published_year     = $this->input->get('published_year');
$warehouse_present   = $this->input->get('warehouse_present');
$stock_lesseq       = $this->input->get('stock_lesseq') ? $this->input->get('stock_lesseq') : $max_stock;
$stock_moreeq       = $this->input->get('stock_moreeq') ? $this->input->get('stock_moreeq') : 0;
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
                <a class="text-muted">Stok Buku</a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Stok Buku </h1>
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
                                <label for="category">Tahun Terbit</label>
                                <?= form_dropdown('published_year', get_published_date(), $published_year, 'id="published_year" class="form-control custom-select d-block" title="Filter Tahun Terbit"'); ?>
                            </div>
                            <div class="col-12 col-md-3 mb-3">
                                <label for="category">Range stok gudang</label>
                                <?= form_input(array(
                                    'name' => 'stock_moreeq',
                                    'id' => 'stock_moreeq',
                                    'class' => 'form-control',
                                    'type' => 'number',
                                    'value' => $stock_moreeq,
                                    'placeholder' => "Stok gudang &#8805;",
                                    'min'   => 0
                                )); ?>
                            </div>
                            <div class="col-12 col-md-3 mb-3">
                                <label for="category">&nbsp;</label>
                                <?= form_input(array(
                                    'name' => 'stock_lesseq',
                                    'id' => 'stock_lesseq',
                                    'class' => 'form-control',
                                    'type' => 'number',
                                    'value' => $stock_lesseq,
                                    'placeholder' => "Stok gudang &#8804;",
                                    'min'   => 0
                                )); ?>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="status">Pencarian</label>
                                <?= form_input('keyword', $keyword, 'placeholder="Cari berdasarkan Nama" class="form-control"'); ?>
                            </div>
                            <div class="col-12 col-lg-6">
                                <label>&nbsp;</label>
                                <div class="btn-group btn-block" role="group" aria-label="Filter button">
                                    <button class="btn btn-secondary col-3" type="button"
                                        onclick="location.href = '<?= base_url($pages); ?>'"> Reset</button>
                                    <button class="btn btn-primary col-3" type="submit" value="Submit"><i
                                            class="fa fa-filter"></i> Filter</button>
                                    <?php if ($level == "superadmin" || $level == "admin_gudang") : ?>
                                    <button class="btn btn-success col-3" type="submit" id="excel" name="excel"
                                        value="1" data-toggle="tooltip" data-placement="top"
                                        title="Download excel stok buku"><i class="fas fa-file-excel mr-2"></i>Stok
                                        Buku</button>
                                    <button class="btn btn-success col-3" id="excel" name="excel" type="button"
                                        onclick="location.href='<?= base_url('/book_stock/generate_retur'); ?>'"
                                        data-toggle="tooltip" data-placement="top" title="Download excel stok retur"><i
                                            class="fas fa-file-excel mr-2"></i>Stok Retur</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?= form_close(); ?>
                    </div>
                    <?php if ($book_stocks) : ?>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" class="pl-4 align-middle text-center" rowspan="2">No</th>
                                    <th scope="col" style="min-width:350px;" class="align-middle text-center"
                                        rowspan="2">
                                        Judul</th>
                                    <th scope="col" style="min-width:100px;" class="align-middle text-center"
                                        rowspan="2">
                                        Tahun Terbit
                                    </th>
                                    <th scope="col" style="min-width:150px;" class="align-middle text-center"
                                        rowspan="2">
                                        Penulis</th>
                                    <th scope="col" style="min-width:150px;" class="align-middle text-center"
                                        rowspan="2">
                                        Lokasi Rak</th>
                                    <th scope="col" style="min-width:100px;" class="align-middle text-center"
                                        colspan="3">
                                        Stok</th>
                                    <?php if ($level == 'superadmin') : ?>
                                    <th style="min-width:150px;" class="align-middle text-center" rowspan="2"> Aksi
                                    </th>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <th scope="col" style="min-width:100px;" class="align-middle text-center">Gudang
                                    </th>
                                    <th scope="col" style="min-width:100px;" class="align-middle text-center">
                                        Perpustakaan</th>
                                    <th scope="col" style="min-width:100px;" class="align-middle text-center">Showroom
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($book_stocks as $book_stock) : ?>
                                <tr>
                                    <td class="align-middle text-center"><?= ++$i; ?></td>
                                    <td class="align-middle">
                                        <a href="<?= base_url('book_stock/view/' . $book_stock->book_stock_id . ''); ?>"
                                            class="font-weight-bold">
                                            <?= highlight_keyword($book_stock->book_title, $keyword); ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?= konversiTahun($book_stock->published_date); ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?= isset($book_stock->author_name) ? highlight_keyword($book_stock->author_name, $keyword) : '-'; ?>
                                        <button type="button"
                                            class="btn btn-link btn-sm m-0 p-0 <?= count($book_stock->authors) <= 1 ? 'd-none' : ''; ?>"
                                            data-container="body" data-toggle="popover" data-placement="right"
                                            data-html="true" data-trigger="hover"
                                            data-content='<?= expand($book_stock->authors); ?>'>
                                            <i class="fa fa-users"></i>
                                        </button>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?= $book_stock->book_location; ?></td>
                                    </td>
                                    <?php if ($book_stock->warehouse_present <= 50) : ?>
                                    <td class="align-middle text-center text-danger"><b>
                                            <?= $book_stock->warehouse_present; ?></b>
                                    </td>
                                    <?php else : ?>
                                    <td class="align-middle text-center">
                                        <?= $book_stock->warehouse_present; ?>
                                    </td>
                                    <?php endif ?>
                                    <td class="align-middle text-center"><?= $book_stock->library_present ?></td>
                                    <td class="align-middle text-center"><?= $book_stock->showroom_present ?></td>
                                    <?php if ($level == 'superadmin') : ?>
                                    <td style="min-width: 130px" class="align-middle text-center">
                                        <div class="text-center">
                                            <button title="Edit Lokasi Rak" type="button"
                                                class="btn btn-sm btn-secondary" data-toggle="modal"
                                                data-target="#modal-edit-rak-<?= $book_stock->book_id; ?>"><i
                                                    class="fa fa-map-marker-alt"></i><span class="sr-only">Edit Lokasi
                                                    Rak</span></button>
                                            <a href="<?= base_url(
                                                                        'book_stock/edit/' . $book_stock->book_stock_id . ''
                                                                    ); ?>" class="btn btn-sm btn-secondary"
                                                title="Edit Stok Buku">
                                                <i class="fa fa-pencil-alt"></i>
                                                <span class="sr-only">Edit Stok Buku</span>
                                            </a>
                                            <a href="<?= base_url(
                                                                        'book_stock/retur/' . $book_stock->book_stock_id . ''
                                                                    ); ?>" class="btn btn-sm btn-secondary"
                                                title="Revisi Stok Retur">
                                                <i class="fas fa-undo-alt"></i>
                                                <span class="sr-only">Revisi Stok Retur</span>
                                            </a>
                                            <div class="text-left">
                                                <div class="modal modal-alert fade"
                                                    id="modal-edit-rak-<?= $book_stock->book_id; ?>" tabindex="-1"
                                                    role="dialog"
                                                    aria-labelledby="modal-edit-rak-<?= $book_stock->book_id; ?>"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    <i class="fa fa-map-marker-alt text-black mr-1"></i>
                                                                    Edit Lokasi Rak
                                                                </h5>
                                                            </div>
                                                            <form
                                                                action="<?= base_url('book_stock/edit_book_location/') ?>"
                                                                method='post'>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label class="font-weight-bold">Judul
                                                                            Buku</label>
                                                                        <input type="text" class="form-control"
                                                                            value="<?= $book_stock->book_title; ?>"
                                                                            disabled />
                                                                        <input type="hidden" class="form-control"
                                                                            id="book_stock_id" name="book_stock_id"
                                                                            value="<?= $book_stock->book_stock_id; ?>" />
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="book_location">Lokasi Rak
                                                                            Buku</label>
                                                                        <?= form_input('book_location', $book_stock->book_location, 'class="form-control" id="book_location" '); ?>
                                                                        <?= form_error('book_location'); ?>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <div class="form-group">
                                                                        <button type="button" class="btn btn-light"
                                                                            data-dismiss="modal">Close</button>
                                                                        <input type="submit" class="btn btn-primary"
                                                                            value="Submit" />
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </td>
                                    <?php endif ?>
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