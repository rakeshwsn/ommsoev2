<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $allowedFields = ['column1', 'column2', 'column3']; // add allowed fields
    protected $returnType = 'object'; // return objects instead of arrays
    protected $useTimestamps = true; // enable created_at and updated_at fields

    public function __construct()
    {
        parent::__construct();
        $this->db->enableAutoTransactions(false); // disable transactions by default
    }

    protected function beforeInsert(array $data)
    {
        // add any modifications before inserting data here
        // for example, hashing passwords

        // set created_at field
        $data['created_at'] = date('Y-m-d H:i:s');

        return $data;
    }

    protected function beforeUpdate(array $data)
    {
        // add any modifications before updating data here

        // set updated_at field
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $data;
    }

    public function getCustomerById($id)
    {
        return $this->where('id', $id)->first();
    }

    public function getCustomersByEmail($email)
    {
        return $this->where('email', $email)->findAll();
    }
}

