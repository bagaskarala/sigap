<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    h1 {
        font-family: 'Calibri', sans-serif;
        font-size: 14px;
        font-weight: bold;
        color: black;
        margin-bottom: 0;
    }

    table{
        border: 0;
        margin-left: -2px;
    }

    .book-list{
        width: 100%;
        border-collapse: collapse;
        margin-top:16px
    }

    .td-book-list {
        border: 1px solid black;
        text-align: center;
        padding-left: 4px;
        padding-right: 4px;
    }

    th{
        text-align: center;
        border: 1px solid black;
        text-align: center;
    }
    tr {
        font-family: 'Calibri', sans-serif;
        font-weight: lighter;
        font-size: 14px;
        color: black;
    }

    p {
        font-family: 'Calibri', sans-serif;
        font-size: 14px;
        color: black;
        border-spacing: 50px;
    }

    .column {
        float: left;
        height: auto;
    }

    .sign-addressee {
        width: 25%;
    }

    .sign-space {
        width: 50%;
    }

    .sign-stated {
        width: 25%;
    }

    .row:after {
        content: "";
        display: table;
        clear: both;
    }

    .image-wrapper {
        width: 10%;
    }

    .notes-wrapper {
        width: 50%;
    }
    .nomor-bon-wrapper{
        width: 10%
    }
    .nomor-bon {
        width: 20%;
    }

    .image-logo{
        width:40px;height:10px;
        background-image:url('/assets/images/logo_ugm_press.jpg');
        background-repeat:no-repeat;
    }

    .border-bottom{
        border-top: 0px;
        border-left: 0px;
        border-right: 0px;
        border-bottom: 1px solid black;
    }

    .box{
        font-family: 'Calibri', sans-serif;
        font-weight: lighter;
        font-size: 14px;
        color: black;
        border: 1px solid black;
        padding: 10px;
        min-width: 20px;
        height: 20px;
        box-sizing: border-box;
    }
    .content{
        height: 30%
    }
    </style>

</head>

<body>
    <div class="row">
        <div class="column image-wrapper">
            <img src="<?=base_url('assets/images/logo_ugm_press.jpg')?>" alt="logo" style="width:100%; max-width:90px;">
        </div>
        <div class="column">
        <p>
            <b>GADJAH MADA UNIVERSITY PRESS</b>
            <br>Jl. Sendok, Karanggayam CT VIII Caturtunggal Depok, Sleman, D.I. Yogyakarta 55281
            <br>Telp/Fax.: 0274-561037, Email: ugmpress.ugm.ac.id | gmupress@ugm.ac.id </p>
        </div>
    </div>
    <br>
    <h1><b>BON PERMINTAAN BUKU NON JUAL</h1>
    <div class="row">
        <div class="column notes-wrapper">
            <table>
                <tr>
                    <td>NAMA</td>
                    <td>:</td>
                    <td><?=$name?></td>
                </tr>
                <tr>
                    <td>ALAMAT</td>
                    <td>:</td>
                    <td><?=$address?></td>
                </tr>
                <tr>
                    <td>JENIS</td>
                    <td>:</td>
                    <td><?=get_book_non_sales_type()[$type]?></td>
                </tr>
                <?php if($notes) : ?>
                <tr>
                    <td>KETERANGAN</td>
                    <td>:</td>
                    <td><?=$notes?></td>
                </tr>
                <?php endif?>
            </table>
        </div>
        <div class="column nomor-bon-wrapper">
            <p><b>Nomor Bon : </b></p>   
        </div>
        <div class="column nomor-bon">
            <div class="box"><?=$number?></div>
        </div>
    </div>
    <div class="content">
    <table class="book-list">
        <tr>
            <th>NO</th>
            <th>JUDUL BUKU</th>
            <th>JUMLAH</th>
        </tr>
        <?php $i=1?>
        <?php foreach($book_list as $books) : ?>
        <tr>
            <td class="td-book-list"><?=$i++?></td>
            <td class="td-book-list" style="text-align: left;"><?= $books->book_title?></td>
            <td class="td-book-list"><?= $books->qty?></td>
        </tr>
        <?php endforeach?>
    </table>
    </div>
    <br><br>
    <div class="row" style="margin-left:50px; margin-right:50px">
        <div class="column sign-addressee" style="text-align:left">
            <p>Tanggal, <?=date('d-m-Y', strtotime($date))?> <br>
            Yang Menerima<br><br><br><br><br><br><br><?=$receiver?></h2>
        </div>
        <div class="column sign-space" style="text-align:left"></div>
        <div class="column sign-stated" style="text-align:left">
            <br>
            <p>Yang Meminta<br><br><br><br><br><br><br><?=$requester?></h2>
        </div>
    </div>

</body>

</html>