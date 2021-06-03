<?php
$level              = check_level();
?>
<header class="page-title-bar mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('book_stock'); ?>">Aset Buku</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">
                    <?= $book_asset->book_title; ?>
                </a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Aset Buku </h1>
        </div>
    </div>
</header>

<div class="page-section">
    <section id="data-draft" class="card">
        <header class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item ">
                    <a class="nav-link active show" data-toggle="tab" href="#asset-data"><i
                            class="fa fa-warehouse pr-1"></i>Detail Aset Buku</a>
                </li>
            </ul>
        </header>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fade active show" id="asset-data">
                    <div id="reload-stock">
                    <?php if ($level == 'superadmin'|| $level == 'admin_gudang' || $level == 'admin_pemasaran') : ?>
                        <?php $i = 1; ?>
                        <div class="row">
                            <div class="col-6 text-left">
                                <strong>Aset Buku</strong>
                            </div>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mb-0 nowrap">
                                <tbody>
                                    <tr>
                                        <td width="160px">Judul Buku</td>
                                        <td><strong>
                                                <?= $book_asset->book_title; ?>
                                        </strong></td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Penulis</td>
                                        <td>
                                            <?= $book_asset->author_name;?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Harga</td>
                                        <td>
                                            Rp <?= number_format($book_asset->harga);?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Stok Keseluruhan</td>
                                        <td><?= $book_asset->warehouse_present+$book_asset->library_present+$book_asset->showroom_present; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Aset Keseluruhan</td>
                                        <td>Rp <?= number_format(($book_asset->warehouse_present+$book_asset->library_present+$book_asset->showroom_present)*$book_asset->harga, 2, ",", "."); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Stok Gudang</td>
                                        <td>
                                            <?= $book_asset->warehouse_present; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Aset Gudang</td>
                                        <td>
                                            Rp <?= number_format($book_asset->warehouse_present*$book_asset->harga, 2, ",", ".") ; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Stok Showroom</td>
                                        <td><?= $book_asset->showroom_present; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Aset Showroom</td>
                                        <td>
                                            Rp <?= number_format($book_asset->showroom_present*$book_asset->harga, 2, ",", ".") ; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Stok Perpustakaan</td>
                                        <td><?= $book_asset->library_present; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Aset Perpustakaan</td>
                                        <td>
                                            Rp <?= number_format($book_asset->library_present*$book_asset->harga, 2, ",", ".") ; ?>
                                        </td>
                                    </tr>
                                    <?php if($book_asset->warehouse_present) :?>
                                    </tr>
                                    <td width="160px">Detail Aset Perpustakaan</td>
                                    <td>
                                        <table class="table table-striped mb-0 table-responsive">
                                            <tbody>
                                                <tr>
                                                    <th class="align-middle text-center">No</th>
                                                    <th style="width:250px;">Nama Perpustakaan</th>
                                                    <th class="align-middle text-center">Stok</th>
                                                    <th class="align-middle text-center">Aset</th>
                                                </tr>
                                                <?php $no=1; foreach($book_asset->library_stock as $library_data) : ?>
                                                <tr>
                                                    <td class="align-middle text-center">
                                                        <?= $no++; ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?=$library_data->library_name?>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <?=$library_data->library_stock?>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        Rp <?= number_format($library_data->library_stock*$book_asset->harga, 2, ",", ".")?>
                                                    </td>
                                                </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </td>
                                    </tr>
                                    <?php endif?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif?>
                </div>
            </div>
        </div>
    </section>
</div>