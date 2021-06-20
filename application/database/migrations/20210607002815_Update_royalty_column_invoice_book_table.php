<?php

class Migration_Update_royalty_column_invoice_book_table extends CI_Migration {

    public function up()
    {
        $this->dbforge->modify_column('invoice_book', 
        [
            'royalty' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 15,
                'null' => TRUE
            ]
        ]);
    }

    public function down(){
        $this->dbforge->modify_column('invoice_book', 
        [
            'royalty' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => TRUE
            ]
        ]);
    }

}