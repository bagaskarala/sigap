<?php

class Migration_Update_book_transfer extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_column('book_transfer', 
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

    public function down()
    {
        $this->dbforge->drop_column('book_transfer', 'requester');
        $this->dbforge->drop_column('book_transfer', 'receiver');
    }
}
