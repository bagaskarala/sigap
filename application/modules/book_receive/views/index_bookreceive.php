<?php
$level              = check_level();
$per_page           = $this->input->get('per_page') ?? 10;
$published_year     = $this->input->get('published_year');
// $bookshelf_location = $this->input->get('bookshelf_location');
// $book_stock_total   = $this->input->get('book_stock_total');
$keyword            = $this->input->get('keyword');
$book_receive_status = $this->input->get('book_receive_status');
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
                <a class="text-muted">Penerimaan Buku</a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Penerimaan Buku </h1>
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
                            <div class="col-12 col-md-4 mb-4">
                                <label for="per_page">Data per halaman</label>
                                <?= form_dropdown('per_page', get_per_page_options(), $per_page, 'id="per_page" class="form-control custom-select d-block" title="List per page"'); ?>
                            </div>
                            <div class="col-12 col-md-4 mb-4">
                                <label for="category">Tahun Terbit</label>
                                <?= form_dropdown('published_year', get_published_date(), $published_year, 'id="published_year" class="form-control custom-select d-block" title="Filter Tahun Terbit"'); ?>
                            </div>
                            <!-- <div class="col-12 col-md-3 mb-3">
                                <label for="category">Lokasi Rak</label>
                                <?//= form_dropdown('bookshelf_location', get_bookshelf_location(), $bookshelf_location, 'id="bookshelf_location" class="form-control custom-select d-block" title="Lokasi Rak"'); ?>
                            </div> -->
                            <div class="col-12 col-md-4 mb-4">
                                <label for="category">Status</label>
                                <?= form_dropdown('book_receive_status', get_book_receive_status(), $book_receive_status, 'id="book_receive_status" class="form-control custom-select d-block" title="Status"'); ?>
                            </div>
                            <div class="col-12 col-md-8">
                                <label for="status">Pencarian</label>
                                <?= form_input('keyword', $keyword, 'placeholder="Cari berdasarkan Nama atau Kode Buku" class="form-control"'); ?>
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
                    <?php if ($book_receives) : ?>
                    <table class="table table-striped mb-0 table-responsive">
                        <thead>
                            <tr>
                                <th scope="col" class="pl-4 align-middle text-center" rowspan="2">No</th>
                                <th scope="col" style="min-width:350px;" class="align-middle text-center" rowspan="2">
                                    Judul</th>
                                <th scope="col" style="min-width:150px;" class="align-middle text-center">
                                    Nomor Order</th>
                                <th scope="col" style="min-width:100px;" class="align-middle text-center" >
                                    Jumlah Order</th>
                                <th scope="col" style="min-width:100px;" class="align-middle text-center" >
                                    Jumlah Tercetak</th>
                                <th scope="col" style="min-width:100px;" class="align-middle text-center" >
                                    Tanggal Mulai</th>
                                <th scope="col" style="min-width:100px;" class="align-middle text-center" >
                                    Tanggal Selesai</th>
                                <th scope="col" style="min-width:100px;" class="align-middle text-center" >
                                    Deadline</th>
                                <th scope="col" style="min-width:100px;" class="align-middle text-center">
                                    Status</th>
                                <?php if ($level == 'superadmin') : ?>
                                <th style="min-width:150px;" class="align-middle text-center" rowspan="2"> Aksi </th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($book_receives as $book_receive) : ?>
                            <tr>
                                <td class="align-middle text-center"><?= ++$i; ?></td>
                                <td class="align-middle">
                                    <a href="<?= base_url('book_receive/view/' . $book_receive->book_receive_id . ''); ?>"
                                        class="font-weight-bold">
                                        <?= highlight_keyword($book_receive->book_title, $keyword); ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?= $book_receive->order_number; ?></td>
                                </td>
                                <td class="align-middle text-center">
                                    <?=$book_receive->total; ?></td>
                                </td>
                                <td class="align-middle text-center">
                                    <?=$book_receive->total_postprint; ?></td>
                                </td>
                                <td class="align-middle text-center">
                                    <?= format_datetime($book_receive->entry_date); ?></td>
                                </td>
                                <td class="align-middle text-center">
                                    <?= format_datetime($book_receive->finish_date); ?></td>
                                </td>
                                <td class="align-middle text-center">
                                    <?= deadline_color($book_receive->deadline, $book_receive->book_receive_status); ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?= get_book_receive_status()[$book_receive->book_receive_status]?></td>
                                </td>
                                <?php if ($level == 'superadmin') : ?>
                                <td style="min-width: 130px" class="align-middle text-center">
                                    <a href="<?= base_url('book_receive/edit/' . $book_receive->book_receive_id . ''
                                    ); ?>" class="btn btn-sm btn-secondary" title="Edit Stok Buku">
                                        <i class="fa fa-pencil-alt"></i>
                                        <span class="sr-only">Edit Stok Buku</span>
                                    </a>
                                    <button title="Delete" type="button" class="btn btn-sm btn-danger"
                                        data-toggle="modal" data-target="#modal-hapus-<?= $book_receive->book_receive_id; ?>"><i
                                            class="fa fa-trash-alt"></i><span class="sr-only">Delete</span></button>
                                    <div class="text-left">
                                        <div class="modal modal-alert fade"
                                            id="modal-hapus-<?= $book_receive->book_receive_id; ?>" tabindex="-1" role="dialog"
                                            aria-labelledby="modal-hapus" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"><i
                                                                class="fa fa-exclamation-triangle text-red mr-1"></i>
                                                            Konfirmasi Hapus</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Apakah anda yakin akan menghapus data penerimaan buku <span
                                                                class="font-weight-bold"><?= $book_receive->book_title; ?></span>?
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light"
                                                            data-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-danger"
                                                            onclick="location.href='<?= base_url('book_receive/delete/' . $book_receive->book_receive_id . ''); ?>'"
                                                            data-dismiss="modal">Hapus</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <?php endif?>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                    <?php else : ?>
                    <p class="text-center my-5">Data tidak tersedia</p>
                    <?php endif; ?>
                    <?= $pagination ?? null; ?>
                </div>
            </section>
        </div>
    </div>
</div>