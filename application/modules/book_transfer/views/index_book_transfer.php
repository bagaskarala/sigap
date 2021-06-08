<?php
$level              = check_level();
$per_page           = $this->input->get('per_page') ?? 10;
$keyword            = $this->input->get('keyword');
$status             = $this->input->get('status');
$book_transfer_category = $this->input->get('book_transfer_category');
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
                <a class="text-muted">Pemindahan Buku</a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Pemindahan Buku </h1>
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
                            <div class="col-12 col-md-6 mb-3">
                                <label for="per_page">Data per halaman</label>
                                <?= form_dropdown('per_page', get_per_page_options(), $per_page, 'id="per_page" class="form-control custom-select d-block" title="List per page"'); ?>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="status">Status</label>
                                <?= form_dropdown('status', get_book_transfer_status(), $status, 'id="status" class="form-control custom-select d-block" title="Filter Status"'); ?>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="keyword">Pencarian</label>
                                <?= form_input('keyword', $keyword, 'placeholder="Cari berdasarkan Nomor Bon" class="form-control"'); ?>
                            </div>
                            <div class="col-12 col-lg-6">
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
                    <?php if ($book_transfer) : ?>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col" class="pl-4">No</th>
                                    <th scope="col" style="min-width:200px;">Nomor Bon</th>
                                    <th scope="col" style="min-width:200px;">Tujuan</th>
                                    <th scope="col" style="min-width:100px;">Tanggal</th>
                                    <th scope="col" style="min-width:200px;">Status</th>
                                    <th style="min-width:100px;"> </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($book_transfer as $book_transfer) : ?>
                                <tr class="text-center">
                                    <td class="align-middle pl-4"><?= ++$i; ?></td>
                                    <td class="text-left align-middle">
                                        <a href="<?= base_url('book_transfer/view/' . $book_transfer->book_transfer_id . ''); ?>"
                                            class="font-weight-bold">
                                            <?= highlight_keyword($book_transfer->transfer_number, $keyword); ?>
                                        </a>
                                    </td>
                                    <?php if ($book_transfer->destination=='library'): ?>
                                    <td class="align-middle">
                                        <?= $book_transfer->library_name; ?>
                                    </td>
                                    <?php else: ?>
                                    <td class="align-middle">
                                        <?= get_book_transfer_destination()[$book_transfer->destination]; ?>
                                    </td>
                                    <?php endif; ?>
                                    <td class="align-middle">
                                        <?= format_datetime($book_transfer->transfer_date); ?>
                                    </td>
                                    <td class="align-middle">
                                        <?= get_book_transfer_status()[$book_transfer->transfer_status ?? $book_transfer->status]; ?>
                                    </td>
                                    <td class="align-middle text-left">
                                        <?php if ($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_gudang' ) : ?>
                                        <a href="<?= base_url('book_transfer/edit/'.$book_transfer->book_transfer_id); ?>"
                                            class="btn btn-sm btn-secondary">
                                            <i class="fa fa-pencil-alt"></i>
                                            <span class="sr-only">Edit</span>
                                        </a>
                                        <?php endif?>
                                        <?php if($book_transfer->status!="finish") : ?>
                                            <?php if ($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_pemasaran' ) : ?>
                                            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal"
                                                data-target="#modal-hapus-<?= $book_transfer->book_transfer_id; ?>"><i
                                                    class="fa fa-trash-alt"></i><span class="sr-only">Delete</span></button>
                                            <div class="text-left">
                                                <div class="modal modal-alert fade"
                                                    id="modal-hapus-<?= $book_transfer->book_transfer_id; ?>" tabindex="-1"
                                                    role="dialog"
                                                    aria-labelledby="modal-hapus-"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    <i class="fa fa-exclamation-triangle text-red mr-1"></i>
                                                                    Konfirmasi Hapus
                                                                </h5>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Apakah anda yakin akan menghapus data pemindahan buku <span
                                                                        class="font-weight-bold"><?= $book_transfer->transfer_number; ?></span>?
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type ="button" class="btn btn-light" 
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-danger"
                                                                    onclick="location.href='<?= base_url('book_transfer/delete_book_transfer/'.$book_transfer->book_transfer_id); ?>'"
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