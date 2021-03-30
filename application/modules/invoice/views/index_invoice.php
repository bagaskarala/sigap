<?php
$level              = check_level();
$per_page           = 10;
$keyword            = $this->input->get('keyword');
$type               = $this->input->get('type');
$status             = $this->input->get('status');
$page               = $this->uri->segment(2);
$i                  = isset($page) ? $page * $per_page - $per_page : 0;


$type_options = [
    ''  => '- Filter Kategori Faktur -',
    'credit' => 'Kredit',
    'cash' => 'Tunai',
    'online' => 'Online',
    'showroom' => 'Showroom'
];

$status_options = [
    ''                  => '- Filter Status Faktur -',
    'waiting'           => 'Belum Konfirmasi',
    'confirm'           => 'Sudan Konfirmasi',
    'preparing_start'   => 'Diproses',
    'preparing_end'     => 'Siap Diambil',
    'finish'            => 'Selesai',
    'cancel'            => 'Dibatalkan'
];
?>


<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item active">
                <a class="text-muted">Faktur</a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Faktur </h1>
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
                            <div class="col-12 col-md-4 mt-2">
                                <label for="per_page">Data per halaman</label>
                                <?= form_dropdown('per_page', get_per_page_options(), $per_page, 'id="per_page" class="form-control custom-select d-block" title="List per page"'); ?>
                            </div>
                            <div class="col-12 col-md-4 mt-2">
                                <label for="type">Jenis</label>
                                <?= form_dropdown('type', $type_options, $type, 'id="type" class="form-control custom-select d-block" title="Invoice Type"'); ?>
                            </div>
                            <div class="col-12 col-md-4 mt-2">
                                <label for="status">Status</label>
                                <?= form_dropdown('status', $status_options, $status, 'id="status" class="form-control custom-select d-block" title="Invoice Status"'); ?>
                            </div>
                            <div class="col-12 col-md-8 mt-2">
                                <label for="status">Pencarian</label>
                                <?= form_input('keyword', $keyword, 'placeholder="Cari berdasarkan Nama, Tipe, Kategori" class="form-control"'); ?>
                            </div>
                            <div class="col-12 col-md-4 mt-2">
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
                    <?php if ($total == 0) : ?>
                        <p class="text-center">Data tidak tersedia</p>
                    <?php else : ?>
                        <table class="table table-striped mb-0 table-responsive">
                            <thead>
                                <tr class="text-center">
                                    <th
                                        scope="col"
                                        style="width:5%;"
                                        class="pl-4"
                                    >No</th>
                                    <th
                                        scope="col"
                                        style="width:30%;"
                                    >Nomor Faktur</th>
                                    <th
                                        scope="col"
                                        style="width:20%;"
                                    >Jenis</th>
                                    <th
                                        scope="col"
                                        style="width:15%;"
                                    >Tanggal Dibuat</th>
                                    <th
                                        scope="col"
                                        style="width:15%;"
                                    >Jatuh Tempo</th>
                                    <th
                                        scope="col"
                                        style="width:20%;"
                                        class="pr-4"
                                    >Status</th>
                                    <th
                                        scope="col"
                                        style="width:20%;"
                                        class="pr-4"
                                    >&nbsp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoice as $lData) : ?>
                                    <tr class="text-center">
                                        <td class="align-middle pl-4">
                                            <?= ++$i; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a
                                                href="<?= base_url("$pages/view/$lData->invoice_id"); ?>"
                                                class="font-weight-bold"
                                            >
                                                <?= highlight_keyword($lData->number, $keyword); ?>
                                            </a>
                                        </td>
                                        <td class="align-middle">
                                            <?= get_invoice_type()[$lData->type]; ?>
                                        </td>
                                        <td class="align-middle">
                                            <?= date("d/m/y", strtotime($lData->issued_date)); ?>
                                        </td>
                                        <td class="align-middle">
                                            <?= date("d/m/y", strtotime($lData->due_date)); ?>
                                        </td>
                                        <td class="align-middle pr-4">
                                            <?= get_invoice_status()[$lData->status]; ?>
                                        </td>
                                        <td class="align-middle pr-4">
                                            <a
                                                href="<?= base_url('invoice/edit/' . $lData->invoice_id . ''); ?>"
                                                class="btn btn-sm btn-secondary"
                                            >
                                                <i class="fa fa-pencil-alt"></i>
                                                <span class="sr-only">Edit</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    <?= $pagination ?? null; ?>
                </div>
            </section>
        </div>
    </div>
</div>
