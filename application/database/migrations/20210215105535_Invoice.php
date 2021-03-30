<?php

class Migration_Invoice extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_field([
            'invoice_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'auto_increment' => TRUE
            ],
            'number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'issued_date' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
            'due_date' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
            ],
            'customer_id' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
        ]);
        $this->dbforge->add_key('invoice_id', TRUE);
        $this->dbforge->create_table('invoice');
    }

    public function down()
    {
        $this->dbforge->drop_table('invoice');
    }
}
