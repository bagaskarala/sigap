<?php
$level              = check_level();
?>
<!-- BREADCUMB,TITLE -->
<header class="page-title-bar mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('book_transfer'); ?>">Pemindahan Buku</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted"><?= $book_transfer->transfer_number; ?></a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center my-3">
        <div class="page-title mb-0 pb-0 h1"> Pemindahan Buku </div>
        <?php if($level == 'superadmin' || $level == 'admin_gudang') : ?>
        <a href="<?= base_url('book_transfer/edit/'.$book_transfer->book_transfer_id); ?>"
            class="btn btn-secondary btn-sm"><i class="fa fa-edit fa-fw"></i> Edit Pemindahan Buku</a>
        <?php endif?>
    </div>
</header>
<!-- BREADCUMB,TITLE -->

<!-- DETAIL -->
<section class="card" id="book-transfer">
    <header class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active show" data-toggle="tab" href="#book-transfer-data-wrapper"><i
                        class="fa fa-info-circle"></i> Detail Pemindahan Buku</a>
            </li>
        </ul>
    </header>

    <div class="card-body">
        <div class="tab-content">
            <!-- DATA INFO -->
            <div id="book-transfer-data-wrapper" class="tab-pane fade active show">
                <div id="book-transfer-data">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0 nowrap">
                            <tbody>
                                <tr>
                                    <td width="200px"> Nomor Pemindahan </td>
                                    <td>
                                        <?= $book_transfer->transfer_number;?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Peminta </td>
                                    <td><?= $book_transfer->requester?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Penerima </td>
                                    <td><?= $book_transfer->receiver?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Tanggal Pindah </td>
                                    <td><?= format_datetime($book_transfer->transfer_date)?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Tujuan Pemindahan </td>
                                    <td> <?= get_book_transfer_destination()[$book_transfer->destination]?>
                                    </td>
                                </tr>
                                <?php if ($book_transfer->destination == 'library') : ?>
                                <tr>
                                    <td width="200px"> Nama Perpustakaan </td>
                                    <td> <?= $book_transfer->library_name?>
                                    </td>
                                </tr>
                                <?php endif?>
                                <tr>
                                    <td width="200px"> Status </td>
                                    <td>
                                        <?= get_book_transfer_status()[$book_transfer->status]?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200px"> File Bon </td>
                                    <td>
                                        <a class="btn btn-outline-danger" id="bon" name="bon" target="_blank"
                                            href = "<?= base_url('/book_transfer/generate_pdf_bon/'.$book_transfer->book_transfer_id); ?>">
                                            <i class="fas fa-file-pdf mr-3" aria-hidden="true"></i>Download file PDF bon
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Buku yang Dipindahkan</td>
                                    <td>
                                        <table class="table table-striped mb-0 table-responsive">
                                            <tbody>
                                                <tr class="text-center">
                                                    <th scope="col">No</th>
                                                    <th scope="col" style="width:300px;">Judul Buku</th>
                                                    <th scope="col">Jumlah</th>
                                                </tr>
                                                <?php $i = 0; ?>
                                                <?php foreach ($book_transfer_list as $book) : ?>
                                                <?php $i++; ?>
                                                <tr>
                                                    <td class="align-middle text-center pl-4">
                                                        <?= $i ?>
                                                    </td>
                                                    <td>
                                                        <?= $book->book_title ?>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <?= $book->qty ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- DATA INFO -->
        </div>
    </div>
</section>
<!-- DETAIL -->

<!-- PREPARING -->
<hr class="my-3">
<?php
$is_preparing_started       = format_datetime($book_transfer->preparing_start_date);
$is_preparing_finished      = format_datetime($book_transfer->preparing_end_date);
$is_preparing_deadline_set  = format_datetime($book_transfer->preparing_deadline);
$staff_gudang               = $this->book_transfer->get_staff_gudang_by_id($book_transfer->book_transfer_id);
?>
<?php if($level == 'superadmin' || $level == 'admin_gudang') :?>
<section id="preparing-progress-wrapper" class="card">
    <div id="preparing-progress">
        <header class="card-header">
            <div class="d-flex align-items-center"><span class="mr-auto">Penyiapan Buku</span>
                <?php  if (!$is_preparing_finished &&  ($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_gudang')) :
                        //modal select staff
                        $this->load->view('book_transfer/view/common/input_staff_modal', [
                            'progress' => 'preparing',
                            'staff_gudang' => $staff_gudang
                        ]);
                    ?>
                <div class="card-header-control">
                    <button id="btn-start-preparing" title="Mulai proses preparing" type="button" class="d-inline btn 
                        <?= !$is_preparing_started ? 'btn-warning' : 'btn-secondary'; ?> <?= ($is_preparing_started || !$is_preparing_deadline_set) ? 'btn-disabled' : ''; ?>
                        " <?= ($is_preparing_started || !$is_preparing_deadline_set) ? 'disabled' : ''; ?>><i
                            class="fas fa-play"></i><span class="d-none d-lg-inline"> Mulai</span></button>
                    <button id="btn-finish-preparing" title="Selesai proses preparing" type="button"
                        class="d-inline btn btn-secondary <?= !$is_preparing_started ? 'btn-disabled' : '' ?>"
                        <?= !$is_preparing_started ? 'disabled' : '' ?>><i class="fas fa-stop"></i><span
                            class="d-none d-lg-inline"> Selesai</span></button>
                </div>
                <?php endif ?>
            </div>
        </header>

        <!-- ALERT -->
        <?php
            $level = check_level();
            $progress = "preparing";
            $progress_text = "penyiapan buku"
        ?>
        <?php if (!$is_preparing_started) : ?>
        <div class="alert alert-warning alert-dismissible fade show mb-1" role="alert">
        <strong>PERHATIAN!</strong> Pastikan mengisi nama staf bertugas dan deadline <?= $progress_text ?> sebelum memulai proses.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php elseif (!$is_preparing_finished) : ?>
        <div class="alert alert-warning alert-dismissible fade show mb-1" role="alert">
            <strong>PERHATIAN!</strong> Pastikan mengisi data-data sebelum menyetujui proses <?= $progress_text ?>.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php else : ?>
        <div class="alert alert-success alert-dismissible fade show mb-1" role="alert">
            Proses <?= $progress_text ?> telah selesai.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <!--END OF ALERT-->

        <?php endif; ?> <div class="list-group list-group-flush list-group-bordered" id="list-group-preparing">
            <div class="list-group-item justify-content-between">
                <span class="text-muted">Status</span>
                <span class="font-weight-bold">
                    <?php if ($is_preparing_finished) : ?>
                    <span class="text-success">
                        <i class="fa fa-check"></i>
                        <span>Selesai</span>
                    </span>
                    <?php elseif (!$is_preparing_finished && $book_transfer->preparing_start_date) : ?>
                    <span class="text-primary">
                        <span>Sedang Diproses</span>
                    </span>
                    <?php endif ?>
                </span>
            </div>

            <div class="list-group-item justify-content-between">
                <span class="text-muted">Tanggal mulai</span>
                <strong>
                    <?= format_datetime($book_transfer->preparing_start_date); ?></strong>
            </div>

            <div class="list-group-item justify-content-between">
                <span class="text-muted">Tanggal selesai</span>
                <strong>
                    <?= format_datetime($book_transfer->preparing_end_date); ?></strong>
            </div>

            <div class="list-group-item justify-content-between">
                <?php if (($_SESSION['level'] == 'superadmin' || ($_SESSION['level'] == 'admin_gudang' && empty($book_transfer->preparing_deadline))) && $staff_gudang && !$is_preparing_finished) : ?>
                <a href="#" id="btn-modal-deadline-preparing" title="Ubah deadline" data-toggle="modal"
                    data-target="#modal-deadline-preparing">Deadline <i class="fas fa-edit fa-fw"></i></a>
                <?php else : ?>
                <span class="text-muted">Deadline</span>
                <?php endif ?>
                <strong><?= format_datetime($book_transfer->preparing_deadline); ?></strong>
            </div>

            <?php if ($staff_gudang) : ?>
            <div class="list-group-item justify-content-between">
                <span class="text-muted">Staff Bertugas</span>
                <strong>
                    <?php foreach ($staff_gudang as $staff) : ?>
                    <span class="badge badge-info p-1"><?= $staff->username; ?></span>
                    <?php endforeach; ?>
                </strong>
            </div>
            <?php endif; ?>
        </div>
        <?php
            // modal deadline
            $this->load->view('book_transfer/view/common/deadline_modal', [
                'progress' => 'preparing',
            ]);
            ?>
    </div>
</section>

<script>
$(document).ready(function() {
    const book_transfer_id = '<?= $book_transfer->book_transfer_id ?>'

    // inisialisasi segment
    reload_preparing_segment()

    // ketika load segment, re-initialize call function-nya
    function reload_preparing_segment() {
        $('#preparing-progress-wrapper').load(' #preparing-progress', function() {
            // reinitiate modal after load
            initFlatpickrModal()
        });
    }

    // mulai preparing
    $('#preparing-progress-wrapper').on('click', '#btn-start-preparing', function() {
        $.ajax({
            type: "POST",
            url: "<?= base_url('book_transfer/api_start_preparing/'); ?>" + book_transfer_id,
            datatype: "JSON",
            data: {
                progress: 'preparing'
            },
            success: function(res) {
                showToast(true, res.data);
            },
            error: function(err) {
                showToast(false, err.responseJSON.message);
            },
            complete: function() {
                // reload segmen preparing
                reload_preparing_segment()
                // reload data 
                $('#book-transfer-data-wrapper').load(' #book-transfer-data');
            },
        })
    })

    // selesai preparing
    $('#preparing-progress-wrapper').on('click', '#btn-finish-preparing', function() {
        $.ajax({
            type: "POST",
            url: "<?= base_url('book_transfer/api_finish_preparing/'); ?>" + book_transfer_id,
            datatype: "JSON",
            data: {
                progress: 'preparing'
            },
            success: function(res) {
                showToast(true, res.data);
            },
            error: function(err) {
                showToast(false, err.responseJSON.message);
            },
            complete: function() {
                // reload segmen preparing
                reload_preparing_segment()
                // reload data
                $('#book-transfer-data-wrapper').load(' #book-transfer-data');
                location.reload()
            },
        })
    })
})
</script>
<?php endif?>
<!-- PREPARING -->

<!-- FINALISASI DARI ADMIN PEMASARAN -->
<?php 
    $this->load->view('book_transfer/view/final.php');
?>
<!-- FINALISASI DARI ADMIN PEMASARAN -->