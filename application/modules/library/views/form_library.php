<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('library'); ?>">Perpustakaan</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">Form</a>
            </li>
        </ol>
    </nav>
</header>
 
<div class="page-section">
    <div class="row">
        <div class="col-md-6">
            <section class="card">
                <div class="card-body">
                    <?= form_open($form_action, 'id="form_library" novalidate=""'); ?>
                    <fieldset>
                        <legend>Form Perpustakaan</legend>
                        <?= isset($input->library_id) ? form_hidden('library_id', $input->library_id) : ''; ?>
                        <div class="form-group">
                            <label for="library_name">Perpustakaan
                                <abbr title="Required">*</abbr>
                            </label>
                            <?= form_input('library_name', $input->library_name, 'class="form-control" id="library_name" autofocus'); ?>
                            <?= form_error('library_name'); ?>
                        </div>
                    </fieldset>
                    <hr>
                    <div class="form-actions">
                        <button class="btn btn-primary ml-auto" type="submit">Submit</button>
                    </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
 
<script>
$(document).ready(function() {
    loadValidateSetting();
    $("#form_library").validate({
            rules: {
                library_name: {
                    crequired: true,
                    alphanum: true,
                }
            },
            errorElement: "span",
            errorPlacement: validateErrorPlacement
        },
        validateSelect2()
    );
})
</script>