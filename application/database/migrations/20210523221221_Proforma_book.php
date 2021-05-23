<?php

class Migration_Proforma_book extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 10,
                'auto_increment' => TRUE
            ],
            'proforma_id' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'book_id' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE,
            ],
            'qty' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE,
            ],
            'discount' => [
                'type' => 'INT',
                'constraint' => 3,
            ],
            'price' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => true
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('proforma_book');
    }

    public function down()
    {
        $this->dbforge->drop_table('proforma_book');
    }
}
