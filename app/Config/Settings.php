<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Database\Exception;
use CodeIgniter\Database\Query;

class Settings extends BaseConfig
{
    /** @var string */
    const TABLE = 'config';

    /** @var \CodeIgniter\Database\ConnectionInterface */
    private $db;

    /**
     * Settings constructor.
     */
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        try {
            $this->get_all();
        } catch (Exception $e) {
            // Handle the exception or log it
            // for better error handling and debugging
            exit('Error fetching settings: ' . $e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function get_all(): array
    {
        $builder = $this->db->table(self::TABLE);
        $query = $builder->get();

        $settings = [];

        foreach ($query->getResult() as $setting) {
            $key = $setting->key;
            $value = $setting->value;
            $serialized = $setting->serialized;

            if (!$serialized) {
                $settings[$key] = $value;
            } else {
                $settings[$key] = json_decode($value, true) ?? [];
            }
        }

        return $settings;
    }
}
