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
COALESCE(fd.year_id, " . getCurrentYearId() . ") AS year_id,
      COALESCE(fd.season, '" . getAftCurrentSeason() . "') AS season,
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
   COALESCE(afdm.year_id, " . getCurrentYearId() . ") AS year_id,
        COALESCE(afdm.season, '" . getAftCurrentSeason() . "') AS season,
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
  WHERE afdm.deleted_at IS NULL"; // Add your specific conditions here

    if (empty($filter['year_id'])) {
      $filter['year_id'] = getCurrentYearId();
    }

    if (empty($filter['season'])) {
      $filter['season'] = getAftCurrentSeason();
    }

    $sql .= " AND COALESCE(afdm.year_id, " . getCurrentYearId() . ") = " . $filter['year_id'];
    $sql .= " AND COALESCE(afdm.season, '" . getAftCurrentSeason() . "') = '" . $filter['season'] . "'";



    $sql .= "
  GROUP BY afdm.gp_id
) AS fd
ON gp.id = fd.gp_id
order by gp.name";

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

      return $this->getGpwiseFinalReport($filter);

    } else if (!empty($filter['district_id'])) {

      return $this->getBlockWiseFinalReport($filter);

    } else {

      return $this->getAllDistrictsFinalReport($filter);

    }
  }
  public function getGpwiseFinalReport($filter = [])
  {
    $sql = "SELECT
  sg.id AS gp_id,
  sg.name AS gp_name,
  COALESCE(afdm.no_of_village, 0) AS total_village,
  COALESCE(afdm.farmers_covered_under_demonstration, 0) AS total_demon_farmer,
  COALESCE(afdm.farmers_covered_under_followup, 0) AS total_follow_farmer,
  COALESCE(
  afdm.fup_ragi+
  afdm.fup_lm +
  afdm.fup_fm +
  afdm.fup_sorghum +
  afdm.fup_km +
  afdm.fup_bm +
  afdm.fup_pm,
  0
) AS total_fup,

  COALESCE(SUM(CASE WHEN t3.crops = 'Ragi' THEN COALESCE(t3.smi, 0) ELSE 0 END), 0) AS ragi_total_smi,
  COALESCE(SUM(CASE WHEN t3.crops = 'Ragi' THEN COALESCE(t3.lt, 0) ELSE 0 END), 0) AS ragi_total_lt,
  COALESCE(SUM(CASE WHEN t3.crops = 'Ragi' THEN COALESCE(t3.ls, 0) ELSE 0 END), 0) AS ragi_ls,
  COALESCE(SUM(CASE WHEN t3.crops = 'Little Millet' THEN COALESCE(t3.lt, 0) ELSE 0 END), 0) AS little_millet_lt,
  COALESCE(SUM(CASE WHEN t3.crops = 'Little Millet' THEN COALESCE(t3.ls, 0) ELSE 0 END), 0) AS little_millet_ls,
  COALESCE(SUM(CASE WHEN t3.crops = 'Foxtail Millet' THEN COALESCE(t3.ls, 0) ELSE 0 END), 0) AS foxtail_millet_ls,
  COALESCE(SUM(CASE WHEN t3.crops = 'Sorghum' THEN COALESCE(t3.ls, 0) ELSE 0 END), 0) AS sorghum_ls,
  COALESCE(SUM(CASE WHEN t3.crops = 'Kodo Millet' THEN COALESCE(t3.ls, 0) ELSE 0 END), 0) AS kodo_millet_ls,
  COALESCE(SUM(CASE WHEN t3.crops = 'Barnyard Millet' THEN COALESCE(t3.ls, 0) ELSE 0 END), 0) AS barnyard_millet_ls,
  COALESCE(SUM(CASE WHEN t3.crops = 'Pearl Millet' THEN COALESCE(t3.ls, 0) ELSE 0 END), 0) AS pearl_millet_ls
FROM
  soe_grampanchayats sg
LEFT JOIN
  ac_final_data_master afdm ON sg.id = afdm.gp_id
LEFT JOIN
  (
    SELECT
      afdm.gp_id,
      t3.crops,
      t3.smi,
      t3.lt,
      t3.ls
    FROM
      ac_final_data_master afdm
    LEFT JOIN
      (
        SELECT
          afad.final_data_id,
          afad.crop_id,
          ac.crops AS crops,
          afad.smi,
          afad.lt,
          afad.ls
        FROM
          ac_final_area_data afad
        LEFT JOIN
          ac_crops ac ON afad.crop_id = ac.id
      ) AS t3 ON afdm.id = t3.final_data_id
    WHERE
      ";
    if (!empty($filter['district_id'])) {
      $sql .= " afdm.district_id =" . $filter['district_id'];

    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND afdm.block_id =" . $filter['block_id'];

    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND afdm.year_id = " . $filter['year_id'];
    }
    if (!empty($filter['season'])) {
      $sql .= " AND afdm.season = '" . $filter['season'] . "'";
    }

    $sql .= "AND afdm.deleted_at IS NULL
  ) AS t3 ON sg.id = t3.gp_id
WHERE
   ";
    if (!empty($filter['block_id'])) {
      $sql .= " afdm.block_id =" . $filter['block_id'];

    }

    $sql .= "
GROUP BY
  sg.id, sg.name
order by sg.name";
    // echo $sql;
    // exit;

    return $this->db->query($sql)->getResult();
  }
  public function getBlockWiseFinalReport($filter = [])
  {
    $sql = "SELECT
  t1.block_id,
  t1.block,
  t1.total_gp,
  t2.total_village,
  t2.total_demon_farmer,
  t2.total_follow_farmer,
  t2.total_fup,
  t2.status,
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
  WHERE ";
    if (!empty($filter['district_id'])) {
      $sql .= " sb.district_id =" . $filter['district_id'];

    }
    $sql .= " AND sg.deleted_at IS NULL
  GROUP BY sb.id, sb.name
) AS t1
LEFT JOIN (
  SELECT
    afdm.id,
    afdm.block_id,
    afdm.status,
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
  WHERE ";
    if (!empty($filter['district_id'])) {
      $sql .= " afdm.district_id =" . $filter['district_id'];

    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND afdm.year_id = " . $filter['year_id'];
    }
    if (!empty($filter['season'])) {
      $sql .= " AND afdm.season = '" . $filter['season'] . "'";
    }

    $sql .= "AND afdm.deleted_at IS NULL GROUP BY afdm.block_id
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
  WHERE";
    if (!empty($filter['district_id'])) {
      $sql .= " afdm.district_id =" . $filter['district_id'];

    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND afdm.year_id = " . $filter['year_id'];
    }
    if (!empty($filter['season'])) {
      $sql .= " AND afdm.season = '" . $filter['season'] . "'";
    }

    $sql .= "AND afdm.deleted_at IS NULL GROUP BY afdm.block_id, t3.crops
) AS t3 ON t2.block_id = t3.block_id
GROUP BY t1.block_id
";
    // echo $sql;
    // exit;

    return $this->db->query($sql)->getResult();
  }



  public function getAllDistrictsFinalReport($filter = [])
  {
    $sql = "SELECT
  sd.id,
  sd.name AS district,
 
  COALESCE(block_data.block_count, 0) AS blocks,
  COALESCE(gp_data.gp_count, 0) AS gps,
  COALESCE(demonstration_data.total_village, 0) AS total_village,
  COALESCE(demonstration_data.season,'" . getAftCurrentSeason() . "') AS season,
  COALESCE(demonstration_data.year_id , " . getCurrentYearId() . ") AS year_id,  
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
  SUM(CASE WHEN t3.crops = 'Pearl Millet' THEN t3.ls END) AS pearl_millet_ls,
  demonstration_data.status AS status
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
      afdm.season,
      afdm.year_id,
      SUM(afdm.no_of_village) AS total_village,
      SUM(afdm.farmers_covered_under_demonstration) AS total_demon_farmer,
      SUM(afdm.farmers_covered_under_followup) AS total_follow_farmer,
      SUM(afdm.fup_ragi + afdm.fup_lm + afdm.fup_fm + afdm.fup_sorghum + afdm.fup_km + afdm.fup_bm + afdm.fup_pm) AS total_fup,
      afdm.status
    FROM ac_final_data_master afdm
    WHERE";

    if (!empty($filter['year_id'])) {
      $sql .= " afdm.year_id = " . $filter['year_id'];
    }
    if (!empty($filter['season'])) {
      $sql .= " AND afdm.season = '" . $filter['season'] . "'";
    }

    $sql .= " AND afdm.deleted_at IS NULL GROUP BY afdm.district_id) AS demonstration_data
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
    WHERE";

    if (!empty($filter['year_id'])) {
      $sql .= " afdm.year_id = " . $filter['year_id'];
    }
    if (!empty($filter['season'])) {
      $sql .= " AND afdm.season = '" . $filter['season'] . "'";
    }

    $sql .= " AND afdm.deleted_at IS NULL
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