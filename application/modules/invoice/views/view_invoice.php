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
                <a href="<?= base_url('invoice'); ?>">Faktur</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">
                    <?= $invoice->number ?></a>
            </li>
        </ol>
    </nav>
</header>

<div class="page-section">
    <section
        id="data-invoice"
        class="card"
    >
        <div class="card-body">
            <?php //=isset($input->draft_id) ? form_hidden('draft_id', $input->draft_id) : ''; 
            ?>
            <div>
                <!-- book-data -->
                <div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <td width="200px"> Nomor Faktur </td>
                                    <td><strong><?= $invoice->number ?></strong> </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Tipe </td>
                                    <td><?= get_invoice_type()[$invoice->type]; ?></td>
                                </tr>
                                <tr>
                                    <td width="200px"> Nama Customer </td>
                                    <td><?= $invoice->customer->name ?? 'Umum' ?></td>
                                </tr>
                                <tr>
                                    <td width="200px"> Nomor Customer </td>
                                    <td><?= $invoice->customer->phone_number ?? '-' ?></td>
                                </tr>
                                <?php if ($invoice->type != 'showroom') : ?>
                                    <tr>
                                        <td width="200px"> Tanggal Jatuh Tempo </td>
                                        <td><?= $invoice->due_date ?></td>
                                    </tr>
                                    <tr>
                                        <td width="200px"> Total Berat </td>
                                        <td><?= $invoice->total_weight / 1000 ?> kg</td>
                                    </tr>
                                    <tr>
                                        <td width="200px"> Total Ongkir </td>
                                        <td>Rp <?= number_format($invoice->delivery_fee, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td width="200px"> Bukti Bayar </td>
                                        <td><?= $invoice->receipt ?></td>
                                    </tr>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <td width="200px"> Tanggal dibuat </td>
                                    <td><?= $invoice->issued_date ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <?php if ($invoice->type != 'showroom') : ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <td
                                            class="align-middle"
                                            width="200px"
                                        > Status </td>
                                        <td class="align-middle"><?= get_invoice_status()[$invoice->status]; ?></td>
                                        <td class="align-middle text-right"><button
                                                class="btn btn-outline-primary"
                                                data-toggle="collapse"
                                                href="#collapse1"
                                            >Detail</btn>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div
                                id="collapse1"
                                class="panel-collapse collapse"
                            >
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <td
                                                class="align-middle"
                                                width="200px"
                                            > Tanggal Konfirmasi </td>
                                            <td class="align-middle"><?= $invoice->confirm_date ?></td>
                                        </tr>
                                        <tr>
                                            <td
                                                class="align-middle"
                                                width="200px"
                                            > Tanggal Mulai Diproses </td>
                                            <td class="align-middle"><?= $invoice->preparing_start_date ?></td>
                                        </tr>
                                        <tr>
                                            <td
                                                class="align-middle"
                                                width="200px"
                                            > Tanggal Selesai Diproses </td>
                                            <td class="align-middle"><?= $invoice->preparing_end_date ?></td>
                                        </tr>
                                        <tr>
                                            <td
                                                class="align-middle"
                                                width="200px"
                                            > Tanggal Diambil Pemasaran </td>
                                            <td class="align-middle"><?= $invoice->finish_date ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr>
                    <?php endif ?>
                </div>

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
                                style="width:25%;"
                            >Nama Penulis</th>
                            <th
                                scope="col"
                                style="width:15%;"
                            >Harga</th>
                            <th
                                scope="col"
                                style="width:10%;"
                            >Jumlah</th>
                            <th
                                scope="col"
                                style="width:5%;"
                            >Diskon</th>
                            <th
                                scope="col"
                                style="width:15%;"
                            >Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 0; ?>
                        <?php foreach ($invoice_books as $invoice_book) : ?>
                            <?php $i++; ?>
                            <tr class="text-center">
                                <td class="align-middle pl-4">
                                    <?= $i ?>
                                </td>
                                <td class="text-left align-middle">
                                    <?= $invoice_book->book_title ?>
                                </td>
                                <td class="align-middle">
                                    <?= $invoice_book->author_name ?>
                                </td>
                                <td class="align-middle">
                                    Rp <?= number_format($invoice_book->price, 0, ',', '.') ?>
                                </td>
                                <td class="align-middle">
                                    <?= $invoice_book->qty ?>
                                </td>
                                <td class="align-middle">
                                    <?= $invoice_book->discount ?> %
                                </td>
                                <td class="align-middle">
                                    Rp <?= number_format($invoice_book->price * $invoice_book->qty * (1 - $invoice_book->discount / 100), 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php
                        $total = 0;
                        foreach ($invoice_books as $invoice_book) {
                            $total += $invoice_book->price * $invoice_book->qty * (1 - $invoice_book->discount / 100);
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr style="text-align:center;">
                            <td colspan="4"></td>
                            <td colspan="2"><b>Grand Total</b></td>
                            <td>Rp <?= number_format($total, 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
                <br>
                <?php if ($invoice->status != 'waiting' && $invoice->status != 'cancel') : ?>
                    <div
                        id="card-button"
                        class="d-flex justify-content-end"
                    >
                        <!-- Faktur Selesai Diproses -->
                        <?php if ($invoice->status == 'preparing_finish') : ?>
                            <button
                                type="button"
                                class="btn btn-primary mr-2"
                                data-toggle="modal"
                                data-target="#modal-finish-invoice"
                            >Selesai</button>
                            <!-- Modal -->
                            <div
                                class="modal fade"
                                id="modal-finish-invoice"
                                role="dialog"
                                aria-hidden="true"
                            >
                                <div
                                    class="modal-dialog modal-dialog-centered"
                                    role="document"
                                >
                                    <div class="modal-content">
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
                                                id="btn-modal-finish-invoice"
                                                data-dismiss="modal"
                                                type="button"
                                                class="btn btn-primary"
                                            >Selesai</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                            $(document).ready(function() {
                                $('#data-invoice').on('click', '#btn-modal-finish-invoice', function() {
                                    $.ajax({
                                        type: "POST",
                                        url: "<?= base_url("invoice/action/$invoice->invoice_id/finish"); ?>",
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
                                })
                            })
                            </script>
                        <?php endif ?>
                        <button
                            onclick="check_delivery()"
                            class="btn btn-outline-danger"
                        >
                            Generate PDF<i class="fas fa-file-pdf fa-fw"></i>
                        </button>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </section>
</div>
<div
    class="modal modal-alert fade"
    id="modal-delivery"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modal-delivery"
    aria-hidden="true"
>
    <div
        class="modal-dialog"
        role="document"
    >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Masukkan Ongkos Kirim</h5>
            </div>
            <div class="modal-body">
                <div class="my-2">
                    Silakan masukkan ongkir dan bukti bayar terlebih dahulu!
                </div>
                <div class="my-2">
                    Total Berat: <b><?= $invoice->total_weight / 1000 ?></b> kg
                </div>
                <div class="my-2">
                    Alamat Customer:
                </div>
                <div class="mb-2">
                    <?= $invoice->customer->address ?? '-' ?>
                </div>
                <div class="form-group">
                    <label
                        for="delivery_fee"
                        class="font-weight-bold"
                    >
                        Ongkos Kirim
                        <abbr title="Required">*</abbr>
                    </label>
                    <input
                        type="number"
                        name="delivery_fee"
                        id="delivery"
                        min=0
                        required
                        class="form-control"
                        value="<?= $invoice->delivery_fee ?>"
                    />
                </div>
                <div class="form-group">
                    <label for="receipt">
                        <b>Bukti Bayar</b> <br>
                        (Masukkan informasi bukti bayar konsumen seperti nama pembayar, nama bank, dll)
                        <abbr title="Required">*</abbr>
                    </label>
                    <input
                        type="text"
                        name="receipt"
                        id="receipt"
                        required
                        class="form-control"
                        value="<?= $invoice->receipt ?>"
                    />
                </div>
            </div>
            <div class="modal-footer">
                <button
                    type="submit"
                    class="btn btn-primary"
                    onclick="save_delivery_fee()"
                >Save</button>
                <button
                    type="button"
                    class="btn btn-light"
                    data-dismiss="modal"
                >Close</button>
            </div>
        </div>
    </div>
</div>
<script>
function check_delivery() {
    var delivery_fee = "<?= $invoice->delivery_fee ?>";
    var receipt = "<?= $invoice->receipt ?>"
    if (delivery_fee == '' || receipt == '') {
        $("#modal-delivery").modal()
    } else {
        location.href = "<?= base_url("invoice/generate_pdf/" . $invoice->invoice_id); ?>"
    }
}

function save_delivery_fee() {
    if ($("#delivery").val() == '') {
        alert("Ongkir wajib diisi!");
    } else if ($("#receipt").val() == '') {
        alert("Bukti Pembayaran wajib diisi!")
    } else {
        var delivery_fee = $("#delivery").val()
        var receipt = $("#receipt").val()
        $.ajax({
            type: "POST",
            url: "<?= base_url('invoice/update_delivery_fee/' . $invoice->invoice_id); ?>",
            data: {
                delivery_fee: delivery_fee,
                receipt: receipt
            },
            success: function(res) {
                var response = $.parseJSON(res)
                //Validation Error
                if (response.status == true) {
                    $("#modal-delivery").modal('toggle')
                    location.href = "<?= base_url("invoice/generate_pdf/" . $invoice->invoice_id); ?>"
                }
            },
            error: function(err) {
                console.log(err)
            },
        })

    }
}
</script>
