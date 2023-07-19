<?php
namespace Admin\CropCoverage\Models;

use CodeIgniter\Model;

class TargetModel extends Model
{
	protected $DBGroup = 'default';
	protected $table = 'ac_target_master';
	protected $primaryKey = 'id';
	protected $useAutoIncrement = true;
	protected $insertID = 0;
	protected $returnType = 'object';
	protected $useSoftDeletes = true;
	protected $protectFields = false;
	// protected $allowedFields = [
	// 	'block_id',
	// 	'year_id',
	// 	'season',
	// 	'ragi_smi',
	// 	'ragi_lt',
	// 	'ragi_ls',
	// ];

	// Dates
	protected $useTimestamps = true;
	protected $dateFormat = 'datetime';
	protected $createdField = 'created_at';
	protected $updatedField = 'updated_at';
	protected $deletedField = 'deleted_at';

	// Validation
	protected $validationRules = [

	];


	protected $validationMessages = [];
	protected $skipValidation = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks = true;
	protected $beforeInsert = [];
	protected $afterInsert = [];
	protected $beforeUpdate = [];
	protected $afterUpdate = [];
	protected $beforeFind = [];
	protected $afterFind = [];
	protected $beforeDelete = [];
	protected $afterDelete = [];



	public function addTargets($data, $target_id)
	{
		// Crop master table

		$this->db->table('ac_target_area')->where('target_id', $target_id)->delete();

		// Crop coverage target table
		foreach ($data['crop_data'] as $crop_id => $area) {
			$targetdata = array(
				"target_id" => $target_id,
				"crop_id" => $crop_id,
				"smi" => isset($area['smi']) ? $area['smi'] : 0,
				// Check if 'SMI' key exists
				"lt" => isset($area['lt']) ? $area['lt'] : 0,
				// Check if 'LT' key exists
				"ls" => isset($area['ls']) ? $area['ls'] : 0, // Check if 'LS' key exists
			);

			$this->db->table('ac_target_area')->insert($targetdata);
		}
	}

	public function getAll($filter = array())
	{

		$district_id = 0;
		if (!empty($filter['district_id'])) {
			$district_id = $filter['district_id'];
		}

		$sql = "SELECT
		block_target.target_id,
		sb.name block,
		sb.id block_id,
		sb.district_id,
		block_target.year_id,
		block_target.season,
		block_target.RAGI RAGI_SMI,
		block_target.RAGI_LT,
		block_target.RAGI_LS,
		block_target.LITTLE_MILLET_LT,
		block_target.LITTLE_MILLET_LS,
		block_target.FOXTAIL_MILLET_LT,
		block_target.FOXTAIL_MILLET_LS,
		block_target.SORGHUM_LT,
		block_target.SORGHUM_LS,
		block_target.PEARL_MILLET_LT,
		block_target.PEARL_MILLET_LS,
		block_target.BARNYARD_MILLET_LT,
		block_target.BARNYARD_MILLET_LS,
		block_target.KODO_MILLET_LT,
		block_target.KODO_MILLET_LS,
		block_target.PROSO_MILLET_LT,
		block_target.PROSO_MILLET_LS
	  FROM soe_blocks sb
		LEFT JOIN (SELECT
			tm.id target_id,
			tm.block_id,
			tm.year_id,
			tm.season,
			MAX(CASE WHEN c.crop_id = 1 THEN ta.smi END) AS RAGI,
			MAX(CASE WHEN c.crop_id = 1 THEN ta.lt END) AS RAGI_LT,
			MAX(CASE WHEN c.crop_id = 1 THEN ta.ls END) AS RAGI_LS,
			MAX(CASE WHEN c.crop_id = 2 THEN ta.smi END) AS LITTLE_MILLET,
			MAX(CASE WHEN c.crop_id = 2 THEN ta.lt END) AS LITTLE_MILLET_LT,
			MAX(CASE WHEN c.crop_id = 2 THEN ta.ls END) AS LITTLE_MILLET_LS,
			MAX(CASE WHEN c.crop_id = 3 THEN ta.smi END) AS FOXTAIL_MILLET,
			MAX(CASE WHEN c.crop_id = 3 THEN ta.lt END) AS FOXTAIL_MILLET_LT,
			MAX(CASE WHEN c.crop_id = 3 THEN ta.ls END) AS FOXTAIL_MILLET_LS,
			MAX(CASE WHEN c.crop_id = 4 THEN ta.smi END) AS SORGHUM,
			MAX(CASE WHEN c.crop_id = 4 THEN ta.lt END) AS SORGHUM_LT,
			MAX(CASE WHEN c.crop_id = 4 THEN ta.ls END) AS SORGHUM_LS,
			MAX(CASE WHEN c.crop_id = 5 THEN ta.smi END) AS PEARL_MILLET,
			MAX(CASE WHEN c.crop_id = 5 THEN ta.lt END) AS PEARL_MILLET_LT,
			MAX(CASE WHEN c.crop_id = 5 THEN ta.ls END) AS PEARL_MILLET_LS,
			MAX(CASE WHEN c.crop_id = 6 THEN ta.smi END) AS BARNYARD_MILLET,
			MAX(CASE WHEN c.crop_id = 6 THEN ta.lt END) AS BARNYARD_MILLET_LT,
			MAX(CASE WHEN c.crop_id = 6 THEN ta.ls END) AS BARNYARD_MILLET_LS,
			MAX(CASE WHEN c.crop_id = 7 THEN ta.smi END) AS KODO_MILLET,
			MAX(CASE WHEN c.crop_id = 7 THEN ta.lt END) AS KODO_MILLET_LT,
			MAX(CASE WHEN c.crop_id = 7 THEN ta.ls END) AS KODO_MILLET_LS,
			MAX(CASE WHEN c.crop_id = 8 THEN ta.smi END) AS PROSO_MILLET,
			MAX(CASE WHEN c.crop_id = 8 THEN ta.lt END) AS PROSO_MILLET_LT,
			MAX(CASE WHEN c.crop_id = 8 THEN ta.ls END) AS PROSO_MILLET_LS
		  FROM ac_target_master tm
			LEFT JOIN ac_target_area ta
			  ON tm.id = ta.target_id";
		if (!empty($filter['year_id'])) {
			$sql .= " AND tm.year_id=" . $filter['year_id'];
		}
		if (!empty($filter['season'])) {
			$sql .= " AND tm.season=" . $filter['season'];
		}
		$sql .= " LEFT JOIN (SELECT
				*,
				id AS crop_id
			  FROM ac_crops) c
			  ON ta.crop_id = c.id
		  GROUP BY tm.block_id) block_target
		  ON block_target.block_id = sb.id
	  WHERE sb.district_id = $district_id";
		//echo $sql;
		//exit;
		return $this->db->query($sql)->getResultArray();

	}

	public function getBlockTargets($filter = [])
	{
		$sql = "SELECT
			ac.id,
			ac.crops,
			cd.smi,
			cd.ls,
			cd.lt
			FROM ac_crops ac
			LEFT JOIN (SELECT
				ata.crop_id,
				ata.lt,
				ata.ls,
				ata.smi
				FROM ac_target_area ata
				LEFT JOIN ac_target_master atm
					ON ata.target_id = atm.id where 1=1 ";
		if (!empty($filter['block_id'])) {
			$sql .= " and atm.block_id = " . $filter['block_id'];
		}
		if (!empty($filter['year_id'])) {
			$sql .= " and atm.year_id = " . $filter['year_id'];
		}
		if (!empty($filter['season'])) {
			$sql .= " and atm.season = '" . $filter['season'] . "'";
		}

		$sql .= " AND atm.deleted_at IS NULL ) cd
				ON ac.id = cd.crop_id
			WHERE ac.crops IS NOT NULL";
		//echo $sql;
		return $this->db->query($sql)->getResultArray();
	}

	public function getPractices()
	{

		$builder = $this->db->table('ac_crop_practices cp');
		$builder->select('cp.crop_id, cp.practice_id, ac.crops, p.name as practice');
		$builder->join('ac_crops ac', 'ac.id = cp.crop_id', 'left');
		$builder->join('ac_practices p', 'cp.practice_id = p.id', 'left');
		$builder->orderBy('ac.id');
		$query = $builder->get();

		return $query->getResultArray();

	}


}
?>