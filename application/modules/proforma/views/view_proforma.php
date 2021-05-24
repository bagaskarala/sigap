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
            <?php //=isset($input->draft_id) ? form_hidden('draft_id', $input->draft_id) : ''; 
            ?>
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
                                    <td><?= $proforma->due_date ?></td>
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
                                    Rp <?= $proforma_book->price ?>
                                </td>
                                <td class="align-middle">
                                    <?= $proforma_book->qty ?>
                                </td>
                                <td class="align-middle">
                                    <?= $proforma_book->discount ?> %
                                </td>
                                <td class="align-middle">
                                    Rp <?= $proforma_book->price * $proforma_book->qty * (1 - $proforma_book->discount / 100) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <br>

                <div
                    id="card-button"
                    class="d-flex justify-content-end"
                >
                    <a
                        href="<?= base_url('proforma/generate_pdf/' . $proforma->proforma_id) ?>"
                        class="btn btn-outline-danger float-right"
                        id="btn-generate-pdf"
                        title="Generate PDF"
                    >Generate PDF <i class="fas fa-file-pdf fa-fw"></i></a>
                </div>


            </div>
        </div>
    </section>
</div>
