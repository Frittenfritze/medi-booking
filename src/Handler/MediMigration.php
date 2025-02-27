<?php

namespace Medi\Handler;

use Medi\Model\MediAccount;
use Medi\Model\MediRoom;
use Medi\Model\MediBooking;

class MediMigration
{
    private $wpdb;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
    }

    public function run() : void {
        $models = [
            new MediAccount($this->wpdb),
            new MediRoom($this->wpdb),
            new MediBooking($this->wpdb)
        ];

        foreach ($models as $model) {
            $model->migrate();
        }
    }

    public function delete() : void {
        $models = [
            new MediBooking($this->wpdb),
            new MediAccount($this->wpdb),
            new MediRoom($this->wpdb),
        ];

        foreach ($models as $model) {
            $model->delete();
        }
    }
}