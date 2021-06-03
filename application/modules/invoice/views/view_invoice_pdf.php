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

    .invoice-box {
        margin: auto;
        padding: 30px;
        /** box-shadow: 0 0 10px rgba(0, 0, 0, .15); **/
    }

    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }

    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
        font-size: 12px;
    }

    /**   .invoice-box table tr td:nth-child(2) {
        text-align: left;
        font-size: 12px;
    } **/

    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }

    .invoice-box table tr.information table td {
        padding-bottom: 5px;
    }

    .invoice-box table tr.heading td {
        font-weight: bold;
    }

    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.item td {
        border-bottom: 1px solid #eee;
    }

    .invoice-box table tr.item.last td {
        border-bottom: none;
    }

    .invoice-box table tr.main table thead {
        vertical-align: middle;
        text-align: center;
    }


    .invoice-box table tr.main table {
        border: 1px solid black;
    }

    .invoice-box table tr.total td:nth-child(7) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }

    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }

        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }

    table.invoice-table,
    thead.invoice-table,
    td.invoice-table,
    tr.invoice-table,
    .invoice-table {
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
                <?= $customer->name ?><br>
                <?= $customer->address ?><br>
                <?= $customer->phone_number ?><br>
                <?= $customer->email ?><br><br>
                No Faktur : <b><?= $invoice->number ?></b><br>
                Bukti Bayar : <?= $invoice->receipt ?>
            </td>
            <td style="width:30%; vertical-align: top;">
                <?php $month = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"] ?>
                Yogyakarta, <?= date("d", strtotime($invoice->issued_date)) . " " . $month[intval(date("m", strtotime($invoice->issued_date)))] . " " . date("Y", strtotime($invoice->issued_date)) ?><br>
                Jatuh Tempo : <?= date("d", strtotime($invoice->due_date)) . " " . $month[intval(date("m", strtotime($invoice->due_date)))] . " " . date("Y", strtotime($invoice->due_date)) ?>
            </td>
        </tr>   
    </table>

    <table
        class="invoice-table"
        style="width: 100%; margin-top: 10px;"
    >
        <thead
            class="invoice-table"
            style="text-align: center;"
        >
            <tr class="invoice-table">
                <th
                    scope="col"
                    width="5%"
                    class="align-middle invoice-table"
                    rowspan="2"
                >No</th>
                <th
                    scope="col"
                    width="5%"
                    class="align-middle invoice-table"
                    rowspan="2"
                >Kode</th>
                <th
                    scope="col"
                    width="54%"
                    class="align-middle invoice-table"
                    rowspan="2"
                >Judul</th>
                <th
                    scope="col"
                    width="5%"
                    class="align-middle invoice-table"
                    rowspan="2"
                >Jml (eks)</th>
                <th
                    scope="col"
                    width="5%"
                    class="align-middle invoice-table"
                    rowspan="2"
                >Disc (%)</th>
                <th
                    scope="col"
                    width="26%"
                    class="align-middle invoice-table"
                    colspan="2"
                >Harga (Rp)</th>
            </tr>

            <tr>
                <th class="align-middle invoice-table">Satuan</th>
                <th class="align-middle invoice-table">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($invoice_books as $invoice_book) : ?>
                <tr class="invoice-table">
                    <td
                        class="invoice-table"
                        style="height: 33px; padding-left:5px;"
                    ><?= $i++ ?></td>
                    <td class="invoice-table"></td>
                    <td class="invoice-table" style="padding-left:5px;"><?= $invoice_book->book_title ?></td>
                    <td
                        class="invoice-table"
                        style="text-align: right; padding-right:5px;"
                    ><?= $invoice_book->qty ?></td>
                    <td
                        class="invoice-table"
                        style="text-align: right; padding-right:5px;"
                    ><?= $invoice_book->discount ?></td>
                    <td
                        class="invoice-table"
                        style="text-align: right; padding-right:5px;"
                    ><?= number_format($invoice_book->price, 0, ',', '.'); ?></td>
                    <td
                        class="invoice-table"
                        style="text-align: right; padding-right:5px;"
                    ><?= number_format($invoice_book->price * $invoice_book->qty * (1 - $invoice_book->discount / 100), 0, ',', '.'); ?></td>
                </tr>
            <?php endforeach ?>

            <?php
            $total_temp = 0;
            foreach ($invoice_books as $invoice_book) {
                $total_temp += $invoice_book->price * $invoice_book->qty * (1 - $invoice_book->discount / 100);
            }
            $total = $total_temp + $invoice->delivery_fee;
            ?>
        </tbody>
    </table>

    <table style="width: 100%;">
        <tr>
            <td
                style="width:50%"
                style="height: 33px;"
            ></td>
            <td style="width:10%"></td>
            <td style="width:10%"></td>
            <td style="width:10%; text-align: right;"><?= $total_temp ?></td>
        </tr>
        <tr>
            <td
                scope="col"
                style="height: 33px;"
            ></td>
            <td scope="col">Ongkos Kirim</td>
            <td
                scope="col"
                style="text-align: right;"
            ></td>
            <td
                scope="col"
                style="text-align: right; border-bottom: 1px solid black"
            ><?= number_format($invoice->delivery_fee, 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td
                scope="col"
                style="height: 33px;"
            ></td>
            <td scope="col"><b>Jumlah</b></td>
            <td
                scope="col"
                style="text-align: right;"
            ></td>
            <td
                scope="col"
                style="text-align: right; border-bottom: 4px double black"
            ><b><?= number_format($total, 0, ',', '.'); ?></b></td>
        </tr>
        <tr>
            <td
                scope="col"
                colspan="4"
                style="text-align: right; height: 33px;"
            ><b><?= ucfirst(view_price_to_text($total)) ?> rupiah</b></td>
        </tr>
        <?php if ($invoice->type == 'cash') : ?>
            <tr>
                <td
                    scope="col"
                    style="height: 33px;"
                ></td>
                <td scope="col">Bayar</td>
                <td
                    scope="col"
                    style="text-align: right;"
                ></td>
                <td
                    scope="col"
                    style="text-align: right;"
                ><?= number_format($total, 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td
                    scope="col"
                    style="height: 33px;"
                ></td>
                <td scope="col">Kurang</td>
                <td
                    scope="col"
                    style="text-align: right;"
                ></td>
                <td
                    scope="col"
                    style="text-align: right;"
                >0</td>
            </tr>
        <?php endif; ?>
    </table>

    <table style="width: 100%;">
        <tr>
            <td style="width:50%"></td>
            <td style="width:20%"></td>
        </tr>
        <tr class="information">
            <td>
                <?php if ($invoice->type != 'cash') : ?>
                    <b>Keterangan :</b><br>
                    Bukti pembayaran mohon dikirimkan melalui email: ugmpress@ugm.ac.id<br>
                    atau whatsapp: 081228478888<br>
                    Pembayaran dapat dilakukan melalui :<br>
                    1. Mandiri Cab. UGM Yogyakarta 137-00-0455085-7<br>
                    <a style="padding-left: 14px;">a/n Gadjah Mada University Press</a><br>
                    2. BNI Cab. UGM Yogyakarta : 0039226571<br>
                    <a style="padding-left: 14px;">a/n GAMA PRESS</a>
                <?php endif ?>
            </td>
            <td style="text-align: center;">
                a.n. Ka. Pemasaran<br><br><br><br><br><br><br>
                <i>Adm. Pemasaran</i>
            </td>
        </tr>
    </table>
</body>

</html>
