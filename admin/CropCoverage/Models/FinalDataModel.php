<?php
namespace Admin\CropCoverage\Models;

use CodeIgniter\Model;

class FinalDataModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'ac_final_data_master';
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
    public function addGpCropsData($mergedData)
    {
        // Delete existing records for the given final_data_id
        $finalDataIds = array_unique(array_column($mergedData, 'final_data_id'));

        // Delete existing records for the given final_data_ids
        $this->db->table('ac_final_area_data')->whereIn('final_data_id', $finalDataIds)->delete();
        // Insert the merged data into the database
        foreach ($mergedData as $data) {
            $this->db->table('ac_final_area_data')->insert($data);
        }
    }



    public function getGpsFinalData($filter = [])
    {
        $sql = "SELECT
      fd.id,
  gp.id AS gp_id,
  gp.name,
  COALESCE(no_of_village, 0) AS no_of_village,
  COALESCE(fd.farmers_covered_under_demonstration, 0) AS farmers_covered_under_demonstration,
  COALESCE(fd.farmers_covered_under_followup, 0) AS farmers_covered_under_followup,
  COALESCE(fd.fup_ragi, 0.00) AS fup_ragi,
  COALESCE(fd.fup_lm, 0.00) AS fup_lm,
  COALESCE(fd.fup_fm, 0.00) AS fup_fm,
  COALESCE(fd.fup_sorghum, 0.00) AS fup_sorghum,
  COALESCE(fd.fup_km, 0.00) AS fup_km,
  COALESCE(fd.fup_bm, 0.00) AS fup_bm,
  COALESCE(fd.fup_pm, 0.00) AS fup_pm
 
FROM (
  SELECT *
  FROM soe_grampanchayats sg
  WHERE 1=1"; // Add your specific conditions here

        if (!empty($filter['block_id'])) {
            $sql .= " AND sg.block_id = " . $filter['block_id'];
        } else {
            $sql .= " AND sg.block_id = " . 0;
        }

        $sql .= ") AS gp
LEFT JOIN (
  SELECT
  afdm.id,
    afdm.gp_id,
    afdm.no_of_village,
    afdm.farmers_covered_under_demonstration,
    afdm.farmers_covered_under_followup,
    afdm.fup_ragi,
    afdm.fup_lm,
    afdm.fup_fm,
    afdm.fup_sorghum,
    afdm.fup_km,
    afdm.fup_bm,
    afdm.fup_pm
   
  FROM ac_final_data_master afdm
    LEFT JOIN ac_final_area_data afad ON afdm.id = afad.final_data_id
    LEFT JOIN (SELECT *, id AS crop_id FROM ac_crops) c ON afad.crop_id = c.crop_id
  WHERE afdm.deleted_at IS NULL
  GROUP BY afdm.gp_id
) AS fd
ON gp.id = fd.gp_id
";

        // echo $sql;
        // exit;

        return $this->db->query($sql)->getResultArray();
    }
    public function getGpsDemonData($final_data_id)
    {
        $sql = "SELECT
            ac.id,
            ac.crops,
            cd.final_data_id,
             COALESCE(cd.smi, 0.00)smi,
    COALESCE(cd.lt, 0.00)lt,
      COALESCE(cd.ls, 0.00)ls
FROM ac_crops ac
  LEFT JOIN (SELECT
      afad.id,
	  afad.crop_id,
      afad.final_data_id,
      afad.smi,
      afad.lt,
      afad.ls
    FROM ac_final_area_data afad
      LEFT JOIN ac_final_data_master afdm
        ON afad.final_data_id = afdm.id
		WHERE 1 = 1";


        if (!empty($final_data_id)) {
            $sql .= " AND afad.final_data_id = " . $final_data_id;
        } else {
            $sql .= " AND afad.final_data_id is null";
        }


        $sql .= " AND afdm.deleted_at IS NULL) cd
    ON ac.id = cd.crop_id
    WHERE ac.crops IS NOT NULL";
        // echo $sql;
        // exit;

        return $this->db->query($sql)->getResultArray();
    }


    public function getAreaCoverageFinalReport($filter = [])
    {

        if (!empty($filter['block_id'])) {

            return $this->getByBlockNew($filter);

        } else if (!empty($filter['district_id'])) {

            return $this->getByDistrictNew($filter);

        } else {

            return $this->getAllDistrictsNew($filter);

        }
    }

    public function getByDistrictNew($filter = [])
    {
        $sql = "SELECT
  t1.block_id,
  t1.block,
  t1.total_gp,
  t2.block_id,
  t2.total_village,
  t2.total_demon_farmer,
  t2.total_follow_farmer,
  t2.total_fup,
  SUM(CASE WHEN t3.crops = 'Ragi' THEN t3.smi END) AS ragi_total_smi,
  SUM(CASE WHEN t3.crops = 'Ragi' THEN t3.lt END) AS ragi_total_lt,
  SUM(CASE WHEN t3.crops = 'Ragi' THEN t3.ls END) AS ragi_ls,
  SUM(CASE WHEN t3.crops = 'Little Millet' THEN t3.lt END) AS little_millet_lt,
  SUM(CASE WHEN t3.crops = 'Little Millet' THEN t3.ls END) AS little_millet_ls,
  SUM(CASE WHEN t3.crops = 'Foxtail Millet' THEN t3.ls END) AS foxtail_millet_ls,
  SUM(CASE WHEN t3.crops = 'Sorghum' THEN t3.ls END) AS sorghum_ls,
  SUM(CASE WHEN t3.crops = 'Kodo Millet' THEN t3.ls END) AS kodo_millet_ls,
  SUM(CASE WHEN t3.crops = 'Barnyard Millet' THEN t3.ls END) AS barnyard_millet_ls,
  SUM(CASE WHEN t3.crops = 'Pearl Millet' THEN t3.ls END) AS pearl_millet_ls
FROM (
  SELECT
    sb.id AS block_id,
    sb.name AS block,
    COUNT(sg.block_id) AS total_gp
  FROM soe_blocks sb
  LEFT JOIN soe_grampanchayats sg ON sb.id = sg.block_id
  WHERE sb.district_id = 15 AND sg.deleted_at IS NULL
  GROUP BY sb.id, sb.name
) AS t1
LEFT JOIN (
  SELECT
    afdm.id,
    afdm.block_id,
    SUM(afdm.no_of_village) AS total_village,
    SUM(afdm.farmers_covered_under_demonstration) AS total_demon_farmer,
    SUM(afdm.farmers_covered_under_followup) AS total_follow_farmer,
    (
      SUM(afdm.fup_ragi) +
      SUM(afdm.fup_lm) +
      SUM(afdm.fup_fm) +
      SUM(afdm.fup_sorghum) +
      SUM(afdm.fup_km) +
      SUM(afdm.fup_bm) +
      SUM(afdm.fup_pm)
    ) AS total_fup
  FROM ac_final_data_master afdm
  WHERE afdm.year_id = 2 AND afdm.district_id = 15 AND afdm.season = 'rabi'
  GROUP BY afdm.block_id
) AS t2 ON t1.block_id = t2.block_id
LEFT JOIN (
  SELECT
    afdm.block_id,
    t3.crops,
    SUM(t3.smi) AS smi,
    SUM(t3.lt) AS lt,
    SUM(t3.ls) AS ls
  FROM ac_final_data_master afdm
  LEFT JOIN (
    SELECT
      final_data_id,
      crop_id,
      crops,
      smi,
      lt,
      ls
    FROM ac_final_area_data afad
    LEFT JOIN ac_crops ac ON ac.id = afad.crop_id
  ) AS t3 ON afdm.id = t3.final_data_id
  WHERE afdm.year_id = 2 AND afdm.district_id = 15 AND afdm.season = 'rabi'
  GROUP BY afdm.block_id, t3.crops
) AS t3 ON t2.block_id = t3.block_id
GROUP BY t1.block_id
";
        // echo $sql;
        // exit;

        return $this->db->query($sql)->getResult();
    }

    public function getByBlockNew($filter = [])
    {

        $sql = "SELECT
  gp,
  m.gp_id,
  nur.nursery_raised,nur.balance_smi,nur.balance_lt,
  nur.farmers_covered,
  m.fc_area,
  m.ragi_smi,
  m.ragi_lt,
  m.ragi_ls,
  m.little_millet_lt,
  m.little_millet_ls,
  m.foxtail_ls,
  m.sorghum_ls,
  m.kodo_ls,
  m.barnyard_ls,
  m.pearl_ls
FROM (SELECT
    gp_id,
    gp,
    year_id,
    season,
    start_date,
    SUM(farmers_covered) farmers_covered,
    SUM(fc_area) fc_area,
    SUM(ragi_smi) ragi_smi,
    SUM(ragi_lt) ragi_lt,
    SUM(ragi_ls) ragi_ls,
    SUM(little_millet_lt) little_millet_lt,
    SUM(little_millet_ls) little_millet_ls,
    SUM(foxtail_ls) foxtail_ls,
    SUM(sorghum_ls) sorghum_ls,
    SUM(kodo_ls) kodo_ls,
    SUM(barnyard_ls) barnyard_ls,
    SUM(pearl_ls) pearl_ls
  FROM vw_area_coverage_report_gpwise
  WHERE 1 = 1";
        if (!empty($filter['year_id'])) {
            $sql .= " AND year_id = " . $filter['year_id'];
        }
        if (!empty($filter['season'])) {
            $sql .= " AND LOWER(season) = '" . strtolower($filter['season']) . "'";
        }
        if (!empty($filter['block_id'])) {
            $sql .= " AND block_id=" . $filter['block_id'];
        }
        if (!empty($filter['start_date'])) {
            $sql .= " AND DATE(start_date)=DATE('" . $filter['start_date'] . "')";
        }
        $sql .= " GROUP BY gp_id) m
  LEFT JOIN (WITH nur
      AS
      (SELECT
            acc.district_id,
            acc.block_id,
            acc.gp_id,
            acc.year_id,
            acc.season,
            SUM(an.nursery_raised) nursery_raised,
            SUM(an.balance_smi) balance_smi,
            SUM(an.balance_lt) balance_lt,
            SUM(acc.farmers_covered) farmers_covered,
            acc.start_date
          FROM ac_nursery an
            LEFT JOIN ac_crop_coverage acc
              ON an.crop_coverage_id = acc.id
          WHERE acc.deleted_at IS NULL
          AND acc.status = 1";
        if (!empty($filter['year_id'])) {
            $sql .= " AND year_id = " . $filter['year_id'];
        }
        if (!empty($filter['season'])) {
            $sql .= " AND LOWER(season) = '" . strtolower($filter['season']) . "'";
        }
        if (!empty($filter['block_id'])) {
            $sql .= " AND block_id=" . $filter['block_id'];
        }
        if (!empty($filter['start_date'])) {
            $sql .= " AND DATE(start_date)=DATE('" . $filter['start_date'] . "')";
        }
        $sql .= " GROUP BY DATE(acc.start_date),acc.gp_id)
    SELECT
      n1.gp_id,
      n1.year_id,
      n1.season,
      nursery_raised,
      balance_smi,
      balance_lt,
      start_date,
      n1.farmers_covered
    FROM nur n1
    WHERE DATE(start_date) = (SELECT
        MAX(DATE(n2.start_date))
      FROM nur n2
      WHERE n2.gp_id = n1.gp_id)) nur
    ON nur.gp_id = m.gp_id
ORDER BY m.gp";
        // echo $sql;
        // exit;

        return $this->db->query($sql)->getResult();
    }

    public function getAllDistrictsNew($filter = [])
    {
        $sql = "SELECT
  sd.id,
  sd.name AS district,
  COALESCE(block_data.block_count, 0) AS blocks,
  COALESCE(gp_data.gp_count, 0) AS gps,
  COALESCE(demonstration_data.total_village, 0) AS total_village,
  COALESCE(demonstration_data.total_demon_farmer, 0) AS total_demon_farmer,
  COALESCE(demonstration_data.total_follow_farmer, 0) AS total_follow_farmer,
  COALESCE(demonstration_data.total_fup, 0) AS total_fup,
  SUM(CASE WHEN t3.crops = 'Ragi' THEN t3.smi END) AS ragi_total_smi,
  SUM(CASE WHEN t3.crops = 'Ragi' THEN t3.lt END) AS ragi_total_lt,
  SUM(CASE WHEN t3.crops = 'Ragi' THEN t3.ls END) AS ragi_ls,
  SUM(CASE WHEN t3.crops = 'Little Millet' THEN t3.lt END) AS little_millet_lt,
  SUM(CASE WHEN t3.crops = 'Little Millet' THEN t3.ls END) AS little_millet_ls,
  SUM(CASE WHEN t3.crops = 'Foxtail Millet' THEN t3.ls END) AS foxtail_millet_ls,
  SUM(CASE WHEN t3.crops = 'Sorghum' THEN t3.ls END) AS sorghum_ls,
  SUM(CASE WHEN t3.crops = 'Kodo Millet' THEN t3.ls END) AS kodo_millet_ls,
  SUM(CASE WHEN t3.crops = 'Barnyard Millet' THEN t3.ls END) AS barnyard_millet_ls,
  SUM(CASE WHEN t3.crops = 'Pearl Millet' THEN t3.ls END) AS pearl_millet_ls
FROM soe_districts sd
  LEFT JOIN (SELECT
      district_id,
      COUNT(id) AS block_count
    FROM soe_blocks
    GROUP BY district_id) AS block_data
    ON sd.id = block_data.district_id
  LEFT JOIN (SELECT
      district_id,
      COUNT(id) AS gp_count
    FROM soe_grampanchayats
    GROUP BY district_id) AS gp_data
    ON sd.id = gp_data.district_id
  LEFT JOIN (SELECT
      afdm.district_id,
      SUM(afdm.no_of_village) AS total_village,
      SUM(afdm.farmers_covered_under_demonstration) AS total_demon_farmer,
      SUM(afdm.farmers_covered_under_followup) AS total_follow_farmer,
      SUM(afdm.fup_ragi + afdm.fup_lm + afdm.fup_fm + afdm.fup_sorghum + afdm.fup_km + afdm.fup_bm + afdm.fup_pm) AS total_fup
    FROM ac_final_data_master afdm
    WHERE afdm.year_id = 2
    AND afdm.season = 'rabi'
    GROUP BY afdm.district_id) AS demonstration_data
    ON sd.id = demonstration_data.district_id
  LEFT JOIN (SELECT
      afdm.district_id,
      t3.crops,
      SUM(t3.smi) AS smi,
      SUM(t3.lt) AS lt,
      SUM(t3.ls) AS ls
    FROM ac_final_data_master afdm
      LEFT JOIN (SELECT
          afad.final_data_id,
          afad.crop_id,
          ac.crops AS crops,
          afad.smi,
          afad.lt,
          afad.ls
        FROM ac_final_area_data afad
          LEFT JOIN ac_crops ac
            ON afad.crop_id = ac.id) AS t3
        ON afdm.id = t3.final_data_id
    WHERE afdm.year_id = 2
    AND afdm.season = 'rabi'
    GROUP BY afdm.district_id,
             t3.crops) AS t3
    ON sd.id = t3.district_id
GROUP BY sd.id,
         sd.name
ORDER BY sd.name

";
        // echo $sql;
        // exit;
        return $this->db->query($sql)->getResult();
    }

}
?>