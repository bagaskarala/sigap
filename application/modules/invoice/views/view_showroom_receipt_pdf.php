<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>Faktur Showroom UGM Press</title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >
    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >
    <link
        href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@400;700&display=swap"
        rel="stylesheet"
    >

    <style>
    @page {
        size: 3in 11.7in;
        margin: 0;
        max-width: 3in;
    }

    body {
        font-size: 11px;
        font-family: 'Inconsolata', monospace;
        color: black;
        padding-left: 19px;
        padding-right: 19px;
    }

    table,
    th,
    td {
        padding: 0;
        margin: 0;
    }

    table {
        border-collapse: collapse;
    }

    </style>
</head>

<body>
    <?php
    $month = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    $date_string = date("d", strtotime($invoice->issued_date)) . " " . $month[intval(date("m", strtotime($invoice->issued_date)) - 1)] . " " . date("Y", strtotime($invoice->issued_date));
    $time_string = date('H:i:s', strtotime($invoice->issued_date));

    ?>
    <table style="width: 100%;">
        <tr>
            <td style="text-align:center">
                GADJAH MADA UNIVERSITY PRESS<br>
                Jl. Sendok, Karanggayam CT VIII<br>
                Caturtunggal Depok, Sleman <br>
                D.I. Yogyakarta 55281<br>
                Telp/Fax (0274)-561037
            </td>
        </tr>
    </table>

    <hr style="border-top: 1px dotted black; border-bottom:none;" />

    <table style="width:100%">
        <tr>
            <td>
                <?= $date_string ?>, <?= $time_string ?>
            </td>
        </tr>
        <tr>
            <td>No: <?= $invoice->number ?></td>
        </tr>
    </table>
    <hr style="border-top: 1px dotted black; border-bottom:none;" />
    <table style="width:100%">
        <tbody>
            <?php foreach ($invoice_books as $invoice_book) : ?>
                <tr>
                    <td
                        colspan="4"
                        style="padding-top: 5px;"
                    ><?= $invoice_book->book_title ?></td>
                </tr>
                <tr>
                    <td style="width:8%">x<?= $invoice_book->qty ?></td>
                    <td style="width:20%; text-align:right;">@<?= number_format($invoice_book->price, 0, ',', '.'); ?></td>
                    <td
                        style="width:25%; text-align:right;"
                        colspan="3"
                    ><?= number_format($invoice_book->price * $invoice_book->qty, 0, ',', '.'); ?></td>
                </tr>
                <?php if ($invoice_book->discount) : ?>
                    <tr>
                        <td
                            style="text-align:right;"
                            colspan="2"
                        >Disc <?= $invoice_book->discount ?> %</td>
                        <td
                            style="text-align:right;"
                            colspan="3"
                        >(<?= number_format($invoice_book->price * $invoice_book->qty * ($invoice_book->discount / 100), 0, ',', '.'); ?>)</td>
                    </tr>
                <?php endif ?>
            <?php endforeach ?>

            <?php
            $total = 0;
            foreach ($invoice_books as $invoice_book) {
                $total += $invoice_book->price * $invoice_book->qty * (1 - $invoice_book->discount / 100);
            }
            ?>
        </tbody>
    </table>

    <hr style="border-top: 1px dotted black; border-bottom:none;" />

    <table style="width: 100%;">
        <tr style="font-weight: bold; font-size:larger">
            <td style="width:30%; text-transform: uppercase;">Total</td>
            <td style="width:25%; text-align:right;"><?= number_format($total, 0, ',', '.'); ?></td>
        </tr>
    </table>

    <hr style="border-top: 1px dotted black; border-bottom:none;" />

    <table style="width: 100%; margin-top:10px;margin-bottom:10px;">
        <tr>
            <td style="text-align: center;">TERIMA KASIH</td>
        </tr>
    </table>

    <hr style="border-top: 1px dotted black; border-bottom:none;" />

</body>

</html>
