<?php

        class Migration_Invoice extends CI_Migration {

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
                    'type' => [
                        'type' => 'VARCHAR',
                        'constraint' => 25,
                    ],
                    'source' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                        'null' => TRUE,
                    ],
                    'source_library_id' => [
                        'type' => 'VARCHAR',
                        'constraint' => 20,
                        'null' => TRUE
                    ],
                    'customer_id' => [
                        'type' => 'VARCHAR',
                        'constraint' => 20,
                        'null' => TRUE
                    ],
                    'status' => [
                        'type' => 'VARCHAR',
                        'constraint' => 25,
                        'default' => 'waiting'
                    ],
                    'due_date' => [
                        'type' => 'TIMESTAMP',
                        'null' => TRUE,
                    ],
                    'issued_date' => [
                        'type' => 'TIMESTAMP',
                        'null' => TRUE,
                    ],
                    'confirm_date' => [
                        'type' => 'TIMESTAMP',
                        'null' => TRUE
                    ],
                    'preparing_deadline' => [
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
                    ],
                    'preparing_staff' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                        'null' => TRUE
                    ],
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
                $this->dbforge->add_key('invoice_id', TRUE);
                $this->dbforge->create_table('invoice');
            }

            public function down() {
                $this->dbforge->drop_table('invoice');
            }

        }