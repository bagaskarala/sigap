<?php
$level = check_level();
if ($level == 'superadmin' || $level == 'admin_gudang') :
    $progress_text = '';
    if ($progress == 'handover') {
        $progress_text = 'Serah Terima';
    } elseif ($progress == 'wrapping') {
        $progress_text = 'Wrapping';
    }
?>
    <button
        id="btn-modal-select-staff-<?= $progress; ?>"
        type="button"
        class="d-inline btn mr-1 <?= empty($staff_gudang) ? 'btn-warning' : 'btn-secondary'; ?>"
        title="Pilih Staff"
    ><i class="fas fa-user-plus fa-fw"></i><span class="d-none d-lg-inline"> Pilih Staff</span></button>

    <div
        class="modal fade"
        id="modal-select-staff-<?= $progress; ?>"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modal-select-staff-<?= $progress; ?>"
        aria-hidden="true"
    >
        <div
            class="modal-dialog modal-lg modal-dialog-centered"
            role="document"
        >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> Pilih staff untuk progress <?= $progress_text; ?> </h5>
                </div>
                <div class="modal-body">
                    <form>
                        <fieldset>
                            <div
                                class="form-group"
                                id="form-staff-gudang-<?= $progress; ?>"
                            >
                                <label for="staff-gudang-id-<?= $progress; ?>">Nama Staff Gudang</label>
                                <select
                                    id="staff-gudang-id-<?= $progress; ?>"
                                    name="staff-gudang-id-<?= $progress; ?>"
                                    class="form-control custom-select d-block"
                                ></select>
                            </div>
                        </fieldset>
                        <div class="d-flex justify-content-end">
                            <button
                                id="btn-select-staff-gudang-<?= $progress; ?>"
                                class="btn btn-primary"
                                type="button"
                            >Pilih</button>
                        </div>
                    </form>
                    <hr>

                    <div id="staff-gudang-list-wrapper-<?= $progress; ?>">
                        <div id="staff-gudang-list-<?= $progress; ?>">
                            <p>Daftar Staff Gudang</p>
                            <?php if ($staff_gudang) : ?>
                                <?php $i = 1; ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered mb-0 nowrap">
                                        <thead>
                                            <tr>
                                                <th scope="col">No</th>
                                                <th scope="col">Nama</th>
                                                <th scope="col">Email</th>
                                                <?php if ($level == 'superadmin' || $level == 'admin_gudang') : ?>
                                                    <th style="width:100px; min-width:100px;"> &nbsp; </th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($staff_gudang as $staff) : ?>
                                                <tr>
                                                    <td class="align-middle">
                                                        <?= $i++; ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?= $staff->username; ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?= $staff->email; ?>
                                                    </td>
                                                    <?php if ($level == 'superadmin' || $level == 'admin_gudang') : ?>
                                                        <td class="align-middle text-center">
                                                            <button
                                                                title="Hapus"
                                                                class="btn btn-sm btn-danger btn-delete-staff-gudang-<?= $progress; ?>"
                                                                data="<?= $staff->book_receive_user_id; ?>"
                                                            >
                                                                <i class="fa fa-trash-alt"></i>
                                                                <span class="sr-only">Delete</span>
                                                            </button>
                                                        </td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else : ?>
                                <p class="text-center text-muted my-3">Staff gudang belum dipilih</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-light"
                        data-dismiss="modal"
                    >Close</button>
                </div>
            </div>
        </div>
    </div>


    <script>
    $(document).ready(function() {
        const book_receive_id = '<?= $book_receive->book_receive_id ?>'
        const progress = '<?= $progress ?>'

        // get data ketika buka modal pilih penulis
        $(`#${progress}-progress-wrapper`).on('click', `#btn-modal-select-staff-${progress}`, function() {

            // reload segmen ketika modal diclose
            $(`#modal-select-staff-${progress}`).off('hidden.bs.modal').on('hidden.bs.modal', function(e) {
                // location.reload()
                $(`#${progress}-progress-wrapper`).load(` #${progress}-progress`, function() {
                    // reinitiate flatpickr modal after load
                    initFlatpickrModal()
                });
            })

            //  open modal
            $(`#modal-select-staff-${progress}`).modal('toggle')


            // get data semua reviewer
            $.get("<?= base_url('book_receive/api_get_staff_gudang'); ?>",
                function(res) {
                    //  inisialisasi select2
                    $(`#staff-gudang-id-${progress}`).select2({
                        placeholder: '-- Pilih --',
                        dropdownParent: $(`#modal-select-staff-${progress}`),
                        allowClear: true,
                        data: res.data.map(r => {
                            return {
                                id: r.user_id,
                                text: `${r.username}`
                            }
                        })
                    });

                    //  reset selected data
                    $(`[name=staff-gudang-id-${progress}]`).val(null).trigger('change');

                    //  event ketika data di select
                    $(`#staff-gudang-id-${progress}`).off('select2:select').on('select2:select',
                        function(e) {
                            var data = e.params.data;
                            console.log(data);
                        });
                }
            )
        })

        // pilih reviewer
        $(`#${progress}-progress-wrapper`).on('click', `#btn-select-staff-gudang-${progress}`, function() {
            const $this = $(this);
            const user_id = $(`#staff-gudang-id-${progress}`).val();

            if (!user_id) {
                showToast(false, 'Pilih staff gudang dahulu');
                return
            }

            $this.attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i>");
            $.ajax({
                type: "POST",
                url: "<?= base_url('book_receive/api_add_staff_gudang'); ?>",
                datatype: "JSON",
                data: {
                    book_receive_id,
                    user_id,
                    progress
                },
                success: function(res) {
                    showToast(true, res.data);
                },
                error: function(err) {
                    showToast(false, err.responseJSON.message);
                },
                complete: function() {
                    $(`[name=staff-gudang-id-${progress}]`).val(null).trigger('change');
                    // reload segemen daftar reviewer
                    $(`#staff-gudang-list-wrapper-${progress}`).load(
                        ` #staff-gudang-list-${progress}`);

                    $this.removeAttr("disabled").html("Submit");
                },
            });
        });

        // hapus reviewer
        $(`#${progress}-progress-wrapper`).on('click', `.btn-delete-staff-gudang-${progress}`, function() {
            $(this).attr('disabled', 'disabled').html("<i class='fa fa-spinner fa-spin '></i>");
            let id = $(this).attr('data');

            $.ajax({
                url: "<?= base_url('book_receive/api_delete_staff_gudang/'); ?>" + id,
                success: function(res) {
                    showToast(true, res.data);
                },
                error: function(err) {
                    showToast(false, err.responseJSON.message);
                },
                complete: function() {
                    // reload segemen daftar reviewer
                    $(`#staff-gudang-list-wrapper-${progress}`).load(
                        ` #staff-gudang-list-${progress}`);
                },
            })
        });
    })
    </script>
<?php endif; ?>


<!-- <?php
        // $level = check_level();
        // if ($level == 'superadmin' || $level == 'admin_gudang') :
        //     $progress_text = '';
        //     if ($progress == 'handover') {
        //         $progress_text = 'Serah Terima';
        //     } elseif ($progress == 'wrapping') {
        //         $progress_text = 'Wrapping';
        //     }
        ?>
<div class="modal fade" id="modal-staff-<?//= $progress; ?>" tabindex="-1" role="dialog"
    aria-labelledby="modal-staff-<?//= $progress; ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Staff Gudang untuk Progress <?//= $progress_text; ?> </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <fieldset>
                        <div class="form-group" id="form-staff-<?//= $progress; ?>">
                            <label for="staff-<?//= $progress; ?>">Nama Staff Gudang</label>
                            <input type="text" id="<?//= $progress ?>_staff" class="form-control">
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button id="btn-submit-staff-<?//= $progress?>" class="btn btn-primary" type="button">Submit</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        const book_receive_id = '<?//= $book_receive->book_receive_id; ?>';
        const progress = '<?//= $progress; ?>';

        // submit progress
        $(`#${progress}-progress-wrapper`).on('click', `#btn-submit-staff-${progress}`, function() {
            const $this = $(this);

            $this.attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i>");
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('book_receive/api_update/'); ?>" + book_receive_id,
                datatype: "JSON",
                data: {
                    progress,
                    [`${progress}_staff`]: $(`#${progress}_staff`).val(),
                },
                success: function(res) {
                    showToast(true, res.data);
                },
                error: function(err) {
                    showToast(false, err.responseJSON.message);
                },
                complete: function() {
                    $this.removeAttr("disabled").html("Submit");
                    $(`#${progress}-progress-wrapper`).load(` #${progress}-progress`);
                    $(`#modal-staff-${progress}`).modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                }
            });
        });
    })
</script>
<?php //endif; 
?> -->
