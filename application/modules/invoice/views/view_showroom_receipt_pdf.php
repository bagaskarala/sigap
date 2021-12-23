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
        margin: 0;
        padding: 0;
        font-size: 10px;
        line-height: 14px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #333;
    }

    #printContainer {
        height: 200mm;
        width: 60mm;
        page-break-after: always;
    }

    </style>
</head>

<body>
    <div id="printContainer">
        <table style="width: 100%;">
            <tr>
                <td style="text-align:center;">
                    GADJAH MADA UNIVERSITY PRESS<br>
                    Jl. Sendok, Karanggayam CT VIII<br>
                    Caturtunggal Depok, Sleman, D.I. Yogyakarta 55281<br>
                    Telp/Fax (0274)-561037
                    <hr style="border-style: dotted;" />
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="vertical-align: top;">
                    <?php $month = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"] ?>
                    Yogyakarta, <?= date("d", strtotime($invoice->issued_date)) . " " . $month[intval(date("m", strtotime($invoice->issued_date)) - 1)] . " " . date("Y", strtotime($invoice->issued_date)) ?>
                </td>

            </tr>
            <tr>
                <td>
                    <?= date('H:i:s', strtotime($invoice->issued_date)) ?><br>
                </td>
            </tr>
        </table>
        <hr style="border-style: dotted;" />
        <table style="width:100%">
            <tbody>
                <tr style="text-align:center;">
                    <td style="width:5%; border-bottom: 1px dotted black; padding-bottom: 6px;">Qty</td>
                    <td style="width:40%; border-bottom: 1px dotted black; padding-bottom: 6px;">Judul Buku</td>
                    <td style="width:20%; border-bottom: 1px dotted black; padding-bottom: 6px;">Harga</td>
                    <td style="width:10%; border-bottom: 1px dotted black; padding-bottom: 6px;">Disc</td>
                    <td style="width:25%; border-bottom: 1px dotted black; padding-bottom: 6px;">Total</td>
                </tr>
                <?php foreach ($invoice_books as $invoice_book) : ?>
                    <tr>
                        <td style="width:5%"><?= $invoice_book->qty ?></td>
                        <td style="width:40%"><?= $invoice_book->book_title ?></td>
                        <td style="width:20%; text-align:right;"><?= number_format($invoice_book->price, 0, ',', '.'); ?></td>
                        <td style="width:10%; text-align:right; white-space:nowrap"><?= $invoice_book->discount ?> %</td>
                        <td style="width:25%; text-align:right;"><?= number_format($invoice_book->price * $invoice_book->qty * (1 - $invoice_book->discount / 100), 0, ',', '.'); ?></td>
                    </tr>
                <?php endforeach ?>
                <?php
                $total = 0;
                foreach ($invoice_books as $invoice_book) {
                    $total += $invoice_book->price * $invoice_book->qty * (1 - $invoice_book->discount / 100);
                }
                ?>
            </tbody>
        </table>
        <table style="width: 100%;">
            <tr>
                <td style="width:45%;"></td>
                <td style="width:30%; border-top: 1px dotted black">Jumlah</td>
                <td style="width:25%; border-top: 1px dotted black; text-align:right;"><?= number_format($total, 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td style="width:45%;"></td>
                <td style="width:30%;">Bayar</td>
                <td style="width:25%; text-align:right;"><?= number_format($total, 0, ',', '.'); ?></td>
            </tr>
            <tr style="border-bottom: 1px dotted black;">
                <td style="width:45%;"></td>
                <td style="width:30%;">Kurang</td>
                <td style="width:25%; text-align:right;">0</td>
            </tr>
        </table>
        <hr style="border-style: dotted;" />
        <table style="width: 100%;">
            <tr>
                <td style="text-align: center;">TERIMA KASIH</td>
            </tr>
        </table>
    </div>
</body>

</html>
