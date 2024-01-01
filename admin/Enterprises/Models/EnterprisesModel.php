<?php

namespace Admin\Enterprises\Models;

use CodeIgniter\Model;

class EnterprisesModel extends Model
{
  protected $DBGroup              = 'default';
  protected $table                = 'enterprises';
  protected $primaryKey           = 'id';
  protected $useAutoIncrement     = true;
  protected $insertID             = 0;
  protected $returnType           = 'object';
  protected $useSoftDeletes        = true;
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

  public function getAll($filter = [])
  {
    $sql = "SELECT
    e.id,
    e.unit_id,
    e.district_id,
    e.block_id,
    e.gp_id,
    e.village_id,
    e.budget_fin_yr_id,
    e.management_unit_type,
    e.managing_unit_name,
    e.contact_person,
    e.contact_mobile,
    e.date_estd,
    e.mou_date,
    e.own_share,
    e.unit_budget,
    e.addl_budget,
    e.unit_budget_amount,
    e.is_support_basis_infr,
    e.is_support_basis_infr,
    e.purpose_infr_support,
    e.support_infr_amount,
    sd.name districts,
    sb.name blocks,
    v.name villages,
    sg.name gps,
    eu.name unit_name,
    eu.group_unit,
    YEAR(e.date_estd) year
    FROM enterprises e
    LEFT JOIN soe_districts sd ON sd.id = e.district_id
    LEFT JOIN soe_blocks sb ON sb.id = e.block_id
    LEFT JOIN villages v ON v.id = e.village_id
    LEFT JOIN soe_grampanchayats sg  ON sg.id = e.gp_id
    LEFT JOIN enterprises_units eu ON eu.id = e.unit_id
   WHERE e.deleted_at IS NULL";

    if (isset($filter['district_id'])) {
      $sql .= " AND e.district_id = " . $filter['district_id'];
    }
    if (isset($filter['block_id'])) {
      $sql .= " AND e.block_id = " . $filter['block_id'];
    }
    if (isset($filter['unit_id'])) {
      $sql .= " AND e.unit_id = " . $filter['unit_id'];
    }
    if (isset($filter['doeyear'])) {
      $sql .= " AND YEAR(e.date_estd) = " . $filter['doeyear'];
    }

    if (isset($filter["management_unit_type"])) {
      $sql .= " AND e.management_unit_type = '" . $filter["management_unit_type"] . "'
       
      ";
    }
    // $sql .=  " ORDER BY sd.name ASC";
    // printr($sql);
    // exit;
    return $this->db->query($sql)->getResult();
  }
  public function yearWise($district_id)
  {
    $sql = "SELECT
    e.id,
    e.district_id,
    YEAR(e.date_estd) year
    FROM enterprises e
    WHERE e.district_id = $district_id  AND YEAR(e.date_estd) > 2000
    GROUP BY year
    ORDER BY year ";
    //  printr($sql);
    //     exit;
    return $this->db->query($sql)->getResult();
  }
  /**
   * This method is called from establishemntreport for districtwise report generation.
   *
   * @param array   $filter 
   * 
   * @return array $result
   */
  public function districtwiseUnits($filter)
  {
    $sql = "SELECT
    disunit.unit_id,
    disunit.unit,
    disunit.district_id,
    disunit.district,
    COALESCE(res.total_units, 0) total_units,
    res.management_unit_type,
    res.date_estd,
    res.year_id,
    res.month
  FROM (SELECT
      eu.id unit_id,
      eu.name unit,
      sd.id district_id,
      sd.name district
    FROM soe_districts sd
      CROSS JOIN (SELECT * FROM enterprises_units WHERE deleted_at IS NULL) eu) disunit
    LEFT JOIN (SELECT
        unit_id,
        COUNT(e.unit_id) total_units,
        e.district_id,
        e.management_unit_type,
        e.date_estd,
        dy.id year_id,
        MONTH(e.date_estd) month
      FROM enterprises e
        LEFT JOIN dashboard_years dy
          ON DATE(e.date_estd) BETWEEN DATE(dy.start_date) AND DATE(dy.end_date)
          LEFT JOIN soe_months sm
        ON MONTH(e.date_estd) = sm.number
      WHERE e.deleted_at IS NULL";
    if (isset($filter['year_id']) && $filter['year_id']) {
      $sql .= " AND dy.id = " . $filter['year_id'];
    }
    if (isset($filter['month']) && $filter['month']) {
      $sql .= " AND sm.id = " . $filter['month'];
    }
    if (isset($filter["management_unit_type"]) && $filter["management_unit_type"] !== 'all') {
      $sql .= " AND e.management_unit_type != '" . $filter["management_unit_type"] . "'";
    }
    if (isset($filter['unit_type'])) {
      if ($filter['unit_type'] == 'without_establishment_date') {
        $sql .= " AND (YEAR(e.date_estd) < 2000 OR e.date_estd IS NULL) ";
      }
      if ($filter['unit_type'] == 'without_mou_date') {
        $sql .= " AND (YEAR(e.date_estd) < 2000 OR e.mou_date IS NULL) ";
      }
      if ($filter['unit_type'] == 'only_establishment_date') {
        $sql .= " AND (YEAR(e.date_estd) > 2000 OR e.date_estd IS NOT NULL) ";
      }
    }
    $sql .= " GROUP BY e.unit_id, e.district_id
      ORDER BY e.district_id) res
      ON disunit.district_id = res.district_id
      AND disunit.unit_id = res.unit_id

 ";

    $sql .=  " ORDER BY unit_id, district";
    // echo $sql;
    // exit;
    return $this->db->query($sql)->getResult();
  }
  /**
   * This method is called from establishemntreport for blockwise report generation.
   *
   * @param array   $filter 
   * 
   * @return array $result
   */
  public function blockwiseUnits($filter)
  {
    $sql = "SELECT
    blkunit.block_id,
    blkunit.block,
    blkunit.district_id,
    blkunit.unit,
    blkunit.unit_id,
    COALESCE(res.total_units, 0) total_units,
    res.management_unit_type,
    res.date_estd,
    res.year_id,
    res.month
  FROM (SELECT
        eu.id unit_id,
        eu.name unit,
        sb.id block_id,
        sb.name block,
        sb.district_id
      FROM soe_blocks sb
        CROSS JOIN (SELECT * FROM enterprises_units WHERE deleted_at IS NULL) eu) blkunit
    LEFT JOIN (SELECT
        e.unit_id,
        COUNT(e.unit_id) total_units,
        e.block_id,
        e.management_unit_type,
        e.date_estd,
        dy.id year_id,
        MONTH(e.date_estd) month
      FROM enterprises e
        LEFT JOIN dashboard_years dy
          ON DATE(e.date_estd) BETWEEN DATE(dy.start_date) AND DATE(dy.end_date)
          LEFT JOIN soe_months sm
        ON MONTH(e.date_estd) = sm.number
      WHERE e.deleted_at IS NULL";
    if (isset($filter['year_id'])) {
      $sql .= " AND dy.id = " . $filter['year_id'];
    }
    if (isset($filter['month'])) {
      $sql .= " AND sm.id = " . $filter['month'];
    }
    if (isset($filter["management_unit_type"]) && $filter["management_unit_type"] !== 'all') {
      $sql .= " AND e.management_unit_type != '" . $filter["management_unit_type"] . "'";
    }

    if (isset($filter['unit_type'])) {
      if ($filter['unit_type'] == 'without_establishment_date') {
        $sql .= " AND (YEAR(e.date_estd) < 2000 OR e.date_estd IS NULL) ";
      }
      if ($filter['unit_type'] == 'without_mou_date') {
        $sql .= " AND (YEAR(e.date_estd) < 2000 OR e.mou_date IS NULL) ";
      }
      if ($filter['unit_type'] == 'only_establishment_date') {
        $sql .= " AND (YEAR(e.date_estd) > 2000 OR e.date_estd IS NOT NULL) ";
      }
    }
    $sql .= " GROUP BY e.unit_id,
               e.block_id) res
      ON blkunit.block_id = res.block_id
      AND blkunit.unit_id = res.unit_id";

    if (isset($filter['district_id'])) {
      $sql .= " WHERE blkunit.district_id = " . $filter['district_id'];
    }

    $sql .=  " ORDER BY unit_id, block";
    // echo $sql;exit;
    return $this->db->query($sql)->getResult();
  }
  /**
   * This method is called from establishemntreport for gpwise report generation.
   *
   * @param array   $filter 
   * 
   * @return array $result
   */
  public function gpwiseUnits($filter)
  {
    $sql = "SELECT
    gpunits.gp_id,
    gpunits.gp,
    gpunits.block_id,
    gpunits.unit_id,
    gpunits.unit,
    COALESCE(res.total_units, 0) total_units,
    res.management_unit_type,
    res.date_estd,
    res.year_id,
    res.month
  FROM (SELECT
      gp.id gp_id,
      gp.name gp,
      gp.block_id,
      eu.id unit_id,
      eu.name unit
    FROM (SELECT
        *
      FROM grampanchayat g
     ) gp
      CROSS JOIN enterprises_units eu) gpunits
    LEFT JOIN (SELECT
        e.unit_id,
        COUNT(e.unit_id) total_units,
        e.gp_id,
        e.management_unit_type,
        e.date_estd,
        dy.id year_id,
        MONTH(e.date_estd) month
      FROM enterprises e
      LEFT JOIN dashboard_years dy
          ON DATE(e.date_estd) BETWEEN DATE(dy.start_date) AND DATE(dy.end_date)
          LEFT JOIN soe_months sm
        ON MONTH(e.date_estd) = sm.number
      WHERE e.deleted_at IS NULL";
    if (isset($filter['year_id'])) {
      $sql .= " AND dy.id = " . $filter['year_id'];
    }
    if (isset($filter['month'])) {
      $sql .= " AND sm.id = " . $filter['month'];
    }
    if (isset($filter["management_unit_type"]) && $filter["management_unit_type"] !== 'all') {
      $sql .= " AND e.management_unit_type != '" . $filter["management_unit_type"] . "'";
    }
    if (isset($filter['unit_type'])) {
      if ($filter['unit_type'] == 'without_establishment_date') {
        $sql .= " AND (YEAR(e.date_estd) < 2000 OR e.date_estd IS NULL) ";
      }
      if ($filter['unit_type'] == 'without_mou_date') {
        $sql .= " AND (YEAR(e.date_estd) < 2000 OR e.mou_date IS NULL) ";
      }
      if ($filter['unit_type'] == 'only_establishment_date') {
        $sql .= " AND (YEAR(e.date_estd) > 2000 OR e.date_estd IS NOT NULL) ";
      }
    }
    $sql .= " GROUP BY e.unit_id,
               e.gp_id) res
      ON res.gp_id = gpunits.gp_id
      AND res.unit_id = gpunits.unit_id";
    if (isset($filter['block_id'])) {
      $sql .= " WHERE gpunits.block_id = " . $filter['block_id'];
    }
    // dd($sql);
    return $this->db->query($sql)->getResult();
  }


}
