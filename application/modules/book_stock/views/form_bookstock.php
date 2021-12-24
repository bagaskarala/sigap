<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item ">
                <a href="<?= base_url('book_stock'); ?>">Stok Buku</a>
            <li class="breadcrumb-item active">
                <a class="text-muted">Form</a>
            </li>
            </li>
        </ol>
    </nav>
</header>
<div class="page-section">
    <div class="row">
        <div class="col-md-8">
            <section class="card">
                <div class="card-body">
                    <?= form_open($form_action, 'novalidate="" id="form_book_stock"'); ?>
                    <fieldset>
                        <legend>Form Stok Buku</legend>
                        <?= isset($input->book_id) ? form_hidden('book_id', $input->book_id) : ''; ?>

                        <div class="form-group">
                            <label for="book_id">
                                <?= $this->lang->line('form_book_title'); ?>
                                <abbr title="Required">*</abbr>
                            </label>
                            <?= form_dropdown('book_id', get_dropdown_list_book(), $input->book_id, 'id="book_id" class="form-control custom-select d-block"'); ?>
                            <?= form_error('book_id'); ?>
                        </div>
                        <div class="form-group">
                            <label for="warehouse_present">
                                <?= $this->lang->line('form_book_stock_warehouse_present'); ?>
                            </label>
                            <?= form_input([
                                'name'  => "warehouse_present",
                                'class' => 'form-control',
                                'id'    => "warehouse_present",
                                'value' => $input->warehouse_present,
                                'type' => 'number',
                                'min' => 0
                            ]) ?>
                            <?= form_error('warehouse_present'); ?>
                        </div>
                    </fieldset>
                    <hr>
                    <div class="form-actions">
                        <button
                            class="btn btn-primary ml-auto"
                            type="submit"
                        >Submit</button>
                    </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#book_id").select2({
        placeholder: '-- Pilih --',
        dropdownParent: $('#app-main')
    });
})
</script>
