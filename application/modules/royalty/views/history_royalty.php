<?php
$level              = check_level();
$per_page           = 10;
$keyword            = $this->input->get('keyword');
$date_year          = $this->input->get('date_year');
$start_date         = $this->input->get('start_date');
$period_end         = $this->input->get('end_date');
$page               = $this->uri->segment(3);
$i                  = isset($page) ? $page * $per_page - $per_page : 0;

for ($dy = intval(date('Y')); $dy >= 2015; $dy--) {
    $date_year_options[$dy] = $dy;
}

?>

<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item active">
                <a href="<?= base_url('royalty'); ?>">Royalti</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">
                    Riwayat</a>
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
                        <?= form_open(base_url('royalty/history/'), ['method' => 'GET']); ?>
                        <div class="row">
                            <div class="col-12 col-md-4 mt-2">
                                <label for="per_page">Data per halaman</label>
                                <?= form_dropdown('per_page', get_per_page_options(), $per_page, 'id="per_page" class="form-control custom-select d-block" title="List per page"'); ?>
                            </div>
                            <div class="col-12 col-md-4 mt-2">
                                <label for="date_end">Tanggal Awal Periode</label>
                                <input
                                    type="date"
                                    id="start_date"
                                    name="start_date"
                                    class="form-control dates"
                                    value="<?= $start_date ?>"
                                    class="form-control custom-select d-block"
                                    min="2021-01-01"
                                >
                            </div>
                            <div class="col-12 col-md-4 mt-2">
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
                                        onclick="location.href = '<?= base_url('royalty/history/'); ?>'"
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
                        <h5>Riwayat Royalti</h5>
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
                                    style="width:15%;"
                                >Nama</th>
                                <th
                                    scope="col"
                                    style="width:15%;"
                                >Tanggal Mulai Periode</th>
                                <th
                                    scope="col"
                                    style="width:15%;"
                                >Tanggal Akhir Periode</th>
                                <th
                                    scope="col"
                                    style="width:15%;"
                                >Status</th>
                                <th
                                    scope="col"
                                    style="width:20%;"
                                >Tanggal Dibayar</th>
                                <th
                                    scope="col"
                                    style="width:15%;"
                                >Total</th>
                                <th
                                    scope="col"
                                    style="width:30%;"
                                > Detail </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($royalty_history as $lData) : ?>
                                <tr class="text-center">
                                    <td class="align-middle pl-4">
                                        <?= ++$i; ?>
                                    </td>
                                    <td class="text-left align-middle">
                                        <a
                                            href="<?= base_url("royalty/view_detail/$lData->royalty_id"); ?>"
                                            class="font-weight-bold"
                                        >
                                            <?= highlight_keyword($lData->author_name, $keyword); ?>
                                        </a>
                                    </td>
                                    <td class="align-middle"><?= $lData->start_date ? date("d F Y", strtotime($lData->start_date)) : '' ?></td>
                                    <td class="align-middle"><?= $lData->end_date ? date("d F Y", strtotime($lData->end_date)) : '' ?></td>
                                    <td class="align-middle"><?= get_royalty_status()[$lData->status] ?></td>
                                    <td class="align-middle"><?= $lData->paid_date ? date("d F Y", strtotime($lData->paid_date)) : '' ?></td>
                                    <td class="align-middle">Rp <?= number_format($lData->details->earned_royalty,  0, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <a
                                            type="button btn-success"
                                            class="btn btn-primary float-right"
                                            href="<?= base_url('royalty/view_detail/' . $lData->royalty_id) ?>"
                                        >Detail</a>
                                    </td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?= $pagination ?? null; ?>
                </div>
            </section>
        </div>
    </div>
</div>
