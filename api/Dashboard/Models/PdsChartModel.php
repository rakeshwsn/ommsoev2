<?php

namespace Api\Dashboard\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class PdsChartModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'dashboard_pds';
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



	public function getYearwisepds() {
		$sql = "SELECT
		p.distributed_year_id,
		y.name `year`,
		p.quantity,
		COUNT(p.district_id) total_district,
		p.card_holders_benifited,
		SUM(p.quantity) total_quantity,
		SUM(p.card_holders_benifited) total_chb
	  FROM (SELECT * FROM dashboard_pds WHERE quantity>0) p
		LEFT JOIN dashboard_years y
		  ON y.id = p.distributed_year_id
	  WHERE p.deleted_at IS NULL";
	 
	  $sql .= " GROUP BY p.distributed_year_id
	  ORDER BY y.start_date";
		// printr($sql);exit;
		return $this->db->query($sql)->getResult();
	}
}
