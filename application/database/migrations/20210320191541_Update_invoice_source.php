<?php

class Migration_Update_invoice_source extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_column('invoice', [
            'source' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('invoice', 'source');
    }
}
