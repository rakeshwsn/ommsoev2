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
  sd.id district_id,
  sd.name district,
  ddm.blocks,
  ddm.gps,
  ddm.villages,
  ddm.tentative_farmers farmers,
  e.total_cmsc chcs,
  e.total_chc cmscs
FROM soe_districts sd
  LEFT JOIN dashboard_district_map ddm
    ON sd.id = ddm.district_id
  LEFT JOIN (SELECT
      id,
      district_id,
    SUM(CASE WHEN establishment_type = 'CHC' THEN main_center + sub_center ELSE 0 END) AS total_chc,
    SUM(CASE WHEN establishment_type = 'CMSC' THEN main_center + sub_center ELSE 0 END) AS total_cmsc
    FROM dashboard_establishment
    WHERE deleted_at IS NULL
    GROUP BY district_id) e
    ON sd.id = e.district_id WHERE ddm.deleted_at IS NULL";
		 return $this->db->query($sql)->getResult(); 
	}

	public function getSummary($year_id){
		$sql="SELECT
        y.id year_id,
        y.name year,
		COUNT(district_id) total_districts,
		SUM(blocks) total_blocks,
		SUM(gps) total_gps,
		SUM(villages) total_villages,
		SUM(tentative_farmers) total_farmers,
		SUM(chcs) total_chc,
		SUM(cmscs) total_cmsc,
        SUM(demo_area) demo_area
	  FROM dashboard_district_map dm
	  LEFT JOIN dashboard_years y ON dm.year_id=y.id 
  LEFT JOIN (SELECT
      year_id,
      SUM(achievement) demo_area
    FROM dashboard_areacoverage
    WHERE deleted_at IS NULL) ac
    ON ac.year_id = dm.year_id
WHERE dm.deleted_at IS NULL
AND dm.year_id =$year_id";

		return $this->db->query($sql)->getFirstRow();
	}
	
}
