<?php

class Migration_Book_transaction extends CI_Migration
{
    public function up(){
        $this->dbforge->add_field([
            'book_transaction_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'auto_increment' => TRUE
            ],
            'book_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'book_stock_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'invoice_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
            'book_receive_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
            'book_transfer_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
            'book_non_sales_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
            'stock_initial' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'stock_last' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'stock_in' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
            'stock_out' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => TRUE
            ],
            'date' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
                ],

        ]);
        $this->dbforge->add_key('book_transaction_id', TRUE);
        $this->dbforge->create_table('book_transaction');
    }

    public function down(){
        $this->dbforge->drop_table('book_transaction');
    }

}
