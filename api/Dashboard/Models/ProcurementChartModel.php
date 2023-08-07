<?php

namespace Api\Dashboard\Models;

use CodeIgniter\Model;

class ProcurementChartModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'dashboard_procurement';
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

	public function getYearwisedata($filter=[]) {
		$sql = "SELECT
		p.year_id,
		y.name `year`,
		count(p.district_id) total_districts,
		SUM(p.farmers) total_farmers,
		SUM(p.quantity) total_quantity,
  		ROUND((SUM(p.quantity) * srp.minimum)/100000,2) total_amount
	  FROM (SELECT * FROM dashboard_procurement WHERE quantity>0) p
		LEFT JOIN dashboard_years y
		  ON y.id = p.year_id
		LEFT JOIN dashboard_summary_ragi_procurement srp
		  ON srp.year_id = p.year_id
	  WHERE p.deleted_at IS NULL";
	  if(!empty($filter['district_id'])){
		$sql .= " AND district_id=".$filter['district_id'];
	  }
	  $sql .= " GROUP BY p.year_id
	  ORDER BY y.start_date";
// echo $sql;exit;
		return $this->db->query($sql)->getResult();
	}
}
