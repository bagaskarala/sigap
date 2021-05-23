<?php

class Migration_Book_transfer extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'book_transfer_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'auto_increment' => TRUE
            ],
            'transfer_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20
            ],
            'library_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 30
            ],
            'destination' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'transfer_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'preparing_deadline' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],           
            'preparing_start_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'preparing_end_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],           
            'finish_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],           
        ]);
        $this->dbforge->add_key('book_transfer_id', TRUE);
        $this->dbforge->create_table('book_transfer');
    }

    public function down()
    {
        $this->dbforge->drop_table('book_transfer');
    }
}
