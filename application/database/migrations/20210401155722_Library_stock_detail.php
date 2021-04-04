<?php
 
class Migration_Library_stock_detail extends CI_Migration{
    public function up(){
        $this->dbforge->add_field([
            'library_stock_detail_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'auto_increment' => TRUE
            ],
            'library_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'book_stock_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'library_stock' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
        ]);
        $this->dbforge->add_key('library_stock_detail_id', TRUE);
        $this->dbforge->create_table('library_stock_detail');
    }
 
    public function down(){
        $this->dbforge->drop_table('library_stock_detail');
    }
}
