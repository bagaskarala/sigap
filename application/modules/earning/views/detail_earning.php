<?php
$date_year          = $this->input->get('date_year');
$invoice_type       = $this->input->get('invoice_type');
$date_month         = $this->input->get('date_month');

$date_year_options = [];

$date_month_options = [
    ''  => 'Satu Tahun'
];
$month_name  = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
for ($i = 1; $i <= 12; $i++) {
    $date_month_options[$i] = $month_name[$i - 1];
}

for ($dy = intval(date('Y')); $dy >= 2015; $dy--) {
    $date_year_options[$dy] = $dy;
}

?>

<link
    rel="stylesheet"
    href="<?= base_url('assets/vendor/chart.js/new/Chart.min.css'); ?>"
>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-url/2.5.3/url.js"></script>
<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a
                    href="<?= base_url('earning'); ?>"
                    class="text-muted"
                >Pendapatan</a>
            </li>
            <li class="breadcrumb-item active">
                <a class="text-muted">Pendapatan Faktur</a>
            </li>
        </ol>
    </nav>
</header>


<div class="page-section">
    <div class="row">
        <div class="col-12">
            <section class="card card-fluid">
                <div class="card-body p-0">
                    <header class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    href="<?= base_url('earning/'); ?>"
                                >Pendapatan Faktur</a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link active"
                                    href="<?= base_url('earning/detail'); ?>"
                                >Detail Pendapatan</a>
                            </li>
                        </ul>
                    </header>
                    <div class="p-3">
                        <?= form_open(base_url('earning/detail'), ['method' => 'GET']); ?>
                        <div class="row">
                            <div class="col">
                                <?= form_dropdown('date_year', $date_year_options, $date_year, 'id="date_year" class="form-control custom-select d-block" title="Filter Tahun Cetak"'); ?>
                            </div>
                            <div class="col">
                                <?= form_dropdown('date_month', $date_month_options, $date_month, 'id="date_month" class="form-control custom-select d-block" title="Filter Tahun Cetak"'); ?>
                            </div>
                            <div class="col">
                                <div
                                    class="btn-group btn-block"
                                    role="group"
                                    aria-label="Filter button"
                                >
                                    <button
                                        class="btn btn-secondary"
                                        type="button"
                                        onclick="location.href = '<?= base_url('earning/detail'); ?>'"
                                    > Reset</button>
                                    <button
                                        class="btn btn-primary"
                                        type="submit"
                                        value="Submit"
                                    ><i class="fa fa-filter"></i> Filter</button>
                                    <button
                                        class="btn btn-success"
                                        type="submit"
                                        id="excel"
                                        name="excel"
                                        value="1"
                                    >Excel</button>
                                </div>
                            </div>
                        </div>
                        <?= form_close(); ?>
                        <hr>
                    </div>
                    <div class="container">
                        <div class="row">
                            <div
                                class="col-md-12"
                                style="text-align: center;"
                            >
                                <b>
                                    <h5>DETAIL PENDAPATAN FAKTUR</h5>
                                </b>
                            </div>
                            <div class="col-md-12">
                                <canvas id="total_year"></canvas>
                            </div>
                        </div>
                        <div
                            id="table_laporan"
                            name="table_laporan"
                            class="mt-5 pb-5"
                        >
                            <hr>
                            <div style="text-align: center;">
                                <b>
                                    <p id="month_year">

                                    </p>
                                </b>
                            </div>
                            <div style="text-align: center;">
                                <b>
                                    <h2>Detail Pendapatan</h2>
                                </b>
                            </div>
                            <table class="table table-striped mb-0 table-responsive">
                                <thead>
                                    <tr class="text-center">
                                        <th
                                            scope="col"
                                            style="width:300px;"
                                        >Tunai</th>
                                        <th
                                            scope="col"
                                            style="width:25%;"
                                        >Showroom</th>
                                        <th
                                            scope="col"
                                            style="width:25%;"
                                        >Kredit</th>
                                        <th
                                            scope="col"
                                            style="width:25%;"
                                        >Online</th>
                                    </tr>
                                </thead>
                                <tbody class="align-middle text-center">
                                    <?php $total = $details['cash'] + $details['showroom'] + $details['credit'] + $details['online']; ?>
                                    <?php if ($total == 0) : ?>
                                        <?php $total_cash = 0;
                                        $total_showroom = 0;
                                        $total_credit = 0;
                                        $total_online = 0; ?>
                                    <?php else : ?>
                                        <?php $total_cash = round($details['cash'] / $total * 100, 2) ?>
                                        <?php $total_showroom = round($details['showroom'] / $total * 100, 2) ?>
                                        <?php $total_credit = round($details['credit'] / $total * 100, 2) ?>
                                        <?php $total_online = round($details['online'] / $total * 100, 2) ?>
                                    <?php endif ?>
                                    <tr class="text-center">
                                        <td><?= 'Rp ' . number_format(intval($details['cash']), 0, ',', '.') . ' (' . $total_cash . '%)' ?></td>
                                        <td><?= 'Rp ' . number_format(intval($details['showroom']), 0, ',', '.') . ' (' . $total_showroom . '%)' ?></td>
                                        <td><?= 'Rp ' . number_format(intval($details['credit']), 0, ',', '.') . ' (' . $total_credit . '%)' ?></td>
                                        <td><?= 'Rp ' . number_format(intval($details['online']), 0, ',', '.') . ' (' . $total_online . '%)' ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/vendor/chart.js/new/Chart.bundle.min.js'); ?>"></script>

<script>
<?php for ($i = 0; $i < 4; $i++) { ?>
    cash_earning = '<?= $details['cash'] ?>'
    showroom_earning = '<?= $details['showroom'] ?>'
    credit_earning = '<?= $details['credit'] ?>'
    online_earning = '<?= $details['online'] ?>'
<?php } ?>

var ctx = document.getElementById("total_year").getContext('2d')
var total_year = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ["Tunai", "Showroom", "Kredit", "Online"],
        datasets: [{
            label: 'Pendapatan Periode',
            data: [cash_earning, showroom_earning, credit_earning, online_earning],
            backgroundColor: ['rgba(254, 250, 8, 0.8)', 'rgba(193, 80, 76, 0.8)', 'rgba(156, 188, 89, 0.8)', 'rgba(77, 126, 177, 0.8)']
        }]
    },
    options: {
        scales: {
            yAxes: [{
                display: false,
                ticks: {
                    beginAtZero: true
                },
                gridLines: {
                    drawOnChartArea: false
                }
            }],
            xAxes: [{
                display: false,
                ticks: {
                    beginAtZero: true
                },
                gridLines: {
                    drawOnChartArea: false
                }
            }]
        },
        tooltips: {
            displayColors: false,
            callbacks: {
                label: function(tooltipItems, data) {
                    var value = parseInt(data.datasets[0].data[tooltipItems.index])
                    if (value >= 1000) {
                        return data.labels[tooltipItems.index] + ' - Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    } else {
                        return data.labels[tooltipItems.index] + ' - Rp ' + value;
                    }
                    // return data.labels[tooltipItems.index] + 'asdadk' + data.datasets[0].data[tooltipItems.index].toLocaleString();
                }
            }
        },
        layout: {
            padding: {
                left: 0,
                right: 0,
                top: 0,
                bottom: 0
            }
        },
        legend: {
            position: 'bottom'
        }
    }
});
</script>
