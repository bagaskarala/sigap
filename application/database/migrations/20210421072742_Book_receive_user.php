<?php

class Migration_Book_receive_user extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_field([
            'book_receive_user_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'auto_increment' => TRUE
            ],
            'book_receive_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'progress' => [
                'type' => 'VARCHAR',
                'constraint' => 10
            ],
        ]);
        $this->dbforge->add_key('book_receive_user_id', TRUE);
        $this->dbforge->create_table('book_receive_user');
    }

    public function down()
    {
        $this->dbforge->drop_table('book_receive_user');
    }
}
