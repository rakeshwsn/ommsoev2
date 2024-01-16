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

	//changed by niranjan
	public function getmapdata_old()
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
	  WHERE ddm.deleted_at IS NULL AND ddm.year_id=7";
// printr($sql);exit;
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
	public function getcurrentyeardata($filter=[]) {
		$sql = "SELECT
		ddm.id,
		ddm.district_id,
		ddm.year_id,
		ddm.tentative_farmers total_farmers,
		ddm.acheivement achievement,
		sd.name districts
	  FROM dashboard_district_map ddm
		LEFT JOIN soe_districts sd
		  ON ddm.district_id = sd.id
	  WHERE ddm.deleted_at IS NULL";

	  if (!empty($filter['year_id'])) {
		$sql .= " AND ddm.year_id=" . $filter['year_id'];
	}



		return $this->db->query($sql)->getResult();
	}

	//added by niranjan
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

	public function getSummary($year_id=''){
		$sql="SELECT
  dm.total_districts,
  dm.total_blocks,
  dm.total_gps,
  dm.total_villages,
  dm.total_farmers,
  dm.total_chc,
  dm.total_cmsc,
  SUM(ac.demo_area) demo_area
FROM (SELECT
    year_id,
    COUNT(district_id) total_districts,
    SUM(blocks) total_blocks,
    SUM(gps) total_gps,
    SUM(villages) total_villages,
    SUM(tentative_farmers) total_farmers,
    SUM(chcs) total_chc,
    SUM(cmscs) total_cmsc
  FROM dashboard_district_map
  WHERE deleted_at IS NULL";
        if($year_id){
            $sql .= " AND year_id =$year_id";
        }
		$sql .= " ) dm
  JOIN (SELECT
      SUM(achievement) demo_area
    FROM dashboard_areacoverage
    WHERE deleted_at IS NULL";
		if($year_id){
            $sql .= " AND year_id =$year_id";
        }
        $sql .= ") ac";

		return $this->db->query($sql)->getFirstRow();
	}
}
