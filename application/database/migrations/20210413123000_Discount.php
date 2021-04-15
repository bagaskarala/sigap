<?php

class Migration_Discount extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 3,
                'auto_increment' => TRUE
            ],
            'membership' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'discount' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => '0'
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('discount');

        $data = array(
            array(
                'id'            => '1',
                'membership'    => 'distributor',
                'discount'      => '45'
            ),
            array(
                'id'            => '2',
                'membership'    => 'reseller',
                'discount'      => '35'
            ),
            array(
                'id'            => '3',
                'membership'    => 'author',
                'discount'      => '30'
            ),
            array(
                'id'            => '4',
                'membership'    => 'member',
                'discount'      => '25'
            ),
            array(
                'id'            => '5',
                'membership'    => 'general',
                'discount'      => '15'
            ),
        );
        $this->db->insert_batch('discount', $data);
    }

    public function down()
    {
        $this->dbforge->drop_table('discount');
    }
}