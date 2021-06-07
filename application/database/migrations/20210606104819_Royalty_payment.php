<?php

    class Migration_Royalty_payment extends CI_Migration {

        public function up()
        {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 10,
                    'auto_increment' => TRUE
                ],
                'author_id' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                ],
                'last_paid_date' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
                'last_request_date' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
                'status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('royalty_payment');
        }

        public function down() {
            $this->dbforge->drop_table('royalty_payment');
        }
    }
