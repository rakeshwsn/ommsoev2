<?php

namespace Admin\Physicalachievement\Models;

use CodeIgniter\Model;

class PhysicalachievementModel extends Model
{
  protected $DBGroup              = 'default';
  protected $table                = 'mpr_components_achive_data';
  protected $primaryKey           = 'id';
  protected $useAutoIncrement     = true;
  protected $insertID             = 0;
  protected $returnType           = 'object';
  protected $useSoftDeletes        = false;
  protected $protectFields        = false;
  //	protected $allowedFields        = [];

  // Dates
  protected $useTimestamps        = false;
  protected $dateFormat           = 'datetime';
  protected $createdField         = 'created_at';
  protected $updatedField         = 'updated_at';
  protected $deletedField         = 'deleted_at';

  // Validation
  protected $validationRules      = [
    'components_name' => array(
      'label' => 'components_name',
      'rules' => 'trim|required|max_length[100]'
    ),
  ];
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

  public function getAll($data = array())
  {
    //printr($data);
    $builder = $this->db->table($this->table);
    $this->filter($builder, $data);

    $builder->select("*");

    if (isset($data['sort']) && $data['sort']) {
      $sort = $data['sort'];
    } else {
      $sort = "components_name";
    }

    if (isset($data['order']) && ($data['order'] == 'desc')) {
      $order = "desc";
    } else {
      $order = "asc";
    }
    $builder->orderBy($sort, $order);

    if (isset($data['start']) || isset($data['limit'])) {
      if ($data['start'] < 0) {
        $data['start'] = 0;
      }

      if ($data['limit'] < 1) {
        $data['limit'] = 10;
      }
      $builder->limit((int)$data['limit'], (int)$data['start']);
    }
    //$builder->where($this->deletedField, null);

    $res = $builder->get()->getResult();

    return $res;
  }

  public function getTotal($data = array())
  {
    $builder = $this->db->table($this->table);
    $this->filter($builder, $data);
    $count = $builder->countAllResults();
    return $count;
  }

  private function filter($builder, $data)
  {

    if (!empty($data['filter_search'])) {
      $builder->where(
        "
				name LIKE '%{$data['filter_search']}%'"
      );
    }
  }
  public function getComponentsAllData($filter = [])
  {
    // printr($filter); exit;
    $from_month = 4;
    $to_month = $filter['month_id'];
    $monthid = $filter['month_id'] - 1;

    $query = "SELECT
            mc.id,
            mc.description AS component,
            mc.comp_categoryid,
            target.no_total targettotal,
            IFNULL(SUM(upto_prev.no_total), 0) AS ach_total,
            cur_ach.cur_total,
            cur_ach.fpo_total,
            cur_ach.wshg_total
          FROM mpr_components mc
            LEFT JOIN (
              SELECT
                mctd.mpr_component_id,
                mctd.no_total,
                mctd.mprcomponents_master_id
              FROM mpr_components_target_data mctd
                LEFT JOIN mpr_components_target_master mctm
                  ON mctd.mprcomponents_master_id = mctm.id
              WHERE mctm.district_id = " . $filter['district_id'] . "
              AND mctm.year_id = " . $filter['year_id'] . "
              AND mctm.fund_agency_id = " . $filter['fund_agency_id'] . "
            ) target ON target.mpr_component_id = mc.id
            LEFT JOIN (
              SELECT
                mcad.mprcomponents_master_id,
                mcad.mpr_component_id,
                mcad.no_total,
                mctm.year_id,
                mcad.month_id
              FROM mpr_components_achive_data mcad
              LEFT JOIN mpr_components_target_master mctm ON mcad.mprcomponents_master_id = mctm.id WHERE mctm.year_id = {$filter['year_id']} AND (";

    if ($from_month <= $to_month) {
      $query .= "mcad.month_id >= $from_month AND mcad.month_id <= $to_month - 1)";
    } else {
      $query .= "mcad.month_id >= $from_month OR mcad.month_id <= $to_month - 1)";
    }

    $query .= ") upto_prev ON upto_prev.mpr_component_id = mc.id AND upto_prev.mprcomponents_master_id = target.mprcomponents_master_id
            LEFT JOIN (
              SELECT
                mcad.mpr_component_id,
                mcad.mprcomponents_master_id,
                mcad.no_total AS cur_total,
                mcad.fpo AS fpo_total,
                mcad.wshg AS wshg_total
              FROM mpr_components_achive_data mcad
              WHERE mcad.month_id = " . $filter['month_id'] . "
            ) cur_ach ON cur_ach.mpr_component_id = mc.id AND cur_ach.mprcomponents_master_id = target.mprcomponents_master_id
            WHERE mc.year_id <= " . $filter['year_id'] . "
          GROUP BY mc.id, mc.description, target.no_total, cur_ach.cur_total";

  //  echo $query; exit;
    return $this->db->query($query)->getResultArray();
  }



  public function showTargetAchData($data = [])
  {
    $from_month = 4;
    $to_month = $data['month_id'];
    $preMonth = $data['month_id'] - 1;
    $currentYearStartMonth = 4;
    $currentMonth = date('n');
    $currentYear = 1;
    $nextYearAprilMonth = 4;

    $sql = "SELECT
        dist.district_id AS district_id,
        dist.district,
        dist.total_block";
    $componets = $this->getMprComponents($data['year_id']);
    foreach ($componets as $comp) {
      $sql .= " , comp_target{$comp['id']}.target{$comp['id']},upto_ach{$comp['id']}.upto_ach{$comp['id']},curr_ach{$comp['id']}.cur_ach{$comp['id']} ";
    }
    $sql .= " FROM (SELECT
        sd.district_id district_id,
        CASE WHEN sd.fund_agency_id > 1 THEN CONCAT(sd.district, ' DMF') ELSE sd.district END AS district,
        sd.fund_agency_id,
        COALESCE(COUNT(sb.id), 0) total_block
      FROM vw_district_fund_agency sd
        LEFT JOIN soe_blocks sb
          ON sb.district_id = sd.district_id
      WHERE sb.fund_agency_id = sd.fund_agency_id";
    if (!empty($data['district_id'])) {
      $sql .= " and sb.district_id = " . $data['district_id'];
    }
    if (!empty($data['district_id']) && !empty($data['fund_agency_id'])) {
      $sql .= " and sb.fund_agency_id = " . $data['fund_agency_id'];
    }
    $sql .= "
            GROUP BY sd.district_id,
           sd.fund_agency_id) dist";

    foreach ($componets as $comp) {
      $sql .= " LEFT JOIN (SELECT
            mctm.fund_agency_id,
            mctm.district_id,
            SUM(mctd.no_total) AS target{$comp['id']}
          FROM mpr_components_target_master mctm
            LEFT JOIN mpr_components_target_data mctd
              ON mctm.id = mctd.mprcomponents_master_id
          WHERE mctm.year_id = " . $data['year_id'] . "
          AND mctd.mpr_component_id = {$comp['id']}
          GROUP BY mctm.fund_agency_id,mctm.district_id) comp_target{$comp['id']}
          ON comp_target{$comp['id']}.district_id = dist.district_id AND comp_target{$comp['id']}.fund_agency_id = dist.fund_agency_id
         LEFT JOIN (SELECT
         mctm.fund_agency_id,
            mctm.district_id,
            SUM(mcad.no_total) AS upto_ach{$comp['id']}
          FROM mpr_components_target_master mctm
            LEFT JOIN mpr_components_achive_data mcad
              ON mctm.id = mcad.mprcomponents_master_id WHERE mctm.year_id = {$data['year_id']} AND (";
              if ($from_month <= $to_month) {
                $sql .= "mcad.month_id >= $from_month AND mcad.month_id <= $to_month - 1)";
              } else {
                $sql .= "mcad.month_id >= $from_month OR mcad.month_id <= $to_month - 1)";
              }
              $sql .= "AND mcad.mpr_component_id = {$comp['id']}
          GROUP BY mctm.fund_agency_id,mctm.district_id) upto_ach{$comp['id']}
          ON upto_ach{$comp['id']}.district_id = dist.district_id AND upto_ach{$comp['id']}.fund_agency_id = dist.fund_agency_id
        LEFT JOIN (SELECT
        mctm.fund_agency_id,
            mctm.district_id,
            SUM(mcad.no_total) AS cur_ach{$comp['id']}
          FROM mpr_components_target_master mctm
            LEFT JOIN mpr_components_achive_data mcad
              ON mctm.id = mcad.mprcomponents_master_id
          WHERE mctm.year_id = " . $data['year_id'] . "
          AND mcad.mpr_component_id = {$comp['id']}
          AND mcad.month_id = " . $data['month_id'] . "
          GROUP BY mctm.fund_agency_id,mctm.district_id) curr_ach{$comp['id']}
          ON curr_ach{$comp['id']}.district_id = dist.district_id AND curr_ach{$comp['id']}.fund_agency_id = dist.fund_agency_id";
    }
    $sql .= " ORDER BY dist.district ASC";
     //echo $sql; exit;
    return $this->db->query($sql)->getResultArray();
  }

  protected function getMprComponents($data)
  {
    $sql = "SELECT * FROM mpr_components WHERE year_id <= " . $data . " AND comp_categoryid = 2 ORDER BY id ASC";
    return $this->db->query($sql)->getResultArray();
  }

  public function getMprComponentsall($data)
  {
    //printr($data['year_id']); exit;
    $sql = "SELECT * FROM mpr_components WHERE year_id <= " . $data['year_id'] . " AND comp_categoryid = 2 ORDER BY id ASC";
    return $this->db->query($sql)->getResultArray();
  }

  public function showTargetAchDataEnt($data = [])
  {
    $from_month = 4;
    $to_month = $data['month_id'];
    $preMonth = $data['month_id'] - 1;
    $currentYearStartMonth = 4;
    $currentMonth = date('n');
    $currentYear = 1;
    $nextYearAprilMonth = 4;

    $sql = "SELECT
        dist.district_id AS district_id,
        dist.district,
        dist.total_block";
    $componets = $this->getMprComponentsEnt($data['year_id']);
    foreach ($componets as $comp) {
      $sql .= " , comp_target{$comp['id']}.target{$comp['id']},upto_ach{$comp['id']}.upto_fpo{$comp['id']},upto_ach{$comp['id']}.upto_wshg{$comp['id']},curr_ach{$comp['id']}.cur_fpo{$comp['id']}, curr_ach{$comp['id']}.cur_wshg{$comp['id']} ";
    }
    $sql .= " FROM (SELECT
        sd.district_id district_id,
        CASE WHEN sd.fund_agency_id > 1 THEN CONCAT(sd.district, ' DMF') ELSE sd.district END AS district,
        sd.fund_agency_id,
        COALESCE(COUNT(sb.id), 0) total_block
      FROM vw_district_fund_agency sd
        LEFT JOIN soe_blocks sb
          ON sb.district_id = sd.district_id
      WHERE sb.fund_agency_id = sd.fund_agency_id";
    if (!empty($data['district_id'])) {
      $sql .= " and sb.district_id = " . $data['district_id'];
    }
    if (!empty($data['district_id']) && !empty($data['fund_agency_id'])) {
      $sql .= " and sb.fund_agency_id = " . $data['fund_agency_id'];
    }
    $sql .= "
            GROUP BY sd.district_id,
            sd.fund_agency_id) dist";

    foreach ($componets as $comp) {
      $sql .= " LEFT JOIN (SELECT
            mctm.fund_agency_id,
            mctm.district_id,
            SUM(mctd.no_total) AS target{$comp['id']}
          FROM mpr_components_target_master mctm
            LEFT JOIN mpr_components_target_data mctd
              ON mctm.id = mctd.mprcomponents_master_id
          WHERE mctm.year_id = " . $data['year_id'] . "
          AND mctd.mpr_component_id = {$comp['id']}
          GROUP BY mctm.district_id,mctm.fund_agency_id) comp_target{$comp['id']}
          ON comp_target{$comp['id']}.district_id = dist.district_id AND comp_target{$comp['id']}.fund_agency_id = dist.fund_agency_id
         LEFT JOIN (SELECT
         mctm.fund_agency_id,
            mctm.district_id,
            SUM(mcad.wshg) AS upto_wshg{$comp['id']},
            SUM(mcad.fpo) AS upto_fpo{$comp['id']}
          FROM mpr_components_target_master mctm
            LEFT JOIN mpr_components_achive_data mcad
              ON mctm.id = mcad.mprcomponents_master_id
              WHERE mctm.year_id = {$data['year_id']} AND (";
              if ($from_month <= $to_month) {
                $sql .= "mcad.month_id >= $from_month AND mcad.month_id <= $to_month - 1)";
              } else {
                $sql .= "mcad.month_id >= $from_month OR mcad.month_id <= $to_month - 1)";
              }
              $sql .= " AND mcad.mpr_component_id = {$comp['id']}
          GROUP BY mctm.district_id,mctm.fund_agency_id) upto_ach{$comp['id']}
          ON upto_ach{$comp['id']}.district_id = dist.district_id AND upto_ach{$comp['id']}.fund_agency_id = dist.fund_agency_id
        LEFT JOIN (SELECT
        mctm.fund_agency_id,
            mctm.district_id,
            SUM(mcad.fpo) AS cur_fpo{$comp['id']},
            SUM(mcad.wshg) AS cur_wshg{$comp['id']}
          FROM mpr_components_target_master mctm
            LEFT JOIN mpr_components_achive_data mcad
              ON mctm.id = mcad.mprcomponents_master_id
          WHERE mctm.year_id = " . $data['year_id'] . "
          AND mcad.mpr_component_id = {$comp['id']}
          AND mcad.month_id = " . $data['month_id'] . "
          GROUP BY mctm.district_id,mctm.fund_agency_id) curr_ach{$comp['id']}
          ON curr_ach{$comp['id']}.district_id = dist.district_id AND curr_ach{$comp['id']}.fund_agency_id = dist.fund_agency_id";
    }
    $sql .= " ORDER BY dist.district ASC";
    //  echo $sql; exit;
    return $this->db->query($sql)->getResultArray();
  }

  protected function getMprComponentsEnt($data)
  {
    $sql = "SELECT * FROM mpr_components WHERE year_id <= " . $data . " AND comp_categoryid = 1 ORDER BY id ASC";
    return $this->db->query($sql)->getResultArray();
  }

  public function getMprComponentsallEnt($data)
  {
    //printr($data['year_id']); exit;
    $sql = "SELECT * FROM mpr_components WHERE year_id <= " . $data['year_id'] . " AND comp_categoryid = 1 ORDER BY id ASC";
    return $this->db->query($sql)->getResultArray();
  }
  public function addPhysicalachData($data)
  {
    //printr($data); exit;
    $monthid = $data['monthid'];
    $masterid = $data['componentidmain'];

    // Check if $monthid and $masterid exist inside the table
    $existingData = $this->db->table("mpr_components_achive_data")
      ->where('mprcomponents_master_id', $masterid)
      ->where('month_id', $monthid)
      ->get()
      ->getResult();

    if (!empty($existingData)) {
      // $monthid and $masterid exist in the table, delete the existing rows
      $this->db->table("mpr_components_achive_data")
        ->where('mprcomponents_master_id', $masterid)
        ->where('month_id', $monthid)
        ->delete();
    }

    // Insert the new data
    if (isset($data['component'])) {
      foreach ($data['component'] as $componentid => $achmultiple) {
        $masterTableData = array(
          "mprcomponents_master_id" => $masterid,
          "mpr_component_id" => $componentid,
          "month_id" => $monthid,
          "no_total" => $achmultiple['data'],
          "wshg" => isset($achmultiple['wshg']) ? $achmultiple['wshg'] : 0,
          "fpo" => isset($achmultiple['fpo']) ?  $achmultiple['fpo'] : 0,
        );

        $this->db->table("mpr_components_achive_data")->insert($masterTableData);
      }
    }
  }



  public function showGetExsistsMonthData($filter = [])
  {

    $builder = $this->db->table('mpr_components_target_master');
    $builder->join('mpr_components_achive_data', 'mpr_components_achive_data.mprcomponents_master_id = mpr_components_target_master.id', 'left');
    $builder->select('mpr_components_achive_data.id, mpr_components_achive_data.month_id');

    if (isset($filter['district_id'])) {
      $builder->where('mpr_components_target_master.district_id', $filter['district_id']);
    }
    if (isset($filter['fund_agency_id'])) {
      $builder->where('mpr_components_target_master.fund_agency_id', $filter['fund_agency_id']);
    }
    if (isset($filter['year_id'])) {
      $builder->where('mpr_components_target_master.year_id', $filter['year_id']);
    }
    if (isset($filter['month_id'])) {
      $builder->where('mpr_components_achive_data.month_id', $filter['month_id']);
    }

    $result = $builder->get()->getRow();
    return $result;
  }


  public function showTrackreport($data = [])
  {
    $sql = "
        SELECT
        main_tbl.district_id,
        CASE WHEN main_tbl.fund_agency_id > 1 THEN CONCAT(main_tbl.district, ' DMF') ELSE main_tbl.district END AS district,
        second_tbl.masterid,
        second_tbl.year_id,
        COUNT(CASE WHEN second_tbl.month_id=4 THEN main_tbl.district_id END) AS 'April',
        COUNT(CASE WHEN second_tbl.month_id=5 THEN main_tbl.district_id END) AS 'May',
        COUNT(CASE WHEN second_tbl.month_id=6 THEN main_tbl.district_id END) AS 'June',
        COUNT(CASE WHEN second_tbl.month_id=7 THEN main_tbl.district_id END) AS 'July',
        COUNT(CASE WHEN second_tbl.month_id=8 THEN main_tbl.district_id END) AS 'August',
        COUNT(CASE WHEN second_tbl.month_id=9 THEN main_tbl.district_id END) AS 'September',
        COUNT(CASE WHEN second_tbl.month_id=10 THEN main_tbl.district_id END) AS 'October',
        COUNT(CASE WHEN second_tbl.month_id=11 THEN main_tbl.district_id END) AS 'November',
        COUNT(CASE WHEN second_tbl.month_id=12 THEN main_tbl.district_id END) AS 'December',
        COUNT(CASE WHEN second_tbl.month_id=1 THEN main_tbl.district_id END) AS 'January',
        COUNT(CASE WHEN second_tbl.month_id=2 THEN main_tbl.district_id END) AS 'February',
        COUNT(CASE WHEN second_tbl.month_id=3 THEN main_tbl.district_id END) AS 'March'
        FROM vw_district_fund_agency main_tbl
        LEFT JOIN (
            SELECT
                mctm.district_id,
                mctm.id AS masterid,
                mcad.month_id,
                mctm.year_id
            FROM mpr_components_target_master mctm
            LEFT  JOIN mpr_components_achive_data mcad ON mctm.id = mcad.mprcomponents_master_id
            WHERE mctm.year_id = 2
            GROUP BY mctm.district_id, mcad.month_id
        ) second_tbl ON main_tbl.district_id = second_tbl.district_id
        GROUP BY main_tbl.district_id,main_tbl.fund_agency_id  ORDER BY main_tbl.district ASC";
    //  echo $sql; exit;
    return $this->db->query($sql)->getResultArray();
  }
}
