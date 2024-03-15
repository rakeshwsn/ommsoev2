<?php

namespace Admin\Transaction\Models;

use CodeIgniter\Model;

class ORCheckModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'soe_fund_receipt_check';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = false;
    protected $allowedFields = ['check_type'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'check_type' => 'required|in_list[or]'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = ['checkType'];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function checkType(){
        $this->where('check_type','or');
        return $this;
    }

    public function getORChecks()
    {
        return $this->checkType()->findAll();
    }
}
