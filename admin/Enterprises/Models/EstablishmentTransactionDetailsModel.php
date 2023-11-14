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
    txn_dtl.txn_id,
    txn_dtl.id,
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
    txn_dtl.period period_type,txn_dtl.created_at
  FROM (SELECT
      etd.id id,
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
      $sql .= " AND txn_dtl.id = " . $filter['id'];
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
    if (isset($filter['period_type'])) {
      $sql .= " AND txn_dtl.period_type = " . $filter['period_type'];
    }
    // $sql .=  " GROUP BY unit.units";
    //  echo $sql;exit;
    return $this->db->query($sql)->getResult();
  }
  
}
