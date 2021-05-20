<?php

class Migration_Book_non_sales extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_field([
            'book_non_sales_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'auto_increment' => TRUE
            ],
            'number' => [
                'type' => 'VARCHAR',
                'constraint' => 20
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
                'null' => TRUE
            ],
            'address' => [
                'type' => 'TEXT',
            ],
            'notes' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE
            ],
            'issued_date' => [
                'type' => 'TIMESTAMP'
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 30
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 30
            ],
        ]);
        $this->dbforge->add_key('book_non_sales_id', TRUE);
        $this->dbforge->create_table('book_non_sales');
    }

    public function down()
    {
        $this->dbforge->drop_table('book_non_sales');
    }
}
