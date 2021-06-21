<?php $level = check_level();
$progress_text = '';
if ($progress == 'handover') {
    $progress_text = 'serah terima';
} elseif ($progress == 'wrapping') {
    $progress_text = 'wrapping';
} 
?>
<div class="modal fade" id="modal-action-<?= $progress ?>" tabindex="-1" role="dialog"
    aria-labelledby="modal-action-<?= $progress ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aksi <?= $progress_text ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <fieldset>
                    <?= form_hidden('progress', $progress) ?>
                    <div class="form-group">
                        <label for="action_notes_admin" class="font-weight-bold">Catatan Admin</label>
                        <div class="alert alert-info">
                            Catatan admin dapat dilihat oleh semua user yang terkait dengan penerimaan ini.
                        </div>
                        <?php
                        if (!is_admin()) {
                            echo '<div class="font-italic">' . nl2br($book_receive->{"{$progress}_notes_admin"}) . '</div>';
                        } else {
                            echo form_textarea([
                                'name'  => "{$progress}_notes_admin",
                                'class' => 'form-control',
                                'id'    => "{$progress}_notes_admin",
                                'rows'  => '6',
                                'value' => $book_receive->{"{$progress}_notes_admin"},
                            ]);
                        }
                        ?>
                        <hr class="my-3">
                        <div class="alert alert-info">
                            Pilih aksi dibawah ini: <br>
                            <ul>
                                <li>Jika <strong class="text-success">Setuju</strong>, maka tahap <?= $progress_text ?> akan
                                diakhiri dan tanggal selesai <?= $progress_text ?> akan dicatat <br></li>
                                <li>Pilih <strong class="text-primary">Reset</strong> jika ingin mengembalikan status progress.</li>
                            </ul>
                        </div>
                        <button class="btn btn-link" id="btn-<?= $progress ?>-revert"><i class="fa fa-history"></i>
                            Reset Aksi</button>
                    </div>
                </fieldset>
            </div>
            <div class="modal-footer">
                <button id="btn-<?= $progress ?>-accept" class="btn btn-success" type="button">Setuju</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const progress = '<?= $progress ?>'
    const url = "<?= base_url('book_receive/api_action_progress'); ?>"

    const bookReceiveId = '<?= $book_receive->book_receive_id ?>'

    function send_action_data({
        isAccept,
        isRevert
    }) {
        this.attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i>");

        $.ajax({
            type: "POST",
            url: `${url}/${bookReceiveId}`,
            data: {
                progress,
                accept: isAccept,
                revert: isRevert,
                [`${progress}_notes_admin`]: $(`#${progress}_notes_admin`).val()
            },
            success: function(res) {
                console.log(res);
                showToast(true, res.data);
            },
            error: function(err) {
                console.log(err);
                showToast(false, err.responseJSON.message);
            },
            complete: function() {
                location.reload()
            },
        });
    }

    // aksi setuju
    $(`#${progress}-progress-wrapper`).on('click', `#btn-${progress}-accept`, function() {
        send_action_data.call($(this), {
            isAccept: 1,
            isRevert: 0
        })
    });

    // aksi tolak
    $(`#${progress}-progress-wrapper`).on('click', `#btn-${progress}-decline`, function() {
        send_action_data.call($(this), {
            isAccept: 0,
            isRevert: 0
        })
    });

    // aksi revert
    $(`#${progress}-progress-wrapper`).on('click', `#btn-${progress}-revert`, function() {
        send_action_data.call($(this), {
            isAccept: null,
            isRevert: 1
        })
    })
})
</script>