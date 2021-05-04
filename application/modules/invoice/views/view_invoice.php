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
            <?php //=isset($input->draft_id) ? form_hidden('draft_id', $input->draft_id) : ''; ?>
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
                                    <td><strong><?= $invoice->number ?></strong> </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Tipe </td>
                                    <td><?= get_invoice_type()[$invoice->type]; ?></td>
                                </tr>
                                <tr>
                                    <td width="200px"> Nama Customer </td>
                                    <td><?= $invoice->customer->name ?></td>
                                </tr>
                                <tr>
                                    <td width="200px"> Nomor Customer </td>
                                    <td><?= $invoice->customer->phone_number ?></td>
                                </tr>
                                <tr>
                                    <td width="200px"> Tanggal Jatuh Tempo </td>
                                    <td><?= $invoice->due_date ?></td>
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
                                    <td><?= $invoice->issued_date ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <td class="align-middle" width="200px"> Status </td>
                                    <td class="align-middle"><?= get_invoice_status()[$invoice->status]; ?></td>
                                    <td class="align-middle text-right"><button class="btn btn-outline-primary" data-toggle="collapse" href="#collapse1">Detail</btn></td>
                                </tr>
                            </tbody>
                        </table>
                        <div id="collapse1" class="panel-collapse collapse">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <td class="align-middle" width="200px"> Tanggal Konfirmasi </td>
                                        <td class="align-middle"><?= $invoice->confirm_date ?></td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle" width="200px"> Tanggal Mulai Diproses </td>
                                        <td class="align-middle"><?= $invoice->preparing_start_date ?></td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle" width="200px"> Tanggal Selesai Diproses </td>
                                        <td class="align-middle"><?= $invoice->preparing_end_date ?></td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle" width="200px"> Tanggal Diambil Pemasaran </td>
                                        <td class="align-middle"><?= $invoice->finish_date ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr>
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
                                Penulis
                            </td>
                            <td class="align-middle">
                                Rp <?= $invoice_book->price ?>
                            </td>
                            <td class="align-middle">
                                <?= $invoice_book->qty ?>
                            </td>
                            <td class="align-middle">
                                <?= $invoice_book->discount ?> %
                            </td>
                            <td class="align-middle">
                                Rp <?= $invoice_book->price * $invoice_book->qty * (1 - $invoice_book->discount/100) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>