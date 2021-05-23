<?php

class Migration_Book_transfer_list extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_field([
            'book_transfer_list_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'auto_increment' => TRUE
            ],
            'book_transfer_id' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'book_id' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE,
            ],
            'qty' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE,
            ],
        ]);
        $this->dbforge->add_key('book_transfer_list_id', TRUE);
        $this->dbforge->create_table('book_transfer_list');
    }

    public function down()
    {
        $this->dbforge->drop_table('book_transfer_list');
    }
}
