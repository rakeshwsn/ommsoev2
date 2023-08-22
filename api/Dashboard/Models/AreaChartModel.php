<?php

namespace Api\Dashboard\Models;

use CodeIgniter\Model;

class AreaChartModel extends Model
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
	  //echo $sql;
		return $this->db->query($sql)->getResult();
	}

	public function getFarmerAreaByDistrict($filter=[]){
		$sql = "SELECT
		da.year_id,
		y.name `year`,
		da.district_id,
		sd.name AS district,
		SUM(da.farmers) AS total_farmer,
		SUM(da.achievement) AS total_area
	  FROM dashboard_areacoverage da
		LEFT JOIN soe_districts sd
		  ON sd.id = da.district_id
		LEFT JOIN dashboard_years y
		  ON y.id = da.year_id
	  WHERE 1=1";

	  if(!empty($filter['year_id'])){
		$sql .= " AND da.year_id=".$filter['year_id'];
	  }

	  $sql .= " AND da.deleted_at IS NULL
	  GROUP BY da.district_id";
		return $this->db->query($sql)->getResult();
	}
}
