<?php

namespace Admin\Dashboard\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class AreaCoverageModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'dashboard_areacoverage';
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
		a.id area_id,
		year_id,
		y.name year,
		district_id,
		d.name district,
		a.farmers,
        a.achievement,
        a.created_at
	  FROM dashboard_areacoverage a
		LEFT JOIN dashboard_years y
		  ON a.year_id = y.id
		LEFT JOIN soe_districts d
		  ON a.district_id = d.id
	  WHERE a.deleted_at IS NULL
	  AND y.deleted_at IS NULL";
		if (isset($filter['year_id'])) {
			$sql .= " AND a.year_id = " . $filter['year_id'];
		}
		$sql .=  " GROUP BY a.year_id";
		return $this->db->query($sql)->getResult();
	}

	public function getOne($filter = [])
	{
		$sql = "SELECT
		a.id area_id,
		year_id,
		y.name year,
		district_id,
		d.name district,
		a.farmers,
        a.achievement,
        a.created_at
	  FROM dashboard_areacoverage a
		LEFT JOIN dashboard_years y
		  ON a.year_id = y.id
		LEFT JOIN soe_districts d
		  ON a.district_id = d.id
	  WHERE a.deleted_at IS NULL
	  AND y.deleted_at IS NULL";
		if (isset($filter['year_id'])) {
			$sql .= " AND a.year_id = " . $filter['year_id'];
		}
		$sql .=  " ORDER BY d.name";

		return $this->db->query($sql)->getResult();
	}

	public function getYearwiseFarmerAchievement($filter=[]) {
		$sql = "SELECT
		a.year_id,
		y.name `year`,
		SUM(a.farmers) total_farmers,
		SUM(a.achievement) total_ach
	  FROM dashboard_areacoverage a LEFT JOIN dashboard_years y ON y.id=a.year_id
	  WHERE a.deleted_at IS NULL";
	  if(!empty($filter['district_id'])){
		$sql .= " AND district_id=".$filter['district_id'];
	  }
	  $sql .= " GROUP BY a.year_id
		ORDER BY y.start_date";

		return $this->db->query($sql)->getResult();
	}
	
}