<?php
$level = check_level();
if ($progress == 'handover') {
    $progress_text = 'serah terima';
} elseif ($progress == 'wrapping') {
    $progress_text = 'wrapping';
} 
?>
<?php if (!$book_receive->{"{$progress}_start_date"}) : ?>
<div class="alert alert-warning alert-dismissible fade show mb-1" role="alert">
    <strong>PERHATIAN!</strong> Pastikan mengisi nama staf bertugas dan deadline <?= $progress_text ?> sebelum memulai proses.
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php elseif (!$book_receive->{"is_{$progress}"}) : ?>
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