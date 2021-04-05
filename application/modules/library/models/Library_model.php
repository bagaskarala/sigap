<?php defined('BASEPATH') or exit('No direct script access allowed');
 
class Library_model extends MY_Model
{
    public function get_validation_rules()
    {
        $validation_rules = [
            [
                'field' => 'library_name',
                'label' => 'Nama Perpustakaan',
                'rules' => 'trim|required|min_length[1]|max_length[256]|callback_unique_library_name',
            ],
        ];
 
        return $validation_rules;
    }
 
    public function get_default_values()
    {
        return [
            'library_name' => '',
        ];
    }
}
