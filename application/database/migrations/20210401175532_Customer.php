<?php

class Migration_Customer extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_field([
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'auto_increment' => TRUE
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'address' => [
                'type' => 'VARCHAR',
				'constraint' => 100,
                'null' => TRUE,
            ],
            'phone_number' => [
                'type' => 'VARCHAR',
				'constraint' => 20,
                'null' => TRUE,
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
            ]
        ]);
        $this->dbforge->add_key('customer_id', TRUE);
        $this->dbforge->create_table('customer');
    }

    public function down()
    {
        $this->dbforge->drop_table('customer');
    }
}
