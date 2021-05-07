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
        ''  => null,
        'credit' => 'Kredit',
        'cash' => 'Tunai',
        'online' => 'Online',
        'showroom' => 'Showroom'
    ];
}

function get_invoice_status()
{
    return [
        '' => null,
        'waiting'           => 'Belum Konfirmasi',
        'confirm'           => 'Sudah Konfirmasi',
        'preparing_start'   => 'Diproses',
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