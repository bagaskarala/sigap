<!doctype html>
<html><head>
    <meta charset="utf-8">
    <title>Faktur UGM Press</title>
    
    <style>
    .invoice-box {
        max-width: 800px;
        margin: auto;
        height:297mm;
        width:210mm;
        padding: 30px;
       /** box-shadow: 0 0 10px rgba(0, 0, 0, .15); **/
        font-size: 12px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #333;
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
    
    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    }
    
    .invoice-box table tr.item.last td {
        border-bottom: none;
    }
        
    .invoice-box table tr.main table thead{
        vertical-align: middle;
        text-align: center;
    }
       
     
    .invoice-box table tr.main table{
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
        
    table.invoice-table, thead.invoice-table, td.invoice-table, tr.invoice-table, .invoice-table{
        border: 1px solid black;
        border-collapse: collapse;
    }
        
    /** RTL **/
/**    .rtl {
        direction: rtl;
        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    }
    
    .rtl table {
        text-align: right;
    }
    
    .rtl table tr td:nth-child(2) {
        text-align: left;
    }
**/
    </style>
</head><body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0"> 
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="https://lh3.googleusercontent.com/p/AF1QipMRVQsDOf2j7ms65hEKQu-N5Zu5bE3XBioRhvb-=s1600-w400" style="width:100%; max-width:120px;">
                            </td>
                            
                            <td style="padding-right: 230px;">
                                GADJAH MADA UNIVERSITY PRESS<br>
                                Jl. Grafika No.1, Kampus UGM<br>
                                P.O. Box 14 Bulaksumur, Yogyakarta 55281<br>
                                Telp/Fax (0274)-561037<br>
                                NPWP:01.246.578.7-542.000 - E-mail : ugmpress@ugm.ac.id
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                Kepada Yth.<br>
                                Andrew Mulya<br><br>
                                No Faktur : P210321/000300
                            </td>
                            
                            <td style="padding-left: 400px;">
                                Yogyakarta, 1 Maret 2021<br>
                                Jatuh Tempo : 8 Maret 2021
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="main">
                <table class="invoice-table">
                    <thead class="invoice-table" style="text-align: center;">
                        <tr class="invoice-table">
                            <th scope="col" width="5%" class="align-middle invoice-table" rowspan="2">No</th>
                            <th scope="col" width="5%" class="align-middle invoice-table" rowspan="2">Kode</th>
                            <th scope="col" width="54%" class="align-middle invoice-table" rowspan="2">Judul</th>
                            <th scope="col" width="5%" class="align-middle invoice-table" rowspan="2">Jml (eks)</th>
                            <th scope="col" width="5%" class="align-middle invoice-table" rowspan="2">Disc (%)</th>
                            <th scope="col" width="26%" class="align-middle invoice-table" colspan="2">Harga</th>
                        </tr>
                        
                        <tr>
                            <th class="align-middle invoice-table">Satuan</th>
                            <th class="align-middle invoice-table">Jumlah</th>
                        </tr>
                    </thead>
                    <tr class="invoice-table">
                        <td class="invoice-table">100</td>
                        <td class="invoice-table">A152</td>
                        <td class="invoice-table">Penelitian Pengaruh Genshin Impact terhadap Psikologi Manusia</td>
                        <td class="invoice-table">10</td>
                        <td class="invoice-table" style="text-align: right;">70</td>
                        <td class="invoice-table" style="text-align: right;">150.000</td>
                        <td class="invoice-table" style="text-align: right;">1.500.000</td>
                    </tr>
                </table>
            </tr>
            <tr>
                <table>
                    <tr>
                        <td width="64%"></td>
                        <td width="10%"></td>
                        <td width="12%" style="text-align: right;"></td>
                        <td width="15%" style="text-align: right;">12.000.000</td>
                    </tr>
                    <tr>
                        <td width="64%"></td>
                        <td width="10%">Diskon</td>
                        <td width="12%" style="text-align: right;">10 %</td>
                        <td width="15%" style="text-align: right; border-bottom: 1px solid black">1.200.000</td>
                    </tr>
                    <tr>
                        <td width="64%"></td>
                        <td width="10%"></td>
                        <td width="12%"></td>
                        <td width="15%" style="text-align: right;">12.000.000</td>
                    </tr>
                    <tr>
                        <td width="64%"></td>
                        <td width="22%" colspan="2">Ongkos Kirim</td>
                        <td width="15%" style="text-align: right; border-bottom: 1px solid black">12.000.000</td>
                    </tr>
                    <tr>
                        <td width="64%"></td>
                        <td width="22%" colspan="2"><b>Jumlah</b></td>
                        <td width="15%" style="text-align: right; border-bottom: 4px double black"><b>12.000.000</b></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: right;"><i><b>Dua belas juta rupiah</b></i></td>
                    </tr>
                    <tr>
                        <td width="64%"></td>
                        <td width="22%" colspan="2">Bayar</td>
                        <td width="15%" style="text-align: right;">12.000.000</td>
                    </tr>
                    <tr>
                        <td width="64%"></td>
                        <td width="22%" colspan="2">Kurang</td>
                        <td width="15%" style="text-align: right;">0</td>
                    </tr>
                </table>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td width="64%">
                                <b>Keterangan :</b><br>
                                Bukti pembayaran mohon dikirimkan melalui email: ugmpress@ugm.ac.id<br>
                                atau whatsapp: 081228478888<br>
                                Pembayaran dapat dilakukan melalui :<br>
                                1. Mandiri Cab. UGM Yogyakarta 137-00-0455085-7<br>
                                <a style="padding-left: 14px;">a/n Gadjah Mada University Press</a><br>
                                2. BNI Cab. UGM Yogyakarta : 0039226571<br>
                                <a style="padding-left: 14px;">a/n GAMA PRESS</a>
                            </td>
                            
                            <td width="36%" style="text-align: center;">
                                a.n. Ka. Pemasaran<br><br><br><br><br><br><br>
                                <i>Adm. Pemasaran</i>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body></html>