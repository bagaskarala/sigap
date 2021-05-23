<?php

class Migration_Update_book_stock_and_revision extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_column('book_stock', 
        [
            'retur_stock' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
        ]);
        $this->dbforge->add_column('book_stock_revision', 
        [
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
            ],
        ]);
    }

    public function down(){
        $this->dbforge->drop_column('book_stock', 'retur_stock');
        $this->dbforge->drop_column('book_stock_revision', 'type');
    }
}