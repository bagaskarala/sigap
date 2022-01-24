<?php
$level              = check_level();
$per_page           = 10;
$keyword            = $this->input->get('keyword');
$customer_type      = $this->input->get('customer_type');
$page               = $this->uri->segment(2);
$i                  = isset($page) ? $page * $per_page - $per_page : 0;

$customer_type_options = [
    ''  => '- Filter Kategori Customer -',
    'distributor' => 'Distributor',
    'reseller' => 'Reseller',
    'author' => 'Penulis',
    'member' => 'Member',
    'general' => 'Umum'
];
?>


<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item active">
                <a class="text-muted">Proforma</a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Proforma </h1>
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
                        <div
                            class="alert alert-info alert-dismissible fade show"
                            role="alert"
                        >
                            <h5>Info</h5>
                            <p class="m-0">Klik tombol <button class="btn btn-sm btn-secondary"><i class="fa fa-thumbs-up"></i>
                                    Aksi</button> untuk membuat faktur tunai atau menghapus proforma
                            </p>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="alert"
                                aria-label="Close"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?= form_open($pages, ['method' => 'GET']); ?>
                        <div class="row">
                            <div class="col-12 col-md-3 mt-2">
                                <label for="per_page">Data per halaman</label>
                                <?= form_dropdown('per_page', get_per_page_options(), $per_page, 'id="per_page" class="form-control custom-select d-block" title="List per page"'); ?>
                            </div>
                            <div class="col-12 col-md-3 mt-2">
                                <label for="customer_type">Jenis Customer</label>
                                <?= form_dropdown('customer_type', $customer_type_options, $customer_type, 'id="customer_type" class="form-control custom-select d-block" title="Customer Type"'); ?>
                            </div>
                            <div class="col-12 col-md-3 mt-2">
                                <label for="status">Pencarian</label>
                                <?= form_input('keyword', $keyword, 'placeholder="Cari berdasarkan Nomor, Nama Customer" class="form-control"'); ?>
                            </div>
                            <div class="col-12 col-md-3 mt-2">
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
                        <table class="table table-striped mb-0 table-fluid">
                            <thead>
                                <tr class="text-center">
                                    <th
                                        scope="col"
                                        style="width:5%;"
                                        class="pl-4"
                                    >No</th>
                                    <th
                                        scope="col"
                                        style="width:25%;"
                                    >Nomor Proforma</th>
                                    <th
                                        scope="col"
                                        style="width:10%;"
                                    >Customer</th>
                                    <th
                                        scope="col"
                                        style="width:15%;"
                                    >Member</th>
                                    <th
                                        scope="col"
                                        style="width:20%;"
                                    >Tanggal Dibuat</th>
                                    <th
                                        scope="col"
                                        style="width:20%;"
                                    >Jatuh Tempo</th>
                                    <th
                                        scope="col"
                                        style="width:15%;"
                                        class="pr-4"
                                    > &nbsp; </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($proforma as $lData) : ?>
                                    <tr class="text-center">
                                        <td class="align-middle pl-4">
                                            <?= ++$i; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a
                                                href="<?= base_url("$pages/view/$lData->proforma_id"); ?>"
                                                class="font-weight-bold"
                                            >
                                                <?= highlight_keyword($lData->number, $keyword); ?>
                                            </a>
                                        </td>
                                        <td class="align-middle">
                                            <?= highlight_keyword($lData->customer_name, $keyword); ?>
                                        </td>
                                        <td class="align-middle">
                                            <?= get_customer_type()[$lData->customer_type]; ?>
                                        </td>
                                        <td class="align-middle">
                                            <?= date("d/m/y", strtotime($lData->issued_date)); ?>
                                        </td>
                                        <td class="align-middle">
                                            <?= date("d/m/y", strtotime($lData->due_date)); ?>
                                        </td>
                                        <td class="align-middle text-right d-flex">
                                            <button
                                                class="btn btn-sm btn-success mr-1"
                                                onclick="accept_proforma(<?= $lData->proforma_id ?>)"
                                                title="Ubah proforma menjadi faktur"
                                            >
                                                <i class="fa fa-check"></i>
                                            </button>
                                            <button
                                                class="btn btn-sm btn-danger mr-1"
                                                onclick="decline_proforma(<?= $lData->proforma_id ?>)"
                                                title="Hapus proforma"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            <a
                                                title="Edit"
                                                href="<?= base_url('proforma/edit/' . $lData->proforma_id . ''); ?>"
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

<script>
const baseUrl = '<?= base_url() ?>'

function accept_proforma(proformaId) {
    const acceptUrl = `${baseUrl}/proforma/action/${proformaId}/confirm`
    if (window.confirm('Apakah anda yakin akan membuat faktur dari proforma ini?')) {
        location.href = acceptUrl
    }
}

function decline_proforma(proformaId) {
    const declineUrl = `${baseUrl}/proforma/action/${proformaId}/cancel`
    if (window.confirm('Apakah anda yakin akan menghapus proforma ini?')) {
        location.href = declineUrl
    }
}
</script>
