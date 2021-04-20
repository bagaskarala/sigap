<?php

class Migration_Book_receive extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'book_receive_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'auto_increment' => TRUE
            ],
            'book_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'print_order_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'entry_date' => [
                'type' => 'TIMESTAMP'
            ],
            'deadline' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'finish_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'book_receive_status' => [
                'type' => 'VARCHAR',
                'constraint' => 20
            ],

            // handover
            'is_handover' => [
                'type' => 'INT',
                'constraint' => 1,
                'default' => 0
            ],
            'handover_start_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'handover_end_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'handover_deadline' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'handover_notes' => [
                'type' => 'TEXT',
            ],
            'handover_notes_admin' => [
                'type' => 'TEXT',
            ],
            // 'handover_staff' => [
            //     'type' => 'VARCHAR',
            //     'constraint' => 100,
            //     'null' => TRUE
            // ],

            // wrapping
            'is_wrapping' => [
                'type' => 'INT',
                'constraint' => 1,
                'default' => 0
            ],
            'wrapping_start_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'wrapping_end_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'wrapping_deadline' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'wrapping_notes' => [
                'type' => 'TEXT',
            ],
            'wrapping_notes_admin' => [
                'type' => 'TEXT',
            ],
            // 'wrapping_staff' => [
            //     'type' => 'VARCHAR',
            //     'constraint' => 100
            // ],
        ]);
        $this->dbforge->add_key('book_receive_id', TRUE);
        $this->dbforge->create_table('book_receive');
    }

    public function down()
    {
        $this->dbforge->drop_table('book_receive');
    }
}
