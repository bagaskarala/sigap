<?php
$level              = check_level();
$per_page           = 10;
$keyword            = $this->input->get('keyword');
$print_category     = $this->input->get('print_category');
$print_type         = $this->input->get('print_type');
$print_priority     = $this->input->get('print_priority');
$progress_status    = $this->input->get('progress_status');
$page               = $this->uri->segment(2);
$i                  = isset($page) ? $page * $per_page - $per_page : 0;

$print_category_options = [
    ''  => '- Filter Kategori Cetak -',
    '0' => 'Cetak Baru',
    '1' => 'Cetak Ulang'
];

$print_type_options = [
    ''  => '- Filter Tipe Cetak -',
    '0' => 'Cetak POD',
    '1' => 'Cetak Offset'
];

$print_priority_options = [
    ''  => '- Filter Prioritas Cetak -',
    '0' => 'Prioritas Rendah',
    '1' => 'Prioritas Sedang',
    '2' => 'Prioritas Tinggi'
];

$print_status_options = [
    ''  => '- Filter Status Cetak -',
    '0' => 'Belum di Proses',
    '1' => 'Proses Pracetak',
    '2' => 'Proses Cetak',
    '3' => 'Proses Jilid',
    '4' => 'Proses Finalisasi',
    '5' => 'Ditolak',
    '6' => 'Selesai'
];
?>

<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item active">
                <a class="text-muted">Order Cetak</a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Order Cetak </h1>
            <span class="badge badge-info">Total : <?= $total; ?></span>
        </div>
        <a
            href="<?= base_url("$pages/add"); ?>"
            class="btn btn-primary btn-sm"
        ><i class="fa fa-plus fa-fw"></i> Tambah</a>
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
                            <div class="col-12 col-md-3 mb-2">
                                <label for="per_page">Data per halaman</label>
                                <?= form_dropdown('per_page', get_per_page_options(), $per_page, 'id="per_page" class="form-control custom-select d-block" title="List per page"'); ?>
                            </div>
                            <div class="col-12 col-md-3 mb-3">
                                <label for="print_category">Kategori Cetak</label>
                                <?= form_dropdown('print_category', $print_category_options, $print_category, 'id="print_category" class="form-control custom-select d-block" title="Filter Kategori Cetak"'); ?>
                            </div>
                            <div class="col-12 col-md-3 mb-3">
                                <label for="print_type">Tipe Cetak</label>
                                <?= form_dropdown('print_type', $print_type_options, $print_type, 'id="print_type" class="form-control custom-select d-block" title="Filter Tipe Cetak"'); ?>
                            </div>
                            <div class="col-12 col-md-3 mb-3">
                                <label for="print_priority">Prioritas Cetak</label>
                                <?= form_dropdown('print_priority', $print_priority_options, $print_priority, 'id="print_priority" class="form-control custom-select d-block" title="Filter Prioritas Cetak"'); ?>
                            </div>
                            <div class="col-12 col-md-3">
                                <label for="progress_status">Status</label>
                                <?= form_dropdown('progress_status', $print_status_options, $progress_status, 'id="progress_status" class="form-control custom-select d-block" title="Filter Status Cetak"'); ?>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="status">Pencarian</label>
                                <?= form_input('keyword', $keyword, 'placeholder="Cari berdasarkan Judul, Kategori, Tema, atau Penulis" class="form-control"'); ?>
                            </div>
                            <div class="col-12 col-lg-3">
                                <label>&nbsp;</label>
                                <div
                                    class="btn-group btn-block"
                                    role="group"
                                    aria-label="Filter button"
                                >
                                    <button
                                        class="btn btn-secondary"
                                        type="button"
                                        onclick="location.href = '<?= base_url($pages); ?>'"
                                    > Reset</button>
                                    <button
                                        class="btn btn-primary"
                                        type="submit"
                                        value="Submit"
                                    ><i class="fa fa-filter"></i> Filter</button>
                                </div>
                            </div>
                        </div>
                        <?= form_close(); ?>
                    </div>
                    <?php if ($printing) : ?>
                        <table class="table table-striped mb-0 table-responsive">
                            <thead>
                                <tr class="text-center">
                                    <th
                                        scope="col"
                                        class="pl-4"
                                    >No</th>
                                    <th
                                        scope="col"
                                        style="min-width:300px;"
                                    >Judul</th>
                                    <th
                                        scope="col"
                                        style="min-width:150px;"
                                    >Nomor Order</th>
                                    <th
                                        scope="col"
                                        style="min-width:150px;"
                                    >Jumlah Cetak</th>
                                    <th
                                        scope="col"
                                        style="min-width:150px;"
                                    >Edisi</th>
                                    <th
                                        scope="col"
                                        style="min-width:150px;"
                                    >Tipe Cetak</th>
                                    <th
                                        scope="col"
                                        style="min-width:150px;"
                                    >Prioritas</th>
                                    <th
                                        scope="col"
                                        style="min-width:200px;"
                                    >Tanggal Masuk</th>
                                    <th
                                        scope="col"
                                        style="min-width:200px;"
                                    >Status</th>
                                    <?php if($level == 'superadmin'): ?>
                                    <th style="min-width:100px;"> &nbsp; </th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($printing as $pData) : ?>
                                    <tr class="text-center">
                                        <td class="align-middle pl-4"><?= ++$i; ?></td>
                                        <td class="text-left align-middle">
                                            <a
                                                href="<?= base_url('printing/view/' . $pData->print_id . ''); ?>"
                                                class="font-weight-bold"
                                            >
                                                <?= ($pData->print_category == 1) ? '<span class="badge badge-warning"><i class="fa fa-redo" data-toggle="tooltip" title="Cetak Ulang"></i></span>' : ''; ?>
                                                <?= highlight_keyword($pData->book_title, $keyword); ?>
                                            </a>
                                        </td>
                                        <td class="align-middle">
                                            <?= highlight_keyword($pData->order_number, $keyword); ?>
                                        </td>
                                        <td class="align-middle">
                                            <?= $pData->print_total; ?>
                                        </td>
                                        <td class="align-middle">
                                            <?= highlight_keyword($pData->print_edition, $keyword); ?>
                                        </td>
                                        <td class="align-middle">
                                            <?php
                                                if($pData->print_type == 0){echo 'POD';}
                                                elseif($pData->print_type == 1){echo 'Offset';}
                                                else{echo '';}
                                            ?>
                                        </td>
                                        <td class="align-middle">
                                            <?php
                                                if($pData->print_priority == 0){echo 'Rendah';}
                                                elseif($pData->print_priority == 1){echo 'Sedang';}
                                                elseif($pData->print_priority == 2){echo 'Tinggi';}
                                                else{echo '';}
                                            ?>
                                        </td>
                                        <td class="align-middle">
                                            <?= date('d F Y H:i:s', strtotime($pData->entry_date)); ?>
                                        </td>
                                        <td class="align-middle">
                                            <?php
                                                if($pData->progress_status == 0){echo 'Belum di proses';}
                                                elseif($pData->progress_status == 1){echo 'Proses Pracetak';}
                                                elseif($pData->progress_status == 2){echo 'Proses Cetak';}
                                                elseif($pData->progress_status == 3){echo 'Proses Jilid';}
                                                elseif($pData->progress_status == 4){echo 'Proses Finalisasi';}
                                                elseif($pData->progress_status == 5){echo 'Ditolak';}
                                                elseif($pData->progress_status == 6){echo 'Selesai';}
                                                else{'';}
                                            ?>
                                        </td>
                                        <?php if($level == 'superadmin'): ?>
                                        <td class="align-middle">
                                            <a
                                                href="<?= base_url('printing/edit/'.$pData->print_id); ?>"
                                                class="btn btn-sm btn-secondary"
                                            >
                                                <i class="fa fa-pencil-alt"></i>
                                                <span class="sr-only">Edit</span>
                                            </a>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-danger"
                                                data-toggle="modal"
                                                data-target="#modal-hapus-<?= $pData->print_id; ?>"
                                            ><i class="fa fa-trash-alt"></i><span class="sr-only">Delete</span></button>
                                            <div class="text-left">
                                                <div
                                                    class="modal modal-alert fade"
                                                    id="modal-hapus-<?= $pData->print_id; ?>"
                                                    tabindex="-1"
                                                    role="dialog"
                                                    aria-labelledby="modal-hapus-<?= $pData->print_id; ?>"
                                                    aria-hidden="true"
                                                >
                                                    <div
                                                        class="modal-dialog"
                                                        role="document"
                                                    >
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    <i class="fa fa-exclamation-triangle text-red mr-1"></i> Konfirmasi
                                                                    Hapus</h5>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Apakah anda yakin akan menghapus order cetak <span class="font-weight-bold"><?= $pData->book_title; ?></span>?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button
                                                                    type="button"
                                                                    class="btn btn-danger"
                                                                    onclick="location.href='<?= base_url('printing/delete_printing/'.$pData->print_id); ?>'"
                                                                    data-dismiss="modal"
                                                                >Hapus</button>
                                                                <button
                                                                    type="button"
                                                                    class="btn btn-light"
                                                                    data-dismiss="modal"
                                                                >Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p class="text-center">Data tidak tersedia</p>
                    <?php endif; ?>
                    <?= $pagination ?? null; ?>
                </div>
            </section>
        </div>
    </div>
</div>