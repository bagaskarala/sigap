<?php
$level              = check_level();
$per_page           = 10;
$keyword            = $this->input->get('keyword');
$invoice_type       = $this->input->get('invoice_type');
$customer_type      = $this->input->get('customer_type');
$status             = $this->input->get('status');
$receipt            = $this->input->get('receipt');
$page               = $this->uri->segment(2);
$i                  = isset($page) ? $page * $per_page - $per_page : 0;
$confirm_invoice    = $this->session->flashdata('confirm_invoice');

if ($confirm_invoice) {
    $message_status2 = 'alert-confirm';
    $message2        = $confirm_invoice;
}

$invoice_type_options = array_merge([''  => '- Filter Kategori Faktur -'], get_invoice_type());

$customer_type_options = array_merge([''  => '- Filter Kategori Customer -'], get_customer_type());

$status_options = array_merge([''  => '- Filter Kategori Status Faktur -'], get_invoice_status());

$receipt_options = array_merge([''  => '- Filter Bukti Bayar Faktur -'], get_invoice_receipt());
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
                                <label for="invoice_type">Jenis Faktur</label>
                                <?= form_dropdown('invoice_type', $invoice_type_options, $invoice_type, 'id="invoice_type" class="form-control custom-select d-block" title="Invoice Type"'); ?>
                            </div>
                            <div class="col-12 col-md-4 mt-2">
                                <label for="customer_type">Jenis Customer</label>
                                <?= form_dropdown('customer_type', $customer_type_options, $customer_type, 'id="customer_type" class="form-control custom-select d-block" title="Customer Type"'); ?>
                            </div>
                            <div class="col-12 col-md-6 mt-2">
                                <label for="status">Status</label>
                                <?= form_dropdown('status', $status_options, $status, 'id="status" class="form-control custom-select d-block" title="Invoice Status"'); ?>
                            </div>
                            <div class="col-12 col-md-6 mt-2">
                                <label for="receipt">Bukti Bayar (akan menampilkan faktur selain showroom)</label>
                                <?= form_dropdown('receipt', $receipt_options, $receipt, 'id="receipt" class="form-control custom-select d-block" title="Receipt"'); ?>
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
                                    <button
                                        class="btn btn-success"
                                        type="submit"
                                        id="excel"
                                        name="excel"
                                        value="1"
                                    ><i class="fas fa-file-excel mr-2"></i>Export</button>
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
                                        <td class="align-middle ">
                                            <?php if ($lData->status == 'waiting') : ?>
                                                <div class="d-flex">
                                                    <button
                                                        class="btn btn-sm btn-success mr-1"
                                                        onclick="accept_invoice(<?= $lData->invoice_id ?>)"
                                                        title="Faktur disetujui"
                                                    >
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <button
                                                        class="btn btn-sm btn-danger mr-1"
                                                        onclick="decline_invoice(<?= $lData->invoice_id ?>)"
                                                        title="Faktur ditolak"
                                                    >
                                                        <i class="fa fa-ban"></i>
                                                    </button>
                                                    <a
                                                        title="Edit"
                                                        href="<?= base_url('invoice/edit/' . $lData->invoice_id . ''); ?>"
                                                        class="btn btn-sm btn-secondary"
                                                    >
                                                        <i class="fa fa-pencil-alt"></i>
                                                        <span class="sr-only">Edit</span>
                                                    </a>
                                                </div>
                                            <?php elseif ($lData->status == 'preparing_finish') : ?>
                                                <!-- Faktur Selesai Diproses -->
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-secondary font-weight-bold w-100"
                                                    data-toggle="modal"
                                                    data-target="#modal-finish-invoice-<?= $lData->invoice_id ?>"
                                                >Selesai</button>
                                                <!-- Modal -->
                                                <div
                                                    class="modal fade"
                                                    id="modal-finish-invoice-<?= $lData->invoice_id ?>"
                                                    role="dialog"
                                                    aria-hidden="true"
                                                >
                                                    <div
                                                        class="modal-dialog modal-dialog-centered"
                                                        role="document"
                                                    >
                                                        <div class="modal-content text-left">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Selesai Transaksi Faktur?</h5>
                                                            </div>
                                                            <div class="modal-body">
                                                                <b> Pastikan jumlah buku yang diambil bagian pemasaran sesuai dengan pesanan faktur! </b> <br>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button
                                                                    type="button"
                                                                    class="btn btn-secondary"
                                                                    data-dismiss="modal"
                                                                >Close</button>
                                                                <button
                                                                    data-dismiss="modal"
                                                                    type="button"
                                                                    class="btn btn-primary"
                                                                    onclick="finishInvoice(<?= $lData->invoice_id ?>)"
                                                                >Selesai</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <script>
                                                function finishInvoice(id) {
                                                    $.ajax({
                                                        type: "POST",
                                                        url: '<?= base_url("invoice/action/"); ?>' + id + '/finish',
                                                        success: function(res) {
                                                            showToast(true, res.data);
                                                            location.reload();
                                                        },
                                                        error: function(err) {
                                                            showToast(false, err.responseJSON.message);
                                                        },
                                                        complete: function(data) {
                                                            console.log(data);
                                                        }
                                                    });
                                                }
                                                </script>
                                            <?php endif ?>
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
$(document).ready(function() {
    <?php if (isset($message_status2)) : ?>
        let status2 = '<?= $message_status2; ?>';
        if (status2 == 'alert-confirm') {
            toastr.success('<?= $message2; ?>');
        }
    <?php endif ?>
    $('#flashmessage').delay(2000).hide(0);

})

const baseUrl = '<?= base_url() ?>'

function accept_invoice(invoiceId) {
    const acceptUrl = `${baseUrl}/invoice/action/${invoiceId}/confirm`
    if (window.confirm('Apakah anda yakin akan menyetujui faktur ini?')) {
        location.href = acceptUrl
    }
}

function decline_invoice(invoiceId) {
    const declineUrl = `${baseUrl}/invoice/action/${invoiceId}/cancel`
    if (window.confirm('Apakah anda yakin akan menolak faktur ini?')) {
        location.href = declineUrl
    }
}
</script>
