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
                    <form id="form_transfer" action="<?= base_url("book_transfer/add"); ?>" method="post">
                        <fieldset>
                            <legend>Form Pemindahan Buku</legend>
                            <div class="form-group">
                                <label for="requester">Peminta<abbr title="Required">*</abbr></label>
                                <input type="text" id="requester" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="receiver">Penerima<abbr title="Required">*</abbr></label>
                                <input type="text" id="receiver" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="destination">Tujuan Pemindahan<abbr title="Required">*</abbr></label>
                                <select name="destination" id="destination" class="form-control custom-select d-block" >
                                    <option value="showroom">Showroom</option>
                                    <option value="library">Perpustakaan</option>
                                </select>
                            </div>
                            <div class="form-group" id="input-perpustakaan" style="display:none">
                                <label for="library-id">Tujuan Perpustakaan
                                    <abbr title="Required">*</abbr>
                                </label>
                                <?= form_dropdown('library_id', get_dropdown_list_library(), $input->library_id, 'id="library-id" class="form-control custom-select d-block"'); ?>
                                <?= form_error('library_id'); ?>
                            </div>
                            <div class="row">
                                <div class="form-group col-10 mb-0">
                                    <label for='book-id'>Judul Buku</label>
                                    <?= form_dropdown('book_id', $book_transfer_available, 0, 'id="book-id" class="form-control custom-select d-block"'); ?>
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
                            <table class="table table-striped mb-0 table-responsive" id="book-list">
		                    	<thead>
		                    		<tr>
		                    			<th scope="col" style="min-width:50px">No</th>
		                    			<th scope="col" style="min-width:400px">Judul Buku</th>
                                        <th scope="col" style="min-width:200px">Penulis</th>
                                        <th scope="col" style="min-width:100px">Stok Gudang</th>
		                    			<th scope="col" style="min-width:100px">Jumlah</th>
		                    			<th scope="col" style="min-width:100px">Aksi</th>
		                    		</tr> 
		                    	</thead>
		                    	<tbody id="book-list-content">

		                    	</tbody>
		                    </table>
                            <br>
                        </fieldset>
                        <hr>
                        <input type="submit" disabled class="btn btn-primary ml-auto" id="submit-form" value="Submit" />
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

    $("#library-id").select2({
        placeholder: '-- Pilih --',
        dropdownParent: $('#app-main')
    });

    $('#destination').change(function(){
        if ($("#destination").val()=="library"){
            $("#input-perpustakaan").show()
            $('#library-id').prop('required', true)
        }
        else {
            $("#input-perpustakaan").hide()
            $('#library-id').prop('required', false)
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
                    var price = res.data.harga
                    var row1 = "<tr><th style='vertical-align: middle'>"+ number +"</th>"
                    var row2 = "<td style='vertical-align: middle'>" + book_name + "<input type='text' hidden name='book_id' class='book_id' value='"+book_id+"'></td>"
                    var row3 = "<td style='vertical-align: middle'>" + author + "</td>"
                    var row4 = "<td style='vertical-align: middle' class='stock'>"+stock+"</td>"
                    var row5 = "<td style='vertical-align: middle'><input type='number' value=0 min=1 max='"+stock+"'class='form-control quantity' name='quantity'></td>"
                    var row6 = "<td style='vertical-align: middle'></button><button type='button' class='btn btn-danger btn-md remove-book'>Hapus</td></tr>"
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

    $("#form_transfer").submit(function(e) {
        e.preventDefault();
        if ($("#destination").val()=="showroom"){
            $("#library-id").val("")
        }
        data = {
            'destination': $("#destination").val(),
            'library_id': $("#library-id").val(),
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
        // console.log(data)
        $.ajax({
            type: "POST",
            url: "<?= base_url("book_transfer/add"); ?>",
            data: data,
            datatype: 'JSON',
            success: function(result) {
                location.href = "<?= base_url('book_transfer'); ?>";
                console.log("sukses")
            },
            error: function(req, err) {
                console.log(err)
            }
        });
    })

});
</script>