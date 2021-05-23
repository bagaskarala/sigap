<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('proforma'); ?>">Proforma</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">Form</a>
            </li>
        </ol>
    </nav>
</header>
<div class="page-section">
    <div class="row">
        <div class="col-md-12">
            <section class="card">
                <div class="card-body">
                    <form
                        id="proforma_form"
                        method="post"
                        action="<?= $form_action ?>"
                    >
                        <legend>Form Tambah Proforma</legend>
                        <?php if($form_type == 'edit'): ?>
                            <div
                                id="proforma-number"
                                style="display: none;"
                            >
                                <div class="form-group">
                                    <input
                                        type="text"
                                        name="number"
                                        id="number"
                                        class="form-control"
                                        hidden
                                    />
                                </div>
                            </div>
                        <?php endif ?>
                        <div class="form-group">
                            <label
                                for="due-date"
                                class="font-weight-bold"
                            >
                                Jatuh Tempo
                                <abbr title="Required">*</abbr></label>
                            <div class="input-group mb-3">
                                <input
                                    name="due-date"
                                    id="due-date"
                                    class="form-control dates"
                                    <?php $current_date = date("Y/m/d", strtotime("+ 3 day")) ?>
                                    value="<?= $current_date; ?>"
                                />
                                <div class="input-group-append">
                                    <button
                                        class="btn btn-outline-secondary"
                                        type="button"
                                        id="due_clear"
                                    >Clear</button>
                                </div>
                            </div>
                            <small
                                id="error-due-date"
                                class="d-none error-message text-danger"
                            >Jatuh Tempo wajib diisi!</small>
                        </div>
                        <hr class="my-4">

                        <div class="form-group">
                            <label for="customer-id" class="font-weight-bold mb-0">
                                Customer
                            </label>
                            <div class="form-group mb-4">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a
                                            id="tab-customer-existing"
                                            class="nav-link active show"
                                            data-toggle="tab"
                                            href="#customer-existing"
                                        ><i class="fa fa-database"></i> Pilih Customer Dari Database</a>
                                    </li>
                                    <li class="nav-item">
                                        <a
                                            id="tab-customer-new"
                                            class="nav-link"
                                            data-toggle="tab"
                                            href="#customer-new"
                                        ><i class="fa fa-user-plus"></i> Tambah Customer Baru</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content mt-4">
                                <!-- Customer Dari Database -->
                                <div
                                    class="tab-pane fade active show"
                                    id="customer-existing"
                                >
                                    <div class="form-group col-md-8 p-0">
                                        <?= form_dropdown('customer-id', get_customer_list(), 0, 'id="customer-id" class="form-control custom-select d-block"'); ?>
                                    </div>

                                    <div
                                        id="customer-info"
                                        style="display: none;"
                                    >
                                        <table class="table table-striped table-bordered mb-0">
                                            <tbody>
                                                <tr>
                                                    <td width="175px"> Nama Pembeli </td>
                                                    <td id="info-customer-name"></td>
                                                </tr>
                                                <tr>
                                                    <td width="175px"> Alamat </td>
                                                    <td id="info-address"></td>
                                                </tr>
                                                <tr>
                                                    <td width="175px"> Nomor Telepon </td>
                                                    <td id="info-phone-number"></td>
                                                </tr>
                                                <tr>
                                                    <td width="175px"> Tipe Membership </td>
                                                    <td id="info-type"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Tambah Customer Baru -->
                                <div
                                    class="tab-pane fade"
                                    id="customer-new"
                                >
                                    <div
                                        id="new-customer-info"
                                        class="col-md-6"
                                    >
                                        <div class="form-group">
                                            <label
                                                for="new-customer-name"
                                                class="font-weight-bold"
                                            >
                                                Nama
                                                <abbr title="Required">*</abbr>
                                            </label>
                                            <input
                                                type="text"
                                                name="new-customer-name"
                                                id="new-customer-name"
                                                class="form-control"
                                            />
                                            <small
                                                id="error-new-customer-name"
                                                class="d-none error-message text-danger"
                                            >Nama wajib diisi!</small>
                                        </div>
                                        <div class="form-group">
                                            <label
                                                for="new-customer-address"
                                                class="font-weight-bold"
                                            >Alamat
                                            </label>
                                            <input
                                                type="text"
                                                name="new-customer-address"
                                                id="new-customer-address"
                                                class="form-control"
                                            />
                                        </div>
                                        <div class="form-group">
                                            <label
                                                for="new-customer-phone-number"
                                                class="font-weight-bold"
                                            >Nomor Telepon
                                                <abbr title="Required">*</abbr>
                                            </label>
                                            <input
                                                type="text"
                                                name="new-customer-phone-number"
                                                id="new-customer-phone-number"
                                                class="form-control"
                                            />
                                            <small
                                                id="error-new-customer-phone-number"
                                                class="d-none error-message text-danger"
                                            >Nomor telepon wajib diisi!</small>
                                        </div>
                                        <div class="form-group">
                                            <label
                                                for="new-customer-type"
                                                class="font-weight-bold"
                                            >Jenis Customer<abbr title="Required">*</abbr></label>
                                            <?= form_dropdown('new-customer-type', $customer_type, null, 'id="new-customer-type" class="form-control custom-select d-block w-100"'); ?>
                                            <small
                                                id="error-new-customer-type"
                                                class="d-none error-message text-danger"
                                            >Jenis customer wajib diisi!</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small
                            id="error-customer-info"
                            class="d-none error-message text-danger"
                        >Data customer wajib diisi!</small>

                        <hr class="my-4">

                        <div class="row">
                            <div class="form-group col-md-8">
                                <label
                                    for="book_id"
                                    class="font-weight-bold"
                                >Judul buku</label>
                                <?= form_dropdown('book_id', $dropdown_book_options, 0, 'id="book-id" class="form-control custom-select d-block"'); ?>
                                <small
                                    id="error-no-book"
                                    class="d-none error-message text-danger"
                                >Buku wajib diisi!</small>
                            </div>
                        </div>

                        <div
                            id="book-info"
                            style="display:none"
                        >
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <td width="175px"> Judul Buku </td>
                                            <td id="info-book-title"></td>
                                        </tr>
                                        <tr>
                                            <td width="175px"> Penulis Buku </td>
                                            <td id="info-book-author"></td>
                                        </tr>
                                        <tr>
                                            <td width="175px"> ISBN </td>
                                            <td id="info-isbn"></td>
                                        </tr>
                                        <tr>
                                            <td width="175px"> Tahun Terbit </td>
                                            <td id="info-year"></td>
                                        </tr>
                                        <tr>
                                            <td width="175px"> Harga </td>
                                            <td id="info-price"></td>
                                        </tr>
                                        <tr>
                                            <td width="175px"> Stock </td>
                                            <td id="info-stock"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br>

                            <div class="row">
                                <div class="form-group col-4 col-md-2">
                                    <label
                                        for="qty"
                                        class="font-weight-bold"
                                    >Jumlah</label>
                                    <input
                                        type="number"
                                        min="1"
                                        name="qty"
                                        id="qty"
                                        value="1"
                                        class="form-control"
                                    />
                                </div>
                                <div class="form-group col-4 col-md-2">
                                    <label
                                        for="discount"
                                        class="font-weight-bold"
                                    >Diskon (%)</label>
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        name="discount"
                                        id="discount"
                                        value="0"
                                        class="form-control"
                                    />
                                </div>
                                <div class="form-group col-4 col-md-2">
                                    <label
                                        for="add-item"
                                        class="font-weight-bold"
                                    >Tambah Barang</label>
                                    <button
                                        type="button"
                                        id="add-item"
                                        name="add-item"
                                        class="form-control btn btn-primary text-white"
                                    >Tambah Barang</button>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr class="text-center">
                                        <th
                                            scope="col"
                                            style="width:40%;"
                                        >Judul Buku</th>
                                        <th
                                            scope="col"
                                            style="width:15%;"
                                        >Harga</th>
                                        <th
                                            scope="col"
                                            style="width:15%;"
                                        >Jumlah</th>
                                        <th
                                            scope="col"
                                            style="width:15%;"
                                        >Diskon</th>
                                        <th
                                            scope="col"
                                            style="width:15%;"
                                        >Total</th>
                                        <th
                                            scope="col"
                                            style="width:8%;"
                                        >&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody id="proforma_items">
                                    <!-- Items -->
                                    <?php if($form_type == 'edit'): ?>
                                        <?php foreach ($proforma_book as $books) : ?>
                                            <tr class="text-center">
                                            <td class="align-middle text-left font-weight-bold"><?= $books->book_title ?>
                                                <input
                                                    type="text"
                                                    hidden
                                                    name="proforma_book_id[]"
                                                    class="form-control"
                                                    value="<?= $books->book_id ?>"
                                                />
                                            </td>
                                            <td class="align-middle">Rp <?= $books->price ?>
                                                <input
                                                    id="proforma-book-price-<?= $books->book_id ?>"
                                                    type="number"
                                                    hidden
                                                    name="proforma_book_price[]"
                                                    class="form-control"
                                                    value="<?= $books->price ?>"
                                                />
                                            </td>
                                            <td class="align-middle">
                                                <input
                                                    id="proforma-book-qty-<?= $books->book_id ?>"
                                                    type="number"
                                                    required
                                                    name="proforma_book_qty[]"
                                                    class="form-control"
                                                    value="<?= $books->qty ?>"
                                                    onchange="updateQty(<?= $books->book_id ?>)"
                                                />
                                            </td>
                                            <td class="align-middle"><?= $books->discount ?>%
                                                <input
                                                    id="proforma-book-discount-<?= $books->book_id ?>"
                                                    type="number"
                                                    hidden
                                                    name="proforma_book_discount[]"
                                                    class="form-control"
                                                    value="<?= $books->discount ?>"
                                                />
                                            </td>
                                            <td class="align-middle">
                                                <span id="proforma-book-total-<?= $books->book_id ?>">
                                                    Rp 
                                                    <?php
                                                    $total = $books->qty * $books->price * (1 - $books->discount/100);
                                                    echo $total;
                                                    ?>
                                                </span>
                                            </td>
                                            <td class="align-middle"><button
                                                    type="button"
                                                    class="btn btn-danger remove"
                                                >Hapus</button></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- button -->
                        <input
                            type="submit"
                            class="btn btn-primary"
                            value="Submit"
                        />
                        <a
                            class="btn btn-secondary"
                            href="<?= base_url($pages); ?>"
                            role="button"
                        >Back</a>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    console.log("<?= $form_action ?>")


    $('#customer-id').change(function(e) {
        const customerId = e.target.value
        $('#new-customer-name').val('')
        $('#new-customer-type').val('')

        if(customerId != '')
        {
            $.ajax({
            type: "GET",
            url: "<?= base_url('invoice/api_get_customer/'); ?>" + customerId,
            datatype: "JSON",
            success: function(res) {
                $('#customer-info').show()
                $('#discount').val(res.data.discount)
                $('#info-customer-name').html(res.data.name)
                $('#info-address').html(res.data.address)
                $('#info-phone-number').html(res.data.phone_number)
                $('#info-type').html(res.data.type)
            },
            error: function(err) {
                $('#customer-info').hide()
            },
        });
        }    
    })

    //hilangin buku yg sudah ada
    <?php if($form_type == 'edit'): ?>
        <?php foreach ($proforma_book as $books) : ?>
            $('#book-id option[value="' + <?= $books->book_id ?> + '"]').remove()
        <?php endforeach; ?>
        $('#discount').val('<?= $discount ?>')
        $('#customer-info').show()
        $('#customer-id').val('<?= $proforma->customer_id ?>').trigger('change')
    <?php endif ?>
    
    $('#tab-customer-new').click(function() {
        $('#customer-info').hide()
        $('#customer-id').val('').trigger('change')
    })

    $('#tab-customer-existing').click(function() {
        $('#new-customer-name').val('')
        $('#new-customer-address').val('')
        $('#new-customer-phone-number').val('')
        $('#new-customer-type').val('')
    })    

    const $flatpickr = $('.dates').flatpickr({
        altInput: true,
        altFormat: 'j F Y',
        dateFormat: 'Y-m-d',
        enableTime: false
    });

    $("#due_clear").click(function() {
        $flatpickr.clear();
    })

    $("#customer-id").select2({
        placeholder: '-- Pilih --',
        dropdownParent: $('#app-main')
    });
    $("#book-id").select2({
        placeholder: '-- Pilih --',
        dropdownParent: $('#app-main')
    });

    $('#add-item').click(function() {
        // Judul buku harus dipilih
        if (document.getElementById('book-id').value === '') {
            alert("Silakan Pilih Judul Buku!");
            return
        }
        // Jumlah buku 1 - stock
        var qty = document.getElementById('qty')
        if ((parseInt(qty.value) < 1) || parseInt(qty.value) > parseInt(qty.max)) {
            alert("Jumlah buku minimal 1 dan tidak boleh melebihi stock!");
            return
        }
        // Diskon antara 0-100%
        var discount = document.getElementById('discount').value
        if (!((discount <= 100) && (discount >= 0))) {
            alert("Masukkan diskon antara 0 - 100!");
            return
        } else {
            add_book_to_invoice(qty.max);
            reset_book();
        }
    });

    $(document).on('click', '.remove', function() {
        $selector = $(this).closest("tr").children("td").first()
        var bookTitle = $selector.text()
        var bookId = $selector.children("input").val()
        $("#book-id").prepend(new Option(bookTitle, bookId))
        $(this).closest("tr").remove();
    });

    $('#book-id').change(function(e) {
        if(e.target.value != '') {
            const bookId = e.target.value
            $.ajax({
                type: "GET",
                url: "<?= base_url('invoice/api_get_book/'); ?>" + bookId,
                datatype: "JSON",
                success: function(res) {
                    var published_date = new Date(res.data.published_date);

                    $('#book-info').show()
                    $('#qty').attr({
                        "max": res.data.warehouse_present
                    });
                    $('#info-book-title').html(res.data.book_title)
                    $('#info-book-author').html(res.data.author_name)
                    $('#info-isbn').html(res.data.isbn)
                    $('#info-price').html(res.data.harga)
                    $('#info-year').html(published_date.getFullYear())
                    $('#info-stock').html(res.data.warehouse_present)
                },
                error: function(err) {
                    console.log(err);
                },
            });
        }
    })

    $('#new-customer-type').change(function(e) {
        var disc = 0
        var customerType = $(this).val()
        $.ajax({
            type: "GET",
            url: "<?= base_url('invoice/api_get_discount/'); ?>" + customerType,
            datatype: "JSON",
            success: function(res) {
                $('#discount').val(res.data.discount)
            },
            error: function(err) {
                console.log(err);
            },
        });
    })

    $("#proforma_form").submit(function(e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.
        var form = $(this);
        console.log(form.serialize())
        $.ajax({
            type: "POST",
            url: "<?= base_url($form_action) ?>",
            data: form.serialize(), // serializes the form's elements.
            success: function(result) {
                var response = $.parseJSON(result)
                //Validation Error
                if (response.status != true) {
                    $(".error-message").addClass('d-none');
                    for (var i = 0; i < response.input_error.length; i++) {
                        // Show error message
                        $('#' + response.input_error[i]).removeClass('d-none');
                    }
                } else {
                    location.href = "<?= base_url('proforma'); ?>";
                }
            },
            error: function(req, err) {
                console.log(err)
            }
        });
    })
});

function add_book_to_invoice(stock) {
    var bookId = document.getElementById('book-id');

    html = '<tr class="text-center">';

    // Judul option yang di select
    html += '<td class="align-middle text-left font-weight-bold">' + bookId.options[bookId.selectedIndex].text;
    html += '<input type="text" hidden name="proforma_book_id[]" class="form-control" value="' + bookId.value + '"/>';
    html += '</td>';

    // Harga
    html += '<td class="align-middle"> Rp ' + $('#info-price').text();
    html += '<input <input id="proforma-book-price-' + bookId.value + '" type="number" hidden name="proforma_book_price[]" class="form-control" value="' + $('#info-price').text() + '"/>';
    html += '</td>';

    // Jumlah
    html += '<td class="align-middle">';
    html += '<input id="proforma-book-qty-' + bookId.value + '" type="number" required name="proforma_book_qty[]" class="form-control" value="' + document.getElementById('qty').value + '" max="' + stock + '" onchange=updateQty(' + bookId.value + ')>';
    html += '</td>';

    // Diskon
    html += '<td class="align-middle">' + document.getElementById('discount').value + '%';
    html += '<input <input id="proforma-book-discount-' + bookId.value + '" type="number" hidden name="proforma_book_discount[]" class="form-control" value="' + document.getElementById('discount').value + '"/>';
    html += '</td>';

    // Total
    var totalPrice = (parseFloat($('#info-price').text())) * (parseFloat($('#qty').val())) * (1 - (parseFloat($('#discount').val()) / 100));
    html += '<td class="align-middle"> <span id="proforma-book-total-' + bookId.value + '"> Rp ' + parseFloat(totalPrice).toFixed(0) + '</span></td>';

    // Button Hapus
    html += '<td class="align-middle"><button type="button" class="btn btn-danger remove">Hapus</button></td></tr>';

    $('#proforma_items').append(html);
    $('#book-id option[value="' + bookId.value + '"]').remove()
}

function reset_book() {

    document.getElementById('qty').value = 1;
    $("#book-id").val('').trigger('change')
    $('#book-info').hide();
}

function updateQty(book_id) {
    var qty = $('#proforma-book-qty-' + book_id).val();
    var price = $('#proforma-book-price-' + book_id).val();
    var discount = $('#proforma-book-discount-' + book_id).val();
    var total_html = $('#proforma-book-total-' + book_id);

    var total = Math.round(qty * price * (1 - discount/100));
    total_html.html('Rp ' + total)
}

</script>
