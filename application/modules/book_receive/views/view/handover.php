<?php
$is_handover_started       = format_datetime($book_receive->handover_start_date);
$is_handover_finished      = format_datetime($book_receive->handover_end_date);
$is_handover_deadline_set  = format_datetime($book_receive->handover_deadline);
$staff_gudang              = $this->book_receive->get_staff_gudang_by_progress('handover', $book_receive->book_receive_id);
?>
<section id="handover-progress-wrapper" class="card">
    <div id="handover-progress">
        <header class="card-header">
            <div class="d-flex align-items-center"><span class="mr-auto">Serah Terima</span>
                <?php if(!$is_final) :
                        // modal staff
                        $this->load->view('book_receive/view/common/input_staff_modal', [
                            'progress' => 'handover',
                            'staff_gudang' => $staff_gudang
                        ]);        
                ?>
                <div class="card-header-control">
                    <button id="btn-start-handover" title="Mulai proses serah terima" type="button" class="d-inline btn 
                        <?= !$is_handover_started ? 'btn-warning' : 'btn-secondary'; ?> <?= ($is_handover_started || !$is_handover_deadline_set) ? 'btn-disabled' : ''; ?>
                        "
                        <?= ($is_handover_started || !$is_handover_deadline_set || !$staff_gudang) ? 'disabled' : ''; ?>><i
                            class="fas fa-play"></i><span class="d-none d-lg-inline"> Mulai</span></button>
                    <button id="btn-finish-handover" title="Selesai proses serah terima" type="button"
                        class="d-inline btn btn-secondary <?= (!$is_handover_started || $is_wrapping) ? 'btn-disabled' : '' ?>"
                        <?= (!$is_handover_started || $is_wrapping) ? 'disabled' : '' ?>><i
                            class="fas fa-stop"></i><span class="d-none d-lg-inline"> Selesai</span></button>
                </div>
                <?php endif ?>
            </div>
        </header>

        <!-- ALERT -->
        <?php 
            $this->load->view('book_receive/view/common/progress_alert', [
                'progress'          => 'handover'
            ]);
            ?>

        <div class="list-group list-group-flush list-group-bordered" id="list-group-handover">
            <div class="list-group-item justify-content-between">
                <span class="text-muted">Status</span>
                <span class="font-weight-bold">
                    <?php if ($book_receive->is_handover) : ?>
                    <span class="text-success">
                        <i class="fa fa-check"></i>
                        <span>Selesai</span>
                    </span>
                    <?php elseif (!$book_receive->is_handover && $book_receive->book_receive_status == 'reject') : ?>
                    <span class="text-danger">
                        <i class="fa fa-times"></i>
                        <span>Ditolak</span>
                    </span>
                    <?php elseif (!$book_receive->is_handover && $book_receive->handover_start_date) : ?>
                    <span class="text-primary">
                        <span>Sedang Diproses</span>
                    </span>
                    <?php endif ?>
                </span>
            </div>

            <div class="list-group-item justify-content-between">
                <span class="text-muted">Tanggal mulai</span>
                <strong>
                    <?= format_datetime($book_receive->handover_start_date); ?></strong>
            </div>

            <div class="list-group-item justify-content-between">
                <span class="text-muted">Tanggal selesai</span>
                <strong>
                    <?= format_datetime($book_receive->handover_end_date); ?></strong>
            </div>

            <div class="list-group-item justify-content-between">
                <?php if (($_SESSION['level'] == 'superadmin' || ($_SESSION['level'] == 'admin_gudang' && empty($book_receive->handover_deadline))) && $staff_gudang && !$is_final) : ?>
                <a href="#" id="btn-modal-deadline-handover" title="Ubah deadline" data-toggle="modal"
                    data-target="#modal-deadline-handover">Deadline <i class="fas fa-edit fa-fw"></i></a>
                <?php else : ?>
                <span class="text-muted">Deadline</span>
                <?php endif ?>
                <strong><?= format_datetime($book_receive->handover_deadline); ?></strong>
            </div>
            <?php if($staff_gudang) : ?>
            <div class="list-group-item justify-content-between">
                <span class="text-muted">Staff Bertugas</span>
                <strong>
                    <?php foreach ($staff_gudang as $staff) : ?>
                    <span class="badge badge-info p-1"><?= $staff->username; ?></span>
                    <?php endforeach; ?>
                </strong>
            </div>
            <?php endif?>

            <div class="list-group-item justify-content-between">
                <span class="text-muted">Jumlah buku diterima</span>
                <strong id="total-handover"><?= $book_receive->total_postprint; ?></strong>
            </div>

            <div class="m-3">
                <div class="text-muted pb-1">Catatan Admin</div>
                <?= $book_receive->handover_notes_admin ?>
            </div>

            <hr class="m-0">
        </div>

        <div class="card-body">
            <div class="card-button">
                <!-- button aksi -->
                <?php if (($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_gudang') && !$is_final) : ?>
                <button title="Aksi admin"
                    class="btn btn-outline-dark <?= !$uploaded_file ? 'btn-disabled' : ''; ?>"
                    data-toggle="modal" data-target="#modal-action-handover"
                    <?= !$uploaded_file ? 'disabled' : ''; ?>>Aksi</button>
                <?php endif; ?>

                <!-- button tanggapan handover -->
                <button type="button" class="btn btn-outline-success" data-toggle="modal"
                    data-target="#modal-handover-notes">Catatan</button>
                <?php if (!$is_final) : ?>
                <a href="<?= base_url('book_receive/generate_pdf_handover/' . $book_receive->book_receive_id . "/handover") ?>"
                    class="btn btn-outline-danger 
                    <?= (!$is_handover_deadline_set) ? 'disabled' : ''; ?>" id="btn-generate-pdf-handover"
                    title="Generate PDF berita acara serah terima">Generate PDF <i class="fas fa-file-pdf fa-fw"></i>
                </a>
                <form action="<?=base_url('book_receive/upload_handover/')?>" method="POST"
                    enctype="multipart/form-data" style="display:inline;" id="handover_upload_form">
                    <label class="btn btn-outline-primary mb-0" id="btn-upload-pdf-handover"
                        title="Upload PDF berita acara serah terima disetujui"><i class="fas fa-upload fa-fw"></i>
                        <input type="file" accept=".pdf,.jpeg,.jpg,.png" class="btn btn-outline-primary"
                            name="handover_file" id="handover_file" style="display:none" onchange="form.submit()" />
                        Upload File Berita Acara
                    </label>
                    <input type="hidden" name="receive_id" id="receive_id"
                        value=<?= $book_receive->book_receive_id ?> />
                </form>
                <?php endif?>
                <?php if ($uploaded_file): ?>
                <a href="<?=base_url('book_receive/download_file/bookreceive/'.$uploaded_file)?>"
                    class="btn btn-outline-success" id="btn-download-pdf-handover"
                    title="Download PDF berita acara serah terima disetujui"><i class="fas fa-download fa-fw"></i>
                    Download File Berita Acara
                </a>
                <?php endif  ?>
            </div>
        </div>

        <?php
            // modal deadline
            $this->load->view('book_receive/view/common/deadline_modal', [
                'progress' => 'handover',
            ]);

            // modal action
            $this->load->view('book_receive/view/common/action_modal', [
                'progress' => 'handover',
            ]);

            // modal note
            $this->load->view('book_receive/view/common/notes_modal', [
                'progress' => 'handover',
            ]);
            ?>
    </div>
</section>

<script>
$(document).ready(function() {
    const book_receive_id = '<?= $book_receive->book_receive_id ?>'

    // inisialisasi segment
    reload_handover_segment()

    // ketika load segment, re-initialize call function-nya
    function reload_handover_segment() {
        $('#handover-progress-wrapper').load(' #handover-progress', function() {
            // reinitiate modal after load
            initFlatpickrModal()
        });
    }

    // mulai serah terima
    $('#handover-progress-wrapper').on('click', '#btn-start-handover', function() {
        $.ajax({
            type: "POST",
            url: "<?= base_url('book_receive/api_start_progress/'); ?>" + book_receive_id,
            datatype: "JSON",
            data: {
                progress: 'handover'
            },
            success: function(res) {
                showToast(true, res.data);
            },
            error: function(err) {
                showToast(false, err.responseJSON.message);
            },
            complete: function() {
                // reload segmen handover
                reload_handover_segment()
                // reload progress
                $('#progress-list-wrapper').load(' #progress-list');
                // reload data 
                $('#book-receive-data-wrapper').load(' #book-receive');
            },
        })
    })

    // selesai serah terima
    $('#handover-progress-wrapper').on('click', '#btn-finish-handover', function() {
        $.ajax({
            type: "POST",
            url: "<?= base_url('book_receive/api_finish_progress/'); ?>" + book_receive_id,
            datatype: "JSON",
            data: {
                progress: 'handover'
            },
            success: function(res) {
                showToast(true, res.data);
            },
            error: function(err) {
                showToast(false, err.responseJSON.message);
            },
            complete: function() {
                // reload segmen handover
                reload_handover_segment()
                // reload progress
                $('#progress-list-wrapper').load(' #progress-list');
                // reload data
                $('#book-receive-data-wrapper').load(' #book-receive');
            },
        })
    })
})
</script>