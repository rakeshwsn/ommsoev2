<?php

namespace Api\Dashboard\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class EstablishmentChartModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'dashboard_establishment';
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

	public function getestablishment($filter = [])
	{
		$sql = "SELECT
		dist.district_id,
		dist.district,
		dist.blocks,
		chc.main_center + chc.sub_center chc,
		cmsc.main_center+cmsc.sub_center cmsc
	  FROM (SELECT
		  d.id district_id,
		  d.name district,
		  COUNT(b.id) blocks
		FROM soe_districts d
		  LEFT JOIN soe_blocks b
			ON b.district_id = d.id
		GROUP BY d.id) dist
		LEFT JOIN dashboard_establishment chc
		  ON chc.district_id = dist.district_id LEFT JOIN dashboard_establishment cmsc ON dist.district_id = cmsc.district_id
	  WHERE chc.deleted_at IS NULL AND cmsc.deleted_at IS NULL
	  AND chc.establishment_type = 'CHC'  AND cmsc.establishment_type='CMSC'";

		return $this->db->query($sql)->getResult();
	}
}
