<!-- .card -->
<div class="card card-fluid">
  <h6 class="card-header"> Profil </h6>
  <!-- .card-body -->
  <div class="card-body">
    <!-- .table-responsive -->
    <div class="table-responsive">
      <!-- .table -->
      <table class="table table-striped table-bordered mb-0 nowrap">
        <!-- tbody -->
        <tbody>
          <!-- tr -->
          <tr>
            <td width="200px"> User ID </td>
            <td><?= (!empty($input->user_id))? konversiID('user','user_id', $input->user_id)->username : '' ?> </td>
          </tr>
          <!-- /tr -->
          <!-- tr -->
          <tr>
            <td width="200px"> NIP </td>
            <td><?= $input->author_nip ?> </td>
          </tr>
          <!-- /tr -->
          <!-- tr -->
          <tr>
            <td width="200px"> Nama </td>
            <td><?= $input->author_name ?> </td>
          </tr>
          <!-- /tr -->
          <!-- tr -->
          <tr>
            <td width="200px"> Gelar </td>
            <td><?= $input->author_degree_front ?>, <?= $input->author_degree_back ?></td>
          </tr>
          <!-- /tr -->
          <!-- tr -->
          <tr>
            <td width="200px"> Pendidikan Terakhir </td>
            <td><?= ($input->author_latest_education == 's4')? 'Professor' : ucwords($input->author_latest_education) ?></td>
          </tr>
          <!-- /tr -->
          <!-- tr -->
          <tr>
            <td width="200px"> Unit Kerja </td>
            <td> <?= konversiID('work_unit','work_unit_id', $input->work_unit_id)->work_unit_name;?>  </td>
          </tr>
          <!-- /tr -->
          <!-- tr -->
          <tr>
            <td width="200px"> Institusi </td>
            <td> <?= konversiID('institute','institute_id', $input->institute_id)->institute_name;?>  </td>
          </tr>
          <!-- /tr -->
          <!-- tr -->
          <tr>
            <td width="200px"> Alamat </td>
            <td><?= $input->author_address ?> </td>
          </tr>
          <!-- /tr -->
          <!-- tr -->
          <tr>
            <td width="200px"> No HP </td>
            <td><?= $input->author_contact ?> </td>
          </tr>
          <!-- /tr -->
          <!-- tr -->
          <tr>
            <td width="200px"> Email </td>
            <td><?= $input->author_email ?>  </td>
          </tr>
          <!-- /tr -->
          <!-- tr -->
          <tr>
            <td width="200px"> Ahli Waris </td>
            <td> <?= $input->heir_name ?>  </td>
          </tr>
          <!-- /tr -->
          <!-- tr -->
          <tr>
            <td width="200px"> KTP </td>
            <td>
            <?php
                //liat ekstensi file 
                if($input->author_ktp!=''){
                  $getextension = explode(".", $input->author_ktp); 
                }else{
                  $getextension[1] = '';
                }
                if($input->author_ktp !=''){
                //jika ekstensi pdf maka tampilkan link
                  if($getextension[1]!='pdf'){
                    echo '<img src="'.base_url('authorktp/'.$input->author_ktp).'" width="30%" class="previewxx"><br>';
                  }else{
                    echo '<div align="left"><a href="'.base_url('authorktp/'.$input->author_ktp).'" class="btn btn-success btn-sm previewxx"><i class="fa fa-download"></i> Lihat KTP</a></div>';
                  }
                }
                ?>
              </td>
          </tr>
          <!-- /tr -->
        </tbody>
        <!-- /tbody -->
      </table>
      <!-- /.table -->
    </div>
    <!-- /.table-responsive -->
  </div>
  <!-- /.card-body -->
  <!-- .card-footer -->
  <footer class="card-footer">
    <div class="card-footer-content text-muted">
      <a href="<?=base_url('author/edit/'.$input->author_id) ?>" class="btn btn-secondary">Edit Data</a>
    </div>
  </footer>
  <!-- /.card-footer -->
</div>
<!-- /.card -->