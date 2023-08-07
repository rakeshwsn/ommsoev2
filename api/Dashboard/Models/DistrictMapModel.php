<?php
namespace Api\Dashboard\Models;
use CodeIgniter\Model;

class DistrictMapModel extends Model
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
	
	public function getMapData(){
		$sql="SELECT
		ddm.year_id,
		ddm.district_id,
		sd.name district,
		ddm.blocks,
		ddm.gps,
		ddm.villages,
		ddm.farmers,
		ddm.chcs,
		ddm.cmscs
	  FROM dashboard_district_map ddm
		LEFT JOIN soe_districts sd
		  ON ddm.district_id = sd.id";
		 return $this->db->query($sql)->getResult(); 
	}

	public function summeryData(){

		$sql="SELECT
		year_id,
		dy.name `year`,
		COUNT(district_id) total_districts,
		SUM(blocks) total_blocks,
		SUM(gps) total_gps,
		SUM(villages) total_villages,
		SUM(farmers) total_farmers,
		SUM(chcs) total_chc,
		SUM(cmscs) total_cmsc,
		SUM(crop_area) total_area
	  FROM dashboard_district_map
		LEFT JOIN dashboard_years dy
		  ON dy.id = year_id
	  GROUP BY year_id";
		return $this->db->query($sql)->getResult(); 
	}
	
}
