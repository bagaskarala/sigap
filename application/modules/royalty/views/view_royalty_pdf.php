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
            <td style="width:80%">
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
        <h3>Daftar Penerima Royalti</h3>
        <h4><b><?= $author->author_name ?></b></h4>
        <?php if ($period_end == NULL) { ?>
            <h4><?= date("d F Y") ?></h4>
        <?php } else { ?>
            <h4><?= date("d F Y", strtotime($period_end)) ?></h4>
        <?php } ?>
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
                    width="30%"
                    class="align-middle royalty-table"
                >Judul Buku</th>
                <th
                    scope="col"
                    width="10%"
                    class="align-middle royalty-table"
                >Stok Lalu (Eks)</th>
                <th
                    scope="col"
                    width="10%"
                    class="align-middle royalty-table"
                >Harga (Rp)</th>
                <th
                    scope="col"
                    width="10%"
                    class="align-middle royalty-table"
                >Terjual (Eks)</th>
                <th
                    scope="col"
                    width="10%"
                    class="align-middle royalty-table"
                >Royalty %</th>
                <th
                    scope="col"
                    width="15%"
                    class="align-middle royalty-table"
                >Dibayar (Rp)</th>
                <th
                    scope="col"
                    width="10%"
                    class="align-middle royalty-table"
                >Sisa Stok (Eks)</th>
            </tr>
        </thead>
        <tbody class="royalty-table">
            <?php $index = 0;
            $total_earning = 0;
            $total_royalty = 0; ?>
            <?php foreach ($royalty_details as $royalty) :
                $stock = $current_stock[$index]->WP + $current_stock[$index]->LP + $current_stock[$index]->SP ?>
                <tr class="royalty-table">
                    <td
                        class="royalty-table"
                        style="text-align: center; height: 30px; padding-left:5px;"
                        width="5%"
                    ><?= $index + 1; ?></td>
                    <td
                        class="royalty-table"
                        width="30%"
                        style="padding-left:5px;"
                    ><?= $royalty->book_title ?></td>
                    <td
                        class="royalty-table"
                        style="text-align: center;"
                        width="10%"
                    ><?= $stock + $royalty->count ?></td>
                    <td
                        class="royalty-table"
                        style="text-align: center;"
                        width="10%"
                    ><?= $royalty->harga ?></td>
                    <td
                        class="royalty-table"
                        style="text-align: center;"
                        width="10%"
                    ><?= $royalty->count ?></td>
                    <td
                        class="royalty-table"
                        style="text-align: center;"
                        width="10%"
                    >15 %</td>
                    <td
                        class="royalty-table"
                        width="15%"
                        style="padding-left:5px;"
                    >Rp <?= number_format($royalty->earned_royalty, 0, ',', '.'); ?></td>
                    <td
                        class="royalty-table"
                        style="text-align: center;"
                        width="10%"
                    ><?= $stock ?></td>
                </tr>
                <?php $index++;
                $total_royalty += $royalty->earned_royalty; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <table style="width: 100%;">
        <tr>
            <td style="width:55%; height: 33px;"></td>
            <td style="width:20%">Jumlah</td>
            <td style="width:15%">Rp <?= number_format($total_royalty, 0, ',', '.'); ?></td>
            <td></td>
        </tr>
        <tr>
            <td style="height: 33px;"></td>
            <td>PPh 15%</td>
            <td style="border-bottom: 1px solid black;">Rp <?= number_format((0.15 *  $total_royalty), 0, ',', '.'); ?></td>
            <td></td>
        </tr>
        <tr>
            <td style="height: 33px;"></td>
            <td><b>Netto</b></td>
            <td style="border-bottom: 4px double black;"><b>Rp <?= number_format((0.85 *  $total_royalty), 0, ',', '.'); ?></b></td>
            <td></td>
        </tr>
    </table>
</body>

</html>
