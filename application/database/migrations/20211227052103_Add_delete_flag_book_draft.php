<?php

class Migration_Add_delete_flag_book_draft extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_column(
            'draft',
            [
                'is_deleted' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ],
            ]
        );
        $this->dbforge->add_column(
            'book',
            [
                'is_deleted' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ],
            ]
        );
    }

    public function down()
    {
        $this->dbforge->drop_column('draft', 'is_deleted');
        $this->dbforge->drop_column('book', 'is_deleted');
    }
}
