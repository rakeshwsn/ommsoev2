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
        // printr($data);
        // exit;
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
    public function addRiceFallowCrops($data, $target_id)
    {
        // printr($data);
        // exit;
        // Crop master table
        if (!empty($data['rice_fallow_data'])) {
            # code...

            $this->db->table('ac_target_rice_fallow')->where('target_id', $target_id)->delete();

            // Crop coverage target table
            foreach ($data['rice_fallow_data'] as $crop_id => $area) {
                $ricefallowdata = array(
                    "target_id" => $target_id,
                    "crop_id" => $crop_id,

                    //Check if 'FOLLOWUP' key exists
                    "rice_fallow" => isset($area['rice_fallow']) ? $area['rice_fallow'] : 0
                );
                // printr($ricefallowdata);
                // exit;

                $this->db->table('ac_target_rice_fallow')->insert($ricefallowdata);
            }
        }
    }

    public function viewBlockTarget($filter = [])
    {

        $sql = "SELECT
  block_target.target_id,
  block.name AS block_name,
  block.id AS block_id,
  COALESCE(block_target.year_id , " . getCurrentYearId() . ") AS year_id,
  COALESCE(block_target.season,'" . $filter['season'] . "') AS season,
  COALESCE(block_target.RAGI_SMI, 0.00) AS RAGI_SMI,
  COALESCE(block_target.RAGI_LT, 0.00) AS RAGI_LT,
  COALESCE(block_target.RAGI_LS, 0.00) AS RAGI_LS,
  COALESCE(block_target.LITTLE_MILLET_LT, 0.00) AS LITTLE_MILLET_LT,
  COALESCE(block_target.LITTLE_MILLET_LS, 0.00) AS LITTLE_MILLET_LS,
  COALESCE(block_target.FOXTAIL_MILLET_LS, 0.00) AS FOXTAIL_MILLET_LS,
  COALESCE(block_target.SORGHUM_LS, 0.00) AS SORGHUM_LS,
  COALESCE(block_target.KODO_MILLET_LS, 0.00) AS KODO_MILLET_LS,
  COALESCE(block_target.BARNYARD_MILLET_LS, 0.00) AS BARNYARD_MILLET_LS,
  COALESCE(block_target.PEARL_MILLET_LS, 0.00) AS PEARL_MILLET_LS,
  COALESCE(followup.RAGI_FOLLOWUP, 0.00) AS RAGI_FOLLOWUP,
  COALESCE(followup.LITTLE_MILLET_FOLLOWUP, 0.00) AS LITTLE_MILLET_FOLLOWUP,
  COALESCE(followup.FOXTAIL_MILLET_FOLLOWUP, 0.00) AS FOXTAIL_MILLET_FOLLOWUP,
  COALESCE(followup.SORGHUM_FOLLOWUP, 0.00) AS SORGHUM_FOLLOWUP,
  COALESCE(followup.KODO_MILLET_FOLLOWUP, 0.00) AS KODO_MILLET_FOLLOWUP,
  COALESCE(followup.BARNYARD_MILLET_FOLLOWUP, 0.00) AS BARNYARD_MILLET_FOLLOWUP,
  COALESCE(followup.PEARL_MILLET_FOLLOWUP, 0.00) AS PEARL_MILLET_FOLLOWUP,
  COALESCE(atrf.ragi_rice_fallow, 0.00) AS ragi_rice_fallow,
  COALESCE(atrf.little_rice_fallow, 0.00) AS little_rice_fallow,
  COALESCE(atrf.foxtail_rice_fallow, 0.00) AS foxtail_rice_fallow,
  COALESCE(atrf.sorghum_rice_fallow, 0.00) AS sorghum_rice_fallow,
   COALESCE(atrf.kodo_rice_fallow, 0.00) AS kodo_rice_fallow,
    COALESCE(atrf.barnyard_rice_fallow, 0.00) AS barnyard_rice_fallow,
  COALESCE(atrf.pearl_rice_fallow, 0.00) AS pearl_rice_fallow
 
 
FROM (
  SELECT *
  FROM soe_blocks sb
  WHERE"; // Add your specific conditions here

        if (!empty($filter['district_id'])) {
            $sql .= " sb.district_id = " . $filter['district_id'];
        }

        $sql .= " AND sb.is_program=1
) AS block
LEFT JOIN (
  SELECT
    atm.id AS target_id,
    atm.block_id,
    COALESCE(atm.year_id, " . getCurrentYearId() . ") AS year_id,
    COALESCE(atm.season, '" . $filter['season'] . "') AS season,
    MAX(CASE WHEN ata.crop_id = 1 THEN ata.smi END) AS RAGI_SMI,
    MAX(CASE WHEN ata.crop_id = 1 THEN ata.lt END) AS RAGI_LT,
    MAX(CASE WHEN ata.crop_id = 1 THEN ata.ls END) AS RAGI_LS,
    MAX(CASE WHEN ata.crop_id = 2 THEN ata.lt END) AS LITTLE_MILLET_LT,
    MAX(CASE WHEN ata.crop_id = 2 THEN ata.ls END) AS LITTLE_MILLET_LS,
    MAX(CASE WHEN ata.crop_id = 3 THEN ata.ls END) AS FOXTAIL_MILLET_LS,
    MAX(CASE WHEN ata.crop_id = 4 THEN ata.ls END) AS SORGHUM_LS,
    MAX(CASE WHEN ata.crop_id = 5 THEN ata.ls END) AS KODO_MILLET_LS,
    MAX(CASE WHEN ata.crop_id = 6 THEN ata.ls END) AS BARNYARD_MILLET_LS,
    MAX(CASE WHEN ata.crop_id = 7 THEN ata.ls END) AS PEARL_MILLET_LS
  FROM ac_target_master atm
  LEFT JOIN ac_target_area ata ON atm.id = ata.target_id
  
  WHERE atm.deleted_at IS NULL"; // Add your specific conditions here

        if (!empty($filter['year_id'])) {
            $sql .= " AND atm.year_id = " . $filter['year_id'];
        }
        if (!empty($filter['season'])) {
            $sql .= " AND atm.season = '" . $filter['season'] . "'";
        }

        $sql .= "
  GROUP BY atm.id
) block_target ON block_target.block_id = block.id
LEFT JOIN (
  SELECT
    target_id,
    MAX(CASE WHEN crop_id = 1 THEN followup END) AS RAGI_FOLLOWUP,
    MAX(CASE WHEN crop_id = 2 THEN followup END) AS LITTLE_MILLET_FOLLOWUP,
    MAX(CASE WHEN crop_id = 3 THEN followup END) AS FOXTAIL_MILLET_FOLLOWUP,
    MAX(CASE WHEN crop_id = 4 THEN followup END) AS SORGHUM_FOLLOWUP,
    MAX(CASE WHEN crop_id = 5 THEN followup END) AS KODO_MILLET_FOLLOWUP,
    MAX(CASE WHEN crop_id = 6 THEN followup END) AS BARNYARD_MILLET_FOLLOWUP,
    MAX(CASE WHEN crop_id = 7 THEN followup END) AS PEARL_MILLET_FOLLOWUP
  FROM ac_target_followup_crop
  GROUP BY target_id
) followup ON block_target.target_id = followup.target_id  LEFT JOIN (SELECT
      target_id,
      MAX(CASE WHEN crop_id = 1 THEN rice_fallow END) AS ragi_rice_fallow,
      MAX(CASE WHEN crop_id = 2 THEN rice_fallow END) AS little_rice_fallow,
      MAX(CASE WHEN crop_id = 3 THEN rice_fallow END) AS foxtail_rice_fallow,
      MAX(CASE WHEN crop_id = 4 THEN rice_fallow END) AS sorghum_rice_fallow,
      MAX(CASE WHEN crop_id = 5 THEN rice_fallow END) AS kodo_rice_fallow,
      MAX(CASE WHEN crop_id = 6 THEN rice_fallow END) AS barnyard_rice_fallow,
      MAX(CASE WHEN crop_id = 7 THEN rice_fallow END) AS pearl_rice_fallow
    FROM ac_target_rice_fallow GROUP BY target_id) atrf ON block_target.target_id=atrf.target_id ORDER BY block_name
";
        // echo $sql;
        // exit;
        return $result = $this->db->query($sql)->getResultArray();

    }

    public function getDistrictWiseData($filter = [])
    {

        $sql = "SELECT
    sd.name AS district,
    sd.id AS district_id,
    sd.no_of_block,
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
    followup.KODO_MILLET AS KODO_MILLET_FOLLOWUP,
    rf.ragi_rice_fallow,
    rf.little_rice_fallow,
    rf.foxtail_rice_fallow,
    rf.sorghum_rice_fallow,
    rf.kodo_rice_fallow,
    rf.barnyard_rice_fallow,
    rf.pearl_rice_fallow
   
FROM
    (
        SELECT
            sd.id,
            sd.name,
            COUNT(sb.id) no_of_block
        FROM soe_districts sd
        LEFT JOIN soe_blocks sb ON sd.id = sb.district_id WHERE sb.is_program=1
        GROUP BY sb.district_id
    ) sd
LEFT JOIN
    (
        SELECT
            tm.id AS target_id,
            tm.district_id,
            SUM(CASE WHEN c.crop_id = 1 THEN ta.smi END) AS RAGI,
            SUM(CASE WHEN c.crop_id = 1 THEN ta.lt END) AS RAGI_LT,
            SUM(CASE WHEN c.crop_id = 1 THEN ta.ls END) AS RAGI_LS,
            SUM(CASE WHEN c.crop_id = 2 THEN ta.smi END) AS LITTLE_MILLET,
            SUM(CASE WHEN c.crop_id = 2 THEN ta.lt END) AS LITTLE_MILLET_LT,
            SUM(CASE WHEN c.crop_id = 2 THEN ta.ls END) AS LITTLE_MILLET_LS,
            SUM(CASE WHEN c.crop_id = 3 THEN ta.smi END) AS FOXTAIL_MILLET,
            SUM(CASE WHEN c.crop_id = 3 THEN ta.lt END) AS FOXTAIL_MILLET_LT,
            SUM(CASE WHEN c.crop_id = 3 THEN ta.ls END) AS FOXTAIL_MILLET_LS,
            SUM(CASE WHEN c.crop_id = 4 THEN ta.smi END) AS SORGHUM,
            SUM(CASE WHEN c.crop_id = 4 THEN ta.lt END) AS SORGHUM_LT,
            SUM(CASE WHEN c.crop_id = 4 THEN ta.ls END) AS SORGHUM_LS,
            SUM(CASE WHEN c.crop_id = 5 THEN ta.smi END) AS PEARL_MILLET,
            SUM(CASE WHEN c.crop_id = 5 THEN ta.lt END) AS PEARL_MILLET_LT,
            SUM(CASE WHEN c.crop_id = 5 THEN ta.ls END) AS PEARL_MILLET_LS,
            SUM(CASE WHEN c.crop_id = 6 THEN ta.smi END) AS BARNYARD_MILLET,
            SUM(CASE WHEN c.crop_id = 6 THEN ta.lt END) AS BARNYARD_MILLET_LT,
            SUM(CASE WHEN c.crop_id = 6 THEN ta.ls END) AS BARNYARD_MILLET_LS,
            SUM(CASE WHEN c.crop_id = 7 THEN ta.smi END) AS KODO_MILLET,
            SUM(CASE WHEN c.crop_id = 7 THEN ta.lt END) AS KODO_MILLET_LT,
            SUM(CASE WHEN c.crop_id = 7 THEN ta.ls END) AS KODO_MILLET_LS
        FROM ac_target_master tm
        LEFT JOIN ac_target_area ta ON tm.id = ta.target_id
        LEFT JOIN (SELECT *, id AS crop_id FROM ac_crops) c ON ta.crop_id = c.id
        WHERE";

        if (!empty($filter['year_id'])) {
            $sql .= " tm.year_id = " . $filter['year_id'];
        }
        if (!empty($filter['season'])) {
            $sql .= " AND tm.season = '" . $filter['season'] . "'";
        }

        $sql .= "AND tm.deleted_at IS NULL
        GROUP BY tm.district_id
    ) district_target ON district_target.district_id = sd.id
LEFT JOIN
    (
        SELECT
            fc.target_id,
            atm.district_id,
            SUM(CASE WHEN fc.crop_id = 1 THEN followup END) AS RAGI,
            SUM(CASE WHEN fc.crop_id = 2 THEN followup END) AS LITTLE_MILLET,
            SUM(CASE WHEN fc.crop_id = 3 THEN followup END) AS FOXTAIL_MILLET,
            SUM(CASE WHEN fc.crop_id = 4 THEN followup END) AS SORGHUM,
            SUM(CASE WHEN fc.crop_id = 5 THEN followup END) AS PEARL_MILLET,
            SUM(CASE WHEN fc.crop_id = 6 THEN followup END) AS BARNYARD_MILLET,
            SUM(CASE WHEN fc.crop_id = 7 THEN followup END) AS KODO_MILLET
        FROM ac_target_followup_crop fc
        LEFT JOIN ac_target_master atm ON fc.target_id = atm.id
        WHERE";

        if (!empty($filter['year_id'])) {
            $sql .= " atm.year_id = " . $filter['year_id'];
        }
        if (!empty($filter['season'])) {
            $sql .= " AND atm.season = '" . $filter['season'] . "'";
        }

        $sql .= " AND atm.deleted_at IS NULL
        GROUP BY atm.district_id
    ) followup ON followup.district_id = sd.id  LEFT JOIN (SELECT
      tr.target_id,
      atm.district_id,
      SUM(CASE WHEN tr.crop_id = 1 THEN tr.rice_fallow END) AS ragi_rice_fallow,
      SUM(CASE WHEN tr.crop_id = 2 THEN tr.rice_fallow END) AS little_rice_fallow,
      SUM(CASE WHEN tr.crop_id = 3 THEN tr.rice_fallow END) AS foxtail_rice_fallow,
      SUM(CASE WHEN tr.crop_id = 4 THEN tr.rice_fallow END) AS sorghum_rice_fallow,
      SUM(CASE WHEN tr.crop_id = 5 THEN tr.rice_fallow END) AS kodo_rice_fallow,
      SUM(CASE WHEN tr.crop_id = 6 THEN tr.rice_fallow END) AS barnyard_rice_fallow,
      SUM(CASE WHEN tr.crop_id = 7 THEN tr.rice_fallow END) AS pearl_rice_fallow
    FROM ac_target_rice_fallow tr
      LEFT JOIN ac_target_master atm
        ON tr.target_id = atm.id  WHERE";

        if (!empty($filter['year_id'])) {
            $sql .= " atm.year_id = " . $filter['year_id'];
        }
        if (!empty($filter['season'])) {
            $sql .= " AND atm.season = '" . $filter['season'] . "'";
        }

        $sql .= " AND atm.deleted_at IS NULL GROUP BY atm.district_id) rf
    ON rf.district_id = sd.id ORDER BY sd.name";

        // echo $sql;
        // exit;
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
            fl.followup,
            rf.rice_fallow
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
          LEFT JOIN (SELECT
      ata.id,
      ata.crop_id,
      ata.rice_fallow 
    FROM ac_target_rice_fallow ata
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
        $sql .= " AND atm.deleted_at IS NULL) rf
          ON ac.id = rf.crop_id
    WHERE ac.crops IS NOT NULL";
        // echo $sql;
        // exit;


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
        // echo $sql;
        // exit;
        return $this->db->query($sql)->getResultArray();

    }
    public function getMilletWiseTarget($filter)
    {
        $year_id = $filter['year_id'];
        $season = $filter['season'];

        $block_id = isset($filter['block_id']) ? $filter['block_id'] : null;
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
        if (!empty($filter['block_id'])) {
            $sql .= " AND atm.block_id = " . $filter['block_id'];
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
        if (!empty($filter['block_id'])) {
            $sql .= " AND acc.block_id = " . $filter['block_id'];
        }

        $sql .= " AND acc.status = 1
    GROUP BY aap.crop_id
) ach ON ac.id = ach.crop_id;
";

        // Execute $sql query


        return $this->db->query($sql)->getResultArray();
    }
    public function getDistTargetVsAchievement($filter)
    {
        $year_id = $filter['year_id'];
        $season = $filter['season'];
        $district_id = $filter['district_id'];
        $sql = "SELECT
    sb.id AS block_id,
    sb.name AS block,
    COALESCE(tar.area_ + tar.fup, 0) AS target_area,
    COALESCE((ach.smi + ach.lt + ach.ls + ach.fup_area), 0) AS ach_area
FROM
    (SELECT * FROM soe_blocks WHERE is_program=1) sb
LEFT JOIN (
    SELECT
        atm.block_id,
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
    WHERE atm.deleted_at IS NULL";

        if (!empty($filter['season'])) {
            $sql .= " AND LOWER(atm.season) = '" . $filter['season'] . "'";
        }
        if (!empty($filter['year_id'])) {
            $sql .= " AND atm.year_id = " . $filter['year_id'];
        }

        $sql .= " GROUP BY
    atm.block_id
) tar ON tar.block_id = sb.id
LEFT JOIN (
    SELECT
        acc.block_id,
        SUM(aap.smi) AS smi,
        SUM(aap.lt) AS lt,
        SUM(ls) AS ls,
        COALESCE(SUM(fup.area), 0) AS fup_area
    FROM
        ac_crop_coverage acc
    LEFT JOIN (
        SELECT
            crop_coverage_id,
            SUM(smi) smi,
            SUM(ls) ls,
            SUM(lt) lt
        FROM ac_area_practices
        GROUP BY crop_coverage_id
    ) aap ON acc.id = aap.crop_coverage_id
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
        acc.deleted_at IS NULL AND acc.status = 1";

        if (!empty($filter['season'])) {
            $sql .= " AND LOWER(acc.season) = '" . $filter['season'] . "'";
        }
        if (!empty($filter['year_id'])) {
            $sql .= " AND acc.year_id = " . $filter['year_id'];
        }

        $sql .= " GROUP BY
    acc.block_id
) ach ON ach.block_id = sb.id
WHERE";
        if (!empty($filter['district_id'])) {
            $sql .= "  sb.district_id = " . $filter['district_id'];
        }

        $sql .= " ORDER BY block;";

        // echo $sql;
        // exit;

        return $this->db->query($sql)->getResultArray();

    }



}
?>