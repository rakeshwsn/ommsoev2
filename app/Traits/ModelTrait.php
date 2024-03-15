<?php

namespace App\Traits;


use Config\Database;

trait ModelTrait
{
    private $_db;

    public function init()
    {
        $this->_db = db_connect();
        $this->settings =  new \Config\Settings();
        return $this;
    }

    public function getFieldNames()
    {
        return $this->_db->getFieldNames($this->table);
    }
}