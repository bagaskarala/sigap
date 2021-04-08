<?php

class Migration_update_book_stock extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('book_stock', 
        [
            'library_present' => [
                'type' => 'INT',
                'constraint' => 10 
            ],
            'showroom_present' => [
                'type' => 'INT',
                'constraint' => 10 
            ],           
            'book_location' => [
                'type' => 'VARCHAR',
                'constraint' => 20 
            ]
        ]);
        $this->dbforge->drop_column('book_stock', 'type');
        $this->dbforge->drop_column('book_stock', 'date');
        $this->dbforge->drop_column('book_stock', 'notes');
        $this->dbforge->drop_column('book_stock', 'warehouse_past');
        $this->dbforge->drop_column('book_stock', 'warehouse_modifier');
        $this->dbforge->drop_column('book_stock', 'warehouse_operator');
        $this->dbforge->drop_column('book_stock', 'user_id');
    }

    public function down(){
        $this->dbforge->drop_column('book_stock', 'library_present');
        $this->dbforge->drop_column('book_stock', 'showroom_present');
        $this->dbforge->drop_column('book_stock', 'book_location');
        $this->dbforge->add_column('book_stock', [
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 15
            ],
            'date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'notes' => [
                'type' => 'TEXT'
            ],
            'warehouse_past' => [
                'type' => 'INT',
                'constraint' => 10
            ],
            'warehouse_modifier' => [
                'type' => 'INT',
                'constraint' => 10
            ],
            'warehouse_operator' => [
                'type' => 'VARCHAR',
                'constraint' => 1
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
        ]);
    }
}