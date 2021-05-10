<?php
    class Migration_Add_Total_Weight_Invoice extends CI_Migration {

        public function up()
        {
            $this->dbforge->add_column('invoice', 
            [
                'total_weight' => [
                    'type' => 'INT',
                    'constraint' => 20,
                    'null' => TRUE
                ],
                'delivery_fee' => [
                    'type' => 'INT',
                    'constraint' => 20,
                    'null' => TRUE
                ],
            ]);
            $this->dbforge->add_column('book', 
            [
                'weight' => [
                    'type' => 'INT',
                    'constraint' => 20,
                    'default' => 0,
                    'null' => TRUE
                ],
            ]);
        }

        public function down(){
            $this->dbforge->drop_column('invoice', 'total_weight');
            $this->dbforge->drop_column('invoice', 'delivery_fee');
            $this->dbforge->drop_column('book', 'weight');
        }

    }