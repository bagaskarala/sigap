<?php
$level              = check_level();
$date_year          = $this->input->get('date_year');

$date_year_options = [];

for ($dy = intval(date('Y')); $dy >= 2015; $dy--) {
    $date_year_options[$dy] = $dy;
}
?>
<header class="page-title-bar mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('book_stock'); ?>">Stok Buku</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">
                    <?= $input->book_title; ?>
                </a>
            </li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"> Stok Buku </h1>
        </div>
        <?php if($level == 'superadmin'):?>
        <div>
            <a href="<?= base_url("$pages/edit/$input->book_stock_id"); ?>" class="btn btn-primary btn-sm"><i
                    class="fa fa-edit fa-fw"></i> Edit Stok Buku</a>
        </div>
        <?php endif?>
    </div>
</header>

<div class="page-section">
    <section id="data-draft" class="card">
        <header class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item ">
                    <a class="nav-link active show" data-toggle="tab" href="#stock-data"><i
                            class="fa fa-warehouse pr-1"></i>Detail Stok Buku</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" data-toggle="tab" href="#chart-book"><i
                            class="fa fa-chart-line pr-1"></i>Transaksi Buku</a>
                </li>
            </ul>
        </header>
        <div class="card-body">
            <div class="tab-content">
                <!--stock data-->
                <div class="tab-pane fade active show" id="stock-data">
                    <div id="reload-stock">
                        <?php if ($level == 'superadmin'|| $level == 'admin_gudang' || $level == 'admin_pemasaran' || $level == 'staff_gudang') : ?>
                        <?php $i = 1; ?>
                        <div class="row">
                            <div class="col-6 text-left">
                                <strong>Stok Buku</strong>
                            </div>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mb-0 nowrap">
                                <tbody>
                                    <tr>
                                        <td width="160px">Judul Buku</td>
                                        <td><strong>
                                                <?= $input->book_title; ?>
                                        </strong></td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Penulis</td>
                                        <td>
                                            <?= $input->author_name;?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Lokasi Rak</td>
                                        <td>
                                            <?= $input->book_location;?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Stok Keseluruhan</td>
                                        <td><?= $input->warehouse_present+$input->library_present+$input->showroom_present; ?>
                                        </td>
                                    </tr>
                                    <td width="160px">Stok Gudang</td>
                                    <td>
                                        <?= $input->warehouse_present; ?>
                                    </td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Stok Showroom</td>
                                        <td><?= $input->showroom_present; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="160px">Stok Perpustakaan</td>
                                        <td><?= $input->library_present; ?></td>
                                    </tr>
                                    <?php if($input->warehouse_present) :?>
                                    </tr>
                                    <td width="160px">Detail Stok Perpustakaan</td>
                                    <td>
                                        <table class="table table-striped mb-0 table-responsive">
                                            <tbody>
                                                <tr>
                                                    <th class="align-middle text-center">No</th>
                                                    <th style="width:250px;">Nama Perpustakaan</th>
                                                    <th class="align-middle text-center">Stok</th>
                                                </tr>
                                                <?php $no=1; foreach($book_stock->library_stock as $library_data) : ?>
                                                <tr>
                                                    <td class="align-middle text-center">
                                                        <?= $no++; ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?=$library_data->library_name?>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <?=$library_data->library_stock?>
                                                    </td>
                                                </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </td>
                                    </tr>
                                    <?php endif?>
                                    <?php if($input->retur_stock) :?>
                                    <tr>
                                        <td width="160px">Stok Retur</td>
                                        <td><?=$input->retur_stock?></td>
                                    </tr>
                                    <?php endif?>
                               
                                </tbody>
                            </table>
                        </div>
                        <?php if (empty($book_stock->revision) == FALSE) : ?>
                        <hr>
                        <!-- Log Revisi Stok -->
                        <p class="font-weight-bold">Log Revisi Stok Gudang</p>
                        <div class="table-responsive" style="max-height:500px;">
                            <table class="table table-striped table-bordered mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">No</th>
                                        <th scope="col">Perubahan</th>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no=1; foreach($book_stock->revision as $revision) : ?>
                                    <tr class="text-center">
                                        <td>
                                            <?= $no++; ?>
                                        </td>
                                        <td>
                                            <?php 
                                                if ($revision->revision_type == "add") {
                                                    echo '<div class="text-success"> ' . '+' . ' ' . $revision->warehouse_revision . '</div>';
                                                } elseif ($revision->revision_type == "sub") {
                                                    echo '<div class="text-danger"> ' . '-' . ' ' . $revision->warehouse_revision . '</div>';
                                                } else {
                                                    echo '<div class="text-danger"> ' . '-' . ' ' . $revision->warehouse_revision . '</div>';
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?= date('d F Y H:i:s', strtotime($revision->revision_date)); ?>
                                        </td>
                                        <td>
                                            <?= $revision->notes; ?>
                                        </td>
                                    </tr>
                                    <?php if($no==6) break;?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                        <!-- Log perubahan Stok -->
                        <?php else : ?>
                        <p>Data hanya dapat dilihat oleh Superadmin, Admin Gudang,
                            dan Admin Pemasaran</p>
                        <?php endif; ?>
                    </div>
                </div>
                <!--stock data-->

                <!-- book transaction chart -->
                <link rel="stylesheet" href="<?= base_url('assets/vendor/chart.js/new/Chart.min.css'); ?>">
                <script src="https://cdnjs.cloudflare.com/ajax/libs/js-url/2.5.3/url.js"></script>
                <div class="tab-pane fade" id="chart-book">
                    <!-- Per month chart -->
                    <div class="row mb-4">
                        <p class="col-12 font-weight-bold">Transaksi Buku per Tahun</p>
                        <div class="col-4">
                            <?= form_dropdown('date_year', $date_year_options, $date_year, 'id="date_year" class="form-control custom-select d-block" title="Filter Tahun Cetak"'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="text-center">
                                <b>
                                    <h5>Transaksi Buku <span>"<?=$input->book_title?>"</span></h5>
                                    <h5>Tahun</h5>
                                </b>
                                <b>
                                    <span id="date-year-title" class="mb-1"></span>
                                </b>
                            </div>
                            <div class="chart-container" style="position: relative; height:80vh; width:100%">
                                <canvas id="chart-transaction-yearly">
                                <script>
                                    $(document).ready(function() {
                                        var year = new Date().getFullYear();
                                        document.getElementById('date_year').valueAsYear = year;
                                        var ctx_peryear = document.getElementById("chart-transaction-yearly");
                                        var chart_transaction_per_year = new Chart(ctx_peryear, {
                                            type: 'bar',
                                            data: {
                                                labels: ['Januari', 'Februari', 'Maret',
                                                    'April', 'Mei', 'Juni', 'Juli',
                                                    'Agustus', 'September', 'Oktober',
                                                    'November', 'Desember'
                                                ],
                                                datasets: [{
                                                        label: 'Buku Masuk',
                                                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                                                        borderColor: 'rgb(54, 162, 235)',
                                                        borderWidth: 1,
                                                        data: []
                                                    },
                                                    {
                                                        label: 'Buku Keluar',
                                                        backgroundColor: 'rgb(252, 116, 101)',
                                                        borderColor: 'rgb(255, 255, 255)',
                                                        borderWidth: 1,
                                                        data: []
                                                    }
                                                ]
                                            },
                                            options: {
                                                responsive: true,
                                                scales: {
                                                    yAxes: [{
                                                        display: true,
                                                        scaleLabel: {
                                                            display: true,
                                                            labelString: 'Jumlah Buku'
                                                        },
                                                        ticks: {
                                                            beginAtZero: true
                                                        }
                                                    }],
                                                    xAxes: [{
                                                        scaleLabel: {
                                                            display: true,
                                                            labelString: 'Bulan'
                                                        }
                                                    }],
                                                }
                                            },
                                        })
                                        var update_per_year = function(year){
                                            $.getJSON('<?=base_url('/book_stock/api_chart_data/')?><?=$book_stock->book_stock_id?>/'+year, 
                                            function(data) {
                                                var stock_in = [];
                                                var stock_out = [];
                                                for (var i in data) {
                                                    stock_in.push(data[i].stock_in);
                                                    stock_out.push(data[i].stock_out);
                                                }
                                                var stock_in_data = [];
                                                $.each(stock_in[1], function(key, val) {
                                                    stock_in_data.push(val);
                                                })
                                                var stock_out_data = [];
                                                $.each(stock_out[1], function(key, val) {
                                                    stock_out_data.push(val);
                                                })
                                                chart_transaction_per_year.data.datasets[0].data = stock_in_data;
                                                chart_transaction_per_year.data.datasets[1].data = stock_out_data;
                                                chart_transaction_per_year.update();
                                                document.getElementById('date-year-title').innerHTML = year;
                                            })
                                        }

                                        update_per_year(year);

                                        $("#date_year").change(function() {
                                            get_url();
                                        });

                                        function get_url() {
                                            year = $("#date_year").val();
                                            url = "<?=base_url('/book_stock/api_chart_data/')?><?=$book_stock->book_stock_id?>/"+year;
                                            update_per_year(year);
                                        }
                                    });
                                </script>
                            </div>

                        </div>
                    </div>
                    <!-- Per month chart -->
                    <hr>
                </div>
                <!-- book transaction data -->
            </div>
        </div>
    </section>
</div>
<script>
$(document).ready(function() {
    $('.dates').flatpickr({
        altInput: true,
        altFormat: 'j F Y H:i:S',
        dateFormat: 'Y-m-d H:i:S',
        enableTime: true
    });

    $("#date_clear").click(function() {
        $('.dates').clear();
    })
})
</script>