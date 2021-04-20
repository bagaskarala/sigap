<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('book_receive'); ?>">Penerimaan Buku</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">Form Edit Penerimaan Buku</a>
            </li>
        </ol>
    </nav>
</header>
<div class="page-section">
    <div class="row">
        <div class="col-md-8">
            <section class="card">
                <div class="card-body">
                    <form method="post" action='<?=base_url('book_receive/update/' . $book_receive->book_receive_id)?>' id="form-book-receive">
                    <fieldset>
                        <legend>Form Edit Penerimaan Buku</legend>
                        <div class="alert alert-danger">
                            <strong>Perhatian</strong>
                            <p class="mb-0">1. Halaman ini digunakan untuk mengedit tanggal secara manual, namun pastikan sudah
                                melakukan proses step-by-step dari halaman <a href="<?= base_url("book_receive/view/$book_receive->book_receive_id"); ?>">view penerimaan buku</a>.</p>
                            <p class="mb-0">2. Halaman ini juga digunakan untuk mereset progress penerimaan buku, dengan cara
                                menyesuaikan status penerimaan buku, dan hapus tanggal masing-masing.</p>
                        </div>
                        <?= isset($book_receive->book_receive_id) ? form_hidden('book_receive_id', $book_receive->book_receive_id) : ''; ?>
                        <div class="form-group">
                            <label for="entry_date">
                                <?= $this->lang->line('form_book_receive_entry_date')?>
                            </label>
                            <?= form_input('entry_date', $book_receive->entry_date, 'class="form-control dates"')?>
                            <?= form_error('entry_date'); ?>
                        </div>
                        <div class="form-group">
                            <label for="deadline">
                                <?= $this->lang->line('form_book_receive_deadline')?>
                            </label>
                            <?= form_input('deadline', $book_receive->deadline, 'class="form-control dates"')?>
                            <?= form_error('deadline'); ?>
                        </div>
                        <div class="form-group">
                            <label for="finish_date">
                                <?= $this->lang->line('form_book_receive_finish_date')?>
                            </label>
                            <?= form_input('finish_date', $book_receive->finish_date, 'class="form-control dates"')?>
                            <?= form_error('finish_date'); ?>
                        </div>
                        <div class="form-group">
                            <label for="book_receive_status">
                                Status Penerimaan Buku
                            </label>
                            <?= form_dropdown('book_receive_status', get_book_receive_status(), $book_receive->book_receive_status, 'id="book_receive_status" class="form-control custom-select d-block" title="Status"'); ?>
                            <?= form_error('finish_date'); ?>
                        </div>
                        <hr>
                        <h5 class="card-title">Serah Terima</h5>
                        <div class="form-group">
                            <label>Status Serah Terima</label>
                            <div class="mb-1">
                                <label>
                                    <?= form_radio('is_handover', 1, isset($book_receive->is_handover) && ($book_receive->is_handover == 1) ? true : false); ?>
                                    <i class="fa fa-check text-success"></i> Sudah selesai serah terima
                                </label>
                            </div>
                            <div class="mb-1">
                                <label>
                                    <?= form_radio('is_handover', 0, isset($book_receive->is_handover) && ($book_receive->is_handover == 0) ? true : false); ?>
                                    <i class="fa fa-times text-danger"></i> Belum selesai serah terima
                                </label>
                            </div>
                            <?= form_error('is_handover'); ?>
                        </div>
                        <div class="form-group">
                            <label for="handover_start_date">Tanggal Mulai Serah Terima
                            </label>
                            <div class="has-clearable">
                                <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">
                                        <i class="fa fa-times-circle"></i>
                                    </span>
                                </button>
                                <?= form_input('handover_start_date', $book_receive->handover_start_date, 'class="form-control dates"'); ?>
                                <?= form_error('handover_start_date'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="handover_deadline">Deadline Serah Terima
                            </label>
                            <div class="has-clearable">
                                <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">
                                        <i class="fa fa-times-circle"></i>
                                    </span>
                                </button>
                                <?= form_input('handover_deadline', $book_receive->handover_deadline, 'class="form-control dates" '); ?>
                                <?= form_error('handover_deadline'); ?>
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <label for="handover_staff">Staff Bertugas
                            </label>
                            <div class="has-clearable">
                                <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">
                                        <i class="fa fa-times-circle"></i>
                                    </span>
                                </button>
                                <?//= form_input('handover_staff', $book_receive->handover_staff, 'class="form-control text" '); ?>
                                <?//= form_error('handover_staff'); ?>
                            </div>
                        </div> -->
                        <div class="form-group">
                            <label for="handover_end_date">Tanggal Selesai Serah Terima
                            </label>
                            <div class="has-clearable">
                                <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">
                                        <i class="fa fa-times-circle"></i>
                                    </span>
                                </button>
                                <?= form_input('handover_end_date', $book_receive->handover_end_date, 'class="form-control dates" '); ?>
                                <?= form_error('handover_end_date'); ?>
                            </div>
                        </div>
                        <hr>
                        <h5 class="card-title">Wrapping</h5>
                        <div class="form-group">
                            <label>Status Wrapping</label>
                            <div class="mb-1">
                                <label>
                                    <?= form_radio('is_wrapping', 1, isset($book_receive->is_wrapping) && ($book_receive->is_wrapping == 1) ? true : false); ?>
                                    <i class="fa fa-check text-success"></i> Sudah selesai wrapping
                                </label>
                            </div>
                            <div class="mb-1">
                                <label>
                                    <?= form_radio('is_wrapping', 0, isset($book_receive->is_wrapping) && ($book_receive->is_wrapping == 0) ? true : false); ?>
                                    <i class="fa fa-times text-danger"></i> Belum selesai wrapping
                                </label>
                            </div>
                            <?= form_error('is_wrapping'); ?>
                        </div>
                        <div class="form-group">
                            <label for="wrapping_start_date">Tanggal Mulai Wrapping
                            </label>
                            <div class="has-clearable">
                                <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">
                                        <i class="fa fa-times-circle"></i>
                                    </span>
                                </button>
                                <?= form_input('wrapping_start_date', $book_receive->wrapping_start_date, 'class="form-control dates"'); ?>
                                <?= form_error('wrapping_start_date'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="wrapping_deadline">Deadline Wrapping
                            </label>
                            <div class="has-clearable">
                                <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">
                                        <i class="fa fa-times-circle"></i>
                                    </span>
                                </button>
                                <?= form_input('wrapping_deadline', $book_receive->wrapping_deadline, 'class="form-control dates" '); ?>
                                <?= form_error('wrapping_deadline'); ?>
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <label for="wrapping_staff">Staff Bertugas
                            </label>
                            <div class="has-clearable">
                                <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">
                                        <i class="fa fa-times-circle"></i>
                                    </span>
                                </button>
                                <?//= form_input('wrapping_staff', $book_receive->wrapping_staff, 'class="form-control text" '); ?>
                                <?//= form_error('wrapping_staff'); ?>
                            </div>
                        </div> -->
                        <div class="form-group">
                            <label for="wrapping_end_date">Tanggal Selesai Wrapping
                            </label>
                            <div class="has-clearable">
                                <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">
                                        <i class="fa fa-times-circle"></i>
                                    </span>
                                </button>
                                <?= form_input('wrapping_end_date', $book_receive->wrapping_end_date, 'class="form-control dates" '); ?>
                                <?= form_error('wrapping_end_date'); ?>
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <div class="form-actions">
                        <input class="btn btn-primary ml-auto" type="submit" value="Submit" id="btn-submit">
                    </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#book-id").select2({
        placeholder: '-- Pilih --',
        dropdownParent: $('#app-main')
    });

    loadValidateSetting();
    $("#form-book-receive").validate({
            rules: {
                book_id: "crequired",
                order_number: "crequired",
                deadline: "crequired",
                total: {
                    crequired: true,
                    cnumber: true
                },
            },
            errorElement: "span",
            errorPlacement: validateErrorPlacement,
        },
        validateSelect2()
    );

    $('.dates').flatpickr({
        altInput: true,
        altFormat: 'j F Y',
        dateFormat: 'Y-m-d'
    });
})
</script>