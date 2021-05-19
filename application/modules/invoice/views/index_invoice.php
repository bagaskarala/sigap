<?php
$level              = check_level();
$per_page           = 10;
$keyword            = $this->input->get('keyword');
$invoice_type       = $this->input->get('invoice_type');
$customer_type      = $this->input->get('customer_type');
$status             = $this->input->get('status');
$page               = $this->uri->segment(2);
$i                  = isset($page) ? $page * $per_page - $per_page : 0;


$invoice_type_options = array_merge([''  => '- Filter Kategori Faktur -'], get_invoice_type());

$customer_type_options = array_merge([''  => '- Filter Kategori Customer -'], get_customer_type());

$status_options = array_merge([''  => '- Filter Kategori Status Faktur -'], get_invoice_status());

function generate_invoice_action($invoice_id)
{
    return html_escape('
    <div class="list-group list-group-bordered" style="margin: -9px -15px;border-radius:0;">
      <a href="' . base_url("invoice/action/{$invoice_id}/confirm") . '" class="list-group-item list-group-item-action p-2">
        <div class="list-group-item-figure">
        <div class="tile bg-success">
        <span class="fa fa-check"></span>
        </div>
        </div>
        <div class="list-group-item-body"> Setuju </div>
      </a>
      <a href="' . base_url("invoice/action/{$invoice_id}/cancel") . '" class="list-group-item list-group-item-action p-2">
        <div class="list-group-item-figure">
        <div class="tile bg-danger">
        <span class="fa fa-ban"></span>
        </div>
        </div>
        <div class="list-group-item-body"> Tolak </div>
      </a>
    </div>
    ');
}
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
                        <div
                            class="alert alert-info alert-dismissible fade show"
                            role="alert"
                        >
                            <h5>Info</h5>
                            <p class="m-0">Klik tombol <button class="btn btn-sm btn-secondary"><i class="fa fa-thumbs-up"></i>
                                    Aksi</button> untuk menyetujui atau menolak faktur
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
                                <label for="invoice_type">Jenis Faktur</label>
                                <?= form_dropdown('invoice_type', $invoice_type_options, $invoice_type, 'id="invoice_type" class="form-control custom-select d-block" title="Invoice Type"'); ?>
                            </div>
                            <div class="col-12 col-md-3 mt-2">
                                <label for="customer_type">Jenis Customer</label>
                                <?= form_dropdown('customer_type', $customer_type_options, $customer_type, 'id="customer_type" class="form-control custom-select d-block" title="Customer Type"'); ?>
                            </div>
                            <div class="col-12 col-md-3 mt-2">
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
                                        style="width:15%;"
                                    >Jenis</th>
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
                                        style="width:10%;"
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
                                    > &nbsp; </th>
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
                                            <?= get_invoice_type()[$lData->invoice_type]; ?>
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
                                            <?= $lData->due_date ? date("d/m/y", strtotime($lData->due_date)) : '-'; ?>
                                        </td>
                                        <td class="align-middle pr-4">
                                            <?= get_invoice_status()[$lData->status]; ?>
                                        </td>
                                        <td class="align-middle text-right d-flex">
                                            <?php if ($lData->status == 'waiting') : ?>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-secondary"
                                                    data-container="body"
                                                    data-toggle="popover"
                                                    data-placement="left"
                                                    data-html="true"
                                                    data-content="<?= generate_invoice_action($lData->invoice_id); ?>"
                                                    data-trigger="focus"
                                                    style="margin-right:5px;"
                                                >
                                                    <i class="fa fa-thumbs-up">Aksi</i>
                                                </button>
                                                <a
                                                    title="Edit"
                                                    href="<?= base_url('invoice/edit/' . $lData->invoice_id . ''); ?>"
                                                    class="btn btn-sm btn-secondary"
                                                >
                                                    <i class="fa fa-pencil-alt"></i>
                                                    <span class="sr-only">Edit</span>
                                                </a>
                                            <?php endif; ?>
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
