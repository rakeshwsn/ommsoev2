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
		ddm.district_id,
		ddm.blocks blocks,
		ddm.gps total_gps,
		ddm.villages total_villages,
		sd.name districts,
		da.total_farmer
	  FROM dashboard_district_map ddm
		LEFT JOIN soe_districts sd
		  ON ddm.district_id = sd.id
		LEFT JOIN (SELECT
			da.district_id,
			SUM(da.farmers) total_farmer
		  FROM dashboard_areacoverage da
		  WHERE da.deleted_at IS NULL
		  GROUP BY da.district_id) da
		  ON da.district_id = ddm.district_id ";

		return $this->db->query($sql)->getResult();
	}
	

	
}
