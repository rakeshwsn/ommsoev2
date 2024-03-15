<?php

namespace App\Traits;

use PDO;
use Config\Database;

trait ModelTrait
{
    /**
     * @var PDO
     */
    private $ _db;

    /**
     * @var \Config\Settings
     */
    private $settings;

    /**
     * ModelTrait constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->_db = $db->connect();
        if (!$this->_db) {
            throw new \RuntimeException('Database connection failed');
        }
        $this->settings = new \Config\Settings();
    }

    /**
     * @return array
     */
    public function getFieldNames(): array
    {
        $fieldNames = $this->_db->getFieldNames($this->table);
        return array_values($fieldNames);
    }
}
