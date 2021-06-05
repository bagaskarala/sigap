<?php

class Migration_update_invoice_receipt extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('invoice', 
        [
            'receipt' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => TRUE
            ],
        ]);
    }

    public function down(){
        $this->dbforge->drop_column('invoice', 'receipt');
    }
}