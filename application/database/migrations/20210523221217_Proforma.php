<?php

class Migration_Proforma extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_field([
            'proforma_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'auto_increment' => TRUE
            ],
            'number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'source' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'due_date' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
            'customer_id' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'issued_date' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ]
        ]);
        $this->dbforge->add_key('proforma_id', TRUE);
        $this->dbforge->create_table('proforma');
    }

    public function down()
    {
        $this->dbforge->drop_table('proforma');
    }
}
