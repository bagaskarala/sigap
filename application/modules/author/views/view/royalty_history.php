<?php $i = 1;?>
<div class="card card-fluid">
   <h6 class="card-header"> Riwayat Royalty </h6>
   <div class="card-body">
      <?php if ($royalty): ?>
      <div class="table-responsive">
         <table class="table table-striped table-bordered mb-0">
            <thead>
               <tr>
                  <th scope="col">No</th>
                  <th scope="col">Periode</th>
                  <th scope="col">Jumlah Royalty</th>
                  <th scope="col">Status</th>
                  <th scope="col">Tanggal Dibayar</th>
               </tr>
            </thead>
            <tbody>
               <?php foreach ($royalty as $lData): ?>
               <tr>
                                    
                  <td class="align-middle"><?=$i++;?></td>
                  <td class="align-middle"><?= $lData->start_date ? date("d F Y", strtotime($lData->start_date)) : '' ?> - <?= $lData->end_date ? date("d F Y", strtotime($lData->end_date)) : '' ?></td>
                  <td class="align-middle text-right">Rp <?= number_format($lData->details->earned_royalty,  0, ',', '.'); ?></td>
                  <td class="align-middle"><?= get_royalty_status()[$lData->status] ?></td>
                  <td class="align-middle"><?= $lData->paid_date ? date("d F Y", strtotime($lData->paid_date)) : '' ?></td>
               </tr>
               <?php endforeach;?>
            </tbody>
         </table>
      </div>
      <?php else: ?>
      <p class="text-center">Data tidak tersedia</p>
      <?php endif;?>
   </div>
</div>