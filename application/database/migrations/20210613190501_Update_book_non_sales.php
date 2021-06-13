<?php

class Migration_Update_book_non_sales extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_column('book_non_sales', 
        [
            'requester' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE
            ],
            'receiver' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE
            ],
        ]);
    }

    public function down(){
        $this->dbforge->drop_column('book_non_sales', 'requester');
        $this->dbforge->drop_column('book_non_sales', 'receiver');
    }
}
