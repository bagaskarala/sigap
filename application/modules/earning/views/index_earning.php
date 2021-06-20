<?php
$date_year          = $this->input->get('date_year');
$invoice_type       = $this->input->get('invoice_type');
$date_month         = $this->input->get('date_month');

$date_year_options = [];

for ($dy = intval(date('Y')); $dy >= 2015; $dy--) {
    $date_year_options[$dy] = $dy;
}

$invoice_type_options = [
    ''  => '- Filter Kategori Faktur -',
    'credit' => 'Kredit',
    'cash' => 'Tunai',
    'online' => 'Online',
    'showroom' => 'Showroom'
];
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
                                    class="nav-link active"
                                    href="<?= base_url('earning/'); ?>"
                                >Pendapatan Faktur</a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    href="<?= base_url('earning/detail'); ?>"
                                >Detail Pendapatan</a>
                            </li>
                        </ul>
                    </header>
                    <div class="p-3">
                        <div
                            class="alert alert-info alert-dismissible fade show"
                            role="alert"
                        >
                            <h5>Info</h5>
                            <p class="m-0">Klik bar pada gafik untuk menampilkan detail transaksi faktur
                            </p>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="alert"
                                aria-label="Close"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?= form_open($pages, ['method' => 'GET']); ?>
                        <div class="row">
                            <div class="col">
                                <?= form_dropdown('date_year', $date_year_options, $date_year, 'id="date_year" class="form-control custom-select d-block" title="Filter Tahun Cetak"'); ?>
                            </div>
                            <div class="col">
                                <?= form_dropdown('invoice_type', $invoice_type_options, $invoice_type, 'id="invoice_type" class="form-control custom-select d-block" title="Filter Tipe Invoice"'); ?>
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
                                        onclick="location.href = '<?= base_url('earning/'); ?>'"
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
                                    <h5>PENDAPATAN FAKTUR</h5>
                                </b>
                                <b>
                                    <p><?= $year ?></p>
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
                            style="display:none;"
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
                                    <h2>Detail Faktur</h2>
                                </b>
                            </div>
                            <table class="table table-striped mb-0 table-responsive">
                                <thead>
                                    <tr class="text-center">
                                        <th
                                            scope="col"
                                            style="width:5%;"
                                        >No</th>
                                        <th
                                            scope="col"
                                            style="width:25%;"
                                        >Nomor Faktur</th>
                                        <th
                                            scope="col"
                                            style="width:15%;"
                                        >Jenis Faktur</th>
                                        <th
                                            scope="col"
                                            style="width:25%;"
                                        >Tanggal Dikeluarkan</th>
                                        <th
                                            scope="col"
                                            style="width:25%;"
                                        >Status</th>
                                        <th
                                            scope="col"
                                            style="width:25%;"
                                        >Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody
                                    id="table_content"
                                    class="align-middle text-center"
                                >
                                    <button
                                        type="button"
                                        class="btn btn-success mr-3 mb-3"
                                        style="float: right;"
                                        id="print_month"
                                    >Excel</button>
                                    <!-- isi tabel -->
                                </tbody>
                            </table>
                        </div>
                    </div>
            </section>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/vendor/chart.js/new/Chart.bundle.min.js'); ?>"></script>

<script>
<?php if ($filter_invoice_type == false) : ?>
    var total_earning_cash = [];
    var total_earning_showroom = [];
    var total_earning_credit = [];
    var total_earning_online = [];
    <?php for ($i = 1; $i <= 12; $i++) { ?>
        total_earning_cash[<?= $i - 1 ?>] = '<?= $total_earning['cash'][$i] ?>'
        total_earning_showroom[<?= $i - 1 ?>] = '<?= $total_earning['showroom'][$i] ?>'
        total_earning_credit[<?= $i - 1 ?>] = '<?= $total_earning['credit'][$i] ?>'
        total_earning_online[<?= $i - 1 ?>] = '<?= $total_earning['online'][$i] ?>'
    <?php } ?>

    var ctx = document.getElementById("total_year").getContext('2d')
    var total_year = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
            datasets: [{
                label: 'Pendapatan Tunai',
                data: total_earning_cash,
                backgroundColor: 'rgba(254, 250, 8, 0.8)',
                borderWidth: 1
            }, {
                label: 'Pendapatan Showroom',
                data: total_earning_showroom,
                backgroundColor: 'rgba(193, 80, 76, 0.8)',
                borderWidth: 1
            }, {
                label: 'Pendapatan Kredit',
                data: total_earning_credit,
                backgroundColor: 'rgba(156, 188, 89, 0.8)',
                borderWidth: 1
            }, {
                label: 'Pendapatan Online',
                data: total_earning_online,
                backgroundColor: 'rgba(77, 126, 177, 0.8)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value, index, values) {
                            if (parseInt(value) >= 1000) {
                                return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            } else {
                                return 'Rp ' + value;
                            }
                        }
                    }
                }],
                xAxes: [{
                    display: true,
                    ticks: {
                        beginAtZero: true
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
            },
            onClick: function(e) {
                var bar = this.getElementAtEvent(e)[0];
                if (bar == null) {
                    $('#table_laporan').hide()
                } else {
                    var index = bar._index //bulan
                    var datasetIndex = bar._datasetIndex //jenis faktur
                    appendTable('<?= $year ?>', index, datasetIndex)
                }
            }
        }
    });

<?php else : ?>
    var total_earning = [];
    <?php for ($i = 1; $i <= 12; $i++) { ?>
        total_earning[<?= $i - 1 ?>] = '<?= $total_earning[$filter_invoice_type][$i] ?>'
    <?php } ?>
    var ctx = document.getElementById("total_year").getContext('2d')
    var total_year = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
            datasets: [{
                label: 'Pendapatan "<?= $filter_invoice_type ?>"',
                data: total_earning,
                backgroundColor: 'rgba(7, 146, 187, 0.8)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value, index, values) {
                            if (parseInt(value) >= 1000) {
                                return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            } else {
                                return 'Rp ' + value;
                            }
                        }
                    }
                }],
                xAxes: [{
                    display: true,
                    ticks: {
                        beginAtZero: true
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
            },
            onClick: function(e) {
                var bar = this.getElementAtEvent(e)[0];
                if (bar == null) {
                    $('#table_laporan').hide()
                } else {
                    var index = bar._index //bulan
                    var datasetIndex = bar._datasetIndex //jenis faktur
                    appendTable('<?= $year ?>', index, datasetIndex)
                }
            }
        }
    });
<?php endif ?>

function appendTable(year, month, invoice_type) {
    var type = ['cash', 'showroom', 'credit', 'online']
    $.ajax({
        type: "GET",
        url: "<?= base_url('earning/api_get_invoice/'); ?>" + year + '/' + month + '/' + type[invoice_type],
        datatype: "JSON",
        success: function(res) {
            $('#print_month').attr("onclick", "generateExcel(" + year + ',' + month + ",'" + type[invoice_type] + "')")
            // $('#print_month').attr("onclick", "generateExcel(" + year + "," + month + ", '" + type[invoice_type] + "')")
            populateTable(res.data)
            $('#table_laporan').show()
        },
        error: function(err) {
            console.log(err);
        },
    });
}

function generateExcel(year, month, type) {
    window.location.href = "<?= base_url('earning/call_generate_excel/'); ?>" + year + '/' + month + '/' + type
}

function populateTable(data) {
    var htmlContent = ""
    var grandTotal = 0
    for (i = 0; i < data.length; i++) {
        grandTotal += data[i].earning
        var type = get_invoice_type(data[i].type)
        var status = get_invoice_status(data[i].status)
        htmlContent += "<tr class='text-center'><td>" + (i + 1) + "</td><td>" + data[i].number + "</td><td>" + type + "</td><td>" + data[i].issued_date.substring(0, 10) + "</td><td>" + status + "</td><td style='text-align:left'>" + convertToRp(data[i].earning) + " </td></tr>"
    }
    htmlContent += "<tr><td colspan='4' style='text-align:center'><b>Grand Total</b></td><td colspan='2' style='text-align:leftt'><b>" + convertToRp(grandTotal) + "</b></td></tr>"
    $('#table_content').html(htmlContent)
}

function convertToRp(earning) {
    if (earning >= 1000) {
        return 'Rp ' + earning.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    } else {
        return 'Rp ' + earning;
    }
}

function get_invoice_type(type) {
    if (type == 'credit') return "Kredit"
    else if (type == 'showroom') return "Showroom"
    else if (type == 'online') return "Online"
    else if (type == 'cash') return "Tunai"
}

function get_invoice_status(status) {
    if (status == 'cancel') return "Dibatalkan"
    else if (status == 'finish') return "Selesai"
    else return status
}
</script>
