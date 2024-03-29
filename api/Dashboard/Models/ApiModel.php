<?php

namespace Api\Dashboard\Models;

use CodeIgniter\Model;

class ApiModel extends Model
{
	protected $DBGroup              = '';
	protected $table                = 'apis';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [];

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
	
	public function getXform($form_id) {
        $sql = "SELECT *
FROM _form_info fi
  LEFT JOIN _form_info_xform_bin fixb
    ON fi._URI = fixb._TOP_LEVEL_AURI
	WHERE fi.FORM_ID = '".$form_id."'";

        return $this->db->query($sql)->getRowArray();
	}
}
