<?php

namespace Admin\Component\Models;

use CodeIgniter\Model;

class ComponentModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'soe_components';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getAll($filter=[]) {
        $this->select('id,number,description,parent,sort_order,row_type')
            ->where('deleted_at',null);
        if(!empty($filter['fund_agency_id'])){
            $this->where('fund_agency_id',$filter['fund_agency_id']);
        }
        $this->orderBy('sort_order','ASC');
        return $this->asArray()->findAll();
    }

    public function getByBlock($block_id) {
        $sql = "SELECT
  c.id component_id,
  c.number,
  c.description,
  c.parent,
  c.sort_order,
  c.row_type
FROM soe_agency_components ac
  LEFT JOIN soe_components c
    ON ac.component_id = c.id WHERE block_id=$block_id
ORDER BY c.sort_order";

        return $this->db->query($sql)->getResultArray();
    }

    public function getComponents($filter=[]) {
        $sql = "SELECT
  bl_comp.assign_id,
  bl_comp.component_id,
  bl_comp.number,
  bl_comp.sort_order,
  bl_comp.parent,
  bl_comp.fund_agency_id,
  bl_comp.description,
  bl_comp.row_type,
  bl_comp.category,
  bl_comp.block_id,
  bl_comp.district_id,
  bl_comp.phase,
  bud.agency_type_id
FROM (SELECT
    ac.*,
    bl.id block_id,
    bl.district_id,
    bl.phase
  FROM vw_agency_components ac
    LEFT JOIN soe_blocks bl
      ON bl.fund_agency_id = ac.fund_agency_id) bl_comp
  LEFT JOIN (SELECT
      bud.agency_type_id,
      pl.fund_agency_id,
      bud.component_id
    FROM soe_budgets bud
      LEFT JOIN soe_budgets_plan pl
        ON bud.budget_plan_id = pl.id
    GROUP BY bud.component_id) bud
    ON bl_comp.component_id = bud.component_id
WHERE 1=1";
        if(!empty($filter['block_id'])){
            $sql .= " AND block_id = ".$filter['block_id'];
        }

        if(!empty($filter['district_id'])){
            $sql .= " AND district_id = ".$filter['district_id'];
        }
        if (!empty($filter['user_group'])) {
            $user_group = (array)$filter['user_group'];
            $sql .= " AND (agency_type_id IN (".implode(',',$user_group).") OR bud.agency_type_id IS NULL)";
        }
        if (!empty($filter['component_category'])) {
            $sql .= " AND bl_comp.category='".$filter['component_category']."'";
        }

        $sql .= " ORDER by sort_order";
//        echo $sql;exit;
        return $this->db->query($sql)->getResultArray();
    }

    public function getComponents_bak($filter=[])
    {
        $sql = "SELECT
  c.id component_id,
  c.number,
  c.description,
  c.parent,
  c.sort_order,
  c.row_type,
  `at`.name agency_type
FROM soe_agency_components ac
  LEFT JOIN soe_components c
    ON ac.component_id = c.id
  LEFT JOIN user_group `at`
    ON `at`.id = ac.agency_type_id
WHERE ac.deleted_at IS NULL
AND c.deleted_at IS NULL";
        if(!empty($filter['block_id'])){
            $sql .= " AND block_id = ".$filter['block_id'];
        }

        if(!empty($filter['district_id'])){
            $sql .= " AND district_id = ".$filter['district_id'];
        }

        if (!empty($filter['user_group'])) {
            $sql .= " AND user_group = " . $filter['user_group'];
        }
        $sql .= " GROUP BY component_id
ORDER BY c.sort_order";
        return $this->db->query($sql)->getResultArray();
    }
}
