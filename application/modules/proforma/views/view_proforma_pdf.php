<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>Faktur UGM Press</title>

    <style>
    body {
        font-size: 12px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #333;
    }

    .proforma-box {
        margin: auto;
        padding: 30px;
        /** box-shadow: 0 0 10px rgba(0, 0, 0, .15); **/
    }

    .proforma-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }

    .proforma-box table td {
        padding: 5px;
        vertical-align: top;
        font-size: 12px;
    }

    /**   .proforma-box table tr td:nth-child(2) {
        text-align: left;
        font-size: 12px;
    } **/

    .proforma-box table tr.top table td {
        padding-bottom: 20px;
    }

    .proforma-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }

    .proforma-box table tr.information table td {
        padding-bottom: 5px;
    }

    .proforma-box table tr.heading td {
        font-weight: bold;
    }

    .proforma-box table tr.details td {
        padding-bottom: 20px;
    }

    .proforma-box table tr.item td {
        border-bottom: 1px solid #eee;
    }

    .proforma-box table tr.item.last td {
        border-bottom: none;
    }

    .proforma-box table tr.main table thead {
        vertical-align: middle;
        text-align: center;
    }


    .proforma-box table tr.main table {
        border: 1px solid black;
    }

    .proforma-box table tr.total td:nth-child(7) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }

    @media only screen and (max-width: 600px) {
        .proforma-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }

        .proforma-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }

    table.proforma-table,
    thead.proforma-table,
    td.proforma-table,
    tr.proforma-table,
    .proforma-table {
        border: 1px solid black;
        border-collapse: collapse;
    }

    </style>
</head>

<body>
    <table style="width: 100%;">
        <tr>
            <td style="width:20%;">
                <img
                    src="<?= base_url('assets/images/logo_ugm_press.jpg'); ?>"
                    style="width:100%; max-width:120px;"
                >
            </td>
            <td style="width:80%">
                <b>GADJAH MADA UNIVERSITY PRESS</b><br>
                Jl. Sendok, Karanggayam CT VIII<br>
                Caturtunggal Depok, Sleman, D.I. Yogyakarta 55281<br>
                Telp/Fax (0274)-561037<br>
                NPWP:01.246.578.7-542.000 - E-mail : ugmpress.ugm.ac.id | ugmpress@ugm.ac.id
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-top: 4em;">
        <tr class="information">
            <td style="width:70%;">
                Kepada Yth.<br>
                <?= $proforma->customer->name ?><br>
                <?= $proforma->customer->address ?><br>
                <?= $proforma->customer->phone_number ?><br><br>
                No Faktur : <?= $proforma->number ?>
            </td>
            <td style="width:30%; vertical-align: top;">
                <?php $month = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"] ?>
                Yogyakarta, <?= date("d", strtotime($proforma->issued_date)) . " " . $month[intval(date("m", strtotime($proforma->issued_date)))] . " " . date("Y", strtotime($proforma->issued_date)) ?><br>
                Jatuh Tempo : <?= date("d", strtotime($proforma->due_date)) . " " . $month[intval(date("m", strtotime($proforma->due_date)))] . " " . date("Y", strtotime($proforma->due_date)) ?>
            </td>
        </tr>
    </table>

    <table
        class="proforma-table"
        style="width: 100%;"
    >
        <thead
            class="proforma-table"
            style="text-align: center;"
        >
            <tr class="proforma-table">
                <th
                    scope="col"
                    width="5%"
                    class="align-middle proforma-table"
                    rowspan="2"
                >No</th>
                <th
                    scope="col"
                    width="5%"
                    class="align-middle proforma-table"
                    rowspan="2"
                >Kode</th>
                <th
                    scope="col"
                    width="54%"
                    class="align-middle proforma-table"
                    rowspan="2"
                >Judul</th>
                <th
                    scope="col"
                    width="5%"
                    class="align-middle proforma-table"
                    rowspan="2"
                >Jml (eks)</th>
                <th
                    scope="col"
                    width="5%"
                    class="align-middle proforma-table"
                    rowspan="2"
                >Disc (%)</th>
                <th
                    scope="col"
                    width="26%"
                    class="align-middle proforma-table"
                    colspan="2"
                >Harga</th>
            </tr>

            <tr>
                <th class="align-middle proforma-table">Satuan</th>
                <th class="align-middle proforma-table">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0; ?>
            <?php if(is_array($proforma_books) || is_object($proforma_books)): ?>
            <?php foreach ($proforma_books as $proforma_book) : ?>
                <?php $i++; ?>
                <tr class="proforma-table">
                    <td class="proforma-table" style="height: 33px;"><?= $i ?></td>
                    <td class="proforma-table"></td>
                    <td class="proforma-table"><?= $proforma_book->book_title ?></td>
                    <td
                        class="proforma-table"
                        style="text-align: right;"
                    ><?= $proforma_book->qty ?></td>
                    <td
                        class="proforma-table"
                        style="text-align: right;"
                    ><?= $proforma_book->discount ?></td>
                    <td
                        class="proforma-table"
                        style="text-align: right;"
                    ><?= $proforma_book->price ?></td>
                    <td
                        class="proforma-table"
                        style="text-align: right;"
                    ><?= $proforma_book->price * $proforma_book->qty * (1 - $proforma_book->discount / 100) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>

            <?php if(is_array($proforma_books) || is_object($proforma_books)): ?>
            <?php
            $total = 0;
            foreach ($proforma_books as $proforma_book) {
                $total += $proforma_book->price * $proforma_book->qty * (1 - $proforma_book->discount / 100);
            }
            ?>
            <?php endif; ?>
        </tbody>
    </table>

    <table style="width: 100%;">
        <tr>
            <td scope="col" style="height: 33px;"></td>
            <td scope="col"><b>Jumlah</b></td>
            <td
                scope="col"
                style="text-align: right;"
            ></td>
            <td
                scope="col"
                style="text-align: right; border-bottom: 4px double black"
            ><b><?= $total ?></b></td>
        </tr>
        <tr>
            <td
                scope="col"
                colspan="4"
                style="text-align: right; height: 33px;"
            ><b><?= ucfirst(view_price_to_text($total)) ?> rupiah</b></td>
        </tr>
            <tr>
                <td scope="col" style="height: 33px;"></td>
                <td scope="col">Bayar</td>
                <td
                    scope="col"
                    style="text-align: right;"
                ></td>
                <td
                    scope="col"
                    style="text-align: right;"
                >0</td>
            </tr>
            <tr>
                <td scope="col" style="height: 33px;"></td>
                <td scope="col">Kurang</td>
                <td
                    scope="col"
                    style="text-align: right;"
                ></td>
                <td
                    scope="col"
                    style="text-align: right;"
                ><?= $total ?></td>
            </tr>
    </table>

    <table style="width: 100%;">
        <tr>
            <td style="width:50%"></td>
            <td style="width:20%"></td>
        </tr>
        <tr class="information">
            <td>
                    <b>Keterangan :</b><br>
                    Bukti pembayaran mohon dikirimkan melalui email: ugmpress@ugm.ac.id<br>
                    atau whatsapp: 081228478888<br>
                    Pembayaran dapat dilakukan melalui :<br>
                    1. Mandiri Cab. UGM Yogyakarta 137-00-0455085-7<br>
                    <a style="padding-left: 14px;">a/n Gadjah Mada University Press</a><br>
                    2. BNI Cab. UGM Yogyakarta : 0039226571<br>
                    <a style="padding-left: 14px;">a/n GAMA PRESS</a>
            </td>
            <td style="text-align: center;">
                a.n. Ka. Pemasaran<br><br><br><br><br><br><br>
                <i>Adm. Pemasaran</i>
            </td>
        </tr>
    </table>
</body>

</html>
