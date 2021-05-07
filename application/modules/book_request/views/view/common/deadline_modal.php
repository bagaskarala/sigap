<div class="modal fade" id="modal-deadline-preparing" tabindex="-1" role="dialog"
    aria-labelledby="modal-deadline-preparing" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title-preparing">Deadline Penyiapan Buku</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <fieldset>
                    <div class="form-group">
                        <div>
                            <input type="text" name="<?= "preparing_deadline" ?>" id="<?= "preparing-deadline" ?>"
                                class="form-control flatpickr_modal d-none"
                                value="<?= $book_request->preparing_deadline ?>" />
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button id="btn-reset-deadline-preparing" class="btn btn-link text-danger"
                    type="button">Reset</button>
                <div>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    <button id="btn-submit-deadline-preparing" class="btn btn-primary"
                        type="button">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    const progress = 'preparing'
    const book_request_id = '<?= $book_request->invoice_id ?>'
    const deadline = '<?= $book_request->preparing_deadline?>'

    // ketika modal tampil, pasang listener
    $(`#preparing-progress-wrapper`).on('shown.bs.modal', `#modal-deadline-preparing`, function() {
        // populate deadline ketika deadline tidak terpilih (avoid bugs)
        if (!$(`#preparing-deadline`).val()) {
            $(`#preparing-deadline`)[0]._flatpickr.setDate(deadline);
        }

        // reload ketika modal diclose
        $(`#modal-deadline-preparing`).off('hidden.bs.modal').on('hidden.bs.modal', function(e) {
            $(`#preparing-progress-wrapper`).load(` #preparing-progress`, function() {
                // reinitiate flatpickr modal after load
                initFlatpickrModal()
            });
        })
    })

    function send_deadline_data(deadline) {
        this.attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i>");

        $.ajax({
            type: "POST",
            url: "<?= base_url('book_request/api_update/'); ?>" + book_request_id,
            data: {
                [`preparing_deadline`]: deadline
            },
            success: function(res) {
                showToast(true, res.data);
                $(`#modal-deadline-preparing`).modal('hide')
            },
            error: function(err) {
                showToast(false, err.responseJSON.message);
            },
            complete: () => {
                const btnName = deadline ? 'Submit' : 'Reset';
                this.removeAttr("disabled").html(btnName);
                // trik mengatasi close modal, ketika file di load ulang
                // $(`#modal-deadline-preparing`).modal('hide');
                // $('body').removeClass('modal-open');
                // $('.modal-backdrop').remove();
            },
        });
    }

    // submit deadline
    $(`#preparing-progress-wrapper`).on('click', `#btn-submit-deadline-preparing`, function() {
        const deadline = $(`#preparing-deadline`).val()
        send_deadline_data.call($(this), deadline)
    });

    // reset deadline
    $(`#preapring-progress-wrapper`).on('click', `#btn-reset-deadline-preparing`, function() {
        send_deadline_data.call($(this), null)
    });
})
</script>