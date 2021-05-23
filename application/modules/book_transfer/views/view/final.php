<?php
if ($book_transfer->status=='preparing_finish') :
    if (($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_pemasaran')) :
?>
<div id="final-progress-wrapper" class="mx-3 mx-md-0">
    <div id="final-progress" class="card-button">
        <?= (!$book_transfer->status=='preparing_finish') ? '<div class="m-0"><small class="text-danger"><i class="fa fa-exclamation-triangle"></i> Terdapat proses yang belum disetujui</small></div>' : null?>
        <button class="btn btn-primary <?= ($book_transfer->status=='preparing_finish') ? null : 'btn-disabled'; ?>" data-toggle="modal"
            data-target="#modal-accept-book-transfer" <?= ($book_transfer->status=='preparing_finish') ? null : 'disabled'; ?>>Finalisasi</button>
    </div>
</div>

<div class="modal modal-warning fade" id="modal-accept-book-transfer" tabindex="-1" role="dialog"
    aria-labelledby="modal-accept-book-transfer" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-bullhorn text-yellow mr-1"></i> Konfirmasi finalisasi pemindahan buku</h5>
            </div>
            <div class="modal-body">
                <p>Apakah anda yakin akan menyelesaikan pemindahan buku ini?</p>
                <div class="alert alert-info">Tanggal selesai pemindahan buku akan tercatat ketika klik Submit</div>
            </div>
            <div class="modal-footer">
                <button id="btn-accept-book-transfer" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    const book_transfer_id = '<?= $book_transfer->book_transfer_id ?>';
    const url = '<?= base_url('book_transfer/final/'); ?>' + book_transfer_id
    // order cetak disetujui
    $('#btn-accept-book-transfer').on('click', function() {
        $(this).attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i>");
        window.location = url + '/finish'
    });
})
</script>
<?php endif; ?>
<?php elseif($book_transfer->status=='finish') : ?>
    <div>Pemindahan buku telah selesai.&nbsp;</div>
<?php endif; ?>