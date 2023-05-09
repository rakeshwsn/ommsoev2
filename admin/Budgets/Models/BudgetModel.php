<?php

namespace Admin\Budgets\Models;

use Admin\Localisation\Models\BlockModel;
use CodeIgniter\Model;

class BudgetModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'soe_budgets';
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
    protected $validationRules      = [

    ];
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

    public function editBudget($budget_plan_id,$data){
        $builder=$this->db->table("{$this->table}");
        $builder->where("budget_plan_id",$budget_plan_id);
        $builder->delete();
        $budget_data=[];
        foreach ($data['phase'] as $phase) {
            foreach($phase['budget'] as $budget) {
                $budget_data[] = array(
                    "budget_plan_id" => $budget_plan_id,
                    "component_id" => $budget['component_id'],
                    "agency_type_id" => (int)$budget['agency_type_id'],
                    "units" => $budget['units'],
                    "unit_cost" => (float)$budget['unit_cost'],
                    "physical" => (float)$budget['physical'],
                    "financial" => (float)$budget['financial'],
                    "phase"=>$phase['phase'],
                    "year"=>$phase['year'],
                    "block_category"=>$budget['category']
                );
            }
            //printr($budget_data);

           // $builder->insertBatch($budget_data);
            //$budget_id=$this->db->insertID();
            //add block budget
           // $this->updateBlockBudget($budget_id,$data['fund_agency_id'],$data['phase']);
        }
        $builder->insertBatch($budget_data);
    }

    public function updateBlockBudget($budget_id,$fund_agency_id,$phase){
        $builder=$this->db->table("soe_block_budgets2");
        $builder->where("budget_id",$budget_id);
        $builder->delete();

        $blocks=(new BlockModel())->where("phase",$phase)->where("fund_agency_id",$fund_agency_id)->findAll();
        $block_budget_data=[];
        foreach($blocks as $block){
            $block_budget_data[]=array(
                'block_id'=>$block->id,
                'budget_id'=>$budget_id,
                'district_id'=>$block->district_id
            );
            //$builder->insert($block_budget_data);

        }
        $builder->insertBatch($block_budget_data);
    }

    public function getTransactionComponents(){
        $sql="SELECT c1.id,c1.component_id old,c2.id new,c1.updated FROM (SELECT stc.id,stc.component_id,sc.description,stc.updated  FROM soe_transaction_components stc LEFT JOIN soe_components sc
ON stc.component_id = sc.id) c1
LEFT JOIN soe_components c2 ON c1.description = c2.description
WHERE c2.id IS NOT NULL AND c1.updated = 0";
        return $this->db->query($sql)->getResultArray();
    }

    public function updateTransaction($id,$data){
        $builder=$this->db->table("soe_transaction_components");
        $builder->where("id",$id);
        $builder->update($data);
    }
    public function getBudgetDetails($filter=[]){
        $sql="SELECT
    t1.id,
    t1.component_id,
    t1.number,
    t1.description,
    t2.agency_type_id,
    t2.units,
    t2.unit_cost,
    t2.physical,
    t2.financial,
    t1.sort_order,
    t1.parent,
    t1.fund_agency_id,
    t1.row_type,
    t1.category
FROM (SELECT
    sca.id,
    sca.component_id,
    sca.number,
    sc.description,
    sca.sort_order,
    sca.parent,
    sca.fund_agency_id,
    sc.row_type,
    sc.category
  FROM (SELECT
      c.*,
      a.fund_agency_id
    FROM soe_components_agency a
      LEFT JOIN soe_components c
        ON component_id = c.ID
    WHERE a.agency_type_id = ".$filter['agency_type_id']."
    AND a.fund_agency_id = ".$filter['fund_agency_id'].") sc
    LEFT JOIN soe_components_assign sca
      ON sca.component_id = sc.ID
      AND sca.fund_agency_id = sc.fund_agency_id) t1
  LEFT JOIN (SELECT
  bc.component_id,
  bc.agency_type_id,
  bc.units,
  bc.unit_cost,
  bc.physical,
  bc.financial
FROM soe_budgets bc
  LEFT JOIN soe_budgets_plan sbp
    ON bc.budget_plan_id = sbp.id
WHERE bc.budget_plan_id = ".$filter['budget_plan_id']."
AND sbp.year = ".$filter['year']."
AND sbp.fund_agency_id = ".$filter['fund_agency_id'].") t2
    ON t1.component_id = t2.component_id
";
//echo $sql;exit;
        return $this->db->query($sql)->getResultArray();
    }

    public function getMprNew($filter=[]) {

        $last_year = ($filter['year_id']-1);

        $sql = "SELECT
    res.scomponent_id,
  res.component_id,
  `number`,
  description,
  parent,
  sort_order,
  row_type,
  (res.fr_upto_phy - res.exp_upto_phy) ob_phy,
  (res.fr_upto_fin - res.exp_upto_fin) ob_fin,
  res.bud_phy,
  res.bud_fin,
  res.fr_upto_cy_phy fr_upto_phy,
  res.fr_upto_cy_fin fr_upto_fin,
  res.fr_mon_phy,
  res.fr_mon_fin,
  (res.fr_upto_cy_phy + res.fr_mon_phy) fr_cum_phy,
  (res.fr_upto_cy_fin + res.fr_mon_fin) fr_cum_fin,
  res.exp_upto_cy_phy exp_upto_phy,
  res.exp_upto_cy_fin exp_upto_fin,
  res.exp_mon_phy,
  res.exp_mon_fin,
  (res.exp_upto_cy_phy + res.exp_mon_phy) exp_cum_phy,
  (res.exp_upto_cy_fin + res.exp_mon_fin) exp_cum_fin,
  (res.fr_upto_phy - res.exp_upto_phy + res.fr_mon_phy - res.exp_mon_phy) cb_phy,
  (res.fr_upto_fin - res.exp_upto_fin + res.fr_mon_fin - res.exp_mon_fin) cb_fin
FROM (SELECT
    comp.*,
    bud.phy bud_phy,
    bud.fin bud_fin,
    COALESCE(expn_mon.phy, 0) exp_mon_phy,
    COALESCE(expn_mon.fin, 0) exp_mon_fin,
    COALESCE(fr_mon.phy, 0) fr_mon_phy,
    COALESCE(fr_mon.fin, 0) fr_mon_fin,
    COALESCE(exp_upto.phy, 0) exp_upto_phy,
    COALESCE(exp_upto.fin, 0) exp_upto_fin,
    COALESCE(fr_upto.phy, 0) fr_upto_phy,
    COALESCE(fr_upto.fin, 0) fr_upto_fin,
    COALESCE(exp_upto_cy.phy, 0) exp_upto_cy_phy,
    COALESCE(exp_upto_cy.fin, 0) exp_upto_cy_fin,
    COALESCE(fr_upto_cy.phy, 0) fr_upto_cy_phy,
    COALESCE(fr_upto_cy.fin, 0) fr_upto_cy_fin
  FROM (SELECT
            sca.id scomponent_id,
  sc.id component_id,
  sca.number,
  sc.description,
  sca.parent,
  sca.sort_order,
  sc.row_type,
  sc.category
FROM (SELECT * FROM soe_components_assign
WHERE deleted_at IS NULL AND fund_agency_id=".$filter['fund_agency_id'].") sca
  LEFT JOIN soe_components sc
    ON sca.component_id = sc.id
    LEFT JOIN (SELECT sb.agency_type_id,sb.component_id FROM soe_budgets sb LEFT JOIN soe_budgets_plan sbp ON sb.budget_plan_id=sbp.id where 1=1 ";
    if(!empty($filter['fund_agency_id'])){
        $sql .= " AND sbp.fund_agency_id = ".$filter['fund_agency_id'];
    }
    $sql .= ") sb 
    ON sb.component_id = sc.id where 1=1 ";
        if (!empty($filter['agency_type_id'])) {
            if(is_array($filter['agency_type_id'])){
                $sql .= " AND ( sb.agency_type_id IN (" . implode(',',$filter['agency_type_id']) . ")";
            } else {
                $sql .= " AND ( sb.agency_type_id = " . $filter['agency_type_id'];
            }
            $sql .= " OR sb.agency_type_id is NULL OR sb.agency_type_id = 0 OR sc.row_type = 'heading')";
        }
        if(!empty($filter['category'])){
            if(is_array($filter['category'])){
                $sql .= " AND sc.category IN (" . implode(',',$filter['category']) . ")";
            } else {
                $sql .= " AND sc.category = '".$filter['category']."'";
            }
        }
        $sql .= " ) comp LEFT JOIN
    (SELECT
  sbb.block_id,
  sbb.budget_id,
  sbb.district_id,
  sb.component_id,
  sb.year,
  sb.unit_cost,
  SUM(sb.physical) phy,
  SUM(sb.financial) fin,
  sb.agency_type_id
  FROM vw_block_budget sbb
  LEFT JOIN soe_budgets sb
    ON sbb.budget_id = sb.id
WHERE sb.deleted_at IS NULL";
        if(!empty($filter['fund_agency_id'])){
            $sql .= " AND sbb.fund_agency_id = ".$filter['fund_agency_id'];
        }

        if(!empty($filter['block_id'])){
            $sql .= " AND sbb.block_id = ".$filter['block_id'];
        }
        if(!empty($filter['district_id'])){
            $sql .= " AND sbb.district_id = ".$filter['district_id'];
        }
        if(!empty($filter['year_id'])){
            $sql .= " AND sb.year = ".$filter['year_id'];
        }
        if (!empty($filter['agency_type_id'])) {
            if(is_array($filter['agency_type_id'])){
                $sql .= " AND ( sb.agency_type_id IN (" . implode(',',$filter['agency_type_id']) . ")";
            } else {
                $sql .= " AND ( sb.agency_type_id = " . $filter['agency_type_id'];
            }
            $sql .= " OR sb.agency_type_id is NULL)";
        }
        $sql .= " GROUP BY sb.component_id) bud ON bud.component_id=comp.component_id";
        /* ******************transaction starts****************** */
        /* ******************month's expense****************** */
        $sql .= " LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'expense'";
        $sql .= " AND t.status = 1";

        if(isset($filter['block_user']) && $filter['block_user']){
            if(!empty($filter['user_id'])){
                $sql .= " AND t.user_id = ".$filter['user_id'];
            }
        }
        if(!empty($filter['block_id'])){
            $sql .= " AND t.block_id = ".$filter['block_id'];
        }
        if(!empty($filter['district_id'])){
            $sql .= " AND t.district_id = ".$filter['district_id'];
        }
        if(!empty($filter['month_id'])){
            $sql .= " AND t.month = ".$filter['month_id'];
        }
        if(!empty($filter['year_id'])){
            $sql .= " AND t.year = ".$filter['year_id'];
        }
        if(!empty($filter['fund_agency_id'])){
            $sql .= " AND t.fund_agency_id = ".$filter['fund_agency_id'];
        }
        $sql .= " GROUP BY tc.component_id) expn_mon
      ON comp.component_id = expn_mon.component_id";
        /* ******************month's fundreceipt****************** */
        $sql .= " LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'fund_receipt'";
        $sql .= " AND t.status = 1";
        if(isset($filter['block_user']) && $filter['block_user']){
            if(!empty($filter['user_id'])){
                $sql .= " AND t.user_id = ".$filter['user_id'];
            }
        } else if(empty($filter['block_id'])) {
            // exclude block fund receipt when user is not block user.
            $sql .= " AND t.agency_type_id NOT IN (" . implode(',',$filter['block_users']).")";
        }

        if(!empty($filter['district_id'])){
            $sql .= " AND t.district_id = ".$filter['district_id'];
        }
        if(!empty($filter['block_id'])){
            $sql .= " AND t.block_id = ".$filter['block_id'];
            if(!empty($filter['block_user_id'])){
                $sql .= " AND t.user_id = ".$filter['block_user_id'];
            }
        }
        if(!empty($filter['month_id'])){
            $sql .= " AND t.month = ".$filter['month_id'];
        }
        if(!empty($filter['year_id'])){
            $sql .= " AND t.year = ".$filter['year_id'];
        }
        if(!empty($filter['fund_agency_id'])){
            $sql .= " AND t.fund_agency_id = ".$filter['fund_agency_id'];
        }
        $sql .= " GROUP BY tc.component_id) fr_mon
      ON comp.component_id = fr_mon.component_id";
        /* ******************expense upto last month****************** */
        $sql .= "
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'expense'";
        $sql .= " AND t.status = 1";
        if(isset($filter['block_user']) && $filter['block_user']){
            if(!empty($filter['user_id'])){
                $sql .= " AND t.user_id = ".$filter['user_id'];
            }
        }
        if(!empty($filter['block_id'])){
            $sql .= " AND t.block_id = ".$filter['block_id'];
        }
        if(!empty($filter['district_id'])){
            $sql .= " AND t.district_id = ".$filter['district_id'];
        }
        if(!empty($filter['month_id']) && !empty($filter['year_id'])){
            $this_month = ($filter['month_id'] - 1);
            $sql .= " AND ((t.year BETWEEN 0 AND $last_year)
      OR (t.year = ".$filter['year_id']."
      AND t.month BETWEEN 0 AND $this_month))";
        }
        if(!empty($filter['fund_agency_id'])){
            $sql .= " AND t.fund_agency_id = ".$filter['fund_agency_id'];
        }
        $sql .= " GROUP BY tc.component_id) exp_upto
      ON comp.component_id = exp_upto.component_id";
        /* ******************fundreceipt upto last month****************** */
        $sql .= "
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'fund_receipt'";
        $sql .= " AND t.status = 1";
        if(isset($filter['block_user']) && $filter['block_user']){
            if(!empty($filter['user_id'])){
                $sql .= " AND t.user_id = ".$filter['user_id'];
            }
        } else if(empty($filter['block_id'])) {
            // exclude block fund receipt when user is not block user.
            $sql .= " AND t.agency_type_id NOT IN (" . implode(',',$filter['block_users']).")";
        }

        if(!empty($filter['block_id'])){
            $sql .= " AND t.block_id = ".$filter['block_id'];
            if(!empty($filter['block_user_id'])){
                $sql .= " AND t.user_id = ".$filter['block_user_id'];
            }
        }
        if(!empty($filter['district_id'])){
            $sql .= " AND t.district_id = ".$filter['district_id'];
        }
        if(!empty($filter['month_id']) && !empty($filter['year_id'])){
            $last_month = ($filter['month_id'] - 1);
            $sql .= " AND ((t.year BETWEEN 0 AND $last_year)
      OR (t.year = ".$filter['year_id']."
      AND t.month BETWEEN 0 AND $last_month))";
        }
        if(!empty($filter['fund_agency_id'])){
            $sql .= " AND t.fund_agency_id = ".$filter['fund_agency_id'];
        }
        $sql .= " GROUP BY tc.component_id) fr_upto
      ON comp.component_id = fr_upto.component_id
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'fund_receipt'";
        $sql .= " AND t.status = 1";
        if(isset($filter['block_user']) && $filter['block_user']){
            if(!empty($filter['user_id'])){
                $sql .= " AND t.user_id = ".$filter['user_id'];
            }
        } else if(empty($filter['block_id'])) {
            // exclude block fund receipt when user is not block user.
            $sql .= " AND t.agency_type_id NOT IN (" . implode(',',$filter['block_users']).")";
        }
        if(!empty($filter['block_id'])){
            $sql .= " AND t.block_id = ".$filter['block_id'];
            if(!empty($filter['block_user_id'])){
                $sql .= " AND t.user_id = ".$filter['block_user_id'];
            }
        }
        if(!empty($filter['district_id'])){
            $sql .= " AND t.district_id = ".$filter['district_id'];
        }
        if(!empty($filter['month_id']) && !empty($filter['year_id'])){
            $last_month = ($filter['month_id'] - 1);
            $sql .= " AND (t.year = ".$filter['year_id']."
      AND t.month BETWEEN 0 AND $last_month)";
        }
        if(!empty($filter['fund_agency_id'])){
            $sql .= " AND t.fund_agency_id = ".$filter['fund_agency_id'];
        }
        $sql .= " GROUP BY tc.component_id) fr_upto_cy
      ON comp.component_id = fr_upto_cy.component_id
    LEFT JOIN (SELECT
        tc.component_id,
        SUM(physical) phy,
        SUM(financial) fin
      FROM soe_transactions t
        RIGHT JOIN soe_transaction_components tc
          ON t.id = tc.transaction_id
      WHERE t.deleted_at IS NULL
      AND tc.deleted_at IS NULL
      AND t.transaction_type = 'expense'";
        $sql .= " AND t.status = 1";
        if(isset($filter['block_user']) && $filter['block_user']){
            if(!empty($filter['user_id'])){
                $sql .= " AND t.user_id = ".$filter['user_id'];
            }
        }
        if(!empty($filter['block_id'])){
            $sql .= " AND t.block_id = ".$filter['block_id'];
        }
        if(!empty($filter['district_id'])){
            $sql .= " AND t.district_id = ".$filter['district_id'];
        }
        if(!empty($filter['month_id']) && !empty($filter['year_id'])){
            $last_month = ($filter['month_id'] - 1);
            $sql .= " AND (t.year = ".$filter['year_id']."
      AND t.month BETWEEN 0 AND $last_month)";
        }
        if(!empty($filter['fund_agency_id'])){
            $sql .= " AND t.fund_agency_id = ".$filter['fund_agency_id'];
        }
        $sql .= " GROUP BY tc.component_id) exp_upto_cy
      ON comp.component_id = exp_upto_cy.component_id) res ORDER BY sort_order";
//echo $sql;
        return $this->db->query($sql)->getResultArray();

    }


}
