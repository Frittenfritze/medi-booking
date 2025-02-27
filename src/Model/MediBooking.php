<?php

namespace Medi\Model;

use Medi\Handler\MediModel;

class MediBooking extends MediModel
{
    private string $table_name;

    public function __construct($wpdb) {
        parent::__construct($wpdb);

        $this->table_name = $wpdb->prefix . 'medi_booking_booking';
    }

    public function migrate() : void
    {
        parent::medi_create_table(
            $this->table_name,
            [
                'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
                'booking_notice' => 'VARCHAR(255) NOT NULL',
                'booking_date_start' => 'VARCHAR(255) NOT NULL',
                'booking_date_end' => 'VARCHAR(255) NOT NULL',
                'account_id' => 'INT(11)',
                'room_id' => 'INT(11)',
                'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
                'updated_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
            ],
            [
                'account_id' => $this->wpdb->prefix . 'medi_booking_account(id)',
                'room_id' => $this->wpdb->prefix . 'medi_booking_room(id)'
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

    public function insert(array $data) : bool
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