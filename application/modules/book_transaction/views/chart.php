<?php
$level              = check_level();
$date_year          = $this->input->get('date_year');

$date_year_options = [];

for ($dy = intval(date('Y')); $dy >= 2015; $dy--) {
    $date_year_options[$dy] = $dy;
}
?>
<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item active">
                <a class="text-muted">Grafik Transaksi Buku</a>
            </li>
        </ol>
    </nav>
</header>
<div class="page-section">
    <div class="row">
        <div class="col-12">
            <section class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="<?= base_url('book_transaction/chart'); ?>">Grafik Transaksi Buku</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-0">
                    <div class="p-3">
                        <!-- book transaction chart -->
                        <link rel="stylesheet" href="<?= base_url('assets/vendor/chart.js/new/Chart.min.css'); ?>">
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/js-url/2.5.3/url.js"></script>
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
                                        <h5>Transaksi Buku Masuk & Keluar Gudang</h5>
                                        <h5>Tahun <span id="date-year-title" class="mb-1"></span></h5>
                                    </b>
                                    <b>
                                    </b>
                                </div>
                                <div class="chart-container" style="position: relative; height:80vh; width:100%">
                                    <canvas id="chart-transaction-yearly">
                                        <script>
                                        $(document).ready(function() {
                                            var year = new Date().getFullYear();
                                            document.getElementById('date_year').valueAsYear = year;
                                            var ctx_peryear = document.getElementById(
                                                "chart-transaction-yearly");
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
                                            var update_per_year = function(year) {
                                                $.getJSON(
                                                    '<?=base_url('/book_transaction/api_all_chart_data/')?>/' +
                                                    year,
                                                    function(data) {
                                                        var stock_in = [];
                                                        var stock_out = [];
                                                        for (var i in data) {
                                                            stock_in.push(data[i].stock_in);
                                                            stock_out.push(data[i].stock_out);
                                                        }
                                                        console.log(stock_in);
                                                        console.log(stock_out);
                                                        var stock_in_data = [];
                                                        $.each(stock_in[1], function(key, val) {
                                                            stock_in_data.push(val);
                                                        })
                                                        var stock_out_data = [];
                                                        $.each(stock_out[1], function(key, val) {
                                                            stock_out_data.push(val);
                                                        })
                                                        console.log(stock_in);
                                                        console.log(stock_out);
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
                                                url = "<?=base_url('/book_transaction/api_all_chart_data/')?>/"+year;
                                                update_per_year(year);
                                            }
                                        });
                                        </script>
                                </div>

                            </div>
                        </div>
                        <!-- Per month chart -->
                        <hr>
                        <!-- book transaction data -->
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>