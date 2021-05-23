<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('book_transfer'); ?>">Pemindahan Buku</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">Form Edit Pemindahan Buku</a>
            </li>
        </ol>
    </nav>
</header>
<div class="page-section">
    <div class="row">
        <div class="col-md-8">
            <section class="card">
                <div class="card-body">
                    <form method="post" action='<?=base_url('book_transfer/update/' . $book_transfer->book_transfer_id)?>' id="form-book-transfer">
                    <fieldset>
                        <legend>Form Edit Pemindahan Buku</legend>
                        <?= isset($input->book_transfer_id) ? form_hidden('book_transfer_id', $input->book_transfer_id) : ''; ?>
                        <div class="form-group">
                            <label for="transfer_date">Tanggal Permintaan Pemindahan Buku
                                <?= $this->lang->line('form_book_transfer_date')?>
                            </label>
                            <?= form_input('transfer_date', $book_transfer->transfer_date, 'class="form-control dates"')?>
                            <?= form_error('entry_date'); ?>
                        </div>
                        <div class="form-group">
                            <label for="preparing_start_date">Tanggal Mulai Penyiapan Buku
                            </label>
                            <div class="has-clearable">
                                <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">
                                        <i class="fa fa-times-circle"></i>
                                    </span>
                                </button>
                                <?= form_input('preparing_start_date', $book_transfer->preparing_start_date, 'class="form-control dates"'); ?>
                                <?= form_error('preparing_start_date'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="preparing_deadline">Deadline Penyiapan Buku
                            </label>
                            <div class="has-clearable">
                                <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">
                                        <i class="fa fa-times-circle"></i>
                                    </span>
                                </button>
                                <?= form_input('preparing_deadline', $book_transfer->preparing_deadline, 'class="form-control dates" '); ?>
                                <?= form_error('preparing_deadline'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="preparing_end_date">Tanggal Selesai Penyiapan Buku
                            </label>
                            <div class="has-clearable">
                                <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">
                                        <i class="fa fa-times-circle"></i>
                                    </span>
                                </button>
                                <?= form_input('preparing_end_date', $book_transfer->preparing_end_date, 'class="form-control dates" '); ?>
                                <?= form_error('preparing_end_date'); ?>
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <input type="submit" class="btn btn-primary" value="Submit" />
                    <a class="btn btn-secondary" href="<?php echo base_url('book_transfer') ?>" role="button">Back</a>
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
    $('.dates').flatpickr({
        altInput: true,
        altFormat: 'j F Y',
        dateFormat: 'Y-m-d'
    });
})
</script>