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
  y.name year,
  y.id year_id,
  d.name district,
  SUM(de.wshg) total_wshg,
  SUM(de.fpos) total_fpos,
  e.id unit_id,
  e.enterprises_name unit_name,
  de.district_id
FROM enterprises e
  LEFT JOIN dashboard_enterprises de
    ON e.id = de.unit_id
  LEFT JOIN dashboard_years y
    ON y.id = de.year_id
  LEFT JOIN soe_districts d
    ON d.id = de.district_id
WHERE de.deleted_at IS NULL";
		if (!empty($filter['district_id'])) {
			$sql .= " AND district_id=" . $filter['district_id'];
		}
		if (!empty($filter['year_id'])) {
			$sql .= " AND year_id=" . $filter['year_id'];
		}
		$sql .= " GROUP BY de.unit_id,de.year_id
		HAVING de.district_id IS NOT NULL AND (total_wshg>0 OR total_fpos>0)
	   ORDER BY de.year_id";
		// echo $sql;exit;
		return $this->db->query($sql)->getResult();
	}
}
