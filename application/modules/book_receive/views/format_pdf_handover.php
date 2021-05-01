<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    h1 {
        font-family: 'Calibri', sans-serif;
        font-size: 20px;
        font-weight: bold;
        color: black;
    }

    table {
        margin-left: 50px;
        margin-right: 50px;
        width: 100%;
        border: 1px solid black;
        border-collapse: collapse;
        margin-top: 50px
    }

    tr {
        font-family: 'Calibri', sans-serif;
        font-weight: lighter;
        font-size: 18px;
        color: black;
    }

    th {
        border: 1px solid black;
    }

    td {
        border: 1px solid black;
    }

    h2 {
        font-family: 'Calibri', sans-serif;
        font-size: 16px;
        color: black;
        margin-left: 50px; margin-right: 50px;
        border-spacing: 50px;
    }

    .column {
        float: left;
        height: auto;
    }

    .left {
        width: 35%;
    }

    .middle {
        width: 30%;
    }

    .right {
        width: 35%;
    }

    .row:after {
        content: "";
        display: table;
        clear: both;
    }
    </style>

</head>

<body>
    <h1 style="text-align: center;"><b>BERITA ACARA SERAH TERIMA<br>(PRODUKSI &ndash; GUDANG)</b></h1>
    <table>
        <tr>
            <th>No</th>
            <th>Judul</th>
            <th>Nomor Order</th>
            <th>Jumlah Order</th>
            <th>Jumlah Tercetak</th>
        </tr>
        <tr>
            <td>1</td>
            <td><?= $title ?></td>
            <td><?= $ordernumber ?></td>
            <td><?= $total ?></td>
            <td><?= $total_postprint ?></td>
        </tr>
    </table>
    <br>
    <div class="row">
        <div class="column left">
        <h2>TANGGAL: <span><?= $handover_end_date ?></span></h2>
        </div>
        <div class="column middle"></div>
        <div class="column right">
        <h2>KETERANGAN: <span><?= $notes ?></span></h2>
        </div>
    </div>
    <br><br><br>
    <div class="row">
        <div class="column left" style="text-align:center">
            <h2>PEMBERI<br><br><br><br><br><br><br>..........................</h2>
        </div>
        <div class="column middle" style="text-align:center"></div>
        <div class="column right" style="text-align:center">
            <h2>PENERIMA<br><br><br><br><br><br><br>..........................</h2>
        </div>
    </div>
</body>

</html>