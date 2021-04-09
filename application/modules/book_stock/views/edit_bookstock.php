<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('book_stock')?>">Stok Buku</a>
            </li>
            <li class="breadcrumb-item active">
                <a class="text-muted">Form Edit Stok Buku</a>
            </li>
        </ol>
    </nav>
</header>

<div class="page-section">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <fieldset>
                        <legend>Form Edit Stok Buku</legend>
                        <div class="alert alert-warning">
                            <strong>PERHATIAN!</strong> Fitur ini berfungsi untuk mengubah stok buku.
                        </div>
                        <form action="<?= base_url('book_stock/edit_book_stock'); ?>" method="post">
                            <div class="form-group">
                                <label class="font-weight-bold">Judul Buku</label>
                                <input type="text" class="form-control" value="<?= $input->book_title; ?>" disabled />
                                <input type="hidden" class="form-control" id="book_id" name="book_id"
                                    value="<?= $input->book_id; ?>" />
                            </div>
                            <div class="form-group">
                                <label for="revision_type" class="d-block font-weight-bold"> Tipe Operasi <abbr
                                        title="Required">*</abbr></label>
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-secondary active">
                                        <input type="radio" name="revision_type" value="add" checked="checked"
                                            class="custom-control-input" /> Tambah
                                    </label>
                                    <label class="btn btn-secondary ">
                                        <input type="radio" name="revision_type" value="sub"
                                            class="custom-control-input" /> Kurang
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold" for="warehouse_modifier">Perubahan<abbr
                                        title="Required">*</abbr></label>
                                <input type="number" class="form-control" name="warehouse_modifier"
                                    id="warehouse_modifier" />
                                <input type="hidden" name="warehouse_past" id="warehouse_past"
                                    value="<?//= $input->stock_warehouse; ?>">
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold" for="notes">Catatan</label>
                                <textarea rows="6" class="form-control summernote-basic" id="notes"
                                    name="notes"></textarea>
                            </div>
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </form>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>