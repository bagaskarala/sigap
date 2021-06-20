<?php

    class Migration_Royalty extends CI_Migration {

        public function up()
        {
            $this->dbforge->add_field([
                'royalty_id' => [
                    'type' => 'INT',
                    'constraint' => 10,
                    'auto_increment' => TRUE
                ],
                'author_id' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                ],
                'start_date' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
                'end_date' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
                'status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'paid_date' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
                'receipt' => [
                    'type' => 'VARCHAR',
                    'constraint' => 200,
                    'null' => TRUE
                ],
            ]);
            $this->dbforge->add_key('royalty_id', TRUE);
            $this->dbforge->create_table('royalty');
        }

        public function down() {
            $this->dbforge->drop_table('royalty');
        }

    }