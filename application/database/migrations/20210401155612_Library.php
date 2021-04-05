<?php

class Migration_Library extends CI_Migration{
    public function up(){
        $this->dbforge->add_field([
            'library_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'auto_increment' => TRUE
            ],
            'library_name' => [
                'type' => 'VARCHAR',
                'constraint' => 256,
            ],
        ]);
        $this->dbforge->add_key('library_id', TRUE);
        $this->dbforge->create_table('library');
    }
 
    public function down(){
        $this->dbforge->drop_table('library');
    }
}
