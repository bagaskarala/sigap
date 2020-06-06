<?php 
  $ceklevel = $this->session->userdata('level'); 
  $sisa_waktu_rev1 = ceil((strtotime($input->review1_deadline)-strtotime(date('Y-m-d H:i:s')))/86400); 
  $sisa_waktu_rev2 = ceil((strtotime($input->review2_deadline)-strtotime(date('Y-m-d H:i:s')))/86400); 
  ?>
<!-- .card -->
<section id="progress-review" class="card">
  <!-- .card-header -->
  <header class="card-header">
    <!-- .d-flex -->
    <div class="d-flex align-items-center"><span class="mr-auto">Review</span>
      <!-- .card-header-control -->
      <div class="card-header-control">
        <?php if ($ceklevel == 'superadmin' || $ceklevel == 'admin_penerbitan'): ?>
        <!-- .tombol add -->
        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#review_deadline">Atur Deadline</button>
        <!-- /.tombol add -->
        <?php endif ?>
      </div>
      <!-- /.card-header-control -->
    </div><!-- /.d-flex -->
  </header>
  <div class="list-group list-group-flush list-group-bordered" id="list-group-review">
    <div class="list-group-item justify-content-between">
      <span class="text-muted">Tanggal mulai</span>
      <strong><?= konversiTanggal($input->review_start_date) ?></strong>
    </div>
    <div class="list-group-item justify-content-between">
      <span class="text-muted">Tanggal selesai</span>
      <strong><?= konversiTanggal($input->review_end_date) ?></strong>
    </div>
    <?php if($reviewer_order=='0' or $reviewer_order!='1'): ?>
    <div class="list-group-item justify-content-between">
      <span class="text-muted">Deadline reviewer 1</span>
      <strong><?= ( $sisa_waktu_rev1 <= 0 and $input->review1_flag =='')? '<span data-toggle="tooltip" data-placement="right" title="Melebihi Deadline" class="text-danger">'.konversiTanggal($input->review1_deadline).'</span>' : konversiTanggal($input->review1_deadline) ?></strong>
    </div>
    <?php endif ?>
    <?php if($reviewer_order=='1' or $reviewer_order!='0'): ?>
    <div class="list-group-item justify-content-between">
      <span class="text-muted">Deadline reviewer 2</span>
      <strong><?= ( $sisa_waktu_rev2 <= 0 and $input->review2_flag =='')? '<span data-toggle="tooltip" data-placement="right" title="Melebihi Deadline" class="text-danger">'.konversiTanggal($input->review2_deadline).'</span>' : konversiTanggal($input->review2_deadline) ?></strong>
    </div>
    <?php endif ?>
    <?php if ($ceklevel != 'author' and $ceklevel != 'reviewer' ): ?>
    <div class="list-group-item justify-content-between">
      <span class="text-muted">Reviewer</span>
      <div>
        <?php if ($reviewers) {
          $i = 1;
          foreach ($reviewers as $reviewer){
            echo '<span class="badge badge-info p-1">'.$i.'. '.$reviewer->reviewer_name.'</span> ';
            $i++;
          }
          }
          ?>
      </div>
    </div>
    <div class="list-group-item justify-content-between">
      <span class="text-muted">Rekomendasi Reviewer</span>
      <div>
        <?php 
          if($input->review1_flag != ''){
            if($input->review1_flag == 'y'){
              echo '<span class="badge badge-success p-1">1. Setuju</span> ';
            }else{
              echo '<span class="badge badge-danger p-1">1. Menolak</span> ';
            }
          }
          if($input->review2_flag != ''){
            if($input->review2_flag == 'y'){
              echo '<span class="badge badge-success p-1">2. Setuju</span> ';
            }else{
              echo '<span class="badge badge-danger p-1">2. Menolak</span> ';
            }
          }
           ?>
      </div>
    </div>
    <?php endif ?>
    <div class="list-group-item justify-content-between">
      <span class="text-muted">Status</span>
      <?php if($input->is_review == 'y'): ?>
      <a href="#" onclick="event.preventDefault()" class="font-weight-bold" data-toggle="popover" data-placement="left" data-container="body" auto="" right="" data-html="true" title="" data-trigger="hover" data-content="<?=$input->review_status ?>" data-original-title="Catatan Admin"><i class="fa fa-info-circle"></i> Review Selesai</a>
      <?php elseif($input->is_review == 'n' and $input->stts == 99): ?>
      <a href="#" onclick="event.preventDefault()" class="font-weight-bold" data-toggle="popover" data-placement="left" data-container="body" auto="" right="" data-html="true" title="" data-trigger="hover" data-content="<?=$input->review_status ?>" data-original-title="Catatan Admin"><i class="fa fa-info-circle"></i> Draft ditolak</a>
      <?php else: ?>
      -
      <?php endif ?>
    </div>
    <hr class="m-0">
  </div>
  <!-- .card-body -->
  <div class="card-body">
    <div class="el-example ">
      <?php if ($ceklevel == 'superadmin' || $ceklevel == 'admin_penerbitan'): ?>
      <button title="Aksi admin" class="btn btn-secondary" data-toggle="modal" data-target="#review_aksi"><i class="fa fa-thumbs-up"></i> Aksi</button>
      <!-- <button class="btn btn-danger" style="width:50px"><i class="fa fa-times"></i></button> -->
      <?php endif ?>
      <?php if($reviewer_order=='0' or $reviewer_order!='1'): ?>
      <button type="button" class="btn <?=($input->review1_notes!='' || $input->review1_notes_author!='')? 'btn-success' : 'btn-outline-success' ?>" data-toggle="modal" data-target="#review1" <?=($ceklevel=='reviewer' and $sisa_waktu_rev1 <=0 and $input->review1_flag =='')? 'disabled' : '' ?>>Tanggapan Review 1
        <?=($input->review1_notes!='' || $input->review1_notes_author!='')? '<i class="fa fa-check"></i>' : '' ?></button>
      <!-- peringatan disabled -->
      <?=($ceklevel=='reviewer' and $sisa_waktu_rev1 <= 0 and $input->review1_flag =='')? '<span class="font-weight-bold text-danger" data-toggle="tooltip" data-placement="bottom" title="Hubungi admin untuk membuka draft ini"><i class="fa fa-info-circle"></i> Melebihi Deadline!</span>' : '' ?>
      <?php endif ?>
      <?php if($reviewer_order=='1' or $reviewer_order!='0'): ?>
      <button type="button" class="btn <?=($input->review2_notes!='' || $input->review2_notes_author!='')? 'btn-success' : 'btn-outline-success' ?>" data-toggle="modal" data-target="#review2" <?=($ceklevel=='reviewer' and $sisa_waktu_rev2 <=0 and $input->review2_flag =='')? 'disabled' : '' ?>>Tanggapan Review 2
        <?=($input->review2_notes!='' || $input->review2_notes_author!='')? '<i class="fa fa-check"></i>' : '' ?></button>
      <!-- peringatan disabled -->
      <?=($ceklevel=='reviewer' and $sisa_waktu_rev2 <= 0 and $input->review2_flag =='')? '<span class="font-weight-bold text-danger" data-toggle="tooltip" data-placement="bottom" title="Hubungi admin untuk membuka draft ini"><i class="fa fa-info-circle"></i> Melebihi Deadline!</span>' : '' ?>
      <?php endif ?>
    </div>
    <!-- modal tanggapan 1-->
    <div class="modal fade" id="review1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <!-- .modal-dialog -->
      <div class="modal-dialog modal-lg modal-dialog-overflow" role="document">
        <!-- .modal-content -->
        <div class="modal-content">
          <!-- .modal-header -->
          <div class="modal-header">
            <h5 class="modal-title"> Progress Review 1</h5>
          </div>
          <!-- /.modal-header -->
          <!-- .modal-body -->
          <div class="modal-body">
            <p class="font-weight-bold">NASKAH</p>
            <!-- if upload ditampilkan di level tertentu -->
            <?php if($ceklevel=='reviewer' or ($ceklevel == 'author' and $author_order==1) or $ceklevel == 'superadmin' or $ceklevel == 'admin_penerbitan'): ?>
            <div class="alert alert-info">Upload file naskah atau sertakan link naskah. Kosongi jika file naskah hard copy.</div>
            <?= form_open_multipart('draft/upload_progress/'.$input->draft_id.'/review1_file', ' novalidate id="rev1form"'); ?>
            <?= isset($input->draft_id) ? form_hidden('draft_id', $input->draft_id) : '' ?>
            <!-- .form-group -->
            <div class="form-group">
              <label for="review1_file">File Naskah</label>
              <!-- .input-group -->
              <div class="custom-file">
                <?= form_upload('review1_file','','class="custom-file-input naskah" id="review1_file"') ?>
                <label class="custom-file-label" for="review1_file">Choose file</label>
              </div>
              <small class="form-text text-muted">Tipe file upload  bertype : docx, doc, dan pdf.</small>
              <!-- /.input-group -->
            </div>
            <!-- /.form-group -->
            <!-- .form-group -->
            <div class="form-group">
              <label for="reviewer1_file_link">Link Naskah</label>
              <div>
                <?= form_input('reviewer1_file_link', $input->reviewer1_file_link, 'class="form-control naskah" id="reviewer1_file_link"') ?>
              </div>
              <?= form_error('reviewer1_file_link') ?>
            </div>
            <!-- /.form-group -->
            <div class="form-group">
              <button class="btn btn-primary " type="submit" value="Submit" id="btn-upload-review1"><i class="fa fa-upload"></i> Upload</button>
            </div>
            <?= form_close(); ?>
            <?php endif ?>
            <!-- endif upload ditampilkan di level tertentu -->
            <!-- keterangan last upload dan tombol download -->
            <div id="modal-review1">
              <p>Last Upload :
                <?=konversiTanggal($input->review1_upload_date) ?>,
                <br> by :
                <?=konversi_username_level($input->review1_last_upload) ?>
                <?php  if($ceklevel !='author' and $ceklevel !='reviewer'):?>
                <em>(<?=$input->review1_last_upload ?>)</em>
                <?php endif ?>
              </p>
              <?=(!empty($input->review1_file))? '<a data-toggle="tooltip" data-placement="right" title="" data-original-title="'.$input->review1_file.'" href="'.base_url('draftfile/'.$input->review1_file).'" class="btn btn-success"><i class="fa fa-download"></i> Download</a>' : '' ?>
              <?=(!empty($input->reviewer1_file_link))? '<a data-toggle="tooltip" data-placement="right" title="" data-original-title="'.$input->reviewer1_file_link.'" href="'.$input->reviewer1_file_link.'" class="btn btn-success"><i class="fa fa-external-link-alt"></i> External file</a>' : '' ?>
            </div>
            <?= form_open('draft/ubahnotes/'.$input->draft_id,'id="formreview1_krit" novalidate=""'); ?>
            <!-- review dari reviewer hanya bisa dibaca admin dan staff ugmpress -->
            <?php if($ceklevel!='author'): ?>
            <hr class="my-3">
            <p class="font-weight-bold">REVIEW</p>
            <?= isset($input->draft_id) ? form_hidden('draft_id', $input->draft_id) : '' ?>
            <!-- .form-group -->
            <div class="alert alert-info">
              <label for="kriteria1_reviewer1" class="font-weight-bold">Substansi naskah (mencerminkan adanya kontribusi dan inovasi pada pengembangan iptek, seni, dan budaya) :</label>
              <div>
                <?php 
                  $kriteria1_reviewer1 = array(
                      'name' => 'kriteria1_reviewer1',
                      'class'=> 'form-control summernote-basic',
                      'id'  => 'kriteria1_reviewer1',
                      'rows' => '6',
                      'value'=> $input->kriteria1_reviewer1
                  );
                  if($ceklevel=='reviewer'){
                    echo form_textarea($kriteria1_reviewer1);
                  }else{
                    echo '<div class="font-italic">'.nl2br($input->kriteria1_reviewer1).'</div>';
                  }
                  ?>
              </div>
              <?php  if($ceklevel=='reviewer'): ?>
              <p class="m-0 p-0">Nilai</p>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_0', 1, isset($input->nilai_reviewer1[0]) && ($input->nilai_reviewer1[0] == 1) ? true : false,'required="" class="custom-control-input" id="nilai_kriteria1_reviewer1_1"')?>
                <label class="custom-control-label" for="nilai_kriteria1_reviewer1_1">1</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_0', 2, isset($input->nilai_reviewer1[0]) && ($input->nilai_reviewer1[0] == 2) ? true : false,'required="" class="custom-control-input" id="nilai_kriteria1_reviewer1_2"')?>
                <label class="custom-control-label" for="nilai_kriteria1_reviewer1_2">2</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_0', 3, isset($input->nilai_reviewer1[0]) && ($input->nilai_reviewer1[0] == 3) ? true : false,'required="" class="custom-control-input" id="nilai_kriteria1_reviewer1_3"')?>
                <label class="custom-control-label" for="nilai_kriteria1_reviewer1_3">3</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_0', 4, isset($input->nilai_reviewer1[0]) && ($input->nilai_reviewer1[0] == 4) ? true : false,'required="" class="custom-control-input" id="nilai_kriteria1_reviewer1_4"')?>
                <label class="custom-control-label" for="nilai_kriteria1_reviewer1_4">4</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_0', 5, isset($input->nilai_reviewer1[0]) && ($input->nilai_reviewer1[0] == 5) ? true : false,'required="" class="custom-control-input" id="nilai_kriteria1_reviewer1_5"')?>
                <label class="custom-control-label" for="nilai_kriteria1_reviewer1_5">5</label>
              </div>
              <?php else: ?>
              <p class="m-0 p-0">Nilai =
                <?=isset($input->nilai_reviewer1[0])?$input->nilai_reviewer1[0]:''; ?>
              </p>
              <?php endif ?>
            </div>
            <!-- /.form-group -->
            <!-- .form-group -->
            <div class="alert alert-info">
              <label for="kriteria2_reviewer1" class="font-weight-bold">Orisinalitas Karya dan bobot ilmiah :</label>
              <div>
                <?php 
                  $kriteria2_reviewer1 = array(
                      'name' => 'kriteria2_reviewer1',
                      'class'=> 'form-control summernote-basic',
                      'id'  => 'kriteria2_reviewer1',
                      'rows' => '6',
                      'value'=> $input->kriteria2_reviewer1
                  );
                  if($ceklevel=='reviewer'){
                    echo form_textarea($kriteria2_reviewer1);
                  }else{
                    echo '<div class="font-italic">'.nl2br($input->kriteria2_reviewer1).'</div>';
                  }
                  ?>
              </div>
              <?php  if($ceklevel=='reviewer'): ?>
              <p class="m-0 p-0">Nilai</p>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_1', 1, isset($input->nilai_reviewer1[1]) && ($input->nilai_reviewer1[1] == 1) ? true : false,'required class="custom-control-input" id="nilai_kriteria2_reviewer1_1"')?>
                <label class="custom-control-label" for="nilai_kriteria2_reviewer1_1">1</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_1', 2, isset($input->nilai_reviewer1[1]) && ($input->nilai_reviewer1[1] == 2) ? true : false,'required class="custom-control-input" id="nilai_kriteria2_reviewer1_2"')?>
                <label class="custom-control-label" for="nilai_kriteria2_reviewer1_2">2</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_1', 3, isset($input->nilai_reviewer1[1]) && ($input->nilai_reviewer1[1] == 3) ? true : false,'required class="custom-control-input" id="nilai_kriteria2_reviewer1_3"')?>
                <label class="custom-control-label" for="nilai_kriteria2_reviewer1_3">3</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_1', 4, isset($input->nilai_reviewer1[1]) && ($input->nilai_reviewer1[1] == 4) ? true : false,'required class="custom-control-input" id="nilai_kriteria2_reviewer1_4"')?>
                <label class="custom-control-label" for="nilai_kriteria2_reviewer1_4">4</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_1', 5, isset($input->nilai_reviewer1[1]) && ($input->nilai_reviewer1[1] == 5) ? true : false,'required class="custom-control-input" id="nilai_kriteria2_reviewer1_5"')?>
                <label class="custom-control-label" for="nilai_kriteria2_reviewer1_5">5</label>
              </div>
              <?php else: ?>
              <p class="m-0 p-0">Nilai =
                <?=isset($input->nilai_reviewer1[1])?$input->nilai_reviewer1[1]:''; ?>
              </p>
              <?php endif ?>
            </div>
            <!-- /.form-group -->
            <!-- .form-group -->
            <div class="alert alert-info">
              <label for="kriteria3_reviewer1" class="font-weight-bold">Kemutahiran Pustaka :</label>
              <div>
                <?php 
                  $kriteria3_reviewer1 = array(
                      'name' => 'kriteria3_reviewer1',
                      'class'=> 'form-control summernote-basic',
                      'id'  => 'kriteria3_reviewer1',
                      'rows' => '6',
                      'value'=> $input->kriteria3_reviewer1
                  );
                  if($ceklevel=='reviewer'){
                    echo form_textarea($kriteria3_reviewer1);
                  }else{
                    echo '<div class="font-italic">'.nl2br($input->kriteria3_reviewer1).'</div>';
                  }
                  ?>
              </div>
              <?php  if($ceklevel=='reviewer'): ?>
              <p class="m-0 p-0">Nilai</p>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_2', 1, isset($input->nilai_reviewer1[2]) && ($input->nilai_reviewer1[2] == 1) ? true : false,'required class="custom-control-input" id="nilai_kriteria3_reviewer1_1"')?>
                <label class="custom-control-label" for="nilai_kriteria3_reviewer1_1">1</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_2', 2, isset($input->nilai_reviewer1[2]) && ($input->nilai_reviewer1[2] == 2) ? true : false,'required class="custom-control-input" id="nilai_kriteria3_reviewer1_2"')?>
                <label class="custom-control-label" for="nilai_kriteria3_reviewer1_2">2</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_2', 3, isset($input->nilai_reviewer1[2]) && ($input->nilai_reviewer1[2] == 3) ? true : false,'required class="custom-control-input" id="nilai_kriteria3_reviewer1_3"')?>
                <label class="custom-control-label" for="nilai_kriteria3_reviewer1_3">3</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_2', 4, isset($input->nilai_reviewer1[2]) && ($input->nilai_reviewer1[2] == 4) ? true : false,'required class="custom-control-input" id="nilai_kriteria3_reviewer1_4"')?>
                <label class="custom-control-label" for="nilai_kriteria3_reviewer1_4">4</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_2', 5, isset($input->nilai_reviewer1[2]) && ($input->nilai_reviewer1[2] == 5) ? true : false,'required class="custom-control-input" id="nilai_kriteria3_reviewer1_5"')?>
                <label class="custom-control-label" for="nilai_kriteria3_reviewer1_5">5</label>
              </div>
              <?php else: ?>
              <p class="m-0 p-0">Nilai =
                <?=isset($input->nilai_reviewer1[2])?$input->nilai_reviewer1[2]:''; ?>
              </p>
              <?php endif ?>
            </div>
            <!-- /.form-group -->
            <!-- .form-group -->
            <div class="alert alert-info">
              <label for="kriteria4_reviewer1" class="font-weight-bold">Kelengkapan unsur (sebagai suatu naskah buku dan keterkaitan antarbab, sistematika) :</label>
              <div>
                <?php 
                  $kriteria4_reviewer1 = array(
                      'name' => 'kriteria4_reviewer1',
                      'class'=> 'form-control summernote-basic',
                      'id'  => 'kriteria4_reviewer1',
                      'rows' => '6',
                      'value'=> $input->kriteria4_reviewer1
                  );
                  if($ceklevel=='reviewer'){
                    echo form_textarea($kriteria4_reviewer1);
                  }else{
                    echo '<div class="font-italic">'.nl2br($input->kriteria4_reviewer1).'</div>';
                  }
                  ?>
              </div>
              <?php  if($ceklevel=='reviewer'): ?>
              <p class="m-0 p-0">Nilai</p>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_3', 1, isset($input->nilai_reviewer1[3]) && ($input->nilai_reviewer1[3] == 1) ? true : false,'required class="custom-control-input" id="nilai_kriteria4_reviewer1_1"')?>
                <label class="custom-control-label" for="nilai_kriteria4_reviewer1_1">1</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_3', 2, isset($input->nilai_reviewer1[3]) && ($input->nilai_reviewer1[3] == 2) ? true : false,'required class="custom-control-input" id="nilai_kriteria4_reviewer1_2"')?>
                <label class="custom-control-label" for="nilai_kriteria4_reviewer1_2">2</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_3', 3, isset($input->nilai_reviewer1[3]) && ($input->nilai_reviewer1[3] == 3) ? true : false,'required class="custom-control-input" id="nilai_kriteria4_reviewer1_3"')?>
                <label class="custom-control-label" for="nilai_kriteria4_reviewer1_3">3</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_3', 4, isset($input->nilai_reviewer1[3]) && ($input->nilai_reviewer1[3] == 4) ? true : false,'required class="custom-control-input" id="nilai_kriteria4_reviewer1_4"')?>
                <label class="custom-control-label" for="nilai_kriteria4_reviewer1_4">4</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer1_3', 5, isset($input->nilai_reviewer1[3]) && ($input->nilai_reviewer1[3] == 5) ? true : false,'required class="custom-control-input" id="nilai_kriteria4_reviewer1_5"')?>
                <label class="custom-control-label" for="nilai_kriteria4_reviewer1_5">5</label>
              </div>
              <?php else: ?>
              <p class="m-0 p-0">Nilai =
                <?=isset($input->nilai_reviewer1[3])?$input->nilai_reviewer1[3]:''; ?>
              </p>
              <?php endif ?>
            </div>
            <?php if($ceklevel != 'author'): ?>
            <div id="total_reviewer1">
              <?php if(!empty($draft->nilai_total_reviewer1)){
                if($draft->nilai_total_reviewer1 >=400){
                  $hasil = '<div class="alert alert-success"><span class="badge badge-success">Naskah Lolos Review</span><br>';
                  $hasil .= '<strong>Nilai total = '.$draft->nilai_total_reviewer1.'</strong><br>';
                  $hasil .= 'Passing Grade = 400 <br>';
                  $hasil .= '</div>';
                }else{
                  $hasil = '<div class="alert alert-danger"><span class="badge badge-danger">Naskah Tidak Lolos Review</span><br>';
                  $hasil .= '<strong>Nilai total = '.$draft->nilai_total_reviewer1.'</strong><br>';
                  $hasil .= 'Passing Grade = 400 <br>';
                  $hasil .= '</div>';
                }
                  echo $hasil;
                } 
                ?>
            </div>
            <?php endif ?>
            <!-- /.form-group -->
            <!-- endif review dari reviewer hanya bisa dibaca admin dan staff ugmpress -->
            <?php endif ?>
            <!-- .fieldset -->
            <fieldset>
              <?php if($ceklevel!='author'): ?>
              <hr class="my-3">
              <!-- .form-group -->
              <div class="form-group">
                <label for="cr1" class="font-weight-bold">Catatan Reviewer 1</label>
                <?php 
                  $optionscr1 = array(
                      'name' => 'review1_notes',
                      'class'=> 'form-control summernote-basic',
                      'id'  => 'cr1',
                      'rows' => '6',
                      'value'=> $input->review1_notes
                  );
                  if($ceklevel!='reviewer'){
                    echo '<div class="font-italic">'.nl2br($input->review1_notes).'</div>';
                  }else{
                    echo form_textarea($optionscr1);
                  }
                  ?>
              </div>
              <?php endif ?>
              <!-- /.form-group -->
              <?php if($ceklevel=='superadmin' or $ceklevel=='admin_penerbitan' or $ceklevel=='author'): ?>
              <hr class="my-3">
              <!-- .form-group -->
              <div class="form-group">
                <label for="cr1a" class="font-weight-bold">Catatan Admin untuk Penulis</label>
                <?php 
                  $optionscr1a = array(
                      'name' => 'catatan_review1_admin',
                      'class'=> 'form-control summernote-basic',
                      'id'  => 'cr1a',
                      'rows' => '6',
                      'value'=> $input->catatan_review1_admin
                  );
                  if($ceklevel=='superadmin' or $ceklevel=='admin_penerbitan'){
                    echo form_textarea($optionscr1a);
                  }elseif($ceklevel=='author'){
                    echo '<div class="font-italic">'.nl2br($input->catatan_review1_admin).'</div>';
                  }else{}
                  ?>
              </div>
              <?php endif ?>
              <!-- /.form-group -->
              <hr class="my-3">
              <!-- .form-group -->
              <div class="form-group">
                <label for="crp1" class="font-weight-bold">Catatan Penulis</label>
                <?php 
                  $optionscrp1 = array(
                      'name' => 'review1_notes_author',
                      'class'=> 'form-control summernote-basic',
                      'id'  => 'crp1',
                      'rows' => '6',
                      'value'=> $input->review1_notes_author
                  );
                  if($ceklevel!='author' or $author_order!=1){
                    echo '<div class="font-italic">'.nl2br($input->review1_notes_author).'</div>';
                  }else{
                    echo form_textarea($optionscrp1);
                  }
                   ?>
              </div>
              <!-- /.form-group -->
            </fieldset>
            <!-- /.fieldset -->
          </div>
          <!-- /.modal-body -->
          <!-- .modal-footer -->
          <div class="modal-footer">
            <?php if($ceklevel=='reviewer'): ?>
            <div class="card-footer-content text-muted p-0 m-0">
              <div class="mb-1 font-weight-bold">Rekomendasi</div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('review1_flag', 'y', isset($input->review1_flag) && ($input->review1_flag == 'y') ? true : false,'required class="custom-control-input" id="review1_flagy"')?>
                <label class="custom-control-label" for="review1_flagy">Setuju</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('review1_flag', 'n', isset($input->review1_flag) && ($input->review1_flag == 'n') ? true : false,'required class="custom-control-input" id="review1_flagn"')?>
                <label class="custom-control-label" for="review1_flagn">Tidak</label>
              </div>
            </div>
            <!-- submit khusus reviewer -->
            <button class="btn btn-primary ml-auto" type="submit" value="Submit" id="btn-submit-review1-rev">Submit</button>
            <?php else: ?>
            <!-- submit untuk selain reviewer -->
            <button class="btn btn-primary ml-auto" type="submit" value="Submit" id="btn-submit-review1-other">Submit</button>
            <?php endif ?>
            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
          </div>
          <!-- /.modal-footer -->
          <?= form_close(); ?>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    <!-- modal tanggapan 2-->
    <div class="modal fade" id="review2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <!-- .modal-dialog -->
      <div class="modal-dialog modal-lg modal-dialog-overflow" role="document">
        <!-- .modal-content -->
        <div class="modal-content">
          <!-- .modal-header -->
          <div class="modal-header">
            <h5 class="modal-title"> Progress Review 2</h5>
          </div>
          <!-- /.modal-header -->
          <!-- .modal-body -->
          <div class="modal-body">
            <!-- if upload ditampilkan di level tertentu -->
            <p class="font-weight-bold">NASKAH</p>
            <?php if($ceklevel=='reviewer' or ($ceklevel == 'author' and $author_order==1) or $ceklevel == 'superadmin' or $ceklevel == 'admin_penerbitan'): ?>
            <?= form_open('draft/upload_progress/'.$input->draft_id.'/review2_file', 'novalidate id="rev2form"'); ?>
            <div class="alert alert-info">Upload file naskah atau sertakan link naskah. Kosongi jika file naskah hard copy.</div>
            <?= isset($input->draft_id) ? form_hidden('draft_id', $input->draft_id) : 'No data' ?>
            <!-- .form-group -->
            <div class="form-group">
              <label for="review2_file">File Naskah</label>
              <!-- .input-group -->
              <div class="custom-file">
                <?= form_upload('review2_file','','class="custom-file-input naskah" id="review2_file"') ?>
                <label class="custom-file-label" for="review2_file">Choose file</label>
              </div>
              <small class="form-text text-muted">Tipe file upload  bertype : docx, doc, dan pdf.</small>
              <!-- /.input-group -->
            </div>
            <!-- /.form-group -->
            <!-- .form-group -->
            <div class="form-group">
              <label for="reviewer2_file_link">Link Naskah</label>
              <div>
                <?= form_input('reviewer2_file_link', $input->reviewer2_file_link, 'class="form-control naskah" id="reviewer2_file_link"') ?>
              </div>
              <?= form_error('reviewer2_file_link') ?>
            </div>
            <!-- /.form-group -->
            <div class="form-group">
              <button class="btn btn-primary " type="submit" value="Submit" id="btn-upload-review2"><i class="fa fa-upload"></i> Upload</button>
            </div>
            <?= form_close(); ?>
            <?php endif ?>
            <!-- endif upload ditampilkan di level tertentu -->
            <!-- keterangan last upload dan tombol download -->
            <div id="modal-review2">
              <p>Last Upload :
                <?=konversiTanggal($input->review2_upload_date) ?>,
                <br> by :
                <?=konversi_username_level($input->review2_last_upload) ?>
                <?php  if($ceklevel !='author' and $ceklevel !='reviewer'):?>
                <em>(<?=$input->review2_last_upload ?>)</em>
                <?php endif ?>
              </p>
              <?=(!empty($input->review2_file))? '<a data-toggle="tooltip" data-placement="right" title="" data-original-title="'.$input->review2_file.'" href="'.base_url('draftfile/'.$input->review2_file).'" class="btn btn-success"><i class="fa fa-download"></i> Download</a>' : '' ?>
              <?=(!empty($input->reviewer2_file_link))? '<a data-toggle="tooltip" data-placement="right" title="" data-original-title="'.$input->reviewer2_file_link.'" href="'.$input->reviewer2_file_link.'" class="btn btn-success"><i class="fa fa-external-link-alt"></i> External file</a>' : '' ?>
            </div>
            <?= form_open('draft/ubahnotes/'.$input->draft_id,'id="formreview2_krit" novalidate=""'); ?>
            <!-- review dari reviewer hanya bisa dibaca admin dan staff ugmpress -->
            <?php if($ceklevel!='author'): ?>
            <hr class="my-3">
            <p class="font-weight-bold">REVIEW</p>
            <?= isset($input->draft_id) ? form_hidden('draft_id', $input->draft_id) : '' ?>
            <!-- .form-group -->
            <div class="alert alert-info">
              <label for="kriteria1_reviewer2" class="font-weight-bold">Substansi naskah (mencerminkan adanya kontribusi dan inovasi pada pengembangan iptek, seni, dan budaya) :</label>
              <div>
                <?php 
                  $kriteria1_reviewer2 = array(
                      'name' => 'kriteria1_reviewer2',
                      'class'=> 'form-control summernote-basic',
                      'id'  => 'kriteria1_reviewer2',
                      'rows' => '6',
                      'value'=> $input->kriteria1_reviewer2
                  );
                  if($ceklevel=='reviewer'){
                    echo form_textarea($kriteria1_reviewer2);
                  }else{
                    echo '<div class="font-italic">'.nl2br($input->kriteria1_reviewer2).'</div>';
                  }
                  ?>
              </div>
              <?php  if($ceklevel=='reviewer'): ?>
              <p class="m-0 p-0">Nilai</p>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_0', 1, isset($input->nilai_reviewer2[0]) && ($input->nilai_reviewer2[0] == 1) ? true : false,'required="" class="custom-control-input" id="nilai_kriteria1_reviewer2_1"')?>
                <label class="custom-control-label" for="nilai_kriteria1_reviewer2_1">1</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_0', 2, isset($input->nilai_reviewer2[0]) && ($input->nilai_reviewer2[0] == 2) ? true : false,'required="" class="custom-control-input" id="nilai_kriteria1_reviewer2_2"')?>
                <label class="custom-control-label" for="nilai_kriteria1_reviewer2_2">2</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_0', 3, isset($input->nilai_reviewer2[0]) && ($input->nilai_reviewer2[0] == 3) ? true : false,'required="" class="custom-control-input" id="nilai_kriteria1_reviewer2_3"')?>
                <label class="custom-control-label" for="nilai_kriteria1_reviewer2_3">3</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_0', 4, isset($input->nilai_reviewer2[0]) && ($input->nilai_reviewer2[0] == 4) ? true : false,'required="" class="custom-control-input" id="nilai_kriteria1_reviewer2_4"')?>
                <label class="custom-control-label" for="nilai_kriteria1_reviewer2_4">4</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_0', 5, isset($input->nilai_reviewer2[0]) && ($input->nilai_reviewer2[0] == 5) ? true : false,'required="" class="custom-control-input" id="nilai_kriteria1_reviewer2_5"')?>
                <label class="custom-control-label" for="nilai_kriteria1_reviewer2_5">5</label>
              </div>
              <?php else: ?>
              <p class="m-0 p-0">Nilai =
                <?=isset($input->nilai_reviewer2[0])?$input->nilai_reviewer2[0]:''; ?>
              </p>
              <?php endif ?>
            </div>
            <!-- /.form-group -->
            <!-- .form-group -->
            <div class="alert alert-info">
              <label for="kriteria2_reviewer2" class="font-weight-bold">Orisinalitas Karya dan bobot ilmiah :</label>
              <div>
                <?php 
                  $kriteria2_reviewer2 = array(
                      'name' => 'kriteria2_reviewer2',
                      'class'=> 'form-control summernote-basic',
                      'id'  => 'kriteria2_reviewer2',
                      'rows' => '6',
                      'value'=> $input->kriteria2_reviewer2
                  );
                  if($ceklevel=='reviewer'){
                    echo form_textarea($kriteria2_reviewer2);
                  }else{
                    echo '<div class="font-italic">'.nl2br($input->kriteria2_reviewer2).'</div>';
                  }
                  ?>
              </div>
              <?php  if($ceklevel=='reviewer'): ?>
              <p class="m-0 p-0">Nilai</p>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_1', 1, isset($input->nilai_reviewer2[1]) && ($input->nilai_reviewer2[1] == 1) ? true : false,'required class="custom-control-input" id="nilai_kriteria2_reviewer2_1"')?>
                <label class="custom-control-label" for="nilai_kriteria2_reviewer2_1">1</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_1', 2, isset($input->nilai_reviewer2[1]) && ($input->nilai_reviewer2[1] == 2) ? true : false,'required class="custom-control-input" id="nilai_kriteria2_reviewer2_2"')?>
                <label class="custom-control-label" for="nilai_kriteria2_reviewer2_2">2</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_1', 3, isset($input->nilai_reviewer2[1]) && ($input->nilai_reviewer2[1] == 3) ? true : false,'required class="custom-control-input" id="nilai_kriteria2_reviewer2_3"')?>
                <label class="custom-control-label" for="nilai_kriteria2_reviewer2_3">3</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_1', 4, isset($input->nilai_reviewer2[1]) && ($input->nilai_reviewer2[1] == 4) ? true : false,'required class="custom-control-input" id="nilai_kriteria2_reviewer2_4"')?>
                <label class="custom-control-label" for="nilai_kriteria2_reviewer2_4">4</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_1', 5, isset($input->nilai_reviewer2[1]) && ($input->nilai_reviewer2[1] == 5) ? true : false,'required class="custom-control-input" id="nilai_kriteria2_reviewer2_5"')?>
                <label class="custom-control-label" for="nilai_kriteria2_reviewer2_5">5</label>
              </div>
              <?php else: ?>
              <p class="m-0 p-0">Nilai =
                <?=isset($input->nilai_reviewer2[1])?$input->nilai_reviewer2[1]:''; ?>
              </p>
              <?php endif ?>
            </div>
            <!-- /.form-group -->
            <!-- .form-group -->
            <div class="alert alert-info">
              <label for="kriteria3_reviewer2" class="font-weight-bold">Kemutahiran Pustaka :</label>
              <div>
                <?php 
                  $kriteria3_reviewer2 = array(
                      'name' => 'kriteria3_reviewer2',
                      'class'=> 'form-control summernote-basic',
                      'id'  => 'kriteria3_reviewer2',
                      'rows' => '6',
                      'value'=> $input->kriteria3_reviewer2
                  );
                  if($ceklevel=='reviewer'){
                    echo form_textarea($kriteria3_reviewer2);
                  }else{
                    echo '<div class="font-italic">'.nl2br($input->kriteria3_reviewer2).'</div>';
                  }
                  ?>
              </div>
              <?php  if($ceklevel=='reviewer'): ?>
              <p class="m-0 p-0">Nilai</p>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_2', 1, isset($input->nilai_reviewer2[2]) && ($input->nilai_reviewer2[2] == 1) ? true : false,'required class="custom-control-input" id="nilai_kriteria3_reviewer2_1"')?>
                <label class="custom-control-label" for="nilai_kriteria3_reviewer2_1">1</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_2', 2, isset($input->nilai_reviewer2[2]) && ($input->nilai_reviewer2[2] == 2) ? true : false,'required class="custom-control-input" id="nilai_kriteria3_reviewer2_2"')?>
                <label class="custom-control-label" for="nilai_kriteria3_reviewer2_2">2</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_2', 3, isset($input->nilai_reviewer2[2]) && ($input->nilai_reviewer2[2] == 3) ? true : false,'required class="custom-control-input" id="nilai_kriteria3_reviewer2_3"')?>
                <label class="custom-control-label" for="nilai_kriteria3_reviewer2_3">3</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_2', 4, isset($input->nilai_reviewer2[2]) && ($input->nilai_reviewer2[2] == 4) ? true : false,'required class="custom-control-input" id="nilai_kriteria3_reviewer2_4"')?>
                <label class="custom-control-label" for="nilai_kriteria3_reviewer2_4">4</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_2', 5, isset($input->nilai_reviewer2[2]) && ($input->nilai_reviewer2[2] == 5) ? true : false,'required class="custom-control-input" id="nilai_kriteria3_reviewer2_5"')?>
                <label class="custom-control-label" for="nilai_kriteria3_reviewer2_5">5</label>
              </div>
              <?php else: ?>
              <p class="m-0 p-0">Nilai =
                <?=isset($input->nilai_reviewer2[2])?$input->nilai_reviewer2[2]:''; ?>
              </p>
              <?php endif ?>
            </div>
            <!-- /.form-group -->
            <!-- .form-group -->
            <div class="alert alert-info">
              <label for="kriteria4_reviewer2" class="font-weight-bold">Kelengkapan unsur (sebagai suatu naskah buku dan keterkaitan antarbab, sistematika) :</label>
              <div>
                <?php 
                  $kriteria4_reviewer2 = array(
                      'name' => 'kriteria4_reviewer2',
                      'class'=> 'form-control summernote-basic',
                      'id'  => 'kriteria4_reviewer2',
                      'rows' => '6',
                      'value'=> $input->kriteria4_reviewer2
                  );
                  if($ceklevel=='reviewer'){
                    echo form_textarea($kriteria4_reviewer2);
                  }else{
                    echo '<div class="font-italic">'.nl2br($input->kriteria4_reviewer2).'</div>';
                  }
                  ?>
              </div>
              <?php  if($ceklevel=='reviewer'): ?>
              <p class="m-0 p-0">Nilai</p>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_3', 1, isset($input->nilai_reviewer2[3]) && ($input->nilai_reviewer2[3] == 1) ? true : false,'required class="custom-control-input" id="nilai_kriteria4_reviewer2_1"')?>
                <label class="custom-control-label" for="nilai_kriteria4_reviewer2_1">1</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_3', 2, isset($input->nilai_reviewer2[3]) && ($input->nilai_reviewer2[3] == 2) ? true : false,'required class="custom-control-input" id="nilai_kriteria4_reviewer2_2"')?>
                <label class="custom-control-label" for="nilai_kriteria4_reviewer2_2">2</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_3', 3, isset($input->nilai_reviewer2[3]) && ($input->nilai_reviewer2[3] == 3) ? true : false,'required class="custom-control-input" id="nilai_kriteria4_reviewer2_3"')?>
                <label class="custom-control-label" for="nilai_kriteria4_reviewer2_3">3</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_3', 4, isset($input->nilai_reviewer2[3]) && ($input->nilai_reviewer2[3] == 4) ? true : false,'required class="custom-control-input" id="nilai_kriteria4_reviewer2_4"')?>
                <label class="custom-control-label" for="nilai_kriteria4_reviewer2_4">4</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('nilai_reviewer2_3', 5, isset($input->nilai_reviewer2[3]) && ($input->nilai_reviewer2[3] == 5) ? true : false,'required class="custom-control-input" id="nilai_kriteria4_reviewer2_5"')?>
                <label class="custom-control-label" for="nilai_kriteria4_reviewer2_5">5</label>
              </div>
              <?php else: ?>
              <p class="m-0 p-0">Nilai =
                <?=isset($input->nilai_reviewer2[3])?$input->nilai_reviewer2[3]:''; ?>
              </p>
              <?php endif ?>
            </div>
            <?php if($ceklevel != 'author'): ?>
            <div id="total_reviewer2">
              <?php if(!empty($draft->nilai_total_reviewer2)){
                if($draft->nilai_total_reviewer2 >=400){
                  $hasil = '<div class="alert alert-success"><span class="badge badge-success">Naskah Lolos Review</span><br>';
                  $hasil .= '<strong>Nilai total = '.$draft->nilai_total_reviewer2.'</strong><br>';
                  $hasil .= 'Passing Grade = 400 <br>';
                  $hasil .= '</div>';
                }else{
                  $hasil = '<div class="alert alert-danger"><span class="badge badge-danger">Naskah Tidak Lolos Review</span><br>';
                  $hasil .= '<strong>Nilai total = '.$draft->nilai_total_reviewer2.'</strong><br>';
                  $hasil .= 'Passing Grade = 400 <br>';
                  $hasil .= '</div>';
                }
                  echo $hasil;
                } 
                ?>
            </div>
            <?php endif ?>
            <!-- /.form-group -->
            <!-- endif review dari reviewer hanya bisa dibaca admin dan staff ugmpress -->
            <?php endif ?>
            <!-- .fieldset -->
            <fieldset>
              <?php if($ceklevel!='author'): ?>
              <hr class="my-3">
              <!-- .form-group -->
              <div class="form-group">
                <label for="cr2" class="font-weight-bold">Catatan Reviewer 2</label>
                <?php 
                  $optionscr2 = array(
                      'name' => 'review2_notes',
                      'class'=> 'form-control summernote-basic',
                      'id'  => 'cr2',
                      'rows' => '6',
                      'value'=> $input->review2_notes
                  );
                  if($ceklevel!='reviewer'){
                    echo '<div class="font-italic">'.nl2br($input->review2_notes).'</div>';
                  }else{
                    echo form_textarea($optionscr2);
                  }
                  ?>
              </div>
              <?php endif ?>
              <!-- /.form-group -->
              <?php if($ceklevel=='superadmin' or $ceklevel=='admin_penerbitan' or $ceklevel=='author'): ?>
              <hr class="my-3">
              <!-- .form-group -->
              <div class="form-group">
                <label for="cr2a" class="font-weight-bold">Catatan Admin untuk Penulis</label>
                <?php 
                  $optionscr2a = array(
                      'name' => 'catatan_review2_admin',
                      'class'=> 'form-control summernote-basic',
                      'id'  => 'cr2a',
                      'rows' => '6',
                      'value'=> $input->catatan_review2_admin
                  );
                  if($ceklevel=='superadmin' or $ceklevel=='admin_penerbitan'){
                    echo form_textarea($optionscr2a);
                  }elseif($ceklevel=='author'){
                    echo '<div class="font-italic">'.nl2br($input->catatan_review2_admin).'</div>';
                  }else{}
                  ?>
              </div>
              <?php endif ?>
              <!-- /.form-group -->
              <hr class="my-3">
              <!-- .form-group -->
              <div class="form-group">
                <label for="crp2" class="font-weight-bold">Catatan Penulis</label>
                <?php 
                  $optionscrp2 = array(
                      'name' => 'review2_notes_author ',
                      'class'=> 'form-control summernote-basic',
                      'id'  => 'crp2',
                      'rows' => '6',
                      'value'=> $input->review2_notes_author
                  );
                  if($ceklevel!='author' or $author_order!=1){
                    echo '<div class="font-italic">'.nl2br($input->review2_notes_author).'</div>';
                  }else{
                    echo form_textarea($optionscrp2);
                  }
                   ?>
              </div>
              <!-- /.form-group -->
            </fieldset>
            <!-- /.fieldset -->
          </div>
          <!-- /.modal-body -->
          <!-- .modal-footer -->
          <div class="modal-footer">
            <?php if($ceklevel=='reviewer'): ?>
            <div class="card-footer-content text-muted p-0 m-0">
              <div class="mb-1 font-weight-bold">Rekomendasi</div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('review2_flag', 'y', isset($input->review2_flag) && ($input->review2_flag == 'y') ? true : false,'required class="custom-control-input" id="review2_flagy"')?>
                <label class="custom-control-label" for="review2_flagy">Setuju</label>
              </div>
              <div class="custom-control custom-control-inline custom-radio">
                <?= form_radio('review2_flag', 'n', isset($input->review2_flag) && ($input->review2_flag == 'n') ? true : false,'required class="custom-control-input" id="review2_flagn"')?>
                <label class="custom-control-label" for="review2_flagn">Tidak</label>
              </div>
            </div>
            <button class="btn btn-primary ml-auto" type="submit" value="Submit" id="btn-submit-review2-rev">Submit</button>
            <?php else: ?>
            <button class="btn btn-primary ml-auto" type="submit" value="Submit" id="btn-submit-review2-other">Submit</button>
            <?php endif ?>
            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
          </div>
          <!-- /.modal-footer -->
          <?= form_close(); ?>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    <!-- modal deadline -->
    <div class="modal fade" id="review_deadline" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <!-- .modal-dialog -->
      <div class="modal-dialog" role="document">
        <!-- .modal-content -->
        <div class="modal-content">
          <!-- .modal-header -->
          <div class="modal-header">
            <h5 class="modal-title">Deadline Review</h5>
          </div>
          <!-- /.modal-header -->
          <!-- .modal-body -->
          <div class="modal-body">
            <!-- .form -->
            <?= form_open('draft/ubahnotes/'.$input->draft_id) ?>
            <!-- .fieldset -->
            <fieldset>
              <!-- .form-group -->
              <div class="form-group">
                <label for="review1_deadline">Deadline Reviewer 1</label>
                <div>
                  <?= form_input('review1_deadline', $input->review1_deadline, 'class="form-control mydate_modal d-none" id="review1_deadline" required=""') ?>
                </div>
                <div class="invalid-feedback">Harap diisi</div>
                <?= form_error('review1_deadline') ?>
              </div>
              <!-- /.form-group -->
              <!-- .form-group -->
              <div class="form-group">
                <label for="review2_deadline">Deadline Reviewer 2</label>
                <div>
                  <?= form_input('review2_deadline', $input->review2_deadline, 'class="form-control mydate_modal d-none" id="review2_deadline" required="" ') ?>
                </div>
                <div class="invalid-feedback">Harap diisi</div>
                <?= form_error('review2_deadline') ?>
              </div>
              <!-- /.form-group -->
            </fieldset>
            <!-- /.fieldset -->
          </div>
          <!-- /.modal-body -->
          <!-- .modal-footer -->
          <div class="modal-footer">
            <button class="btn btn-primary" type="submit" id="btn-review-deadline">Pilih</button>
            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
          </div>
          <!-- /.modal-footer -->
          <?=form_close(); ?>
          <!-- /.form -->
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    <!-- modal aksi review -->
    <div class="modal fade" id="review_aksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <!-- .modal-dialog -->
      <div class="modal-dialog" role="document">
        <!-- .modal-content -->
        <div class="modal-content">
          <!-- .modal-header -->
          <div class="modal-header">
            <h5 class="modal-title">Aksi</h5>
          </div>
          <!-- /.modal-header -->
          <!-- .modal-body -->
          <div class="modal-body">
            <!-- .form -->
            <?= form_open('draft/ubahnotes/'.$input->draft_id) ?>
            <!-- .fieldset -->
            <fieldset>
              <!-- .form-group -->
              <div class="form-group">
                <label for="review_status" class="font-weight-bold">Catatan Admin</label>
                <div class="alert alert-info">
                  Catatan admin dapat dilihat oleh semua user yang terkait dengan draft ini.
                </div>
                <?php 
                  $hidden_date = array(
                      'type'  => 'hidden',
                      'id'    => 'review_end_date',
                      'value' => date('Y-m-d H:i:s')
                  );
                  echo form_input($hidden_date);
                  $review_status = array(
                      'name' => 'review_status',
                      'class'=> 'form-control summernote-basic',
                      'id'  => 'crp2',
                      'rows' => '6',
                      'value'=> $input->review_status
                  );
                  if($ceklevel!='superadmin' and $ceklevel!='admin_penerbitan'){
                    echo '<div class="font-italic">'.nl2br($input->review_status).'</div>';
                  }else{
                    echo form_textarea($review_status);
                  }
                   ?>
                <div class="alert alert-info">
                  Pilih salah satu tombol dibawah ini: <br>
                  Jika <strong class="text-success">Setuju</strong>, maka tahap review akan diakhiri dan tanggal selesai review akan dicatat <br>
                  Jika <strong class="text-danger">Tolak</strong> maka proses draft akan diakhiri sampai tahap ini.
                </div>
              </div>
              <!-- /.form-group -->
            </fieldset>
            <!-- /.fieldset -->
          </div>
          <!-- /.modal-body -->
          <!-- .modal-footer -->
          <div class="modal-footer">
            <button class="btn btn-success" type="submit" id="review-setuju" value="5">Setuju</button>
            <button class="btn btn-danger" type="submit" id="review-tolak" value="99">Tolak</button>
            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
          </div>
          <!-- /.modal-footer -->
          <?=form_close(); ?>
          <!-- /.form -->
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
  </div>
  </div>
  <!-- /.card-body -->
</section>
<!-- /.card -->
<script>
$(document).ready(function() {
  //panggil setingan validasi di ugmpress js
  setting_validasi();

  //validasi review1
  $("#rev1form").validate({
      rules: {
        review1_file: {
          require_from_group: [1, ".naskah"],
          dokumen: "docx|doc|pdf",
          filesize50: 52428200
        },
        reviewer1_file_link: {
          curl: true,
          require_from_group: [1, ".naskah"]
        }
      },
      errorElement: "span",
      errorClass: "none",
      validClass: "none",
      errorPlacement: function(error, element) {
        error.addClass("invalid-feedback");
        if (element.parent('.input-group').length) {
          error.insertAfter(element.next('span.select2')); // input group
        } else if (element.hasClass("select2-hidden-accessible")) {
          error.insertAfter(element.next('span.select2')); // select2
        } else if (element.parent().parent().hasClass('input-group')) {
          error.insertAfter(element.closest('.input-group')); // fileinput append
        } else if (element.hasClass("custom-file-input")) {
          error.insertAfter(element.next('label.custom-file-label')); // fileinput custom
        } else if (element.hasClass("custom-control-input")) {
          error.insertAfter($(".custom-radio").last()); // radio
        } else {
          error.insertAfter(element); // default
        }
      },
      submitHandler: function(form) {
        var $this = $('#btn-upload-review1');
        $this.attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i> Uploading ");
        let id = $('[name=draft_id]').val();
        var formData = new FormData(form);
        $.ajax({
          url: "<?php echo base_url('draft/upload_progress/') ?>" + id + "/review1_file",
          type: "post",
          data: formData,
          processData: false,
          contentType: false,
          cache: false,
          success: function(data) {
            let datax = JSON.parse(data);
            console.log(datax);
            $this.removeAttr("disabled").html("Upload");
            if (datax.status == true) {
              toastr_view('111');
            } else {
              toastr_view('000');
            }
            $('#modal-review1').load(' #modal-review1');
          }
        });
        $resetform = $('#review1_file');
        $resetform.val('');
        $resetform.next('label.custom-file-label').html('');
        return false;
      }
    },
    select2_validasi()
  );

  //validasi review2
  $("#rev2form").validate({
      rules: {
        review2_file: {
          require_from_group: [1, ".naskah"],
          dokumen: "docx|doc|pdf",
          filesize50: 52428200
        },
        reviewer2_file_link: {
          curl: true,
          require_from_group: [1, ".naskah"]
        }
      },
      errorElement: "span",
      errorClass: "none",
      validClass: "none",
      errorPlacement: function(error, element) {
        error.addClass("invalid-feedback");
        if (element.parent('.input-group').length) {
          error.insertAfter(element.next('span.select2')); // input group
        } else if (element.hasClass("select2-hidden-accessible")) {
          error.insertAfter(element.next('span.select2')); // select2
        } else if (element.parent().parent().hasClass('input-group')) {
          error.insertAfter(element.closest('.input-group')); // fileinput append
        } else if (element.hasClass("custom-file-input")) {
          error.insertAfter(element.next('label.custom-file-label')); // fileinput custom
        } else if (element.hasClass("custom-control-input")) {
          error.insertAfter($(".custom-radio").last()); // radio
        } else {
          error.insertAfter(element); // default
        }
      },
      submitHandler: function(form) {
        var $this = $('#btn-upload-review2');
        $this.attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i> Uploading ");
        let id = $('[name=draft_id]').val();
        var formData = new FormData(form);
        $.ajax({
          url: "<?php echo base_url('draft/upload_progress/') ?>" + id + "/review2_file",
          type: "post",
          data: formData,
          processData: false,
          contentType: false,
          cache: false,
          success: function(data) {
            let datax = JSON.parse(data);
            console.log(datax);
            $this.removeAttr("disabled").html("Upload");
            if (datax.status == true) {
              toastr_view('111');
            } else {
              toastr_view('000');
            }
            $('#modal-review2').load(' #modal-review2');
          }
        });
        $resetform = $('#review2_file');
        $resetform.val('');
        $resetform.next('label.custom-file-label').html('');
        return false;
      }
    },
    select2_validasi()
  );

  $('#btn-submit-review1-rev').on('click', function() {
    var $this = $(this);
    let id = $('[name=draft_id]').val();
    let cr1 = $('#cr1').val();
    let crp1 = $('#crp1').val();
    let rev1_flag = $('[name=review1_flag]:checked').val();
    let kriteria1_reviewer1 = $('#kriteria1_reviewer1').val();
    let kriteria2_reviewer1 = $('#kriteria2_reviewer1').val();
    let kriteria3_reviewer1 = $('#kriteria3_reviewer1').val();
    let kriteria4_reviewer1 = $('#kriteria4_reviewer1').val();
    let nilai_reviewer1_0 = $('[name=nilai_reviewer1_0]:checked').val();
    let nilai_reviewer1_1 = $('[name=nilai_reviewer1_1]:checked').val();
    let nilai_reviewer1_2 = $('[name=nilai_reviewer1_2]:checked').val();
    let nilai_reviewer1_3 = $('[name=nilai_reviewer1_3]:checked').val();
    let nilai_reviewer1 = [nilai_reviewer1_0, nilai_reviewer1_1, nilai_reviewer1_2, nilai_reviewer1_3];
    if (nilai_reviewer1_0 == null || nilai_reviewer1_1 == null || nilai_reviewer1_2 == null || nilai_reviewer1_3 == null) {
      toastr_view('penilaian');
      return false;
    }
    if (rev1_flag == null) {
      toastr_view('flag');
      return false;
    }
    $this.attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i> Processing ");
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('draft/ubahnotes/') ?>" + id + "/1",
      datatype: "JSON",
      data: {
        review1_notes: cr1,
        review1_notes_author: crp1,
        review1_flag: rev1_flag,
        kriteria1_reviewer1: kriteria1_reviewer1,
        kriteria2_reviewer1: kriteria2_reviewer1,
        kriteria3_reviewer1: kriteria3_reviewer1,
        kriteria4_reviewer1: kriteria4_reviewer1,
        nilai_reviewer1: nilai_reviewer1
      },
      success: function(data) {
        let datax = JSON.parse(data);
        console.log(datax)
        $this.removeAttr("disabled").html("Submit");
        if (datax.status == true) {
          toastr_view('111');
        } else {
          toastr_view('000');
        }
        $('#total_reviewer1').load(' #total_reviewer1');
        $('#review1').modal('toggle');
      }
    });
    return false;
  });

  $('#btn-submit-review1-other').on('click', function() {
    var $this = $(this);
    let id = $('[name=draft_id]').val();
    let cr1 = $('#cr1').val();
    let cr1a = $('#cr1a').val();
    let crp1 = $('#crp1').val();
    let rev1_flag = $('[name=review1_flag]:checked').val();
    $this.attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i> Processing ");
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('draft/ubahnotes/') ?>" + id,
      datatype: "JSON",
      data: {
        catatan_review1_admin: cr1a,
        review1_notes: cr1,
        review1_notes_author: crp1,
        review1_flag: rev1_flag,
      },
      success: function(data) {
        let datax = JSON.parse(data);
        console.log(datax)
        $this.removeAttr("disabled").html("Submit");
        if (datax.status == true) {
          toastr_view('111');
        } else {
          toastr_view('000');
        }
        $('#list-group-review').load(' #list-group-review');
        $('#review1').modal('toggle');
      }
    });
    return false;
  });

  $('#btn-submit-review2-rev').on('click', function() {
    var $this = $(this);
    let id = $('[name=draft_id]').val();
    let cr2 = $('#cr2').val();
    let crp2 = $('#crp2').val();
    let rev2_flag = $('[name=review2_flag]:checked').val();
    let kriteria1_reviewer2 = $('#kriteria1_reviewer2').val();
    let kriteria2_reviewer2 = $('#kriteria2_reviewer2').val();
    let kriteria3_reviewer2 = $('#kriteria3_reviewer2').val();
    let kriteria4_reviewer2 = $('#kriteria4_reviewer2').val();
    let nilai_reviewer2_0 = $('[name=nilai_reviewer2_0]:checked').val();
    let nilai_reviewer2_1 = $('[name=nilai_reviewer2_1]:checked').val();
    let nilai_reviewer2_2 = $('[name=nilai_reviewer2_2]:checked').val();
    let nilai_reviewer2_3 = $('[name=nilai_reviewer2_3]:checked').val();
    let nilai_reviewer2 = [nilai_reviewer2_0, nilai_reviewer2_1, nilai_reviewer2_2, nilai_reviewer2_3];
    if (nilai_reviewer2_0 == null || nilai_reviewer2_1 == null || nilai_reviewer2_2 == null || nilai_reviewer2_3 == null) {
      toastr_view('penilaian');
      return false;
    }
    if (rev2_flag == null) {
      toastr_view('flag');
      return false;
    }
    $this.attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i> Processing ");
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('draft/ubahnotes/') ?>" + id + "/2",
      datatype: "JSON",
      data: {
        review2_notes: cr2,
        review2_notes_author: crp2,
        review2_flag: rev2_flag,
        kriteria1_reviewer2: kriteria1_reviewer2,
        kriteria2_reviewer2: kriteria2_reviewer2,
        kriteria3_reviewer2: kriteria3_reviewer2,
        kriteria4_reviewer2: kriteria4_reviewer2,
        nilai_reviewer2: nilai_reviewer2

      },
      success: function(data) {
        let datax = JSON.parse(data);
        console.log(datax)
        $this.removeAttr("disabled").html("Submit");
        if (datax.status == true) {
          toastr_view('111');
        } else {
          toastr_view('000');
        }
        $('#total_reviewer2').load(' #total_reviewer2');
        $('#review2').modal('toggle');
      },
      error: function(a, b, c){
        alert(a.responseText);
      }
    });
    return false;
  });

  $('#btn-submit-review2-other').on('click', function() {
    var $this = $(this);
    let id = $('[name=draft_id]').val();
    let cr2 = $('#cr2').val();
    let cr2a = $('#cr2a').val();
    let crp2 = $('#crp2').val();
    let rev2_flag = $('[name=review2_flag]:checked').val();
    $this.attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i> Processing ");
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('draft/ubahnotes/') ?>" + id,
      datatype: "JSON",
      data: {
        catatan_review2_admin: cr2a,
        review2_notes: cr2,
        review2_notes_author: crp2,
        review2_flag: rev2_flag,
      },
      success: function(data) {
        let datax = JSON.parse(data);
        console.log(datax)
        $this.removeAttr("disabled").html("Submit");
        if (datax.status == true) {
          toastr_view('111');
        } else {
          toastr_view('000');
        }
        $('#list-group-review').load(' #list-group-review');
        $('#review2').modal('toggle');
      }
    });
    return false;
  });


  $('#review-setuju').on('click', function() {
    var $this = $(this);
    $this.attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i> Processing ");
    let id = $('[name=draft_id]').val();
    let review_status = $('[name=review_status]').val();
    let action = $('#review-setuju').val();
    let end_date = $('#review_end_date').val();
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('draft/ubahnotes/') ?>" + id,
      datatype: "JSON",
      data: {
        review_status: review_status,
        draft_status: action,
        review_end_date: end_date,
        is_review: 'y'
      },
      success: function(data) {
        let datax = JSON.parse(data);
        console.log(datax)
        $this.removeAttr("disabled").html("Setuju");
        if (datax.status == true) {
          toastr_view('111');
        } else {
          toastr_view('000');
        }
        //$('#list-group-review').load(' #list-group-review');
        location.reload();
      }
    });

    // $('#review_aksi').modal('hide');

    return false;
  });

  $('#review-tolak').on('click', function() {
    var $this = $(this);
    $this.attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i> Processing ");
    let id = $('[name=draft_id]').val();
    let review_status = $('[name=review_status]').val();
    let action = $('#review-tolak').val();
    let end_date = $('#review_end_date').val();
    console.log(end_date);
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('draft/ubahnotes/') ?>" + id,
      datatype: "JSON",
      data: {
        review_status: review_status,
        draft_status: action,
        review_end_date: end_date,
        is_review: 'n'
      },
      success: function(data) {
        let datax = JSON.parse(data);
        console.log(datax);
        $this.removeAttr("disabled").html("Tolak");
        if (datax.status == true) {
          toastr_view('111');
        } else {
          toastr_view('000');
        }
        //$('#list-group-review').load(' #list-group-review');
        location.reload();
      }
    });

    // $('#review_aksi').modal('hide');
    return false;
  });

  //review deadline
  $('#btn-review-deadline').on('click', function() {
    var $this = $(this);
    $this.attr("disabled", "disabled").html("<i class='fa fa-spinner fa-spin '></i> Processing ");
    let id = $('[name=draft_id]').val();
    let rd1 = $('[name=review1_deadline]').val();
    let rd2 = $('[name=review2_deadline]').val();
    $.ajax({
      type: "POST",
      url: "<?php echo base_url('draft/ubahnotes/') ?>" + id,
      datatype: "JSON",
      data: {
        review1_deadline: rd1,
        review2_deadline: rd2,
      },
      success: function(data) {
        let datax = JSON.parse(data);
        console.log(datax)
        $this.removeAttr("disabled").html("Submit");
        if (datax.status == true) {
          toastr_view('111');
        } else {
          toastr_view('000');
        }
        $('#list-group-review').load(' #list-group-review');
        $('#review_deadline').modal('toggle');
      }
    });
    return false;
  });

})
</script>