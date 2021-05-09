<?php
$level = check_level();
$progress = 'preparing';
$progress_text = 'penyiapan buku'; 
?>
<?php if (!$book_request->preparing_start_date) : ?>
<div class="alert alert-warning alert-dismissible fade show mb-1" role="alert">
    <strong>PERHATIAN!</strong> Pastikan mengisi nama staf yang bertugas untuk <?= $progress_text ?> sebelum memulai proses.
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php elseif (!$book_request->preparing_start_date || !$book_request->preparing_end_date) : ?>
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
<?php endif; ?>