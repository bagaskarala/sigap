<?php

function get_customer_list()
{
    $condition = function () {
        $CI = &get_instance();
        $CI->db->order_by('name', 'asc');
        return $CI;
    };

    return get_dropdown_list('customer', ['customer_id', 'name'], $condition);
}

function get_invoice_type()
{
    return [
        'credit' => 'Kredit',
        'cash' => 'Tunai',
        'online' => 'Online',
        'showroom' => 'Showroom'
    ];
}

function get_invoice_status()
{
    return [
        'waiting'           => 'Belum Konfirmasi',
        'confirm'           => 'Sudah Konfirmasi',
        'preparing'         => 'Diproses',
        'preparing_finish'  => 'Siap Diambil',
        'finish'            => 'Selesai',
        'cancel'            => 'Dibatalkan'
    ];
}

function get_customer_type()
{
    return [
        'distributor'   => 'Distributor',
        'reseller'      => 'Reseller',
        'author'        => 'Penulis',
        'member'        => 'Member',
        'general'       => 'Umum'
    ];
}

function get_dropdown_list_library()
{
    $condition = function () {
        $CI = &get_instance();
        $CI->db->order_by('library_name', 'asc');
        return $CI;
    };

    return get_dropdown_list('library', ['library_id', 'library_name'], $condition);
}

function price_to_text($total) {
    $total = abs($total);
    $words = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if ($total < 12) {
        $temp = " ". $words[$total];
    } else if ($total <20) {
        $temp = price_to_text($total - 10). " belas";
    } else if ($total < 100) {
        $temp = price_to_text($total/10)." puluh". price_to_text($total % 10);
    } else if ($total < 200) {
        $temp = " seratus" . price_to_text($total - 100);
    } else if ($total < 1000) {
        $temp = price_to_text($total/100) . " ratus" . price_to_text($total % 100);
    } else if ($total < 2000) {
        $temp = " seribu" . price_to_text($total - 1000);
    } else if ($total < 1000000) {
        $temp = price_to_text($total/1000) . " ribu" . price_to_text($total % 1000);
    } else if ($total < 1000000000) {
        $temp = price_to_text($total/1000000) . " juta" . price_to_text($total % 1000000);
    } else if ($total < 1000000000000) {
        $temp = price_to_text($total/1000000000) . " milyar" . price_to_text(fmod($total,1000000000));
    } else if ($total < 1000000000000000) {
        $temp = price_to_text($total/1000000000000) . " trilyun" . price_to_text(fmod($total,1000000000000));
    }     
    return $temp;
}

function view_price_to_text($total) {
    if($total<0) {
        $result = "minus ". trim(price_to_text($total));
    } else {
        $result = trim(price_to_text($total));
    }     		
    return $result;
}