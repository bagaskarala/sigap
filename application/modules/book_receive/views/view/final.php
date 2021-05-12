<?php
$is_ready = $book_receive->is_handover && $book_receive->is_wrapping;

if (!$is_final) :
    if (($_SESSION['level'] == 'superadmin' || $_SESSION['level'] == 'admin_gudang')) :
?>
<div id="final-progress-wrapper" class="mx-3 mx-md-0">
    <div id="final-progress" class="card-button">
        <?= (!$is_ready) ? '<div class="m-0"><small class="text-danger"><i class="fa fa-exclamation-triangle"></i> Terdapat proses yang belum disetujui</small></div>' : null
                ?>

        <button class="btn btn-primary <?= ($is_ready) ? null : 'btn-disabled'; ?>" data-toggle="modal"
            data-target="#modal-accept-book-receive" <?= ($is_ready) ? null : 'disabled'; ?>>Finalisasi</button>
    </div>
</div>

<div class="modal modal-warning fade" id="modal-accept-book-receive" tabindex="-1" role="dialog"
    aria-labelledby="modal-accept-book-receive" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-bullhorn text-yellow mr-1"></i> Konfirmasi finalisasi penerimaan buku</h5>
            </div>
            <div class="modal-body">
                <p>Apakah anda yakin akan menyelesaikan penerimaan buku ini?</p>
                
                <div class="alert alert-info">
                    <p class="mb-0">Ketika klik submit:</p>
                    <p class="mb-0">1. Stok buku akan ditambahkan ke gudang</p>
                    <p class="mb-0">2. Tanggal selesai penerimaan akan tercatat</p>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-accept-book-receive" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const bookReceiveId = '<?= $book_receive->book_receive_id ?>';
    const url = '<?= base_url('book_receive/final/'); ?>' + bookReceiveId

    $('#btn-accept-book-receive').on('click', function() {
        $(this).attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i>");
        window.location = url
    });
})
</script>
<?php endif; ?>
<?php else : ?>
    <div>Penerimaan buku telah selesai.&nbsp;
    </div>
<?php endif; ?>