<?php

class Migration_Add_proforma_delivery_fee extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_column(
            'proforma',
            [
                'delivery_fee' => [
                    'type' => 'INT',
                    'constraint' => 20,
                    'null' => TRUE
                ],
            ]
        );
    }

    public function down()
    {
        $this->dbforge->drop_column('proforma', 'delivery_fee');
    }
}
