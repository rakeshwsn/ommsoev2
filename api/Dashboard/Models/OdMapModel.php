<?php

namespace Api\Dashboard\Models;

use CodeIgniter\Model;

class OdMapModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'dashboard_district_map';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;
	protected $allowedFields        = [];

	// Dates
	protected $useTimestamps        = false;
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

	public function getestablishmentmap()
	{
		$sql = "SELECT
		ddm.id,
		ddm.district_id,
		ddm.blocks,
		ddm.gps total_gps,
		ddm.villages total_villages,
		ddm.created_at,
		ddm.year_id,
		ddm.tentative_farmers total_farmers,
		sd.name districts
	  FROM dashboard_district_map ddm
		LEFT JOIN soe_districts sd
		  ON ddm.district_id = sd.id
	  WHERE ddm.deleted_at IS NULL";

		return $this->db->query($sql)->getResult();
	}

	public function getEstablishSumData()
	{
		$sql = "SELECT
		SUM(gps) sum_of_gps,
		SUM(villages) sum_of_villages,
		SUM(tentative_farmers) sum_of_farmers,
		SUM(blocks) sum_of_blocks,
		COUNT(district_id) sum_districts
		FROM dashboard_district_map
		WHERE deleted_at IS NULL";

		return $this->db->query($sql)->getResult();
	}
}
