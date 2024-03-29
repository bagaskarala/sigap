<?php
$total_weight = 0;
$grand_total = 0;
foreach ($proforma_books as $pb) {
    $total_weight +=  $pb->weight * $pb->qty;
    $grand_total += $pb->price * $pb->qty * (1 - $pb->discount / 100);
}
?>
<header class="page-title-bar mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('proforma'); ?>">Proforma</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">
                    <?= $proforma->number ?></a>
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
                <!-- book-data -->
                <div
                    class="tab-pane fade active show"
                    id="logistic-data"
                >
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <td width="200px"> Nomor Faktur </td>
                                    <td><strong><?= $proforma->number ?></strong> </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Nama Customer </td>
                                    <td><?= $proforma->customer->name ?></td>
                                </tr>
                                <tr>
                                    <td width="200px"> Nomor Customer </td>
                                    <td><?= $proforma->customer->phone_number ?></td>
                                </tr>
                                <tr>
                                    <td width="200px"> Tanggal Jatuh Tempo </td>
                                    <td><?= $proforma->due_date ?> <?= $proforma->is_expired ? '- <em class="text-danger">Expired</em>' : '' ?></td>
                                </tr>
                                <tr>
                                    <td width="200px"> Total Berat </td>
                                    <td><?= $total_weight ?> gram</td>
                                </tr>
                                <tr>
                                    <td width="200px"> Ongkir </td>
                                    <td>Rp <?= number_format($proforma->delivery_fee, 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <td width="200px"> Tanggal dibuat </td>
                                    <td><?= $proforma->issued_date ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
                        <?php foreach ($proforma_books as $proforma_book) : ?>
                            <?php $i++; ?>
                            <tr class="text-center">
                                <td class="align-middle pl-4">
                                    <?= $i ?>
                                </td>
                                <td class="text-left align-middle">
                                    <?= $proforma_book->book_title ?>
                                </td>
                                <td class="align-middle">
                                    Penulis
                                </td>
                                <td class="align-middle">
                                    Rp <?= number_format($proforma_book->price, 0, ',', '.') ?>
                                </td>
                                <td class="align-middle">
                                    <?= $proforma_book->qty ?>
                                </td>
                                <td class="align-middle">
                                    <?= $proforma_book->discount ?> %
                                </td>
                                <td class="align-middle">
                                    Rp <?= number_format($proforma_book->price * $proforma_book->qty * (1 - $proforma_book->discount / 100), 0, ',', '.'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="text-align:center;">
                            <td colspan="4"></td>
                            <td colspan="2"><b>Grand Total</b></td>
                            <td>Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
                <br>

                <?php if (!$proforma->is_expired) : ?>
                    <div
                        id="card-button"
                        class="d-flex justify-content-end"
                    >
                        <button
                            type="button"
                            class="btn btn-primary mr-2"
                            data-toggle="modal"
                            data-target="#modal-generate-invoice"
                        >Buat Faktur</button>
                        <!-- Modal -->
                        <div
                            class="modal fade"
                            id="modal-generate-invoice"
                            role="dialog"
                            aria-hidden="true"
                        >
                            <div
                                class="modal-dialog modal-dialog-centered"
                                role="document"
                            >
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Ubah proforma jadi faktur?</h5>
                                    </div>
                                    <div class="modal-footer">
                                        <button
                                            type="button"
                                            class="btn btn-secondary"
                                            data-dismiss="modal"
                                        >Close</button>
                                        <a
                                            id="btn-modal-generate-invoice"
                                            href="<?= base_url("proforma/action/$proforma->proforma_id/confirm") ?>"
                                            class="btn btn-primary"
                                        >
                                            Confirm
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a
                            href="<?= base_url('proforma/edit/' . $proforma->proforma_id) ?>"
                            class="btn btn-outline-primary mr-2"
                            title="Edit"
                        >Edit <i class="fas fa-edit fa-fw"></i></a>
                        <a
                            href="<?= base_url('proforma/generate_pdf/' . $proforma->proforma_id) ?>"
                            class="btn btn-outline-danger"
                            id="btn-generate-pdf"
                            title="Generate PDF"
                            target="_blank"
                        >Generate PDF <i class="fas fa-file-pdf fa-fw"></i></a>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </section>
</div>
