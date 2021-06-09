<?php
$level              = check_level();
$per_page           = 10;
$keyword            = $this->input->get('keyword');
$date_year          = $this->input->get('date_year');
$period_end         = $this->input->get('end_date');
$page               = $this->uri->segment(2);
$i                  = isset($page) ? $page * $per_page - $per_page : 0;

for ($dy = intval(date('Y')); $dy >= 2015; $dy--) {
    $date_year_options[$dy] = $dy;
}

function royalti_action()
{
    return html_escape('
    <div class="list-group list-group-bordered" style="margin: -9px -15px;border-radius:0;">
      <a href="' . base_url("royalty/index") . '" class="list-group-item list-group-item-action p-2">
        <div class="list-group-item-figure">
        <div class="tile bg-success">
        <span class="fa fa-check"></span>
        </div>
        </div>
        <div class="list-group-item-body"> Sudah Dibayar </div>
      </a>
      <a href="' . base_url("royalty/index") . '" class="list-group-item list-group-item-action p-2">
        <div class="list-group-item-figure">
        <div class="tile bg-danger">
        <span class="fa fa-ban"></span>
        </div>
        </div>
        <div class="list-group-item-body"> Belum Dibayar </div>
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
                <a class="text-muted">Royalti</a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Royalti </h1>
            <span class="badge badge-info">Total : <?= $total ?></span>
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
                            <div class="col-12 col-md-6 mt-2">
                                <label for="per_page">Data per halaman</label>
                                <?= form_dropdown('per_page', get_per_page_options(), $per_page, 'id="per_page" class="form-control custom-select d-block" title="List per page"'); ?>
                            </div>
                            <div class="col-12 col-md-6 mt-2">
                                <label for="date_end">Tanggal Terakhir Periode</label>
                                <input
                                    type="date"
                                    id="end_date"
                                    name="end_date"
                                    class="form-control dates"
                                    value="<?= $period_end ?>"
                                    class="form-control custom-select d-block"
                                    min="2021-01-01"
                                    max="<?php
                                            echo date('Y-m-d', strtotime("-1 days"));
                                            ?>"
                                >
                            </div>
                            <div class="col-12 col-md-8 mt-2">
                                <label for="status">Pencarian</label>
                                <?= form_input('keyword', $keyword, 'placeholder="Cari berdasarkan Nama Penulis" class="form-control"'); ?>
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
                    </div><br>
                    <div class="text-center">
                        <h5>Daftar Penerima Royalti</h5>
                        <?php $url = '';
                        if ($period_end == null) $url = '';
                        else $url = '/' . $period_end; ?>
                    </div>
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
                                    style="width:35%;"
                                >Nama</th>
                                <!-- <th
                                    scope="col"
                                    style="width:15%;"
                                >NIP</th>
                                <th
                                    scope="col"
                                    style="width:15%;"
                                >Institusi</th> -->
                                <th
                                    scope="col"
                                    style="width:15%;"
                                >Jumlah Penjualan</th>
                                <th
                                    scope="col"
                                    style="width:15%;"
                                >Jumlah Royalti</th>
                                <th
                                    scope="col"
                                    style="width:20%;"
                                    class="pr-4"
                                >Tanggal Pembayaran Terakhir</th>
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
                            <?php foreach ($royalty as $lData) : ?>
                                <tr class="text-center">
                                    <td class="align-middle pl-4">
                                        <?= ++$i; ?>
                                    </td>
                                    <td class="text-left align-middle">
                                        <a
                                            href="<?= base_url("$pages/view/$lData->author_id" . $url); ?>"
                                            class="font-weight-bold"
                                        >
                                            <?= highlight_keyword($lData->author_name, $keyword); ?>
                                        </a>
                                    </td>
                                    <td class="text-right align-middle">
                                        Rp <?= number_format($lData->total_sales, 0, ',', '.'); ?>
                                    </td>
                                    <td class="text-right align-middle">
                                        Rp <?= number_format($lData->earned_royalty, 0, ',', '.'); ?>
                                    </td>
                                    <td><?= $lData->end_date ? date("d F Y", strtotime($lData->end_date)) : '' ?></td>
                                    <td><?= get_royalty_status()[$lData->status] ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr style="text-align:center;">
                                <td>&nbsp;</td>
                                <td
                                    scope="col"
                                    class="align-middle"
                                >
                                    <b>Total</b>
                                </td>
                                <td
                                    scope="col"
                                    class="text-right align-middle"
                                >
                                    <b>Rp <?= number_format($total_penjualan, 0, ',', '.'); ?></b>
                                </td>
                                <td
                                    scope="col"
                                    class="text-right align-middle"
                                >
                                    <b>Rp <?= number_format($total_royalty, 0, ',', '.'); ?></b>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                    <?= $pagination ?? null; ?>
                </div>
            </section>
        </div>
    </div>
</div>
<script>
// $(document).ready(function() {
//     var today = new Date();
//     today.setDate(today.getDate() - 1)
//     document.getElementById("end_date").setAttribute("max", today);
// });
</script>
