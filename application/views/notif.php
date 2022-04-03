<?php
$per_page       = $this->input->get('per_page') ?? 10;
$keyword        = $this->input->get('keyword');
$is_read        = $this->input->get('is_read');
$is_starred     = $this->input->get('is_starred');
$id_draft       = $this->input->get('id_draft');
$id_user_pembuat = $this->input->get('id_user_pembuat');
$page           = $this->uri->segment(3);
// data table series number
$i = isset($page) ? $page * $per_page - $per_page : 0;

$is_read_options = [
    '' => 'Semua',
    'y' => 'Sudah dibaca',
    'n' => 'Belum dibaca'
];

$is_starred_options = [
    '' => 'Semua',
    'y' => 'Notifikasi berbintang',
    'n' => 'Notifikasi tidak berbintang',
];
?>

<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item active">
                <a class="text-muted">Notifikasi</a>
            </li>
        </ol>
    </nav>
    
</header>

<div class="page-section">
    <div class="row">
        <div class="col-12">
            <section class="card card-fluid">
                <div class="card-body p-0">
                    <div class="p-3">
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <h5>Info</h5>
                            <p class="m-0">Klik tombol <button class="btn btn-sm btn-secondary"><i class="fa fa-check"></i>
                                    Dibaca</button> untuk membuat status pesan menjadi sudah terbaca</p>
                            <p class="m-0">Lambang <button class="btn btn-sm btn-secondary"><i class="fa fa-star"></i></button> adalah tanda bahwa pesan merupakan notifikasi berbintang. Anda dapat melakukan klik pada lambang tersebut untuk toggle pesan berbintang.</p>
                            
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?= form_open($pages, ['method' => 'GET']); ?>
                        <div class="row">
                            <div class="col-12 col-md-2 mb-3">
                                <label for="per_page">Data per halaman</label>
                                <?= form_dropdown('per_page', get_per_page_options(), $per_page, 'id="per_page" class="form-control custom-select d-block" title="List per page"'); ?>
                            </div>
                            <div class="col-12 col-md-3 mb-3">
                                <label for="is_read">Status Notifikasi</label>
                                <?= form_dropdown('is_read', $is_read_options, $is_read, 'id="is_read" class="form-control custom-select d-block" title="Filter Status Notifikasi"'); ?>
                            </div>
                            <div class="col-12 col-md-3 mb-3">
                                <label for="is_starred">Status Bintang</label>
                                <?= form_dropdown('is_starred', $is_starred_options, $is_starred, 'id="is_starred" class="form-control custom-select d-block" title="Filter Notifikasi Berbintang"'); ?>
                            </div>
                            <div class="col-12 col-lg-5 mb-3">
                                <label>&nbsp;</label>
                                <?= form_input('keyword', $keyword, 'id="keyword" placeholder="Cari berdasarkan keyword pesan" class="form-control"'); ?>
                            </div>
                            <div class="col-12 col-lg-5">
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
                        <?php 
                            if($notif) :
                                ?>
                            <div class="double-scroll">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="pl-3">No</th>
                                            <th scope="col" style="min-width:150px;">Pengirim</th>
                                            <th scope="col" style="min-width:220px;">Judul Buku</th>
                                            <th scope="col" style="min-width:350px;">Isi Notifikasi</th>
                                            <th scope="col" style="min-width:100px">Status</th>
                                            <th scope="col" style="min-width:100px">Berbintang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $j = 1;
                                        foreach ($notif as $key ) { 
                                        
                                        echo "<tr>";
                                        echo "<td class='align-middle'>".$j."</td>";
                                        echo "<td class='align-middle'>".$key->pengirim."</td>";
                                        echo "<td class='align-middle'><a href='".base_url('draft/view/' . $key->id_draft . '')."' class='font-weight-bold'>
                                                    ".highlight_keyword($key->judul_buku, $keyword)."
                                                </a></td>";
                                        echo "<td class='align-middle'>".$key->ket."</td>";
                                        if(empty($key->is_read))
                                            echo "<td class='align-middle'>
                                                <a href='".base_url('notifikasi/read/1/'.$key->id.'')."' class='btn btn-sm btn-secondary'><i class='fa fa-check'></i>
                                                Dibaca</a></td>";
                                        else
                                            echo "<td class='align-middle'>Sudah Dibaca</td>";
                                        if(empty($key->is_starred))
                                        echo "<td class='align-middle'>
                                                <a href='".base_url('notifikasi/toggle_bintang/1/'.$key->id.'')."' class='btn btn-sm btn-secondary'><i class='far fa-star'></i>
                                                </a></td>";
                                        else
                                            echo "<td class='align-middle'>
                                                <a href='".base_url('notifikasi/toggle_bintang/0/'.$key->id.'')."' class='btn btn-sm btn-secondary'><i class='fa fa-star'></i>
                                                </a></td>";
                                        echo "</tr>";
                                            $j++;
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php
                            else : ?>
                        <p class="text-center">Data tidak tersedia</p>
                        <?php endif; ?>
                        <?= $pagination ?? null; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    doublescroll();

});
</script>
