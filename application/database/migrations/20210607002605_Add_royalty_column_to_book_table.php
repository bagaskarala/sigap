<?php

class Migration_Add_royalty_column_to_book_table extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_column('book', 
        [
            'royalty' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 15,
                'null' => TRUE
            ],
        ]);
    }

    public function down(){
        $this->dbforge->drop_column('book', 'royalty');
    }

}