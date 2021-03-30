<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('invoice'); ?>">Faktur</a>
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
                        id="invoice_form"
                        method="post"
                        action="<?= base_url("invoice/add_invoice"); ?>"
                        redirect="<?= base_url("invoice"); ?>"
                    >
                        <legend>Form Tambah Faktur</legend>
                        <div class="form-group">
                            <label
                                for="type"
                                class="font-weight-bold"
                            >Jenis Faktur<abbr title="Required">*</abbr></label>

                            <?= form_dropdown('type', $invoice_type, 0, 'id="type" class="form-control custom-select d-block"'); ?>
                        </div>
                        <div
                            class="form-group"
                            style="display: none;"
                            id='sourceDropdown'
                        >
                            <label
                                for="type"
                                class="font-weight-bold"
                            >Asal Stok<abbr title="Required">*</abbr></label>
                            <?= form_dropdown('source', $source, 0, 'id="source" class="form-control custom-select d-block"'); ?>
                        </div>
                        <div
                            id="invoice-type"
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
                                />
                                <div class="input-group-append">
                                    <button
                                        class="btn btn-outline-secondary"
                                        type="button"
                                        id="due_clear"
                                    >Clear</button>
                                </div>
                            </div>
                            <?= form_error('due_date'); ?>
                        </div>
                        <hr class="my-4">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label
                                    for="customer-id"
                                    class="font-weight-bold"
                                >Customer</label>
                                <div class="row">
                                    <div class="form-group col-md-8">
                                        <?= form_dropdown('customer-id', get_customer_list(), 0, 'id="customer-id" class="form-control custom-select d-block"'); ?>
                                    </div>
                                    <div class="form-group col-md-4"><input
                                            class="btn btn-primary"
                                            value="Customer Baru"
                                            id="tambahCustomer"
                                            readonly
                                        /></div>
                                </div>

                            </div>
                        </div>
                        <div
                            id="new-customer-info"
                            class="col-md-4"
                            style="display: none"
                        >
                            <label
                                for="new-customer-name"
                                class="font-weight-bold"
                            >Nama</label>
                            <input
                                name="new-customer-name"
                                id="new-customer-name"
                                class="form-control"
                            /><label
                                for="new-customer-type"
                                class="font-weight-bold"
                            >Membership</label>
                            <?= form_dropdown('new-customer-type', $customer_type, 0, 'id="new-customer-type" class="form-control custom-select d-block"'); ?>
                        </div>
                        <div
                            id="customer-info"
                            style="display: none;"
                        >
                            <table class="table table-striped table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <td width="175px"> Nama Pembeli </td>
                                        <td id="info-customer-name"></a></td>
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
                        <hr class="my-4">
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label
                                    for="book_id"
                                    class="font-weight-bold"
                                >Judul buku</label>
                                <?= form_dropdown('book_id', $dropdown_book_options, 0, 'id="book-id" class="form-control custom-select d-block"'); ?>
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
                                        for="add_item"
                                        class="font-weight-bold"
                                    >Tambah Barang</label>
                                    <button
                                        type="button"
                                        id="add_item"
                                        name="add_item"
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
                                <tbody id="invoice_items">
                                    <!-- Items -->
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
    $('#type').val('')
    $('#source').val('')

    $('#tambahCustomer').click(function() {
        var value = $('#tambahCustomer').val()
        if (value == "Customer Baru") {
            $('#tambahCustomer').val("Hapus Customer")
            $('#tambahCustomer').removeClass('btn-primary')
            $('#tambahCustomer').addClass('btn-danger')
            $('#new-customer-info').show()
            $('#customer-info').hide()
            $('#customer-id').val('')
        } else {
            $('#tambahCustomer').val("Customer Baru")
            $('#tambahCustomer').removeClass('btn-danger')
            $('#tambahCustomer').addClass('btn-primary')
            $('#new-customer-info').hide()
            $('#new-customer-name').val('')
            $('#new-customer-type').val('')
        }
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

    function add_book_to_invoice() {
        var bookId = document.getElementById('book-id');

        html = '<tr class="text-center">';

        // Judul option yang di select
        html += '<td class="align-middle text-left font-weight-bold">' + bookId.options[bookId.selectedIndex].text;
        html += '<input type="text" hidden name="invoice_book_id[]" class="form-control" value="' + bookId.value + '"/>';
        html += '</td>';

        // Harga
        html += '<td class="align-middle">' + $('#info-price').text();
        html += '<input type="number" hidden name="invoice_book_price[]" class="form-control" value="' + $('#info-price').text() + '"/>';
        html += '</td>';

        // Jumlah
        html += '<td class="align-middle">' + document.getElementById('qty').value;
        html += '<input type="number" hidden name="invoice_book_qty[]" class="form-control" value="' + document.getElementById('qty').value + '"/>';
        html += '</td>';

        // Diskon
        html += '<td class="align-middle">' + document.getElementById('discount').value + '%';
        html += '<input type="number" hidden name="invoice_book_discount[]" class="form-control" value="' + document.getElementById('discount').value + '"/>';
        html += '</td>';

        // Total
        var totalPrice = (parseFloat($('#info-price').text())) * (parseFloat($('#qty').val())) * (1 - (parseFloat($('#discount').val()) / 100));
        html += '<td class="align-middle">' + totalPrice + '</td>';

        // Button Hapus
        html += '<td class="align-middle"><button type="button" class="btn btn-danger remove">Hapus</button></td></tr>';

        $('#invoice_items').append(html);
        $('#book-id option[value="' + bookId.value + '"]').remove()
    }

    function reset_book() {
        document.getElementById('qty').value = 1;
        $("#book-id").val('').trigger('change')
        $('#book-info').hide();
    }

    $('#add_item').click(function() {
        // Judul buku harus dipilih
        if (document.getElementById('book-id').value === '') {
            alert("Silakan Pilih Judul Buku!");
            return
        }
        // Jumlah buku 1 - stock
        var qty = document.getElementById('qty')
        if ((qty.value < 1) || qty.value > qty.max) {
            alert("Jumlah buku minimal 1 dan tidak boleh melebihi stock!");
            return
        }
        // Diskon antara 0-100%
        var discount = document.getElementById('discount').value
        if (!((discount <= 100) && (discount >= 0))) {
            alert("Masukkan diskon antara 0 - 100!");
            return
        } else {
            add_book_to_invoice();
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
        const bookId = e.target.value
        $.ajax({
            type: "GET",
            url: "<?= base_url('invoice/api_get_book/'); ?>" + bookId,
            datatype: "JSON",
            success: function(res) {
                var published_date = new Date(res.data.published_date);

                $('#book-info').show()
                $('#qty').attr({
                    "max": res.data.stock
                });
                $('#info-book-title').html(res.data.book_title)
                $('#info-isbn').html(res.data.isbn)
                $('#info-price').html(res.data.harga)
                $('#info-year').html(published_date.getFullYear())
                $('#info-stock').html(res.data.stock)
            },
            error: function(err) {
                console.log(err);
            },
        });
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

    $('#customer-id').change(function(e) {
        const customerId = e.target.value
        $('#tambahCustomer').val("Customer Baru")
        $('#tambahCustomer').removeClass('btn-danger')
        $('#tambahCustomer').addClass('btn-primary')
        $('#new-customer-info').hide()
        $('#new-customer-name').val('')
        $('#new-customer-type').val('')
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
    })

    $('#type').change(function(e) {
        const type = e.target.value
        if (type == 'cash') {
            $('#sourceDropdown').show()
        } else {
            $('#sourceDropdown').hide()
            $('#source').val('')
        }
        $.ajax({
            type: "GET",
            url: "<?= base_url('invoice/api_get_last_invoice_number/'); ?>" + type,
            datatype: "JSON",
            success: function(res) {
                $('#invoice-type').show()
                $('#number').val(res.data)
            },
            error: function(err) {
                $('#invoice-type').hide()
            },
        });
    })

    $("#invoice_form").submit(function(e) {

        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var url = form.attr('action');
        var redirect = form.attr('redirect');
        var form_valid = "TRUE";
        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            success: function(result) {
                //Validation Error
                if (!(result === "no_errors")) {
                    alert("Semua data Faktur harus diisi dan Faktur tidak boleh kosong!");
                    form_valid = "FALSE";
                }
            },
            complete: function() {
                if (form_valid == "TRUE") {
                    location.href = redirect;
                }
            }
        });
    })



});
</script>