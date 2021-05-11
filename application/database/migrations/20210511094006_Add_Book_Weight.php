<?php
    class Migration_Add_Book_Weight extends CI_Migration {

        public function up()
        {
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
            $this->dbforge->drop_column('book', 'weight');
        }

    }