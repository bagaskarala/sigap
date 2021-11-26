<?php

        class Migration_Create_book_stock_log extends CI_Migration {

            public function up() {
                $this->dbforge->add_field([
                    'book_stock_log_id' => [
                        'type' => 'INT',
                        'constraint' => 10,
                        'auto_increment' => TRUE
                    ],
                    'date' => [
                        'type' => 'TIMESTAMP',
                        'null' => TRUE,
                    ],
                    'book_id' => [
                        'type' => 'VARCHAR',
                        'constraint' => 20,
                    ],
                    'warehouse_stock' => [
                        'type' => 'INT',
                        'constraint' => 20,
                        'null' => TRUE
                    ],
                    'showroom_stock' => [
                        'type' => 'INT',
                        'constraint' => 20,
                        'null' => TRUE
                    ],
                    'library_stock' => [
                        'type' => 'INT',
                        'constraint' => 20,
                        'null' => TRUE
                    ],
                    'retur_stock' => [
                        'type' => 'INT',
                        'constraint' => 20,
                        'null' => TRUE,
                    ],
                ]);
                $this->dbforge->add_key('book_stock_log_id', TRUE);
                $this->dbforge->create_table('book_stock_log');
            }

            public function down() {
                $this->dbforge->drop_table('book_stock_log');
            }

        }