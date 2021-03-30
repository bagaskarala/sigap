<?php

class Migration_Update_invoice_datetime extends CI_Migration
{

    public function up()
    {
        
        $this->dbforge->add_column('invoice', [
            'confirm_date' => [
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
            'cancel_date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('invoice', 'confirm_date');
        $this->dbforge->drop_column('invoice', 'preparing_start_date');
        $this->dbforge->drop_column('invoice', 'preparing_end_date');
        $this->dbforge->drop_column('invoice', 'finish_date');
        $this->dbforge->drop_column('invoice', 'cancel_date');
    }
}
