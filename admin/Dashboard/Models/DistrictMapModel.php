<?php

namespace Admin\Dashboard\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class DistrictMapModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'dashboard_district_map';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = true;
	protected $protectFields        = false;
	protected $allowedFields        = [];

	// Dates
	protected $useTimestamps        = true;
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
	protected $bulider;

    public function getAll($filter = [])
	{
		$sql = "SELECT
        ddm.id,
        ddm.year_id,
        ddm.district_id,
        ddm.blocks,
        ddm.gps,
		ddm.chcs,
        ddm.cmscs,
        ddm.villages,
        ddm.tentative_farmers,
        sd.name district,
        ddm.created_at,
        sy.name year
      FROM dashboard_district_map ddm
        LEFT JOIN dashboard_years sy
          ON ddm.year_id = sy.id
        LEFT JOIN soe_districts sd
          ON ddm.district_id = sd.id
	  WHERE ddm.deleted_at IS NULL
	  AND sy.deleted_at IS NULL";
		if (isset($filter['year_id'])) {
			$sql .= " AND ddm.year_id = " . $filter['year_id'];
		}
		$sql .=  " GROUP BY ddm.year_id";
		return $this->db->query($sql)->getResult();
	}

	

	public function getOne($filter = [])
	{
		$sql = "SELECT
        ddm.id,
        ddm.district_id,
        ddm.blocks,
        ddm.gps,
		ddm.chcs,
        ddm.cmscs,
        sd.name district,
        ddm.villages,
        ddm.tentative_farmers,
        ddm.created_at,
        sy.name year
      FROM dashboard_district_map ddm
        LEFT JOIN dashboard_years sy
          ON ddm.year_id = sy.id
        LEFT JOIN soe_districts sd
          ON ddm.district_id = sd.id
          WHERE ddm.deleted_at IS NULL
	  AND sy.deleted_at IS NULL";
		if (isset($filter['year_id'])) {
			$sql .= " AND ddm.year_id = " . $filter['year_id'];
		}
		$sql .=  " ORDER BY sd.name";

		return $this->db->query($sql)->getResult();
	}

	
	
}
