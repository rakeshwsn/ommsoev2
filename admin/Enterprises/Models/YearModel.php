<?php

namespace Admin\Enterprises\Models;

use CodeIgniter\Model;

class YearModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'dashboard_years';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
    protected $useSoftDeletes        = false;
    protected $protectFields        = false;
//	protected $allowedFields        = [];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
    protected $validationRules      = [];
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

	public function getCurrentYear() {
        $date = date('Y-m-d');
        $sql = "SELECT * FROM ".$this->table." WHERE DATE('$date') BETWEEN DATE(start_date) AND DATE(end_date)";
        return $this->db->query($sql)->getFirstRow();
	}

	
}
