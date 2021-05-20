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
                <a class="text-muted">Form Retur Stok Buku</a>
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
                        <legend>Form Retur Stok Buku</legend>
                        <div class="alert alert-warning">
                            <strong>PERHATIAN!</strong> Fitur ini berfungsi untuk mengubah stok buku.
                        </div>
                        <form action="<?= base_url('book_stock/retur_book_stock/'); ?>" method="post">
                            <div class="form-group">
                                <label class="font-weight-bold">Judul Buku</label>
                                <input type="text" class="form-control" value="<?= $input->book_title; ?>" disabled />
                                <input type="hidden" class="form-control" id="book_id" name="book_id"
                                    value="<?= $input->book_id; ?>" />
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Stok Gudang</label>
                                <span class ="form-control bg-secondary" readonly><?=$input->warehouse_present?></span>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Stok Retur</label>
                                <span class ="form-control bg-secondary" readonly>
                                    <?php if($input->retur_stock) : ?>
                                        <?=$input->retur_stock?>
                                    <?php else : ?>
                                        0
                                    <?php endif?>
                                </span>
                            </div>
                            <div class="form-group" id="retur-buku"> 
                                <label for="revision_type" class="d-block font-weight-bold"> Tipe Retur <abbr
                                        title="Required">*</abbr></label>
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-secondary active">
                                        <input required type="radio" name="revision_type" value="sub"
                                            class="custom-control-input" /> Tambah Stok Retur
                                    </label>
                                    <label class="btn btn-secondary ">
                                        <input type="radio" name="revision_type" value="delete"
                                            class="custom-control-input" /> Penghapusan Stok Retur
                                    </label>
                                </div>
                            </div>

                            <div class="form-group" id="revision_date">
                                <label for="revision_date" class="d-block font-weight-bold">Tanggal Retur <abbr
                                    title="Required">*</abbr> </label>
                                <div class="has-clearable">
                                    <button type="button" class="close" aria-label="Close">
                                        <span aria-hidden="true">
                                            <i class="fa fa-times-circle"></i>
                                        </span>
                                    </button>
                                    <input required type="date" class="form-control dates" name="revision_date"
                                        id="revision_date" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold" for="warehouse_modifier">Perubahan<abbr
                                        title="Required">*</abbr></label>
                                <input required type="number" class="form-control" name="warehouse_modifier"
                                    id="warehouse_modifier" min="1" max="" />
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold" for="notes">Catatan<abbr
                                        title="Required">*</abbr></label>
                                <textarea required rows="6" class="form-control summernote-basic" id="notes"
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

<script>
$(document).ready(function() {
    $('.dates').flatpickr({
        allowInput:true,
        altInput: true,
        altFormat: 'j F Y',
        dateFormat: 'Y-m-d'
    });
    $("input[name='revision_type']").click(function(){
        if ($("input[name='revision_type']:checked").val()=="delete"){
            $("#warehouse_modifier").attr({
                "max" : <?= $input->retur_stock ? $input->retur_stock : 0 ?>
            });
        }
        else {
            $("#warehouse_modifier").attr({
                "max" : <?=$input->warehouse_present?>,
            });
        }
    })
});
</script>