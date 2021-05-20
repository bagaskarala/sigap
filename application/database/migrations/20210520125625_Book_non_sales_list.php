<?php

class Migration_Book_non_sales_list extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_field([
            'book_non_sales_list_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'auto_increment' => TRUE
            ],
            'book_non_sales_id' => [
                'type' => 'INT',
                'constraint' => 10
            ],
            'book_id' => [
                'type' => 'INT',
                'constraint' => 10
            ],
            'qty' => [
                'type' => 'INT',
                'constraint' => 10
            ],
        ]);
        $this->dbforge->add_key('book_non_sales_list_id', TRUE);
        $this->dbforge->create_table('book_non_sales_list');
    }

    public function down()
    {
        $this->dbforge->drop_table('book_non_sales_list');
    }
}
