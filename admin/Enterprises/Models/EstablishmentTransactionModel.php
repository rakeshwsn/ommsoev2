<?php

namespace Admin\Enterprises\Models;

use CodeIgniter\Model;

class EstablishmentTransactionModel extends Model
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


  public function getAll($unit_id, $district_id)
  {
    $sql = "SELECT
    e.id,
    e.unit_id,
    e.block_id,
    e.district_id,
    e.gp_id,
    e.village_id,
    e.managing_unit_name shg_name,
    eu.name unit_name,
    sb.name block,
    g.name grampanchayat,
    v.name villages,
    e.district_id
  FROM enterprises e
    LEFT JOIN soe_blocks sb
      ON e.block_id = sb.id
    LEFT JOIN soe_grampanchayats g
      ON e.gp_id = g.id
    LEFT JOIN villages v
      ON e.village_id = v.id
    LEFT JOIN enterprises_units eu
      ON e.unit_id = eu.id where sb.is_program=1 AND e.unit_id = $unit_id and e.district_id= $district_id ";

    return $this->db->query($sql)->getResult();
  }

  public function getCheckEnterpriseTransaction($data = [])
  {
    $this->db->select("*");
    $this->db->from("enterprises e");

    if (!empty($data['district_id'])) {
      $this->db->where("e.district_id", $data['district_id']);
    }
    if (!empty($data['year_id'])) {
      $this->db->where("e.year_id", $data['year_id']);
    }
    if (!empty($data['month_id'])) {
      $this->db->where("e.month_id", $data['month_id']);
    }
    if (!empty($data['period'])) {
      $this->db->where("e.period", $data['period']);
    }


    $res = $this->db->get()->row_array();

    return $res;
  }
}
