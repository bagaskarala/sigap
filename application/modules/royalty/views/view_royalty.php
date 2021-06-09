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
                <a href="<?= base_url('royalty'); ?>">Royalti</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">
                    <?= $author->author_name ?></a>
            </li>
        </ol>
    </nav>
</header>
<?php $selected_date = date('Y-m-d', strtotime("-1 days"));
if ($period_end != null) {
    $selected_date = $period_end;
}
$url = '';
if ($period_end == null) $url = '';
else $url = '/' . $period_end;
$month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
if ($latest_royalty == NULL) {
    $button_label = 'Ajukan Royalti';
    $pending_royalty = false;
} else {
    if ($latest_royalty->status == 'requested') {
        $button_label = 'Konfirmasi Pembayaran';
        $pending_royalty = true;
    } else {
        $button_label = 'Ajukan Royalti';
        $pending_royalty = false;
    }
}
?>
<div class="page-section">
    <section class="card">
        <header class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a
                        class="nav-link active show"
                        data-toggle="tab"
                        href="#request-royalty"
                    ><i class="fa fa-info-circle"></i> Ajukan Royalti </a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link"
                        data-toggle="tab"
                        href="#history-royalty"
                    ><i class="fa fa-user-tie"></i> Riwayat Royalti </a>
                </li>
            </ul>
        </header>
        <div class="card-body">
            <div class="tab-content">
                <div
                    id="request-royalty"
                    class="tab-pane fade active show"
                >
                    <?php if ($pending_royalty) : ?>
                        <div
                            class="alert alert-info alert-dismissible fade show"
                            role="alert"
                        >
                            <h5>Info</h5>
                            <div id="confirm_notif"></div>
                        </div>
                        <hr>
                        <div class="mt-3">
                            <h6 class="mb-3">Royalti yang belum dibayar</h6>
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th
                                            scope="col"
                                            style="width:20%;"
                                        >Tanggal Mulai Periode</th>
                                        <th
                                            scope="col"
                                            style="width:20%;"
                                        >Tanggal Akhir Periode</th>
                                        <th
                                            scope="col"
                                            style="width:15%;"
                                        >Total</th>
                                        <th
                                            scope="col"
                                            style="width:15%;"
                                        >Detail</th>
                                        <th
                                            scope="col"
                                            style="width:15%;"
                                        >Bayar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center align-middle"><?= date("d F Y", strtotime($latest_royalty->start_date)) ?? '1 Januari 2021'; ?> </td>
                                        <td class="text-center align-middle"><?= date("d F Y", strtotime($latest_royalty->end_date)) ?? '' ?></td>
                                        <td class="text-center align-middle">Rp <?= round($latest_royalty->details->earned_royalty, 0) ?? 0; ?></td>
                                        <td class="text-center">
                                            <a
                                                type="button btn-success"
                                                class="btn btn-primary text-center"
                                                href="<?= base_url('royalty/view_detail/' . $latest_royalty->royalty_id) ?>"
                                            >Detail</a>
                                        </td>
                                        <td class="text-center">
                                            <button
                                                type="button"
                                                class="btn btn-primary text-center"
                                                id="pay-royalty"
                                            >Bayar</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <br>
                    <?php endif ?>
                    
                    <h5 class="mb-3">Form Pengajuan Royalti</h5>
                    <div class="form-group row">
                        <div class="col-12 col-md-6 mt-2">
                            <label
                                for="last-paid-date"
                                class="font-weight-bold"
                            >
                                Akhir Periode Royalti Sebelumnya</label>
                            <!-- <?php if ($pending_royalty) : ?> -->
                            <input
                                type="date"
                                id="last-paid-date"
                                class="form-control dates d-none"
                                value="<?= $latest_royalty->end_date ?>"
                            />
                            <input
                                type="text"
                                class="form-control"
                                value="<?= date("d", strtotime($latest_royalty->end_date)) . " " . $month[intval(date("m", strtotime($latest_royalty->end_date))) - 1] . " " . date("Y", strtotime($latest_royalty->end_date)) ?>"
                                readonly
                            />
                            <!-- <?php else : ?> -->
                            <input
                                type="date"
                                id="last-paid-date"
                                class="form-control dates d-none"
                                value="<?= $last_paid_date ?>"
                            />
                            <input
                                type="text"
                                class="form-control"
                                value="<?= date("d", strtotime($last_paid_date)) . " " . $month[intval(date("m", strtotime($last_paid_date))) - 1] . " " . date("Y", strtotime($last_paid_date)) ?>"
                                readonly
                            />
                            <!-- <?php endif ?> -->
                        </div>
                        <div class="col-12 col-md-6 mt-2">
                            <label
                                for="due-date"
                                class="font-weight-bold"
                            >
                                Akhir Periode Royalti Saat Ini</label>
                            <div class="input-group mb-3">
                                <input
                                    name="due-date"
                                    id="due-date"
                                    class="form-control dates"
                                    value="<?= $selected_date ?>"
                                />
                            </div>
                        </div>
                    </div>
                    <div id="paid_period"></div>
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr class="text-center">
                                <th
                                    scope="col"
                                    style="width:2%;"
                                >No</th>
                                <th
                                    scope="col"
                                    style="width:30%;"
                                >Judul Buku</th>
                                <th
                                    scope="col"
                                    style="width:15%;"
                                >Jumlah Buku Terjual</th>
                                <th
                                    scope="col"
                                    style="width:15%;"
                                >Penjualan</th>
                                <th
                                    scope="col"
                                    style="width:15%;"
                                >Royalti</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $index = 0;
                            $total_sales = 0;
                            $total_royalty = 0; ?>
                            <?php foreach ($royalty_details as $royalty) : ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1; ?></td>
                                    <td class="text-left"><?= $royalty->book_title; ?></td>
                                    <td class="text-center"><?= $royalty->count; ?></td>
                                    <td class="text-right pr-5">Rp <?= $royalty->total_sales; ?></td>
                                    <td class="text-right pr-5">Rp <?= round($royalty->earned_royalty, 0); ?></td>
                                </tr>
                                <?php $index++;
                                $total_sales += $royalty->total_sales;
                                $total_royalty += $royalty->earned_royalty; ?>
                            <?php endforeach; ?>
                            <tr style="text-align:center;">
                                <td
                                    scope="col"
                                    class="align-middle"
                                    colspan="3"
                                >
                                    <b>Total</b>
                                </td>
                                <td class="text-right pr-5">
                                    <b>Rp <?= $total_sales; ?></b>
                                </td>
                                <td class="text-right pr-5">
                                    <b>Rp <?= $total_royalty; ?></b>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <?php if (!$pending_royalty) : ?>
                        <button
                            type="button"
                            class="btn btn-primary float-right ml-3"
                            id="pay-royalty"
                        ><?= $button_label; ?></button>
                    <?php endif ?>

                    <div
                        class="modal modal-warning fade"
                        id="modal-confirm"
                        tabindex="-1"
                        role="dialog"
                        aria-labelledby="modal-confirm"
                        aria-hidden="true"
                    >
                        <div
                            class="modal-dialog"
                            role="document"
                        >
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><?= $button_label; ?></h5>
                                </div>
                                <form
                                    id="confirm-royalty"
                                    method="post"
                                >
                                    <?php if ($pending_royalty) : ?>
                                        <p class="mt-3 mx-3">
                                            Apakah Anda yakin akan membayar royalty periode
                                            <b><?= date("d F Y", strtotime($latest_royalty->start_date)) ?? '1 January 2021' ?></b>
                                            hingga
                                            <b><?= date("d F Y", strtotime($latest_royalty->end_date)) ?></b>
                                            ?
                                        </p>
                                        <div class="form-group mx-3">
                                            <label
                                                for="receipt"
                                                class="font-weight-bold"
                                            >
                                                Masukkan Bukti Bayar
                                                <abbr title="Required">*</abbr>
                                            </label>
                                            <input
                                                type="text"
                                                name="receipt"
                                                id="receipt"
                                                class="form-control"
                                                required
                                            />
                                        </div>
                                    <?php else : ?>
                                        <p class="mt-3 mx-3">
                                            Apakah Anda yakin akan mengajukan royalty periode ini?
                                        </p>
                                    <?php endif ?>

                                    <div class="modal-footer">
                                        <button
                                            type="submit"
                                            class="btn btn-primary"
                                        >Confirm</button>
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                            data-dismiss="modal"
                                        >Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    id="history-royalty"
                    class="tab-pane fade"
                >
                    <div class="text-center mb-4">
                        <h4>Riwayat Royalti</h4>
                        <h6><?= $author->author_name ?></h6>
                    </div>
                    <div>
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th
                                        scope="col"
                                        style="width:2%;"
                                    >No</th>
                                    <th
                                        scope="col"
                                        style="width:20%;"
                                    >Tanggal Mulai Periode</th>
                                    <th
                                        scope="col"
                                        style="width:20%;"
                                    >Tanggal Akhir Periode</th>
                                    <th
                                        scope="col"
                                        style="width:15%;"
                                    >Status</th>
                                    <th
                                        scope="col"
                                        style="width:15%;"
                                    >Tanggal Dibayar</th>
                                    <th
                                        scope="col"
                                        style="width:15%;"
                                    >Total</th>
                                    <th
                                        scope="col"
                                        style="width:15%;"
                                    >Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $index = 0; ?>
                                <?php foreach ($royalty_history as $lData) : ?>
                                    <tr>
                                        <td class="text-center align-middle"><?= $index + 1; ?></td>
                                        <td class="text-center align-middle"><?= date("d F Y", strtotime($lData->start_date)) ?></td>
                                        <td class="text-center align-middle"><?= date("d F Y", strtotime($lData->end_date)) ?></td>
                                        <td class="text-center align-middle"><?= get_royalty_status()[$lData->status] ?></td>
                                        <td class="text-center align-middle"><?= $lData->paid_date ? date("d F Y", strtotime($lData->paid_date)) : '' ?></td>
                                        <td class="text-right align-middle">Rp <?= round($lData->details->earned_royalty, 0);; ?></td>
                                        <td class="text-center">
                                            <a
                                                type="button btn-success"
                                                class="btn btn-primary float-right"
                                                href="<?= base_url('royalty/view_detail/' . $lData->royalty_id) ?>"
                                            >Detail</a>
                                        </td>
                                    </tr>
                                    <?php $index++; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
$(document).ready(function() {
    <?php if ($pending_royalty) { ?>
        $('#paid_period').hide();
        var startPeriod = '<?= date("d", strtotime($latest_royalty->start_date)) . " " . $month[intval(date("m", strtotime($latest_royalty->start_date))) - 1] . " " . date("Y", strtotime($latest_royalty->start_date)) ?>'
        var endPeriod = '<?= date("d", strtotime($latest_royalty->end_date)) . " " . $month[intval(date("m", strtotime($latest_royalty->end_date))) - 1] . " " . date("Y", strtotime($latest_royalty->end_date)) ?>'
        $('#confirm_notif').html('<p>Silakan konfirmasi pembayaran royalti untuk periode <b>' + startPeriod + '</b> hingga <b>' + endPeriod + '</b> sebelum mengajukan royalty lagi</p>')
    <?php } ?>
    showPaidPeriod()

    const $flatpickr = $('.dates').flatpickr({
        altInput: true,
        altFormat: 'j F Y',
        dateFormat: 'Y-m-d',
        enableTime: false,
        minDate: '<?= $last_paid_date ?>',
        maxDate: new Date().fp_incr(-1)
    });

    $("#due-date").change(function() {
        filterDate()
    })

    $('#pay-royalty').on("click", function() {
        $('#modal-confirm').modal('toggle')
    })

    $('#confirm-royalty').on("submit", function() {
        var paid_date = "<?= $period_end; ?>"
        if (paid_date == "") {
            paid_date = new Date()
            paid_date.setDate(paid_date.getDate() - 1)
            paid_date = paid_date.toISOString().slice(0, 10)
        }
        var receipt = $('#receipt').val()
        $.ajax({
            type: "POST",
            url: "<?= base_url("royalty/pay"); ?>",
            data: {
                paid_date: paid_date,
                author_id: "<?= $author->author_id ?>",
                receipt: receipt
            },
            success: function(result) {
                var response = $.parseJSON(result)
                window.location = "<?= base_url('royalty'); ?>"
            },
            error: function(req, err) {
                console.log(err)
            }
        });
    })
})

function filterDate() {
    location.href = "<?= base_url('royalty/view/' . $author->author_id . '/'); ?>" + $('#due-date').val();
}

function showPaidPeriod() {
    var Month = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
    var dueDate = $('#due-date').val().split("-")
    var startDate = $('#last-paid-date').val()
    var endDate = $('#due-date').val()
    var stringDueDate = dueDate[2] + " " + Month[dueDate[1] - 1] + " " + dueDate[0]
    $('#paid_period').html("<p class='pl-1'>Periode royalti yang akan dibayarkan <b>" + '</b> hingga <b>' + stringDueDate + '</b></p>')

}

$('#due-date').change(function() {
    showPaidPeriod()
})
</script>
