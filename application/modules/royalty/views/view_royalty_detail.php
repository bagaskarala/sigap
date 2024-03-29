<?php
$level          = check_level();
$startDate      = $this->input->get('start_date');
$endDate        = $this->input->get('end_date');
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
                <a href="<?= base_url("royalty/view/$author->author_id"); ?>"><?= $author->author_name ?></a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">
                    Detail Royalti
                </a>
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
            <div class="tab-content">
                <div class="text-center">
                    <h4>Detail Royalti</h4>
                    <h6><?= $author->author_name ?></h6>
                    <p>Periode <b><?= date("d F Y", strtotime($royalty->start_date)) ?></b> hingga <b><?= date("d F Y", strtotime($royalty->end_date)) ?></b></p>
                </div>
                <div class="my-4">
                    <p>Status: <b><?= get_royalty_status()[$royalty->status] ?></b></p>
                    <p><b><?= $royalty->receipt ?></b></p>
                </div>
                <hr>
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
                        <?php foreach ($royalty_details as $lData) : ?>
                            <tr>
                                <td class="text-center"><?= $index + 1; ?></td>
                                <td class="text-left"><?= $lData->book_title; ?></td>
                                <td class="text-center"><?= $lData->sold_books; ?></td>
                                <td class="text-right pr-5">Rp <?= number_format($lData->total_sales, 0, ',', '.'); ?></td>
                                <td class="text-right pr-5">Rp <?= number_format($lData->earned_royalty, 0, ',', '.'); ?> (<?= $lData->royalty ?>%)</td>
                            </tr>
                            <?php $index++;
                            $total_sales += $lData->total_sales;
                            $total_royalty += $lData->earned_royalty; ?>
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
                <div class="d-flex d-flex justify-content-end mt-3">
                    <?php if ($royalty->status == 'requested') : ?>
                        <button
                            type="button"
                            class="btn btn-primary text-right mr-3"
                            data-toggle="modal"
                            data-target="#modal-confirm"
                        >Bayar</button>
                        <button
                            type="button"
                            class="btn btn-danger text-right mr-3"
                            data-toggle="modal"
                            data-target="#modal-cancel"
                        >Batalkan</button>
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
                                        <h5 class="modal-title">Konfirmasi Pembayaran</h5>
                                    </div>
                                    <form
                                        id="confirm-royalty"
                                        method="post"
                                    >
                                        <p class="mt-3 mx-3">
                                            Apakah Anda yakin akan membayar royalty periode
                                            <b><?= date("d F Y", strtotime($royalty->start_date)) ?></b>
                                            hingga
                                            <b><?= date("d F Y", strtotime($royalty->end_date)) ?></b>
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
                        <div
                            class="modal modal-warning fade"
                            id="modal-cancel"
                            tabindex="-1"
                            role="dialog"
                            aria-labelledby="modal-cancel"
                            aria-hidden="true"
                        >
                            <div
                                class="modal-dialog"
                                role="document"
                            >
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Pembatalan</h5>
                                    </div>
                                    <p class="mt-3 mx-3">
                                        Apakah Anda yakin akan membatalkan pengajuan royalty periode
                                        <br>
                                        <b><?= date("d F Y", strtotime($royalty->start_date)) ?></b>
                                        hingga
                                        <b><?= date("d F Y", strtotime($royalty->end_date)) ?></b>
                                        atas nama
                                        <b><?= $author->author_name ?></b>?
                                    </p>
                                    <div class="modal-footer">
                                        <a
                                            href="<?= base_url('royalty/cancel/' . $royalty->royalty_id) ?>"
                                            type="submit"
                                            class="btn btn-primary"
                                        >Confirm</a>
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
                        $('#confirm-royalty').on("submit", function(e) {
                            e.preventDefault();
                            var paid_date = new Date()
                            paid_date.setDate(paid_date.getDate() - 1)
                            paid_date = paid_date.toISOString().slice(0, 10)
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
                                    location.href = "<?= base_url('royalty'); ?>"
                                },
                                error: function(req, err) {
                                    console.log(err)
                                }
                            });
                        })
                        </script>
                    <?php endif ?>
                    <a
                        href="<?= base_url('royalty/generate_pdf/' . $royalty->royalty_id); ?>"
                        class="btn btn-outline-danger mr-3"
                        id="btn-generate-pdf"
                        title="Generate PDF"
                    >Generate PDF <i class="fas fa-file-pdf fa-fw"></i></a>
                    <a
                        href="<?= base_url('royalty/generate_pdf/' . $royalty->royalty_id . '/author'); ?>"
                        class="btn btn-outline-danger mr-3"
                        id="btn-generate-pdf-author"
                        title="Generate PDF Author"
                    >Generate PDF Penulis<i class="fas fa-file-pdf fa-fw"></i></a>
                    <a
                        type="button"
                        class="btn btn-secondary"
                        href="<?= base_url('royalty/view/' . $author->author_id) ?>"
                    >Kembali</a>
                </div>
            </div>
        </div>
    </section>
</div>
