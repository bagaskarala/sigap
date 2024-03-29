<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>Royalty Penulis</title>

    <style>
    body {
        font-size: 12px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #333;
    }

    table.royalty-table,
    thead.royalty-table,
    td.royalty-table,
    tr.royalty-table,
    .royalty-table {
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
            <td style="width:80%; padding-left: -5px;">
                <b>GADJAH MADA UNIVERSITY PRESS</b><br>
                Jl. Sendok, Karanggayam CT VIII<br>
                Caturtunggal Depok, Sleman, D.I. Yogyakarta 55281<br>
                Telp/Fax (0274)-561037<br>
                NPWP:01.246.578.7-542.000 - E-mail : ugmpress.ugm.ac.id | ugmpress@ugm.ac.id
            </td>
        </tr>
    </table>
    <br>
    <div style="text-align: center;">
        <h2>Daftar Penerima Royalti</h2>
        <h3><b><?= $author->author_name ?></b></h3>
        <?php if ($period_end == NULL) : ?>
            <h4><?= date("d F Y") ?></h4>
        <?php else : ?>
            <h4><?= date("d F Y", strtotime($start_date)) ?> - <?= date("d F Y", strtotime($period_end)) ?></h4>
        <?php endif ?>
    </div>
    <br>
    <table
        class="royalty-table"
        style="width: 100%;"
    >
        <thead
            class="royalty-table"
            style="text-align: center;"
        >
            <tr class="royalty-table">
                <th
                    scope="col"
                    width="5%"
                    class="align-middle royalty-table"
                >No</th>
                <th
                    scope="col"
                    width="25%"
                    class="align-middle royalty-table"
                >Judul Buku</th>
                <?php if ($pdf_type == 'author') : ?>
                    <th
                        scope="col"
                        width="10%"
                        class="align-middle royalty-table"
                    >Stok Lalu (Eks)</th>
                <?php endif ?>
                <th
                    scope="col"
                    width="10%"
                    class="align-middle royalty-table"
                >Harga</th>
                <th
                    scope="col"
                    width="10%"
                    class="align-middle royalty-table"
                >Terjual (Eks)</th>
                <?php if ($pdf_type == 'author') : ?>
                    <th
                        scope="col"
                        width="10%"
                        class="align-middle royalty-table"
                    >Non Penjualan (Eks)</th>
                <?php endif ?>
                <th
                    scope="col"
                    width="10%"
                    class="align-middle royalty-table"
                >Royalty</th>
                <th
                    scope="col"
                    width="10%"
                    class="align-middle royalty-table"
                >Dibayar</th>
                <?php if ($pdf_type == 'author') : ?>
                    <th
                        scope="col"
                        width="10%"
                        class="align-middle royalty-table"
                    >Sisa Stok (Eks)</th>
                <?php endif ?>
            </tr>
        </thead>
        <tbody class="royalty-table">
            <?php $index = 0;
            $total_earning = 0;
            $total_royalty = 0; ?>
            <?php foreach ($royalty_details as $royalty) :
                $prev_stock = $book_details[$index]->warehouse_start + $book_details[$index]->library_start + $book_details[$index]->showroom_start;
            ?>
                <tr class="royalty-table">
                    <td
                        class="royalty-table"
                        style="text-align: center; height: 30px;"
                        width="5%"
                    ><?= $index + 1; ?></td>
                    <td
                        class="royalty-table"
                        width="25%"
                        style="padding-left:5px;"
                    ><?= $royalty->book_title ?></td>
                    <?php if ($pdf_type == 'author') : ?>
                        <td
                            class="royalty-table"
                            style="text-align: center;"
                            width="10%"
                        ><?= $prev_stock ?></td>
                    <?php endif ?>
                    <td
                        class="royalty-table"
                        style="text-align: left; padding-left:5px;"
                        width="10%"
                    >Rp <?= number_format($royalty->price, 0, ',', '.'); ?></td>
                    <td
                        class="royalty-table"
                        style="text-align: center;"
                        width="10%"
                    ><?= $royalty->sold_books ?></td>
                    <?php if ($pdf_type == 'author') : ?>
                        <td
                            class="royalty-table"
                            style="text-align: center;"
                            width="10%"
                        ><?= isset($book_details[$index]->non_sales_last) ? $book_details[$index]->non_sales_last : 0 ?></td>
                    <?php endif ?>
                    <td
                        class="royalty-table"
                        style="text-align: center;"
                        width="10%"
                    ><?= $royalty->royalty ?> %</td>
                    <td
                        class="royalty-table"
                        width="10%"
                        style="padding-left:5px;"
                    >Rp <?= number_format($royalty->earned_royalty, 0, ',', '.'); ?></td>
                    <?php if ($pdf_type == 'author') : ?>
                        <td
                            class="royalty-table"
                            style="text-align: center;"
                            width="10%"
                        ><?= $prev_stock - $royalty->sold_books - $book_details[$index]->non_sales_last ?></td>
                    <?php endif ?>
                </tr>
                <?php $index++;
                $total_royalty += $royalty->earned_royalty; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <table style="width: 100%;">
        <tr>
            <td style="width:55%; height: 33px;"></td>
            <td style="width:20%"><b>Jumlah</b></td>
            <td style="width:15%;border-bottom: 4px double black;">Rp <?= number_format($total_royalty, 0, ',', '.'); ?></td>
            <td></td>
        </tr>
    </table>
</body>

</html>
