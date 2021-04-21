<section class="card">
    <header class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active show" data-toggle="tab" href="#book-receive-data-wrapper"><i
                        class="fa fa-info-circle"></i> Detail Penerimaan Buku</a>
            </li>
        </ul>
    </header>

    <div class="card-body">
        <div class="tab-content">
            <!-- BOOK RECEIVE DATA -->
            <div id="book-receive-data-wrapper" class="tab-pane fade active show">
                <div id="book-receive">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <td width="200px"> Judul Buku </td>
                                    <td><strong><?=$book_receive->book_title?></strong> </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Nomor Order </td>
                                    <td><?= $book_receive->order_number;?> </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Hasil Cetak </td>
                                    <td><?=$book_receive->total_postprint?></td>
                                </tr>
                                <tr>
                                    <td width="200px"> Tanggal Masuk Gudang </td>
                                    <td><?=format_datetime($book_receive->entry_date)?> </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Tanggal Selesai</td>
                                    <td><?=format_datetime($book_receive->finish_date)?> </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Deadline</td>
                                    <td>
                                        <?php if($book_receive->deadline==null) : ?>
                                        <a href="#" class="btn btn-primary" id="btn-modal-deadline" title="Isi Deadline"
                                            data-toggle="modal" data-target="#modal-deadline"><i
                                                class="fas fa-clock mr-2"></i> Deadline</a>
                                        <br>
                                        <small class="text-danger">Isi deadline sebelum memulai proses penerimaan
                                            buku</small>
                                        <?php else: ?>
                                        <?=deadline_color($book_receive->deadline, $book_receive->book_receive_status)?>
                                        <?php endif?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="200px"> Status</td>
                                    <td><?=get_book_receive_status()[$book_receive->book_receive_status];?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-deadline" tabindex="-1" role="dialog" aria-labelledby="modal-deadline"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Deadline Penerimaan Buku</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post"
                        action='<?=base_url('book_receive/add_deadline/' . $book_receive->book_receive_id)?>'
                        id="form-book-receive-add-deadline" enctype="multipart/form-data">
                        <fieldset>
                            <div class="form-group col-8">
                                <input type="text" name="deadline" id="deadline"
                                    class="form-control flatpickr_modal d-none"
                                    value="<?= $book_receive->deadline ?>" />
                                <?= form_error('deadline'); ?>
                                <br>
                            </div>
                            <input type="submit" value="Submit" class="btn btn-primary d-flex ml-auto mb-3">
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
$(document).ready(function() {
    loadValidateSetting();
    $("#form-book-receive-add-deadline").validate({
            rules: {
                deadline: "crequired",
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