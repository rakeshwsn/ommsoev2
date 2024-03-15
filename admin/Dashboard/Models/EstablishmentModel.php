<?php
namespace Admin\Dashboard\Models;
use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class EstablishmentModel extends Model
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
	

	
	public function getAll()
	{
		$sql = "SELECT
		e.id,
		e.district_id,
		d.name district,
		e.main_center,
		e.sub_center,
		e.establishment_type,
		e.created_at,
		e.deleted_at
	  FROM dashboard_establishment e
		LEFT JOIN soe_districts d
		  ON e.district_id = d.id
	  WHERE e.deleted_at IS NULL";
	  $sql .=  " GROUP BY e.establishment_type";
		return $this->db->query($sql)->getResult();

	}
	public function getDistricts($filter=[]){
		$sql = "SELECT
			e.id,
			e.district_id,
			d.name district,
			e.main_center,
			e.sub_center,
			e.establishment_type,
			e.created_at,
			e.deleted_at
		  FROM dashboard_establishment e
			LEFT JOIN soe_districts d
			  ON e.district_id = d.id
		  WHERE e.deleted_at IS NULL";
		  if(!empty($filter['establishment_type'])){
			$sql .= " AND e.establishment_type = '".$filter['establishment_type']."'";
		  }
		$sql .= " ORDER BY e.establishment_type";
	
		return $this->db->query($sql)->getResult();
	}
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
		sd.name districts,
		sum_dddm.sum_of_gps,
		sum_dddm.sum_of_villages,
		sum_dddm.sum_of_farmers,
		sum_dddm.sum_of_blocks,
		sum_dddm.sum_districts
	  FROM dashboard_district_map ddm
		LEFT JOIN soe_districts sd
		  ON ddm.district_id = sd.id
		LEFT JOIN (SELECT
			SUM(gps) sum_of_gps,
			SUM(villages) sum_of_villages,
			SUM(tentative_farmers) sum_of_farmers,
			SUM(blocks) sum_of_blocks,
			COUNT(district_id) sum_districts
		  FROM dashboard_district_map
		  WHERE deleted_at IS NULL) sum_dddm
		  ON 1 = 1
	  WHERE ddm.deleted_at IS NULL";

		return $this->db->query($sql)->getResult();
	}
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
			ON b.district_id = d.id WHERE b.is_program=1
		GROUP BY d.id) dist
		LEFT JOIN dashboard_establishment chc
		  ON chc.district_id = dist.district_id LEFT JOIN dashboard_establishment cmsc ON dist.district_id = cmsc.district_id
	  WHERE chc.deleted_at IS NULL AND cmsc.deleted_at IS NULL
	  AND chc.establishment_type = 'CHC'  AND cmsc.establishment_type='CMSC'";

		return $this->db->query($sql)->getResult();
	}


}
