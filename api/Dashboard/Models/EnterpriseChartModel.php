<?php

namespace Api\Dashboard\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class EnterpriseChartModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'dashboard_enterprises';
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

	public function getYearwisedata($filter = [])
	{
		$sql = "SELECT
		e.year_id,
		y.name year,
		y.id ,
        d.name district,
		SUM(e.wshg) total_wshg,
		SUM(e.fpos) total_fpos,
		e.unit_id,
		e.unit_name,
		e.district_id
	  FROM dashboard_enterprises e
		LEFT JOIN dashboard_years y
		  ON y.id = e.year_id
		LEFT JOIN soe_districts d
		  ON d.id = e.district_id
		  WHERE e.deleted_at IS NULL";
		if (!empty($filter['district_id'])) {
			$sql .= " AND district_id=" . $filter['district_id'];
		}
		if (!empty($filter['year_id'])) {
			$sql .= " AND year_id=" . $filter['year_id'];
		}
		$sql .= " GROUP BY e.unit_name
		HAVING total_wshg>0 OR total_fpos>0
	   ORDER BY e.year_id";

		return $this->db->query($sql)->getResult();
	}

	public function getYearwisedataAll($filter = [])
	{
		$sql = "SELECT
		e.year_id,
		y.name year,
		y.id ,
        d.name district,
		SUM(e.wshg) total_wshg,
		SUM(e.fpos) total_fpos,
		e.unit_id,
		e.unit_name,
		e.district_id
	  FROM dashboard_enterprises e
		LEFT JOIN dashboard_years y
		  ON y.id = e.year_id
		LEFT JOIN soe_districts d
		  ON d.id = e.district_id
		  WHERE e.deleted_at IS NULL";
		if (!empty($filter['district_id'])) {
			$sql .= " AND district_id=" . $filter['district_id'];
		}
		if (!empty($filter['year_id'])) {
			$sql .= " AND year_id=" . $filter['year_id'];
		}
		$sql .= " GROUP BY e.unit_name
	   ORDER BY e.year_id";

		return $this->db->query($sql)->getResult();
	}



	public function getDistrictwiseEnterprisedata($filter = [])
	{
		$sql = "SELECT
		e.year_id,
		u.name,
		u.icons,
		y.name year,
		y.id ,
        d.name district,
		SUM(e.wshg) total_wshg,
		SUM(e.fpos) total_fpos,
		e.unit_id,
		e.unit_name,
		e.district_id
	  FROM dashboard_enterprises e
	  LEFT JOIN enterprises_units u
		on u.id = e.unit_id
		LEFT JOIN dashboard_years y
		  ON y.id = e.year_id
		LEFT JOIN soe_districts d
		  ON d.id = e.district_id
		  WHERE e.deleted_at IS NULL";
		if (!empty($filter['district_id'])) {
			$sql .= " AND district_id=" . $filter['district_id'];
		}

		$sql .= " GROUP BY e.unit_name  HAVING total_wshg !=0 ";
	//    echo $sql; exit;

		return $this->db->query($sql)->getResult();
	}
}
