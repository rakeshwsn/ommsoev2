<?php

namespace Admin\Customer\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'Customer';
    protected $allowedFields = ['column1', 'column2', 'column3']; // add allowed fields
    protected $beforeInsert = ['beforeInsert'];
    protected $beforeUpdate = ['beforeUpdate'];

    public function __construct()
    {
        parent::__construct();
    }

    protected function beforeInsert(array $data)
    {
        // add any modifications before inserting data here
        // for example, hashing passwords

        return $data;
    }

    protected function beforeUpdate(array $data)
    {
        // add any modifications before updating data here

        return $data;
    }
}
