<?php
$level              = check_level();
$per_page           = $this->input->get('per_page') ?? 10;
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
                <a class="text-muted">Aset Buku</a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Aset Buku </h1>
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
                <h5 class="mb-0 mt-3 ml-3">Info Total Aset</h5>
                    <div class="row px-3 mb-2">
                        <div class="col-12">
                            <div class="metric-row metric-flush ">
                                <div class="col metric metric-bordered align-items-center">
                                    <h2 class="metric-label"> Gudang </h2>
                                    <p class="metric-value h3 px-2">
                                        <span class="value">Rp <?= number_format($count['warehouse'], 2, ",", "."); ?></span>
                                    </p>
                                </div>
                                <div class="col metric metric-bordered align-items-center">
                                    <h2 class="metric-label"> Showroom </h2>
                                    <p class="metric-value h3 px-2">
                                        <span class="value">
                                            Rp <?= number_format($count['showroom'], 2, ",", "."); ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="col metric metric-bordered align-items-center">
                                    <h2 class="metric-label"> Perpustakaan </h2>
                                    <p class="metric-value h3 px-2">
                                        <span class="value">
                                            Rp <?= number_format($count['library'], 2, ",", "."); ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="col metric metric-bordered align-items-center">
                                    <h2 class="metric-label"> Total Keseluruhan </h2>
                                    <p class="metric-value h3 px-2">
                                        <span class="value">
                                            Rp <?= number_format($count['all'], 2, ",", "."); ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="card card-fluid">
                <div class="card-body p-0">
                    <div class="p-3">
                        <?= form_open($pages, ['method' => 'GET']); ?>
                        <div class="row">
                            <div class="col-12 col-md-2 mb-3">
                                <label for="per_page">Data per halaman</label>
                                <?= form_dropdown('per_page', get_per_page_options(), $per_page, 'id="per_page" class="form-control custom-select d-block" title="List per page"'); ?>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="status">Pencarian</label>
                                <?= form_input('keyword', $keyword, 'placeholder="Cari berdasarkan Nama" class="form-control"'); ?>
                            </div>
                            <div class="col-12 col-md-4">
                                <label>&nbsp;</label>
                                <div
                                    class="btn-group btn-block"
                                    role="group"
                                    aria-label="Filter button"
                                >
                                    <button
                                        class="btn btn-secondary col-4"
                                        type="button"
                                        onclick="location.href = '<?= base_url($pages); ?>'"
                                    > Reset</button>
                                    <button
                                        class="btn btn-primary col-4"
                                        type="submit"
                                        value="Submit"
                                    ><i class="fa fa-filter"></i> Filter</button>
                                    <?php if ($level == "superadmin" || $level == "admin_gudang") : ?>
                                        <button
                                            class="btn btn-success col-4"
                                            type="submit"
                                            id="excel"
                                            name="excel"
                                            value="1"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="Download excel aset buku"
                                        ><i class="fas fa-file-excel mr-2"></i>Excel</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?= form_close(); ?>
                    </div>
                    <?php if ($book_assets) : ?>
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th
                                            scope="col"
                                            class="pl-4 align-middle text-center"
                                            rowspan="2"
                                        >No</th>
                                        <th
                                            scope="col"
                                            style="min-width:350px;"
                                            class="align-middle text-center"
                                            rowspan="2"
                                        >
                                            Judul</th>
                                        <th
                                            scope="col"
                                            style="min-width:150px;"
                                            class="align-middle text-center"
                                            rowspan="2"
                                        >
                                            Penulis</th>
                                        <th
                                            scope="col"
                                            style="min-width:150px;"
                                            class="align-middle text-center"
                                            rowspan="2"
                                        >
                                            Harga</th>
                                        <th
                                            scope="col"
                                            style="min-width:100px;"
                                            class="align-middle text-center"
                                            colspan="3"
                                        >
                                            Stok</th>
                                        <th
                                            scope="col"
                                            style="min-width:100px;"
                                            class="align-middle text-center"
                                            colspan="3"
                                        >
                                            Aset (Rupiah)</th>
                                    </tr>
                                    <tr>
                                        <th
                                            scope="col"
                                            style="min-width:100px;"
                                            class="align-middle text-center"
                                        >Gudang
                                        </th>
                                        <th
                                            scope="col"
                                            style="min-width:100px;"
                                            class="align-middle text-center"
                                        >
                                            Perpustakaan</th>
                                        <th
                                            scope="col"
                                            style="min-width:100px;"
                                            class="align-middle text-center"
                                        >Showroom
                                        </th>
                                        <th
                                            scope="col"
                                            style="min-width:100px;"
                                            class="align-middle text-center"
                                        >Gudang
                                        </th>
                                        <th
                                            scope="col"
                                            style="min-width:100px;"
                                            class="align-middle text-center"
                                        >
                                            Perpustakaan</th>
                                        <th
                                            scope="col"
                                            style="min-width:100px;"
                                            class="align-middle text-center"
                                        >Showroom
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($book_assets as $book_asset) : ?>
                                        <tr>
                                            <td class="align-middle text-center"><?= ++$i; ?></td>
                                            <td class="align-middle font-weight-bold">
                                                <a href="<?= base_url('book_asset/view/' . $book_asset->book_id . ''); ?>"
                                                class="font-weight-bold">
                                                <?= highlight_keyword($book_asset->book_title, $keyword); ?>
                                            </td>
                                            <td class="align-middle text-center">
                                                <?= isset($book_asset->author_name) ? highlight_keyword($book_asset->author_name, $keyword) : '-'; ?>
                                                <button
                                                    type="button"
                                                    class="btn btn-link btn-sm m-0 p-0 <?= count($book_asset->authors) <= 1 ? 'd-none' : ''; ?>"
                                                    data-container="body"
                                                    data-toggle="popover"
                                                    data-placement="right"
                                                    data-html="true"
                                                    data-trigger="hover"
                                                    data-content='<?= expand($book_asset->authors); ?>'
                                                >
                                                    <i class="fa fa-users"></i>
                                                </button>
                                            </td>
                                            <td class="align-middle text-center">
                                                <?= number_format($book_asset->harga, 2, ",", "."); ?></td>
                                            </td>
                                            <?php if ($book_asset->warehouse_present <= 50) : ?>
                                                <td class="align-middle text-center text-danger"><b>
                                                        <?= $book_asset->warehouse_present; ?></b>
                                                </td>
                                            <?php else : ?>
                                                <td class="align-middle text-center">
                                                    <?= $book_asset->warehouse_present; ?>
                                                </td>
                                            <?php endif ?>
                                            <td class="align-middle text-center"><?= $book_asset->library_present ?></td>
                                            <td class="align-middle text-center"><?= $book_asset->showroom_present ?></td>
                                            <td class="align-middle text-center"><?= number_format($book_asset->warehouse_present * $book_asset->harga, 2, ",", ".") ?></td>
                                            <td class="align-middle text-center"><?= number_format($book_asset->library_present * $book_asset->harga, 2, ",", ".") ?></td>
                                            <td class="align-middle text-center"><?= number_format($book_asset->showroom_present * $book_asset->harga, 2, ",", ".") ?></td>
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
