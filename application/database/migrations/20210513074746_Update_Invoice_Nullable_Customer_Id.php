<?php

        class Migration_Update_Invoice_Nullable_Customer_Id extends CI_Migration {

            public function up()
            {
                $this->dbforge->modify_column('invoice', 
                [
                    'customer_id' => [
                        'name' => 'customer_id',
                        'type' => 'VARCHAR',
                        'constraint' => 20,
                        'null' => TRUE
                    ]
                ]);
            }

            public function down(){
                $this->dbforge->modify_column('invoice', 
                [
                    'customer_id' => [
                        'name' => 'customer_id',
                        'type' => 'VARCHAR',
                        'constraint' => 20,
                        'null' => FALSE
                    ]
                ]);

            }

        }