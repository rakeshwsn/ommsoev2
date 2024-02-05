<?php

namespace Admin\Enterprises\Models;

use CodeIgniter\Model;

class EstablishmentTransactionDetailsModel extends Model
{
  protected $DBGroup              = 'default';
  protected $table                = 'enterprises_transactiondetails';
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


  public function periodswisetrans($filter = [])
  {
    $sql = "SELECT
    e.id ent_id,
    txn_dtl.txn_id,
    txn_dtl.est_id,
    sd.name district_name,
    sb.name block_name,
    v.name village_name,
    g.name gp_name,
    txn_dtl.no_of_days_functional,
    txn_dtl.charges_per_qtl,
    txn_dtl.total_expend,
    txn_dtl.enterprise_id,
    txn_dtl.district_id,
    txn_dtl.block_id,
    txn_dtl.village_id,
    txn_dtl.gp_id,
    txn_dtl.produced,
    txn_dtl.total_turnover,
    dy.name year_name,
    sm.name month_name,
    eu.name unit_name,
    txn_dtl.period,txn_dtl.created_at
      FROM (SELECT
      etd.id est_id,
      etd.no_of_days_functional,
      etd.produced,
      etd.total_turnover,
      etd.charges_per_qtl,
      etd.total_expend,
      et.id txn_id,
      etd.enterprise_id,
      et.district_id,
      et.period,
      etd.block_id,
      etd.gp_id,
      etd.village_id,
      et.created_at,
      et.year_id,
      et.month_id,
      et.unit_id
    FROM enterprises_transactiondetails etd
      INNER JOIN enterprises_transaction et
        ON etd.transaction_id = et.id
    WHERE et.deleted_at IS NULL 
    AND etd.deleted_at IS NULL) txn_dtl
    LEFT JOIN enterprises e
      ON e.id = txn_dtl.enterprise_id
    LEFT JOIN soe_blocks sb
      ON sb.id = txn_dtl.block_id
    LEFT JOIN soe_districts sd
      ON sd.id = txn_dtl.district_id
    LEFT JOIN soe_grampanchayats g
      ON g.id = txn_dtl.gp_id
    LEFT JOIN villages v
      ON v.id = txn_dtl.village_id
    LEFT JOIN dashboard_years dy
      ON dy.id = txn_dtl.year_id
    LEFT JOIN soe_months sm
      ON sm.id = txn_dtl.month_id
    LEFT JOIN enterprises_units eu
      ON eu.id = txn_dtl.unit_id WHERE 1=1 ";
    if (isset($filter['id'])) {
      $sql .= " AND e.id = " . $filter['id'];
    }
    if (isset($filter['year_id']) && $filter['year_id']) {
      $sql .= " AND txn_dtl.year_id = " . $filter['year_id'];
    }
    if (isset($filter['district_id'])) {
      $sql .= " AND txn_dtl.district_id = " . $filter['district_id'];
    }
    if (isset($filter['month_id'])) {
      $sql .= " AND txn_dtl.month_id = " . $filter['month_id'];
    }
    if (isset($filter['period'])) {
      $sql .= " AND txn_dtl.period = " . $filter['period'];
    }

    // $sql .=  " GROUP BY unit.units";
    //  echo $sql;exit;
    if (isset($filter['id'])) {
      return $this->db->query($sql)->getRow();
    } else {
      return $this->db->query($sql)->getResult();
    }
  }

  public function idwisetrans($id)
  {
    $sql = "SELECT
    e.id ent_id,
    txn_dtl.txn_id,
    txn_dtl.est_id,
    sd.name district_name,
    sb.name block_name,
    v.name village_name,
    g.name gp_name,
    txn_dtl.no_of_days_functional,
    txn_dtl.charges_per_qtl,
    txn_dtl.total_expend,
    txn_dtl.enterprise_id,
    txn_dtl.district_id,
    txn_dtl.block_id,
    txn_dtl.village_id,
    txn_dtl.gp_id,
    txn_dtl.produced,
    txn_dtl.total_turnover,
    txn_dtl.under_maintenance,
    txn_dtl.event_attend,
    txn_dtl.farmer_user,
    txn_dtl.service_charge,
    txn_dtl.seed_support,
    txn_dtl.seed_store,
    txn_dtl.year_id,
    dy.name year_name,
    sm.name month_name,
    eu_unit.unit_name,
    eu_unit.unit_group_name,
    eu_unit.unit_group_id,
    txn_dtl.period,
    txn_dtl.created_at
  FROM (SELECT
      etd.id est_id,
      etd.no_of_days_functional,
      etd.produced,
      etd.total_turnover,
      etd.charges_per_qtl,
      etd.total_expend,
      et.id txn_id,
      etd.enterprise_id,
      et.district_id,
      et.period,
      etd.block_id,
      etd.gp_id,
      etd.village_id,
      et.created_at,
      et.year_id,
      et.month_id,
      et.unit_id,
      etd.under_maintenance,
      etd.event_attend,
      etd.farmer_user,
      etd.service_charge,
      etd.seed_support,
      etd.seed_store
    FROM enterprises_transactiondetails etd
      INNER JOIN enterprises_transaction et
        ON etd.transaction_id = et.id
    WHERE et.deleted_at IS NULL
    AND etd.deleted_at IS NULL) txn_dtl
    LEFT JOIN enterprises e
      ON e.id = txn_dtl.enterprise_id
    LEFT JOIN soe_blocks sb
      ON sb.id = txn_dtl.block_id
    LEFT JOIN soe_districts sd
      ON sd.id = txn_dtl.district_id
    LEFT JOIN soe_grampanchayats g
      ON g.id = txn_dtl.gp_id
    LEFT JOIN villages v
      ON v.id = txn_dtl.village_id
    LEFT JOIN dashboard_years dy
      ON dy.id = txn_dtl.year_id
    LEFT JOIN soe_months sm
      ON sm.id = txn_dtl.month_id
    LEFT JOIN (SELECT
        eu.name unit_name,
        eu.id unit_id,
        eu.unit_group_id,
        eug.name unit_group_name
      FROM enterprises_units eu
        LEFT JOIN enterprise_unit_group eug
          ON eu.unit_group_id = eug.id) eu_unit
      ON eu_unit.unit_id = txn_dtl.unit_id WHERE txn_id = $id ";
    // echo $sql;exit;
    return $this->db->query($sql)->getResult();
  }


  public function unitwisereport($filter = [])
  {
    $last_year = 0;
    if (!empty($filter['year_id'])) {
      $last_year = $filter['year_id'] - 1;
    }
    $pmonth = 0;
    if (!empty($filter['month_id'])) {
      $pmonth = $filter['month_id'] - 1;
    }
    $sql = "SELECT
  units.unit_id,
  units.unit_name,
  COALESCE(func_unit_upto.total_units, 0) total_units_upto,
  COALESCE(func_unit_mon.total_units, 0) total_units_mon,
  COALESCE(func_unit_upto.total_units, 0) + COALESCE(func_unit_mon.total_units, 0) total_units_cumm,
  COALESCE(trxn_upto.turn_over, 0) turnover_upto,
  COALESCE(txn_mon.turn_over, 0) turnover_mon,
  COALESCE(trxn_upto.turn_over, 0) + COALESCE(txn_mon.turn_over, 0) turnover_cumm,
  COALESCE(trxn_upto.expense, 0) expn_upto,
  COALESCE(txn_mon.expense, 0) expn_mon,
  COALESCE(trxn_upto.expense, 0) + COALESCE(txn_mon.expense, 0) expn_cumm,
  COALESCE(trxn_upto.turn_over, 0) - COALESCE(trxn_upto.expense, 0) incm_upto,
  COALESCE(txn_mon.turn_over, 0) - COALESCE(txn_mon.expense, 0) incm_mon
 FROM (SELECT
    eu.id unit_id,
    eu.name unit_name
  FROM enterprises_units eu
  ORDER BY eu.name) units
  LEFT JOIN (SELECT
      e.unit_id,
      COUNT(e.id) total_units
    FROM enterprises e
      LEFT JOIN dashboard_years dy
        ON DATE(e.mou_date) BETWEEN DATE(dy.start_date) AND DATE(dy.end_date)
      LEFT JOIN soe_months sm
        ON MONTH(e.mou_date) = sm.number
    WHERE e.deleted_at IS NULL
    AND e.unit_id > 0
    AND YEAR(e.mou_date) > 2000";
    if (!empty($filter['management_unit_type'])) {
      $sql .= " AND (e.management_unit_type = '" . $filter['management_unit_type'] . "')";
    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND ((dy.id BETWEEN 0 AND $last_year) OR (dy.id = {$filter['year_id']} AND sm.id BETWEEN 0 AND $pmonth))";
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND (e.district_id = {$filter['district_id']})";
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND (e.block_id = {$filter['block_id']})";
    }


    $sql .= " GROUP BY e.unit_id) func_unit_upto
    ON func_unit_upto.unit_id = units.unit_id
  LEFT JOIN (SELECT
      e.unit_id,
      COUNT(e.id) total_units
    FROM enterprises e
      LEFT JOIN dashboard_years dy
        ON DATE(e.mou_date) BETWEEN DATE(dy.start_date) AND DATE(dy.end_date)
      LEFT JOIN soe_months sm
        ON MONTH(e.mou_date) = sm.number
    WHERE e.deleted_at IS NULL
    AND e.unit_id > 0
    AND YEAR(e.mou_date) > 2000";
    if (!empty($filter['management_unit_type'])) {
      $sql .= " AND (e.management_unit_type = '" . $filter['management_unit_type'] . "')";
    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND (dy.id = {$filter['year_id']} AND sm.id = $pmonth)";
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND (e.district_id = {$filter['district_id']})";
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND (e.block_id = {$filter['block_id']})";
    }

    $sql .= " GROUP BY e.unit_id) func_unit_mon
    ON func_unit_mon.unit_id = units.unit_id
  LEFT JOIN (SELECT
      et.unit_id,
      SUM(etd.total_turnover) turn_over,
      SUM(etd.total_expend) expense,
      et.district_id,
      etd.block_id
    FROM enterprises_transactiondetails etd
      LEFT JOIN enterprises_transaction et
        ON etd.transaction_id = et.id
      LEFT JOIN enterprises e
        ON etd.enterprise_id = e.id
    WHERE etd.deleted_at IS NULL
    AND et.deleted_at IS NULL";
    if (!empty($filter['management_unit_type'])) {
      $sql .= " AND (e.management_unit_type = '" . $filter['management_unit_type'] . "')";
    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND ((et.year_id BETWEEN 0 AND $last_year) OR (et.year_id = {$filter['year_id']} 
      AND et.month_id BETWEEN 0 AND $pmonth))";
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND (et.district_id = {$filter['district_id']})";
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND (etd.block_id = {$filter['block_id']})";
    }

    $sql .= " GROUP BY et.unit_id) trxn_upto
    ON trxn_upto.unit_id = units.unit_id
  LEFT JOIN (SELECT
      et.unit_id,
      et.district_id,
      SUM(etd.total_turnover) turn_over,
      SUM(etd.total_expend) expense,
      etd.block_id
    FROM enterprises_transactiondetails etd
      LEFT JOIN enterprises_transaction et
        ON etd.transaction_id = et.id
      LEFT JOIN enterprises e
        ON etd.enterprise_id = e.id
    WHERE etd.deleted_at IS NULL
    AND et.deleted_at IS NULL";
    if (!empty($filter['management_unit_type'])) {
      $sql .= " AND (e.management_unit_type = '" . $filter['management_unit_type'] . "')";
    }
    if (!empty($filter['year_id'])) {
      $sql .= " AND (et.year_id = {$filter['year_id']} AND et.month_id = $pmonth)";
    }
    if (!empty($filter['district_id'])) {
      $sql .= " AND (et.district_id = {$filter['district_id']})";
    }
    if (!empty($filter['block_id'])) {
      $sql .= " AND (etd.block_id = {$filter['block_id']})";
    }

    $sql .= " GROUP BY et.unit_id) txn_mon
    ON txn_mon.unit_id = units.unit_id";
    // echo $sql;exit;
    return $this->db->query($sql)->getResult();
  }
}
