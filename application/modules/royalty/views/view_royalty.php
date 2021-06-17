<?php
$level              = check_level();
$start_date         = $this->input->get('start-date');
$end_date           = $this->input->get('due-date');
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
<?php $yesterday = date('Y-m-d', strtotime("-1 days"));
$selected_date = $yesterday;
if ($end_date != null) {
    $selected_date = $end_date;
}

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
                                        <td class="text-center align-middle"><?= date("d F Y", strtotime($latest_royalty->start_date)) ?? ''; ?> </td>
                                        <td class="text-center align-middle"><?= date("d F Y", strtotime($latest_royalty->end_date)) ?? '' ?></td>
                                        <td class="text-center align-middle">Rp <?= number_format($latest_royalty->details->earned_royalty, 0, ',', '.') ?? 0; ?></td>
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
                    <?php if (strtotime($current_start_date) > strtotime($yesterday)) : ?>
                        <div
                            class="alert alert-info alert-dismissible fade show"
                            role="alert"
                        >
                            <h5>Info</h5>
                            <div>Semua royalti hingga saat ini sudah diajukan/dibayar.</div>
                        </div>
                    <?php endif ?>
                    <div
                        id="form-royalty"
                        class="<?php if (strtotime($current_start_date) > strtotime($yesterday)) {
                                    echo 'd-none';
                                } ?>"
                    >
                        <?= form_open(base_url('royalty/view/' . $author->author_id), ['method' => 'GET']); ?>
                        <div class="form-group row">
                            <div class="col-12 col-md-5 mt-2">
                                <label
                                    for="last-paid-date"
                                    class="font-weight-bold"
                                >
                                    Tanggal Awal Periode yang Akan Diajukan</label>
                                <small
                                    id="error-null-start-date"
                                    class="d-none error-message text-danger"
                                >Tanggal mulai periode wajib diisi!</small>
                                <small
                                    id="error-invalid-range"
                                    class="d-none error-message text-danger"
                                >Tanggal mulai periode tidak bisa melebihi tanggal akhir periode!</small>

                                <?php if ($latest_royalty == NULL) : //Baru pertama kali
                                ?>
                                    <input
                                        id="start-date"
                                        name="start-date"
                                        class="form-control dates"
                                        value="<?= $start_date ?>"
                                    />
                                <?php else : ?>
                                    <input
                                        id="start-date"
                                        name="start-date"
                                        class="form-control dates d-none"
                                        value="<?= $current_start_date ?>"
                                    />
                                    <input
                                        type="text"
                                        class="form-control"
                                        value="<?= date("d", strtotime($current_start_date)) . " " . $month[intval(date("m", strtotime($current_start_date))) - 1] . " " . date("Y", strtotime($current_start_date)) ?>"
                                        readonly
                                    />
                                <?php endif ?>
                            </div>
                            <div class="col-12 col-md-5 mt-2">
                                <label
                                    for="due-date"
                                    class="font-weight-bold"
                                >
                                    Tanggal Akhir Periode yang Akan Diajukan</label>
                                <div class="input-group mb-3">
                                    <input
                                        name="due-date"
                                        id="due-date"
                                        class="form-control dates"
                                        value="<?= $selected_date ?>"
                                    />
                                </div>
                            </div>
                            <div class="col-12 col-md-2 mt-2">
                                <label>&nbsp;</label>
                                <div
                                    class="btn-group btn-block"
                                    role="group"
                                    aria-label="Filter button"
                                >
                                    <button
                                        class="btn btn-primary"
                                        type="submit"
                                        value="Submit"
                                    ><i class="fa fa-filter"></i> Filter</button>
                                </div>

                            </div>
                        </div>
                        <?= form_close(); ?>
                        <div class="<?php if ($start_date == NULL) {
                                        echo 'd-block';
                                    } else {
                                        echo 'd-none';
                                    }
                                    ?>">
                            <div
                                class="alert alert-info alert-dismissible fade show"
                                role="alert"
                            >
                                <h5>Info</h5>
                                <div>Silakan filter tanggal awal dan akhir pembayaran royalti untuk menampilkan data royalti</div>
                            </div>
                        </div>
                        <div class="<?php if ($start_date == NULL) {
                                        echo 'd-none';
                                    } ?>">
                            <div
                                id="changed_date"
                                style="display: none;"
                            >
                                <div
                                    class="alert alert-info alert-dismissible fade show"
                                    role="alert"
                                >
                                    <h5>Info</h5>
                                    <div>Tanggal periode royalti telah diubah. Silakan filter ulang untuk menampilkan data royalti yang baru.</div>
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
                                            <td class="text-right pr-5">Rp <?= number_format($royalty->total_sales, 0, ',', '.'); ?></td>
                                            <td class="text-right pr-5">Rp <?= number_format($royalty->earned_royalty, 0, ',', '.'); ?></td>
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
                                            <b>Rp <?= number_format($total_sales, 0, ',', '.'); ?></b>
                                        </td>
                                        <td class="text-right pr-5">
                                            <b>Rp <?= number_format($total_royalty, 0, ',', '.'); ?></b>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <?php if (!$pending_royalty) : ?>
                                <button
                                    type="button"
                                    class="btn btn-primary mt-3 float-right ml-3"
                                    id="pay-royalty"
                                ><?= $button_label; ?></button>
                            <?php endif ?>


                        </div>
                    </div>
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
        minDate: '<?= $latest_royalty->end_date ?? '' ?>',
        maxDate: new Date().fp_incr(-1)
    });

    $('#pay-royalty').on("click", function() {
        $('#modal-confirm').modal('toggle')
    })

    $('#confirm-royalty').on("submit", function(e) {
        e.preventDefault();
        var receipt = $('#receipt').val()
        var end_date = $('#due-date').val()
        var start_date = $('#start-date').val()
        $.ajax({
            type: "POST",
            url: "<?= base_url("royalty/pay"); ?>",
            data: {
                start_date: start_date,
                end_date: end_date,
                author_id: "<?= $author->author_id ?>",
                receipt: receipt
            },
            success: function(result) {
                var response = $.parseJSON(result)
                //Validation Error
                if (response.status != true) {
                    console.log(response)
                    $(".error-message").addClass('d-none');
                    alert("Input tidak valid! Pastikan tanggal awal periode tidak kosong dan tidak melebihi tanggal akhir periode")
                    for (var i = 0; i < response.input_error.length; i++) {
                        // Show error message
                        $('#' + response.input_error[i]).removeClass('d-none');
                    }
                } else {
                    location.href = "<?= base_url('royalty'); ?>";
                }
            },
            error: function(req, err) {
                console.log(err)
            }
        });
    })
})

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
    $('#changed_date').show()
})

$('#start-date').change(function() {
    $('#changed_date').show()
})
</script>
