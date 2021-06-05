<?php

class Migration_update_customer_email extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('customer', 
        [
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE
            ],
        ]);
    }

    public function down(){
        $this->dbforge->drop_column('customer', 'email');
    }
}