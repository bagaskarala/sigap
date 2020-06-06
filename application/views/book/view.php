<?php $ceklevel = $this->session->userdata('level'); ?>
<!-- .page-title-bar -->
  <header class="page-title-bar">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="<?=base_url()?>"><span class="fa fa-home"></span></a>
        </li>
        <li class="breadcrumb-item">
          <a href="<?=base_url()?>">Penerbitan</a>
        </li>
                <li class="breadcrumb-item">
          <a href="<?=base_url('book')?>">Buku</a>
        </li>
        <li class="breadcrumb-item">
          <a class="text-muted"><?= $input->book_title ?></a>
        </li>
      </ol>
    </nav> 
  </header>
  <!-- /.page-title-bar -->
<!-- .page-section -->
<div class="page-section">
  <div class="d-xl-none">
    <button class="btn btn-danger btn-floated" type="button" data-toggle="sidebar">
      <i class="fa fa-th-list"></i>
    </button>
  </div>
  <!-- .card -->
  <section id="data-draft" class="card">
    <!-- .card-header -->
    <header class="card-header">
      <ul class="nav nav-tabs card-header-tabs">
        <li class="nav-item">
          <a class="nav-link active show" data-toggle="tab" href="#data-drafts">Data Buku</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#data-penulis">Data Penulis</a>
        </li>
      </ul>
    </header>
    <!-- /.card-header -->
    <!-- .card-body -->
    <div class="card-body">
    <?= isset($input->draft_id) ? form_hidden('draft_id', $input->draft_id) : '' ?>
    <!-- .tab-content -->
      <div id="myTabCard" class="tab-content">
        <div class="tab-pane fade active show" id="data-drafts">
          <!-- .table-responsive -->
        <div class="table-responsive">
          <!-- .table -->
          <table class="table table-striped table-bordered mb-0">
            <!-- tbody -->
            <tbody>
              <!-- tr -->
              <tr>
                <td width="200px"> Judul Buku </td>
                <td>
                  <?php if($ceklevel==='superadmin' or $ceklevel==='admin_penerbitan') { ?>
                    <form method="post" action="<?php echo site_url('book/update_judul'); ?>" class="form-inline">
                      <input type="hidden" name="judul_buku_id" value="<?php echo $input->book_id; ?>">
                      <input type="text" name="judul_buku_edit" value="<?php echo $input->book_title; ?>" class="form form-control" style="width: 90%;">
                      &nbsp;&nbsp;&nbsp;
                      <button class="btn btn-success" title="klik untuk menyimpan judul buku"><i class="fa fa-save"></i></button>
                    </form>
                  <?php }else{ ?>
                      <strong><?= $input->book_title ?></strong> 
                  <?php } ?>

                </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Kode Buku </td>
                <td><?= $input->book_code ?> </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Edisi Buku </td>
                <td><?= $input->book_edition ?> </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Halaman Buku </td>
                <td><?= $input->book_pages ?> </td>
              </tr>
              <!-- /tr -->
               <!-- tr -->
              <tr>
                <td width="200px"> ISBN </td>
                <td><?= $input->isbn ?> </td>
              </tr>
              <!-- /tr -->
               <!-- tr -->
              <tr>
                <td width="200px"> eISBN </td>
                <td><?= $input->eisbn ?> </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Kategori </td>
                <td><?=isset($input->category_id)? konversiID('category','category_id', $input->category_id)->category_name : ''?> </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Tema </td>
                <td><?=isset($input->theme_id)? konversiID('theme','theme_id', $input->theme_id)->theme_name : ''?> </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> File Buku </td>
                <td>
                <?php
                if(!empty($input->book_file)){
                  if(!empty($draft->print_file) and $draft->print_file == $input->book_file){
                    echo '<a data-toggle="tooltip" data-placement="right" title="'.$input->book_file.'" class="btn btn-success btn-xs my-0" href="'.base_url('draftfile/'.$input->book_file).'"><i class="fa fa-book"></i> File Buku</a>';
                  }else{
                    echo '<a data-toggle="tooltip" data-placement="right" title="'.$input->book_file.'" class="btn btn-success btn-xs my-0" href="'.base_url('bookfile/'.$input->book_file).'"><i class="fa fa-book"></i> File Buku</a>';
                  }
                }

                ?>

                <?=(!empty($input->book_file_link))? '<a data-toggle="tooltip" data-placement="right" title="'.$input->book_file_link.'" class="btn btn-success btn-xs my-0" target="_blank" href="'.$input->book_file_link.'"><i class="fa fa-external-link-alt"></i> External file</a>' : '' ?>
                </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> File Cover </td>
                <td>
                  <?=(!empty($draft->cover_file))? '<a data-toggle="tooltip" data-placement="right" title="'.$input->cover_file.'" class="btn btn-success btn-xs my-0" href="'.base_url('draft/download/coverfile/'.urlencode($draft->cover_file)).'"><i class="fa fa-file-image"></i> File Cover</a>' : '' ?>

                  <?=(!empty($draft->cover_file_link))? '<a data-toggle="tooltip" data-placement="right" title="'.$draft->cover_file_link.'" class="btn btn-success btn-xs my-0" target="_blank" href="'.$draft->cover_file_link.'"><i class="fa fa-external-link-alt"></i> External file</a>' : '' ?>
                </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Catatan Buku </td>
                <td><?= $input->book_notes ?></td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Referensi Draft </td>
                <td><a href="<?=base_url('draft/view/'.$input->draft_id) ?>"><?=$input->draft_title ?></a></td>
              </tr>
              <!-- /tr -->
            </tbody>
            <!-- /tbody -->
          </table>
          <!-- /.table -->
        </div>
        <!-- /.table-responsive -->
        <hr class="my-4">
        <!-- .table-responsive -->
        <div class="table-responsive">
          <!-- .table -->
          <table class="table table-striped table-bordered mb-0">
            <!-- tbody -->
            <tbody>
              <!-- tr -->
              <tr>
                <td width="200px"> Nomor Hak Cipta</td>
                <td><?= $input->nomor_hak_cipta ?>  </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Status Hak Cipta</td>
                <td>
                  <?= ($input->status_hak_cipta == '')? '-' : '' ?>
                  <?= ($input->status_hak_cipta == 1)? 'Dalam Proses' : '' ?>
                  <?= ($input->status_hak_cipta == 2)? 'Sudah Jadi' : '' ?>
                </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> File Hak Cipta </td>
                <td>
                  <?=(!empty($input->file_hak_cipta))? '<a data-toggle="tooltip" data-placement="right" title="'.$input->file_hak_cipta.'" class="btn btn-success btn-xs my-0" href="'.base_url('draft/download/hakcipta/'.urlencode($input->file_hak_cipta)).'"><i class="fa fa-file-alt"></i> File Hak Cipta</a>' : '' ?>

                  <?=(!empty($input->file_hak_cipta_link))? '<a data-toggle="tooltip" data-placement="right" title="'.$input->file_hak_cipta_link.'" class="btn btn-success btn-xs my-0" target="_blank" href="'.$input->file_hak_cipta_link.'"><i class="fa fa-external-link-alt"></i> External file</a>' : '' ?>
                   </td>
              </tr>
              <!-- /tr -->
            </tbody>
            <!-- /tbody -->
          </table>
          <!-- /.table -->
        </div>
        <!-- /.table-responsive -->
        <hr class="my-4">
        <!-- .table-responsive -->
        <div class="table-responsive">
          <!-- .table -->
          <table class="table table-striped table-bordered mb-0">
            <!-- tbody -->
            <tbody>
              <!-- tr -->
              <tr>
                <td width="200px"> Tipe printing</td>
                <td><?= ($input->printing_type == 'o')? 'Offset' : '' ?> <?= ($input->printing_type == 'p')? 'Print On Demand' : '' ?>  </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Serial Number</td>
                <td><?= $input->serial_num ?>  </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Serial Number per tahun </td>
                <td><?= $input->serial_num_per_year ?>  </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Jumlah copy </td>
                <td><?= $input->copies_num ?>  </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Cetakan ke </td>
                <td><?= $input->cetakan_ke ?>  </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Kertas isi </td>
                <td><?= $input->kertas_isi ?>  </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Kertas cover </td>
                <td><?= $input->kertas_cover ?>  </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Ukuran </td>
                <td><?= $input->ukuran ?>  </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Tipe Naskah </td>
                <td><?= ($input->is_reprint == 'y')? 'Cetak Ulang' : '' ?> <?= ($input->is_reprint == 'n')? 'Naskah baru' : '' ?>  </td>
              </tr>
              <!-- /tr -->
            </tbody>
            <!-- /tbody -->
          </table>
          <!-- /.table -->
        </div>
        <!-- /.table-responsive -->
        <hr class="my-4">
        <!-- .table-responsive -->
        <div class="table-responsive">
          <!-- .table -->
          <table class="table table-striped table-bordered mb-0">
            <!-- tbody -->
            <tbody>
              <!-- tr -->
              <tr>
                <td width="200px"> Tanggal Masuk Draft</td>
                <td><?= konversiTanggal($input->entry_date) ?>  </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Tanggal Selesai Draft</td>
                <td><?= konversiTanggal($input->finish_date) ?>  </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Tanggal Cetak </td>
                <td><?= konversiTanggal($input->print_end_date) ?>  </td>
              </tr>
              <!-- /tr -->
              <!-- tr -->
              <tr>
                <td width="200px"> Tanggal Terbit </td>
                <td><?= konversiTanggal($input->published_date) ?>  </td>
              </tr>
              <!-- /tr -->
            </tbody>
            <!-- /tbody -->
          </table>
          <!-- /.table -->
        </div>
        <!-- /.table-responsive -->
        </div>
        <div class="tab-pane fade" id="data-penulis">
          <div id="reload-author">
          <?php if ($authors):?>
          <?php $i=1; ?>
          <!-- .table-responsive -->
            <div class="table-responsive" >
              <!-- .table -->
              <table class="table table-striped table-bordered mb-0">
                <!-- thead -->
                  <thead>
                    <tr>
                      <th scope="col">No</th>
                      <th scope="col">Nama</th>
                      <th scope="col">NIP</th>
                      <th scope="col">Unit Kerja</th>
                      <th scope="col">Institusi</th>
                    </tr>
                  </thead>
                  <!-- /thead -->
                <!-- tbody -->
                <tbody>
                  <?php foreach($authors as $author): ?>
                  <!-- tr -->
                  <tr>
                    <td class="align-middle"><?= $i++ ?></td>
                    <!-- jika admin maka ada linknya ke profil -->
                    <td class="align-middle"><a href="<?= base_url('author/profil/'.$author->author_id) ?>"><?= $author->author_name ?></a></td>
                    <td class="align-middle"><?= $author->author_nip ?></td>
                    <td class="align-middle"><?= $author->work_unit_name ?></td>
                    <td class="align-middle"><?= $author->institute_name ?></td>
                  </tr>
                  <!-- /tr -->
                  <?php endforeach ?>
                </tbody>
                <!-- /tbody -->
              </table>
              <!-- /.table -->
            </div>
            <!-- /.table-responsive -->
          <?php else: ?>
              <p>Data penulis tidak tersedia</p>
          <?php endif ?>
          </div>
        </div>
      </div>
      <!-- /.tab-content -->
    </div>
    <!-- /.card-body -->
  </section>
  <!-- /.card -->
</div>
<!-- /.page-section -->




