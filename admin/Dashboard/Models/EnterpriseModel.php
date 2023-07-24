<?php

namespace Admin\Dashboard\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class EnterpriseModel extends Model
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



	public function getAll($filter = [])
	{
		$sql = "SELECT
		e.id enterprises_id,
		e.year_id,
		e.unit_id,
		e.unit_name,
		e.wshg,
        e.fpos,
		y.name year,
		d.name district_name,
		e.district_id,
		e.created_at,
		e.deleted_at
	  FROM dashboard_enterprises e
		LEFT JOIN dashboard_years y
		  ON e.year_id = y.id
		LEFT JOIN soe_districts d
		  ON e.district_id = d.id
	  WHERE e.deleted_at IS NULL";
		if (isset($filter['year_id'])) {
			$sql .= " AND e.year_id = " . $filter['year_id'];
		}
		if (isset($filter['district_id'])) {
			$sql .= " AND e.district_id = " . $filter['district_id'];
		}
		$sql .=  " GROUP BY e.year_id,e.district_id";

		return $this->db->query($sql)->getResult();
	}

	public function getByDistYear($filter = [])
	{
		$sql = "SELECT
		e.id enterprises_id,
		e.year_id,
		e.unit_id,
		e.unit_name,
		e.wshg,
        e.fpos,
		y.name year,
		d.name district_name,
		e.district_id,
		e.created_at,
		e.deleted_at
	  FROM dashboard_enterprises e
		LEFT JOIN dashboard_years y
		  ON e.year_id = y.id
		LEFT JOIN soe_districts d
		  ON e.district_id = d.id
	  WHERE e.deleted_at IS NULL";
		if (isset($filter['year_id'])) {
			$sql .= " AND e.year_id = " . $filter['year_id'];
		}
		if (isset($filter['district_id'])) {
			$sql .= " AND e.district_id = " . $filter['district_id'];
		}

		return $this->db->query($sql)->getResult();
	}
    public function getYearwisedata($filter = [])
	{
		$sql = "SELECT
		e.year_id,
		y.name year,
		y.id ,
        d.name district,
		SUM(e.wshg) total_wshg,
		SUM(e.fpos) total_fpos,
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
	   ORDER BY e.unit_name";
		// echo $sql;exit;
		return $this->db->query($sql)->getResult();
	}
}
