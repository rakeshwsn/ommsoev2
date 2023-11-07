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
    et1.id,
    et.unit_id,
    et.period,
    et.district_id,
    et.year_id,
    et.month_id,
    et1.transaction_id,
    et1.block_id,
    et1.gp_id,
    et.created_at,
    et1.village_id,
    et1.no_of_days_functional,
    et1.produced,
    et1.charges_per_qtl,
    et1.total_expend,
    et1.total_turnover,
    sm.name month_name,
    dy.name year_name,
    sd.name district_name,
    sb.name block_name,
    g.name gp_name,
    v.name village_name,
    eu.name unit_name,
    CASE WHEN et.period = 1 THEN '1st fortnight' ELSE '2nd fortnight' END  period_type
    FROM enterprises_transaction et
    LEFT JOIN enterprises_transactiondetails et1
      ON et.id = et1.transaction_id
    LEFT JOIN soe_months sm
      ON sm.id = et.month_id
    LEFT JOIN dashboard_years dy
      ON dy.id = et.year_id
    LEFT JOIN soe_districts sd
      ON sd.id = et.district_id
    LEFT JOIN soe_blocks sb
      ON sb.id = et1.block_id
    LEFT JOIN grampanchayat g
      ON g.id = et1.gp_id
    LEFT JOIN villages v
      ON v.id = et1.village_id
    LEFT JOIN enterprises_units eu
      ON eu.id = et.unit_id
  WHERE et1.deleted_at IS NULL ";
  if (isset($filter['id'])) {
    $sql .= " AND et1.id = " . $filter['id'];
  }
    if (isset($filter['year_id'])) {
      $sql .= " AND et.year_id = " . $filter['year_id'];
    }
    if (isset($filter['district_id'])) {
      $sql .= " AND et.district_id = " . $filter['district_id'];
    }
    if (isset($filter['month_id'])) {
      $sql .= " AND et.month_id = " . $filter['month_id'];
    }
    if (isset($filter['period'])) {
      $sql .= " AND et.period = " . $filter['period'];
    }
    // $sql .=  " GROUP BY unit.units";
    // echo $sql;exit;
    return $this->db->query($sql)->getResult();
  }
 
}
