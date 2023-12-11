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
    e.id,
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
    LEFT JOIN grampanchayat g
      ON g.id = txn_dtl.gp_id
    LEFT JOIN villages v
      ON v.id = txn_dtl.village_id
    LEFT JOIN dashboard_years dy
      ON dy.id = txn_dtl.year_id
    LEFT JOIN soe_months sm
      ON sm.id = txn_dtl.month_id
    LEFT JOIN enterprises_units eu
      ON eu.id = txn_dtl.unit_id WHERE 1=1";
    if (isset($filter['id'])) {
      $sql .= " AND e.id = " . $filter['id'];
    }
    if (isset($filter['year_id'])) {
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
    return $this->db->query($sql)->getResult();
  }
  public function unitwisereport($filter = [])
  {
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
        et.unit_id,
        COUNT(et.id) total_units,
        et.district_id,
        etd.block_id ,
        e.management_unit_type
    FROM enterprises_transaction et
      LEFT JOIN enterprises e
        ON et.unit_id = e.unit_id
      LEFT JOIN enterprises_transactiondetails etd
        ON et.id = etd.transaction_id
     
      WHERE et.deleted_at IS NULL";
    if ($filter['year_id']) {
      $sql .= " AND ((year_id BETWEEN 0 AND {$filter['year_id']})";
    }
    if ($filter['month_id']) {
      $pmonth = $filter['month_id'] - 1;
      $sql .= " OR (year_id = {$filter['year_id']} AND month_id BETWEEN 0 AND $pmonth))";
    }
    if ($filter['district_id']) {
      $sql .= " AND (et.district_id = {$filter['district_id']})";
    }
    if ($filter['block_id']) {
      $sql .= " AND (etd.block_id = {$filter['block_id']})";
    }
    if ($filter['management_unit_type']) {
      $sql .= " AND (e.management_unit_type = '" . $filter['management_unit_type'] . "')";
    }
    // AND ((year_id BETWEEN 0 AND 6)
    // OR (year_id = 7
    // AND month_id BETWEEN 0 AND 7))
    $sql .= " AND et.unit_id > 0
      GROUP BY et.unit_id) func_unit_upto
      ON func_unit_upto.unit_id = units.unit_id
    LEFT JOIN (SELECT
        et.unit_id,
        COUNT(et.id) total_units,
        et.district_id,
        etd.block_id,
        e.management_unit_type
    FROM enterprises_transaction et
      LEFT JOIN enterprises e
        ON et.unit_id = e.unit_id
      LEFT JOIN enterprises_transactiondetails etd
        ON et.id = etd.transaction_id
     
      WHERE et.deleted_at IS NULL";
    if (($filter['year_id']) && ($filter['month_id'])) {
      $sql .= " AND (year_id = {$filter['year_id']} AND month_id = {$filter['month_id']})";
    }
    if ($filter['district_id']) {
      $sql .= " AND (et.district_id = {$filter['district_id']})";
    }
    if ($filter['block_id']) {
      $sql .= " AND (etd.block_id = {$filter['block_id']})";
    }
    if ($filter['management_unit_type']) {
      $sql .= " AND (e.management_unit_type = '" . $filter['management_unit_type'] . "')";
    }
    // AND (year_id = 7
    // AND month_id = 8)
    $sql .= " AND et.unit_id > 0
      GROUP BY et.unit_id) func_unit_mon
      ON func_unit_mon.unit_id = units.unit_id
    LEFT JOIN (SELECT
        et.unit_id,
        SUM(etd.total_turnover) turn_over,
        SUM(etd.total_expend) expense,
        et.district_id,etd.block_id,
        mangment_type.management_unit_type
    FROM enterprises_transactiondetails etd
      LEFT JOIN (SELECT
          et.id,
          e.management_unit_type
        FROM enterprises_transaction et
          LEFT JOIN enterprises e
            ON et.unit_id = e.unit_id) mangment_type
        ON etd.transaction_id = mangment_type.id
        LEFT JOIN enterprises_transaction et
          ON etd.transaction_id = et.id
      WHERE etd.deleted_at IS NULL
      AND et.deleted_at IS NULL";
    if ($filter['year_id']) {
      $sql .= " AND ((et.year_id BETWEEN 0 AND {$filter['year_id']})";
    }
    if ($filter['month_id']) {
      $pmonth = $filter['month_id'] - 1;
      $sql .= " OR (et.year_id = {$filter['year_id']} AND et.month_id BETWEEN 0 AND $pmonth))";
    }
    if ($filter['district_id']) {
      $sql .= " AND (et.district_id = {$filter['district_id']})";
    }
    if ($filter['block_id']) {
      $sql .= " AND (etd.block_id = {$filter['block_id']})";
    }
    if ($filter['management_unit_type']) {
      $sql .= " AND (mangment_type.management_unit_type = '" . $filter['management_unit_type'] . "')";
    }
    // AND ((et.year_id BETWEEN 0 AND 6)
    // OR (et.year_id = 7
    // AND et.month_id BETWEEN 0 AND 7))
    $sql .= " GROUP BY et.unit_id) trxn_upto
      ON trxn_upto.unit_id = units.unit_id
    LEFT JOIN (SELECT
        et.unit_id,
        et.district_id,
        SUM(etd.total_turnover) turn_over,
        SUM(etd.total_expend) expense,etd.block_id,
        mangment_type.management_unit_type
        FROM enterprises_transactiondetails etd
          LEFT JOIN (SELECT
              et.id,
              e.management_unit_type
            FROM enterprises_transaction et
              LEFT JOIN enterprises e
                ON et.unit_id = e.unit_id) mangment_type
            ON etd.transaction_id = mangment_type.id
        LEFT JOIN enterprises_transaction et
          ON etd.transaction_id = et.id
      WHERE etd.deleted_at IS NULL
      AND et.deleted_at IS NULL";
    if (($filter['year_id']) && ($filter['month_id'])) {
      $sql .= " AND (et.year_id = {$filter['year_id']} AND et.month_id = {$filter['month_id']})";
    }
    if ($filter['district_id']) {
      $sql .= " AND (et.district_id = {$filter['district_id']})";
    }
    if ($filter['block_id']) {
      $sql .= " AND (etd.block_id = {$filter['block_id']})";
    }
    if ($filter['management_unit_type']) {
      $sql .= " AND (mangment_type.management_unit_type = '" . $filter['management_unit_type'] . "')";
    }
    // AND (et.year_id = 7
    // AND et.month_id = 8)
    $sql .= " GROUP BY et.unit_id) txn_mon
      ON txn_mon.unit_id = units.unit_id";
    // echo $sql;
    // exit;
    return $this->db->query($sql)->getResult();
  }
}
