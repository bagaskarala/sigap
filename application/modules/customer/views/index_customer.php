<?php
$per_page = $this->input->get('per_page') ?? 10;
$keyword  = $this->input->get('keyword');
$type     = $this->input->get('type');
$page     = $this->uri->segment(2);
// data table series number
$i = isset($page) ? $page * $per_page - $per_page : 0;

$type_options =  array_merge(['' => '- Filter Jenis Customer -'], $customer_type);

?>

<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">Customer</a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Customer </h1>
            <span class="badge badge-info">Total : <?= $total; ?></span>
        </div>
        <div>
            <button
                type="button"
                class="btn btn-sm btn-primary"
                data-toggle="modal"
                data-target="#modal-discount"
            >
                <i
                    class="fa fa-pencil-alt fa-fw"
                    style="margin-right: 5px;"
                ></i>Diskon
            </button>
            <button
                type="button"
                class="btn btn-sm btn-primary"
                data-toggle="modal"
                data-target="#modal-add"
            >
                <i class="fa fa-plus fa-fw"></i>Tambah
            </button>
        </div>
    </div>
</header>
<div class="page-section">
    <div class="row">
        <div class="col-12">
            <section class="card card-fluid">
                <div class="card-body p-0">
                    <div class="p-3">
                        <?= form_open($pages, ['method' => 'GET']); ?>
                        <div class="row">
                            <div class="col-12 col-md-6 mt-2">
                                <label for="type">Data per halaman</label>
                                <?= form_dropdown('per_page', get_per_page_options(), $per_page, 'id="per_page" class="form-control custom-select d-block" title="List per page"'); ?>
                            </div>
                            <div class="col-12 col-md-6 mt-2">
                                <label for="type">Jenis</label>
                                <?= form_dropdown('type', $type_options, $type, 'id="type" class="form-control custom-select d-block" title="Customer Type"'); ?>
                            </div>
                            <div class="col-12 col-md-8 mt-2">
                                <label for="status">Pencarian</label>
                                <?= form_input('keyword', $keyword, ['placeholder' => 'Cari berdasarkan Nama Pelanggan', 'class' => 'form-control']); ?>
                            </div>
                            <div class="col-12 col-md-4 mt-2">
                                <label>&nbsp;</label>
                                <div
                                    class="btn-group btn-block"
                                    role="group"
                                    aria-label="Filter button"
                                >
                                    <button
                                        class="btn btn-secondary"
                                        type="button"
                                        onclick="location.href = '<?= base_url($pages); ?>'"
                                    > Reset</button>
                                    <button
                                        class="btn btn-primary"
                                        type="submit"
                                        value="Submit"
                                    ><i class="fa fa-filter"></i> Filter</button>
                                </div>
                            </div>
                        </div>
                        <?= form_close(); ?>
                    </div>
                    <?php if ($customers) : ?>
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th
                                            scope="col"
                                            class="pl-4"
                                        >No</th>
                                        <th scope="col">Nama</th>
                                        <th scope="col">Alamat</th>
                                        <th scope="col">No Telepon</th>
                                        <th scope="col">Jenis Customer</th>
                                        <th style="width:100px; min-width:100px;"> &nbsp; </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($customers as $customer) : ?>
                                        <tr>
                                            <td class="align-middle pl-4"><?= $i + 1; ?></td>
                                            <td class="align-middle"><?= highlight_keyword($customer->name, $keyword); ?></td>
                                            <td class="align-middle"><?= highlight_keyword($customer->address, $keyword); ?></td>
                                            <td class="align-middle"><?= highlight_keyword($customer->phone_number, $keyword); ?></td>
                                            <td class="align-middle"><?= highlight_keyword($customer->type, $keyword); ?></td>
                                            <td class="align-middle text-right">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-secondary"
                                                    onclick="modalEdit(<?= $customer->customer_id ?>)"
                                                >
                                                    <i class="fa fa-pencil-alt"></i><span class="sr-only">Edit</span>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-danger"
                                                    data-toggle="modal"
                                                    data-target="#modal-delete-<?= $customer->customer_id; ?>"
                                                >
                                                    <i class="fa fa-trash-alt"></i><span class="sr-only">Delete</span>
                                                </button>
                                            </td>
                                            <div
                                                class="modal modal-alert fade"
                                                id="modal-delete-<?= $customer->customer_id; ?>"
                                                tabindex="-1"
                                                role="dialog"
                                                aria-labelledby="modal-delete"
                                                aria-hidden="true"
                                            >
                                                <div
                                                    class="modal-dialog"
                                                    role="document"
                                                >
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                <i class="fa fa-exclamation-triangle text-red mr-1"></i> Konfirmasi Hapus
                                                            </h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Apakah anda yakin akan menghapus customer <span class="font-weight-bold"><?= $customer->name; ?></span>?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button
                                                                type="button"
                                                                class="btn btn-danger"
                                                                onclick="location.href='<?= base_url('customer/delete/' . $customer->customer_id . ''); ?>'"
                                                                data-dismiss="modal"
                                                            >Delete</button>
                                                            <button
                                                                type="button"
                                                                class="btn btn-light"
                                                                data-dismiss="modal"
                                                            >Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </tr>
                                        <?php ++$i ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <p class="text-center">Data tidak tersedia</p>
                    <?php endif; ?>
                    <?= $pagination ?? null; ?>
                </div>
                <!-- Modals -->
                <div
                    class="modal modal-alert fade"
                    id="modal-add"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="modal-add"
                    aria-hidden="true"
                >
                    <div
                        class="modal-dialog"
                        role="document"
                    >
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Tambah Customer</h5>
                            </div>
                            <form
                                id="add-customer-form"
                                method="post"
                                action="<?= base_url('customer/add_customer/'); ?>"
                            >
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label
                                            for="name"
                                            class="font-weight-bold"
                                        >
                                            Nama
                                            <abbr title="Required">*</abbr>
                                        </label>
                                        <input
                                            type="text"
                                            name="name"
                                            id="name"
                                            class="form-control"
                                        />
                                        <small id="error-name" class="d-none error-message text-danger">Nama wajib diisi!</small>
                                    </div>
                                    <div class="form-group">
                                        <label
                                            for="address"
                                            class="font-weight-bold"
                                        >Alamat
                                        </label>
                                        <input
                                            type="text"
                                            name="address"
                                            id="address"
                                            class="form-control"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label
                                            for="phone-number"
                                            class="font-weight-bold"
                                        >Nomor Telepon
                                            <abbr title="Required">*</abbr>
                                        </label>
                                        <input
                                            type="text"
                                            name="phone-number"
                                            id="phone-number"
                                            class="form-control"
                                        />
                                        <small id="error-phone-number" class="d-none error-message text-danger">Nomor telepon wajib diisi!</small>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label
                                                    for="type"
                                                    class="font-weight-bold"
                                                >Jenis Customer<abbr title="Required">*</abbr></label>

                                                <?= form_dropdown('type', $customer_type, null, 'id="edit-type" class="form-control custom-select d-block"'); ?>
                                                <small id="error-type" class="d-none error-message text-danger">Jenis customer wajib diisi!</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >Save</button>
                                    <button
                                        type="button"
                                        class="btn btn-light"
                                        data-dismiss="modal"
                                    >Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div
                    class="modal modal-alert fade"
                    id="modal-edit"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="modal-edit"
                    aria-hidden="true"
                >
                    <div
                        class="modal-dialog"
                        role="document"
                    >
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Customer</h5>
                            </div>
                            <form
                                id="edit-customer-form"
                                method="post"
                                action="<?= base_url('customer/edit/'); ?>"
                            >
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label
                                            for="edit-name"
                                            class="font-weight-bold"
                                        >
                                            Nama
                                            <abbr title="Required">*</abbr>
                                        </label>
                                        <input
                                            type="text"
                                            name="edit-id"
                                            id="edit-id"
                                            class="form-control"
                                            hidden
                                        />
                                        <input
                                            type="text"
                                            name="edit-name"
                                            id="edit-name"
                                            class="form-control"
                                        />
                                        <small id="error-edit-name" class="d-none error-message text-danger">Nama customer wajib diisi!</small>
                                    </div>
                                    <div class="form-group">
                                        <label
                                            for="edit-address"
                                            class="font-weight-bold"
                                        >Alamat
                                        </label>
                                        <input
                                            type="text"
                                            name="edit-address"
                                            id="edit-address"
                                            class="form-control"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label
                                            for="edit-phone-number"
                                            class="font-weight-bold"
                                        >Nomor Telepon
                                            <abbr title="Required">*</abbr>
                                        </label>
                                        <input
                                            type="text"
                                            name="edit-phone-number"
                                            id="edit-phone-number"
                                            class="form-control"
                                        />
                                        <small id="error-edit-phone-number" class="d-none error-message text-danger">Nomor telepon customer wajib diisi!</small>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label
                                                    for="edit-type"
                                                    class="font-weight-bold"
                                                >Jenis Customer<abbr title="Required">*</abbr></label>

                                                <?= form_dropdown('edit-type', $customer_type, null, 'id="edit-type" class="form-control custom-select d-block"'); ?>
                                                <small id="error-edit-type" class="d-none error-message text-danger">Jenis customer wajib diisi!</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >Save</button>
                                    <button
                                        type="button"
                                        class="btn btn-light"
                                        data-dismiss="modal"
                                    >Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div
                    class="modal modal-alert fade"
                    id="modal-discount"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="modal-discount"
                    aria-hidden="true"
                >
                    <div
                        class="modal-dialog"
                        role="document"
                    >
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Diskon</h5>
                            </div>
                            <form
                                id="update-discount-form"
                                method="post"
                                action="<?= base_url('customer/edit_discount/'); ?>"
                            >
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label
                                            for="discount-distributor"
                                            class="font-weight-bold"
                                        >
                                            Distributor
                                            <abbr title="Required">*</abbr>
                                        </label>
                                        <input
                                            type="number"
                                            name="discount-distributor"
                                            id="discount-distributor"
                                            min=0
                                            max=100
                                            class="form-control"
                                            value="<?= $discount[0]->discount ?>"
                                        />
                                        <small id="error-discount-distributor" class="d-none error-message text-danger">Masukkan diskon antara 0-100 untuk distributor!</small>
                                    </div>
                                    <div class="form-group">
                                        <label
                                            for="discount-reseller"
                                            class="font-weight-bold"
                                        >Reseller
                                            <abbr title="Required">*</abbr>
                                        </label>
                                        <input
                                            type="number"
                                            name="discount-reseller"
                                            id="discount-reseller"
                                            min=0
                                            max=100
                                            class="form-control"
                                            value="<?= $discount[1]->discount ?>"
                                        />
                                        <small id="error-discount-reseller" class="d-none error-message text-danger">Masukkan diskon antara 0-100 untuk reseller!</small>
                                    </div>
                                    <div class="form-group">
                                        <label
                                            for="discount-author"
                                            class="font-weight-bold"
                                        >Penulis
                                            <abbr title="Required">*</abbr>
                                        </label>
                                        <input
                                            type="number"
                                            name="discount-author"
                                            id="discount-author"
                                            min=0
                                            max=100
                                            class="form-control"
                                            value="<?= $discount[2]->discount ?>"
                                        />
                                        <small id="error-discount-author" class="d-none error-message text-danger">Masukkan diskon antara 0-100 untuk penulis!</small>
                                    </div>
                                    <div class="form-group">
                                        <label
                                            for="discount-member"
                                            class="font-weight-bold"
                                        >Member
                                            <abbr title="Required">*</abbr>
                                        </label>
                                        <input
                                            type="number"
                                            name="discount-member"
                                            id="discount-member"
                                            min=0
                                            max=100
                                            class="form-control"
                                            value="<?= $discount[3]->discount ?>"
                                        />
                                        <small id="error-discount-member" class="d-none error-message text-danger">Masukkan diskon antara 0-100 untuk member!</small>
                                    </div>
                                    <div class="form-group">
                                        <label
                                            for="discount-general"
                                            class="font-weight-bold"
                                        >Umum
                                            <abbr title="Required">*</abbr>
                                        </label>
                                        <input
                                            type="number"
                                            name="discount-general"
                                            id="discount-general"
                                            min=0
                                            max=100
                                            class="form-control"
                                            value="<?= $discount[4]->discount ?>"
                                        />
                                        <small id="error-discount-general" class="d-none error-message text-danger">Masukkan diskon antara 0-100 untuk umum!</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >Save</button>
                                    <button
                                        type="button"
                                        class="btn btn-light"
                                        data-dismiss="modal"
                                    >Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script>
//fetch customer info untuk edit
function modalEdit(customerId) {
    $.ajax({
        type: "GET",
        url: "<?= base_url('customer/api_customer_info/'); ?>" + customerId,
        dataType: "JSON",
        success: function(res) {
            console.log(res)
            $('#edit-id').val(res.customer_id)
            $('#edit-name').val(res.name)
            $('#edit-address').val(res.address)
            $('#edit-phone-number').val(res.phone_number)
            $("#edit-type option").removeAttr('selected'); 
            $("#edit-type option[value=" + res.type + "]").attr('selected', 'selected');
        },
        error: function(err) {
            alert(err)
        },
    });
    $('#modal-edit').modal('show')
}

$("#add-customer-form").submit(function(e) {
    e.preventDefault();
    var form = $(this);

    $.ajax({
        type: "POST",
        url: "<?= base_url('customer/add/'); ?>",
        data: form.serialize(),
        success: function(res) {
            // Parse server response to JSON
            var response = $.parseJSON(res)

            // No validation error
            if(response.status){
                location.href = "<?= base_url('customer'); ?>";
            }
            else{
                // Hide all error message
                $(".error-message").addClass('d-none');
                for (var i = 0; i < response.input_error.length; i++) 
                {
                    // Show error message
                    $('#' + response.input_error[i]).removeClass('d-none');
                }
                console.log(response)
            }
        },
        error: function(err) {
            alert(err)
        },
    });
})

$("#edit-customer-form").submit(function(e) {
    e.preventDefault();
    var form = $(this);

    $.ajax({
        type: "POST",
        url: "<?= base_url('customer/edit/'); ?>" + $('#edit-id').val(),
        data: form.serialize(),
        success: function(res) {
            // Parse server response to JSON
            var response = $.parseJSON(res)

            // No validation error
            if(response.status){
                location.href = "<?= base_url('customer'); ?>";
            }
            else{
                // Hide all error message
                $(".error-message").addClass('d-none');
                for (var i = 0; i < response.input_error.length; i++) 
                {
                    // Show error message
                    $('#' + response.input_error[i]).removeClass('d-none');
                }
                console.log(response)
            }
        },
        error: function(err) {
            alert(err)
        },
    });
})

$("#update-discount-form").submit(function(e) {
    e.preventDefault();
    var form = $(this);

    $.ajax({
        type: "POST",
        url: "<?= base_url('customer/edit_discount/'); ?>",
        data: form.serialize(),
        success: function(res) {
            // Parse server response to JSON
            var response = $.parseJSON(res)

            // No validation error
            if(response.status){
                location.href = "<?= base_url('customer'); ?>";
            }
            else{
                // Hide all error message
                $(".error-message").addClass('d-none');
                for (var i = 0; i < response.input_error.length; i++) 
                {
                    // Show error message
                    $('#' + response.input_error[i]).removeClass('d-none');
                }
                console.log(response)
            }
        },
        error: function(err) {
            console.log(err)
            alert(err)
        },
    });
})

</script>
