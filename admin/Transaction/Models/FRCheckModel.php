<?php

namespace Admin\Transaction\Models;

use CodeIgniter\Model;

class FRCheckModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'soe_fund_receipt_check';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes        = false;
	protected $protectFields        = false;
	protected $allowedFields        = ['check_type'];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = '';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'check_type' => 'required|in_list[fr,cr]'
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

    public function checkType($checkType = 'fr'){
        $this->where('check_type', $checkType);
        return $this;
    }
}

