<?php

namespace App\Models;

use CodeIgniter\Model;

class Test extends Model
{
    // Database connection & table name
    protected $DBGroup = 'default';
    protected $table = 'tests';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;

    // Result type
    protected $returnType = 'array';

    // Soft deletes
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    // Timestamps
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    // Add custom methods here
}
