<?php

    class Migration_Invoice_book extends CI_Migration {

        public function up()
        {
            $this->dbforge->add_field([
                'invoice_book_id' => [
                    'type' => 'INT',
                    'constraint' => 10,
                    'auto_increment' => TRUE
                ],
                'invoice_id' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                ],
                'book_id' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                ],
                'qty' => [
                    'type' => 'INT',
                    'constraint' => 10,
                ],
                'discount' => [
                    'type' => 'INT',
                    'constraint' => 3,
                ],
                'price' => [
                    'type' => 'INT',
                    'constraint' => 10,
                    'null' => TRUE
                ],
                'royalty' => [
                    'type' => 'INT',
                    'constraint' => 3,
                    'null' => TRUE,
                ],
            ]);
            $this->dbforge->add_key('invoice_book_id', TRUE);
            $this->dbforge->create_table('invoice_book');
        }

        public function down() {
            $this->dbforge->drop_table('invoice_book');
        }

    }