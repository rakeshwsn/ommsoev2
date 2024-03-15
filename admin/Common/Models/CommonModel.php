<?php

namespace Admin\Common\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exception\DatabaseException;

class BaseModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        try {
            $this->db = db_connect();
        } catch (DatabaseException $e) {
            exit($e->getMessage());
        }
    }

    protected function runQuery($sql, $data = [])
    {
        try {
            return $this->db->query($sql, $data);
        } catch (DatabaseException $e) {
            exit($e->getMessage());
        }
    }
}
