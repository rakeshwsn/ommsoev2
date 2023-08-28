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
            $targetdata = [
                "target_id" => $target_id,
                "crop_id" => $crop_id,
                "smi" => isset($area['smi']) ? $area['smi'] : 0,
                // Check if 'SMI' key exists
                "lt" => isset($area['lt']) ? $area['lt'] : 0,
                // Check if 'LT' key exists
                "ls" => isset($area['ls']) ? $area['ls'] : 0

            ];
            $this->db->table('ac_target_area')->insert($targetdata);

        }
    }
    public function addFollowUpCrops($data, $target_id)
    {
        // Crop master table

        $this->db->table('ac_target_followup_crop')->where('target_id', $target_id)->delete();

        // Crop coverage target table
        foreach ($data['followup_data'] as $crop_id => $area) {
            $followupdata = array(
                "target_id" => $target_id,
                "crop_id" => $crop_id,

                //Check if 'FOLLOWUP' key exists
                "followup" => isset($area['followup']) ? $area['followup'] : 0
            );

            $this->db->table('ac_target_followup_crop')->insert($followupdata);
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
        sb.name AS block,
        sb.id AS block_id,
        sb.district_id,
        block_target.year_id,
        block_target.season,
        block_target.RAGI AS RAGI_SMI,
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
        followup.RAGI AS RAGI_FOLLOWUP,
        followup.LITTLE_MILLET AS LITTLE_MILLET_FOLLOWUP,
        followup.FOXTAIL_MILLET AS FOXTAIL_MILLET_FOLLOWUP,
        followup.SORGHUM AS SORGHUM_FOLLOWUP,
        followup.PEARL_MILLET AS PEARL_MILLET_FOLLOWUP,
        followup.BARNYARD_MILLET AS BARNYARD_MILLET_FOLLOWUP,
        followup.KODO_MILLET AS KODO_MILLET_FOLLOWUP
    FROM soe_blocks sb
    LEFT JOIN (
        SELECT
            tm.id AS target_id,
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
            MAX(CASE WHEN c.crop_id = 7 THEN ta.ls END) AS KODO_MILLET_LS
        FROM ac_target_master tm
        LEFT JOIN ac_target_area ta ON tm.id = ta.target_id
        LEFT JOIN (SELECT *, id AS crop_id FROM ac_crops) c ON ta.crop_id = c.id
        GROUP BY tm.block_id
    ) block_target ON block_target.block_id = sb.id
    LEFT JOIN (
        SELECT
            target_id,
            MAX(CASE WHEN crop_id = 1 THEN followup END) AS RAGI,
            MAX(CASE WHEN crop_id = 2 THEN followup END) AS LITTLE_MILLET,
            MAX(CASE WHEN crop_id = 3 THEN followup END) AS FOXTAIL_MILLET,
            MAX(CASE WHEN crop_id = 4 THEN followup END) AS SORGHUM,
            MAX(CASE WHEN crop_id = 5 THEN followup END) AS PEARL_MILLET,
            MAX(CASE WHEN crop_id = 6 THEN followup END) AS BARNYARD_MILLET,
            MAX(CASE WHEN crop_id = 7 THEN followup END) AS KODO_MILLET
        FROM ac_target_followup_crop
        GROUP BY target_id
    ) followup ON block_target.target_id = followup.target_id
    LEFT JOIN ac_target_master atm ON block_target.target_id = atm.id
    WHERE sb.district_id = $district_id AND atm.deleted_at IS NULL";
        // echo $sql;
        // exit;
        return $result = $this->db->query($sql)->getResultArray();
        // printr($result);
        // exit;
    }

    public function getDistrictWiseData($filter = [])
    {
        $sql = "SELECT
  sd.name AS district,
  sd.id AS district_id,
  sd.no_of_block,
  district_target.year_id,
  district_target.season,
  district_target.RAGI AS RAGI_SMI,
  district_target.RAGI_LT,
  district_target.RAGI_LS,
  district_target.LITTLE_MILLET_LT,
  district_target.LITTLE_MILLET_LS,
  district_target.FOXTAIL_MILLET_LT,
  district_target.FOXTAIL_MILLET_LS,
  district_target.SORGHUM_LT,
  district_target.SORGHUM_LS,
  district_target.PEARL_MILLET_LT,
  district_target.PEARL_MILLET_LS,
  district_target.BARNYARD_MILLET_LT,
  district_target.BARNYARD_MILLET_LS,
  district_target.KODO_MILLET_LT,
  district_target.KODO_MILLET_LS,
  followup.RAGI AS RAGI_FOLLOWUP,
  followup.LITTLE_MILLET AS LITTLE_MILLET_FOLLOWUP,
  followup.FOXTAIL_MILLET AS FOXTAIL_MILLET_FOLLOWUP,
  followup.SORGHUM AS SORGHUM_FOLLOWUP,
  followup.PEARL_MILLET AS PEARL_MILLET_FOLLOWUP,
  followup.BARNYARD_MILLET AS BARNYARD_MILLET_FOLLOWUP,
  followup.KODO_MILLET AS KODO_MILLET_FOLLOWUP
FROM (SELECT
    sd.id,
    sd.name,
    COUNT(sb.id) no_of_block
  FROM soe_districts sd
    LEFT JOIN soe_blocks sb
      ON sd.id = sb.district_id
  GROUP BY sb.district_id) sd
  LEFT JOIN (SELECT
      tm.id AS target_id,
      tm.district_id,
      tm.year_id,
      tm.season,
      sum(CASE WHEN c.crop_id = 1 THEN ta.smi END) AS RAGI,
      sum(CASE WHEN c.crop_id = 1 THEN ta.lt END) AS RAGI_LT,
      sum(CASE WHEN c.crop_id = 1 THEN ta.ls END) AS RAGI_LS,
      sum(CASE WHEN c.crop_id = 2 THEN ta.smi END) AS LITTLE_MILLET,
      sum(CASE WHEN c.crop_id = 2 THEN ta.lt END) AS LITTLE_MILLET_LT,
      sum(CASE WHEN c.crop_id = 2 THEN ta.ls END) AS LITTLE_MILLET_LS,
      sum(CASE WHEN c.crop_id = 3 THEN ta.smi END) AS FOXTAIL_MILLET,
      sum(CASE WHEN c.crop_id = 3 THEN ta.lt END) AS FOXTAIL_MILLET_LT,
      sum(CASE WHEN c.crop_id = 3 THEN ta.ls END) AS FOXTAIL_MILLET_LS,
      sum(CASE WHEN c.crop_id = 4 THEN ta.smi END) AS SORGHUM,
      sum(CASE WHEN c.crop_id = 4 THEN ta.lt END) AS SORGHUM_LT,
      sum(CASE WHEN c.crop_id = 4 THEN ta.ls END) AS SORGHUM_LS,
      sum(CASE WHEN c.crop_id = 5 THEN ta.smi END) AS PEARL_MILLET,
      sum(CASE WHEN c.crop_id = 5 THEN ta.lt END) AS PEARL_MILLET_LT,
      sum(CASE WHEN c.crop_id = 5 THEN ta.ls END) AS PEARL_MILLET_LS,
      sum(CASE WHEN c.crop_id = 6 THEN ta.smi END) AS BARNYARD_MILLET,
      sum(CASE WHEN c.crop_id = 6 THEN ta.lt END) AS BARNYARD_MILLET_LT,
      sum(CASE WHEN c.crop_id = 6 THEN ta.ls END) AS BARNYARD_MILLET_LS,
      sum(CASE WHEN c.crop_id = 7 THEN ta.smi END) AS KODO_MILLET,
      sum(CASE WHEN c.crop_id = 7 THEN ta.lt END) AS KODO_MILLET_LT,
      sum(CASE WHEN c.crop_id = 7 THEN ta.ls END) AS KODO_MILLET_LS
    FROM ac_target_master tm
      LEFT JOIN ac_target_area ta
        ON tm.id = ta.target_id
      LEFT JOIN (SELECT
          *,
          id AS crop_id
        FROM ac_crops) c
        ON ta.crop_id = c.id
    GROUP BY tm.district_id) district_target
    ON district_target.district_id = sd.id
  LEFT JOIN (SELECT
      fc.target_id,
      atm.district_id,
      sum(CASE WHEN fc.crop_id = 1 THEN followup END) AS RAGI,
      sum(CASE WHEN fc.crop_id = 2 THEN followup END) AS LITTLE_MILLET,
      sum(CASE WHEN fc.crop_id = 3 THEN followup END) AS FOXTAIL_MILLET,
      sum(CASE WHEN fc.crop_id = 4 THEN followup END) AS SORGHUM,
      sum(CASE WHEN fc.crop_id = 5 THEN followup END) AS PEARL_MILLET,
      sum(CASE WHEN fc.crop_id = 6 THEN followup END) AS BARNYARD_MILLET,
      sum(CASE WHEN fc.crop_id = 7 THEN followup END) AS KODO_MILLET
    FROM ac_target_followup_crop fc
      LEFT JOIN ac_target_master atm
        ON fc.target_id = atm.id
    GROUP BY atm.district_id) followup
    ON followup.district_id=sd.id";
        return $this->db->query($sql)->getResultArray();
    }

    public function getBlockTargets($filter = [])
    {
        $sql = "SELECT
            ac.id,
            ac.crops,
            cd.smi,
            cd.ls,
            cd.lt,
            fl.followup
FROM ac_crops ac
  LEFT JOIN (SELECT
      ata.id,
	  ata.crop_id,
      ata.lt,
      ata.ls,
      ata.smi
    FROM ac_target_area ata
      LEFT JOIN ac_target_master atm
        ON ata.target_id = atm.id
		WHERE 1 = 1";


        if (!empty($filter['block_id'])) {
            $sql .= " AND atm.block_id = " . $filter['block_id'];
        }
        if (!empty($filter['year_id'])) {
            $sql .= " AND atm.year_id = " . $filter['year_id'];
        }
        if (!empty($filter['season'])) {
            $sql .= " AND atm.season = '" . $filter['season'] . "'";
        }

        $sql .= " AND atm.deleted_at IS NULL) cd
    ON ac.id = cd.crop_id
 LEFT JOIN (SELECT
      ata.id,
      ata.crop_id,
      ata.followup
    FROM ac_target_followup_crop ata
      LEFT JOIN ac_target_master atm
        ON ata.target_id = atm.id

    WHERE 1 = 1";

        if (!empty($filter['block_id'])) {
            $sql .= " AND atm.block_id = " . $filter['block_id'];
        }
        if (!empty($filter['year_id'])) {
            $sql .= " AND atm.year_id = " . $filter['year_id'];
        }
        if (!empty($filter['season'])) {
            $sql .= " AND atm.season = '" . $filter['season'] . "'";
        }
        $sql .= " AND atm.deleted_at IS NULL) fl
		 ON ac.id = fl.crop_id
    WHERE ac.crops IS NOT NULL";



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

    public function getDistrictWiseTarget($filter)
    {
        $year_id = $filter['year_id'];
        $season = $filter['season'];

        $sql = "SELECT
    sd.id AS district_id,
    sd.name AS district,
    COALESCE(tar.area_ + tar.fup, 0) AS target_area,
    COALESCE((ach.smi + ach.lt + ach.ls + ach.fup_area), 0) AS ach_area
FROM
    soe_districts sd
LEFT JOIN (
    SELECT
        atm.district_id,
        SUM(ar.area_) AS area_,
        SUM(fup.area_) AS fup
    FROM
        ac_target_master atm
    LEFT JOIN (
        SELECT
            ata.target_id,
            SUM(ata.smi + ata.lt + ata.ls) AS area_
        FROM
            ac_target_area ata
        GROUP BY
            ata.target_id
    ) ar ON ar.target_id = atm.id
    LEFT JOIN (
        SELECT
            atfc.target_id,
            SUM(atfc.followup) AS area_
        FROM
            ac_target_followup_crop atfc
        GROUP BY
            atfc.target_id
    ) fup ON atm.id = fup.target_id
    GROUP BY
        atm.district_id
) tar ON tar.district_id = sd.id
LEFT JOIN (
    SELECT
        acc.district_id,
        SUM(aap.smi) AS smi,
        SUM(aap.lt) AS lt,
        SUM(ls) AS ls,
        COALESCE(SUM(fup.area), 0) AS fup_area
    FROM
        ac_crop_coverage acc
    LEFT JOIN (SELECT
          crop_coverage_id,
          SUM(smi) smi,
          SUM(ls) ls,
          SUM(lt) lt
        FROM ac_area_practices
        GROUP BY crop_coverage_id) aap
        ON acc.id = aap.crop_coverage_id
    LEFT JOIN (
        SELECT
            aafu.crop_coverage_id,
            SUM(aafu.area) AS area
        FROM
            ac_area_follow_up aafu
        GROUP BY
            aafu.crop_coverage_id
    ) fup ON acc.id = fup.crop_coverage_id
    WHERE
        acc.deleted_at IS NULL AND status=1";

        if (!empty($filter['season'])) {
            $sql .= " AND LOWER(acc.season) = '" . $filter['season'] . "'";
        }
        if (!empty($filter['year_id'])) {
            $sql .= " AND acc.year_id = " . $filter['year_id'];
        }

        $sql .= "
    GROUP BY
        acc.district_id
) ach ON ach.district_id = sd.id ORDER BY district";

        return $this->db->query($sql)->getResultArray();

    }
    public function getMilletWiseTarget($filter)
    {
        $year_id = $filter['year_id'];
        $season = $filter['season'];
        $sql = "
SELECT
  ac.id AS crop_id,
  ac.crops AS crop,
  COALESCE(tar.total, 0) + COALESCE(tar.total_fup, 0) AS target_area,
  COALESCE(ach.smi, 0) + COALESCE(ach.lt, 0) + COALESCE(ach.ls, 0) AS achievement_area
FROM ac_crops ac
LEFT JOIN (
    SELECT
      ata.crop_id,
      SUM(ata.smi + ata.lt + ata.ls) AS total,
      COALESCE(SUM(fup.followup), 0) AS total_fup
    FROM ac_target_area ata
    LEFT JOIN ac_target_followup_crop fup ON ata.target_id = fup.target_id AND ata.crop_id = fup.crop_id
    LEFT JOIN ac_target_master atm ON ata.target_id = atm.id
    WHERE atm.deleted_at IS NULL";

        if (!empty($filter['season'])) {
            $sql .= " AND LOWER(atm.season) = '" . $filter['season'] . "'";
        }
        if (!empty($filter['year_id'])) {
            $sql .= " AND atm.year_id = " . $filter['year_id'];
        }

        $sql .= " GROUP BY ata.crop_id
) tar ON ac.id = tar.crop_id
LEFT JOIN (
    SELECT
        aap.crop_id,
        COALESCE(SUM(aap.smi), 0) AS smi,
        COALESCE(SUM(aap.lt), 0) AS lt,
        COALESCE(SUM(aap.ls), 0) AS ls
    FROM ac_area_practices aap
    LEFT JOIN ac_crop_coverage acc ON aap.crop_coverage_id = acc.id
    WHERE acc.deleted_at IS NULL";

        if (!empty($filter['season'])) {
            $sql .= " AND LOWER(acc.season) = '" . $filter['season'] . "'";
        }
        if (!empty($filter['year_id'])) {
            $sql .= " AND acc.year_id = " . $filter['year_id'];
        }

        $sql .= " AND acc.status = 1
    GROUP BY aap.crop_id
) ach ON ac.id = ach.crop_id;
";

        // Execute $sql query


        return $this->db->query($sql)->getResultArray();
    }



}
?>