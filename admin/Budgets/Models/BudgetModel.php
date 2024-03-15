<?php

namespace Admin\Budgets\Models;

use Admin\Localisation\Models\BlockModel;
use CodeIgniter\Model;

class BudgetModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'soe_budgets';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $protectFields = false;
    protected $allowedFields = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = '';
    protected $updatedField = '';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function editBudget($budget_plan_id, $data)
    {
        $this->db->table("{$this->table}")->where("budget_plan_id", $budget_plan_id)->delete();

        $budget_data = [];
        foreach ($data['budget'] as $budget) {
            $budget_data[] = [
                "budget_plan_id" => $budget_plan_id,
                "component_id" => $budget['component_id'],
                "unit_cost" => (float)$budget['unit_cost'],
                "physical" => (float)$budget['physical'],
                "financial" => (float)$budget['financial'],
                "phase" => $data['phase'],
                "year" => $data['year'],
                "block_category" => $budget['category']
            ];
        }

        $builder = $this->db->table($this->table);
        $builder->insertBatch($budget_data);

        // ... (rest of the method)
    }

    // ... (other methods)

    protected function getBudgetDetailsQuery($filter)
    {
        $query = $this->db
            ->table('soe_components_agency a')
            ->select('c.*, a.fund_agency_id')
            ->join('soe_components c', 'component_id = c.ID', 'left')
            ->where('a.agency_type_id', $filter['agency_type_id'])
            ->where('a.fund_agency_id', $filter['fund_agency_id']);

        // ... (additional query conditions)

        return $query;
    }

    protected function getBulkBudgetDetailsQuery($filter)
    {
        $query = $this->db
            ->table('soe_components_assign sca')
            ->select('sca.id, sca.component_id, sca.number, sc.description, sca.sort_order, sca.parent, sca.fund_agency_id, sc.row_type, sc.category')
            ->join('soe_components sc', 'sca.component_id = sc.ID', 'left')
            ->where('sca.fund_agency_id', $filter['fund_agency_id']);

        // ... (additional query conditions)

        return $query;
    }

    protected function getMprNewQuery($filter)
    {
        $query = $this->db
            ->table('soe_components_assign sca')
            ->select('comp.*, bud.phy bud_phy, bud.fin bud_fin, COALESCE(expn_mon.phy, 0) exp_mon_phy, COALESCE(expn_mon.fin, 0) exp_mon_fin, COALESCE(fr_mon.phy, 0) fr_mon_phy, COALESCE(fr_mon.fin, 0) fr_mon_fin, COALESCE(exp_upto.phy, 0) exp_upto_phy, COALESCE(exp_upto.fin, 0) exp_upto_fin, COALESCE(fr_upto.phy, 0) fr_upto_phy, COALESCE(fr_upto.fin, 0) fr_upto_fin, COALESCE(exp_upto_cy.phy, 0) exp_upto_cy_phy, COALESCE(exp_upto_cy.fin, 0) exp_upto_cy_fin, COALESCE(fr_upto_cy.phy, 0) fr_upto_cy_phy, COALESCE(fr_upto_cy.fin, 0) fr_upto_cy_fin')
            ->join('soe_components sc', 'sca.component_id = sc.id', 'left')
            ->join('(SELECT sb.agency_type_id, sb.component_id FROM soe_budgets sb LEFT JOIN soe_budgets_plan sbp ON sb.budget_plan_id=sbp.id WHERE 1=1');

        // ... (additional query conditions)

        return $query;
    }

    // ... (other methods)
}
