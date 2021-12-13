<?php

class Reset_book_stock extends Seeder
{
    private $table = 'book_stock';

    public function run()
    {
        $this->db->truncate($this->table);
        $book_stock_count = $this->db->count_all_results($this->table);
        if ($book_stock_count == 0) {
            echo 'book stock cleared';
        } else {
            echo 'failed to reset book stock';
        }

        echo PHP_EOL;
    }
}
