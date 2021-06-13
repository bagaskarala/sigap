<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('book_non_sales'); ?>">Permintaan Buku Non Penjualan</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">Form Tambah</a>
            </li>
        </ol>
    </nav>
</header>
<div class="page-section">
    <div class="row">
        <div class="col-md-12">
            <section class="card">
                <div class="card-body">
                    <form id="form_non_sales" action="<?= base_url("book_non_sales/add"); ?>" method="post">
                        <fieldset>
                            <legend>Form Buku Non Penjualan</legend>
                            <div class="form-group">
                                <label for="name">Nama / Acara<abbr title="Required">*</abbr></label>
                                <input type="text" id="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Alamat<abbr title="Required">*</abbr></label>
                                <?= form_textarea([
                                'name'  => "address",
                                'class' => 'form-control',
                                'id'    => "address",
                                'rows'  => '3',
                                'value' => $input->address,
                                'required' => true
                            ]); ?>
                            <?= form_error('address'); ?>
                            </div>
                            <div class="form-group">
                                <label for="requester">Peminta<abbr title="Required">*</abbr></label>
                                <input type="text" id="requester" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="receiver">Penerima<abbr title="Required">*</abbr></label>
                                <input type="text" id="receiver" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="type">Tipe Permintaan Non Penjualan<abbr title="Required">*</abbr></label>
                                <select name="type" id="type" class="form-control custom-select d-block">
                                    <option value="presentcopies">Present Copies</option>
                                    <option value="doorprize">Doorprize</option>
                                    <option value="bedahbuku">Bedah Buku</option>
                                    <option value="dll">Lain-lain</option>
                                </select>
                                <div class="row">
                                    <div class="col" id="select_type">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-10 mb-0">
                                    <label for='book-id'>Judul Buku</label>
                                    <?= form_dropdown('book_id', $book_non_sales_available, 0, 'id="book-id" class="form-control custom-select d-block"'); ?>
                                    <?= form_error('book_id'); ?>
                                </div>
                                <div class="form-group col-2 mb-0">
                                    <label for="add-book">Tambah Buku</label>
                                    <button disabled type="button" id="add-book" name="add-book" 
                                    class="form-control btn btn-primary text-white">
                                    Tambah Buku</button>
                                </div>
                            </div>
                            <br>
                            <table class="table table-striped" id="book-list">
		                    	<thead>
		                    		<tr>
		                    			<th style="text-align: center; width:5%">No</th>
		                    			<th style="text-align: center; width:40%">Judul Buku</th>
                                        <th style="text-align: center; width:10%">Penulis</th>
                                        <th style="text-align: center; width:15%">Stok</th>
		                    			<th style="text-align: center; width:15%">Jumlah</th>
		                    			<th style="text-align: center; width:15%">Aksi</th>
		                    		</tr>
		                    	</thead>
		                    	<tbody id="book-list-content">

		                    	</tbody>
		                    </table>
                        </fieldset>
                        <hr>
                            <!-- button -->
                        <input type="submit" class="btn btn-primary ml-auto" id="submit-form" disabled value="Submit" />
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

    $('#type').change(function(){
        var input_dll = '<small>Silakan isi keterangan tipe non penjualan di bawah ini</small><input type="text" id="notes" class="form-control" required>'
        if ($("#type").val()=='dll'){
            $("#select_type").append(input_dll);
        }
        else{
            $("#select_type").empty();
        }
    })

    $('#book-id').change(function(){
        if ($("#book-id").val()){
            $('#add-book').prop('disabled', false)
        }
    })
    $('#add-book').click(function(){
        if ($("#book-id").val()){
            $("#no-stock-warning").html('')
            var number = $("#book-list-content tr").length+1
            var book_name = $('#book-id option:selected').text()
            var book_id = $('#book-id').val()
            $.ajax({
                type: "GET",
                url: "<?= base_url('book_stock/api_get_by_book_id/'); ?>" + book_id,
                datatype: "JSON",
                success: function(res) {
                    var stock = res.data.warehouse_present
                    var author = res.data.author_name
                    var row1 = "<tr><th style='vertical-align: middle;text-align: center'>"+ number +"</th>"
                    var row2 = "<td style='vertical-align: middle'>" + book_name + "<input type='text' hidden name='book_id' class='book_id' value='"+book_id+"'></td>"
                    var row3 = "<td style='vertical-align: middle'>" + author + "</td>"
                    var row4 = "<td style='vertical-align: middle;text-align: center' class='stock'>"+stock+"</td>"
                    var row5 = "<td style='vertical-align: middle'><input type='number' value=0 min=1 max='"+stock+"'class='form-control quantity' name='quantity'></td>"
                    var row6 = "<td style='vertical-align: middle;text-align: center'></button><button type='button' class='btn btn-danger btn-md remove-book'>Hapus</td></tr>"
                    var html = row1+row2+row3+row4+row5+row6
                    $('#book-id option[value="' + book_id + '"]').remove()
                    $('#submit-form').prop('disabled', false)
                    $("#book-list-content").append(html);
                },
                error: function(err) {
                    console.log(err);
                },
            });
        }
    })
    
    $("#book-list-content").on('click','.remove-book', function(event){
        let selector = $(this).closest("tr").children("td").first()
        let book_title = selector.text()
        let book_id = selector.children("input").val()
        $(this).closest('tr').remove();
        $("#book-list-content tr").each(function(index){
            $(this).children('th').html(index+1)
        });
        if ($('#book-list tr').length <= 1){
            $('#submit-form').prop('disabled', true)
        }
        $("#book-id").prepend(new Option(book_title, book_id))
    });

    $("#form_non_sales").submit(function(e) {
        e.preventDefault();
        data = {
            'type': $("#type").val(),
            'name': $("#name").val(),
            'address': $("#address").val(),
            'notes': $("#notes").val(),
            'requester': $("#requester").val(),
            'receiver': $("#receiver").val(),
            'book_list': []
        }
        $("#book-list-content tr").each(function(){
            book_data = {
                'book_id': $(this).find("input.book_id").val(),
                'qty': Number($(this).find("input.quantity").val())
            }
            data['book_list'].push(book_data)
        })
        console.log(data)
        $.ajax({
            type: "POST",
            url: "<?= base_url("book_non_sales/add"); ?>",
            data: data,
            datatype: 'JSON',
            success: function(result) {
                location.href = "<?= base_url('book_non_sales'); ?>";
                console.log("sukses")
            },
            error: function(req, err) {
                console.log(err)
            }
        });
    })
});
</script>