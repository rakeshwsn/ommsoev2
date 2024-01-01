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
// echo $sql;exit;
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
		$sql .= " GROUP BY e.unit_name ";
		// echo $sql;exit;
		return $this->db->query($sql)->getResult();
	}

    /** Sync data from table
     * This function is used to sync data from table to dashboard_enterprises from enterprises table
     * This is not implemented yet. Please remove this line after implementing
     * author: Rakesh Sahu
     */
    public function syncFromTable() {
        $sql = "INSERT INTO dashboard_enterprises_copy (year_id, district_id, unit_id, unit_name, wshg, fpos, created_at, updated_at)
  SELECT
    ydu.year_id,
    ydu.district_id,
    ydu.unit_id,
    ydu.unit_name,
    COALESCE(res.shg_units, 0) wshg,
    COALESCE(res.fpo_units, 0) fpo,NOW(),NOW()
  FROM (SELECT
      dy.id year_id,
      sd.id district_id,
      eu.id unit_id,
      eu.name unit_name
    FROM enterprises_units eu
      CROSS JOIN soe_districts sd
      CROSS JOIN (SELECT
          *
        FROM dashboard_years
        WHERE id < 8) dy) ydu
    LEFT JOIN (SELECT
        unit_id,
        year,
        year_id,
        district_id,
        MAX(CASE WHEN management_unit_type = 'shg' THEN total_units ELSE 0 END) AS shg_units,
        MAX(CASE WHEN management_unit_type = 'fpo' THEN total_units ELSE 0 END) AS fpo_units
      FROM (SELECT
          e.unit_id,
          COUNT(e.unit_id) total_units,
          dy.name year,
          dy.id year_id,
          e.district_id,
          e.management_unit_type
        FROM (SELECT
            *
          FROM enterprises
          WHERE deleted_at IS NULL
          AND unit_id > 0
          AND YEAR(date_estd) >= 2017) e
          LEFT JOIN dashboard_years dy
            ON DATE(e.date_estd) BETWEEN DATE(dy.start_date) AND DATE(dy.end_date)
        GROUP BY e.unit_id,
                 e.district_id,
                 e.management_unit_type,
                 year_id) rs
      GROUP BY unit_id,
               year,
               year_id,
               district_id
      ORDER BY year_id, district_id) res
      ON res.unit_id = ydu.unit_id
      AND res.district_id = ydu.district_id
      AND res.year_id = ydu.year_id";


    }
}
