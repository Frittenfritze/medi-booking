<?php

namespace Medi\Model;

use Medi\Handler\MediModel;

class MediAccount extends MediModel
{
    private string $table_name;

    public function __construct($wpdb) {
        parent::__construct($wpdb);

        $this->table_name = $wpdb->prefix . 'medi_booking_account';
    }

    public function migrate() : void
    {
        parent::medi_create_table(
            $this->table_name,
            [
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'account_username' => 'VARCHAR(255) NOT NULL',
                'account_firstname' => 'VARCHAR(255) NOT NULL',
                'account_lastname' => 'VARCHAR(255) NOT NULL',
                'account_mail' => 'VARCHAR(255) NOT NULL',
                'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
                'updated_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
            ]
        );
    }

    public function delete() : void
    {
        parent::medi_delete_table($this->table_name);
    }

    public function render() : array
    {
        return parent::medi_get_data($this->table_name);
    }

    public function get_data(int $data_id)
    {
        return parent::medi_get_data_by_id($data_id, $this->table_name);
    }

    public function get_data_by_username(string $username)
    {
        return parent::medi_get_data_by_username($username, $this->table_name);
    }

    public function insert(array $data) : int
    {
        return parent::medi_insert_data($data, $this->table_name);
    }

    public function update(array $data, int $data_id) : bool
    {
        return parent::medi_update_data($data, $data_id, $this->table_name);
    }

    public function delete_data( int $data_id) : bool
    {
        return parent::medi_delete_data($data_id, $this->table_name);
    }
}