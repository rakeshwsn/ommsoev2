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
ORDER BY year 
 
 ";
    //  printr($sql);
    //     exit;
    return $this->db->query($sql)->getResult();
  }
}
