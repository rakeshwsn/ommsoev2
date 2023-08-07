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
       
        foreach($data['budget'] as $budget) {
            $budget_data[] = array(
                "budget_plan_id" => $budget_plan_id,
                "component_id" => $budget['component_id'],
                "unit_cost" => (float)$budget['unit_cost'],
                "physical" => (float)$budget['physical'],
                "financial" => (float)$budget['financial'],
                "phase"=>$data['phase'],
                "year"=>$data['year'],
                "block_category"=>$budget['category']
            );
        }
        //printr($budget_data);

        // $builder->insertBatch($budget_data);
        //$budget_id=$this->db->insertID();
        //add block budget
        // $this->updateBlockBudget($budget_id,$data['fund_agency_id'],$data['phase']);
    
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
            WHERE 1=1";
            if(!empty($filter['agency_type_id'])){
                $sql .= " AND a.agency_type_id = ".$filter['agency_type_id'];
            } else {
                //$sql .= " AND a.agency_type_id IS NULL ";
            }
            
            $sql .= " AND a.fund_agency_id = ".$filter['fund_agency_id'].") sc
            LEFT JOIN soe_components_assign sca
            ON sca.component_id = sc.ID
            AND sca.fund_agency_id = sc.fund_agency_id GROUP BY sca.component_id,sca.number) t1
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
            ON t1.component_id = t2.component_id ORDER BY t1.id
        ";

        //echo $sql."<br>";

        return $this->db->query($sql)->getResultArray();
    }

    public function getBulkBudgetDetails($filter=[]){
        $sql="SELECT
                t1.id,
                t1.component_id,
                t1.number,
                t1.description,
                t1.sort_order,
                t1.parent,
                t1.fund_agency_id,
                0 units,
                0 unit_cost,
                0 physical,
                0 financial,
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
        
        ";
       //echo $sql;
       //exit;
        return $this->db->query($sql)->getResultArray();
    }
    public function getCumulativeBudgetDetails($filter=[]){
        $sql="SELECT
            res.scomponent_id as id,
            res.component_id,
            res.number,
            res.description,
            res.agency_type_id,
            res.unit_cost,
            res.physical,
            res.financial,
            res.sort_order,
            res.parent,
            res.fund_agency_id,
            res.row_type,
            res.category
        FROM (SELECT
            comp.*,
            bud.unit_cost,
            bud.phy physical,
            bud.fin financial
            FROM (SELECT
                sca.id scomponent_id,
                sc.id component_id,
                sca.fund_agency_id,
                sca.number,
                sc.description,
                sca.parent,
                sca.sort_order,
                sc.row_type,
                sc.category,
                agency_type_id
            FROM (SELECT
                c.*,
                sca.fund_agency_id,
                agency_type_id
                FROM soe_components_agency sca
                LEFT JOIN soe_components c
                    ON component_id = c.id
                WHERE 1 = 1
                AND sca.agency_type_id IS NULL
                AND sca.fund_agency_id = ".$filter['fund_agency_id'].") sc
                LEFT JOIN soe_components_assign sca
                ON sca.component_id = sc.id
                AND sca.fund_agency_id = sc.fund_agency_id
            GROUP BY component_id,
                    sca.number) comp
            LEFT JOIN user_group ug
                ON comp.agency_type_id = ug.id
            LEFT JOIN (SELECT
                component_id,
                agency_type_id,
                units,
                unit_cost,
                SUM(physical) phy,
                SUM(financial) fin,
                block_category
                FROM soe_budgets_plan bp
                LEFT JOIN soe_budgets b
                    ON b.budget_plan_id = bp.id
                WHERE fund_agency_id = ".$filter['fund_agency_id']."
                AND b.deleted_at IS NULL AND bp.deleted_at IS NULL and bp.year = ".$filter['year']."
                AND bp.district_id = ".$filter['district_id']."
                GROUP BY b.component_id) bud
                ON bud.component_id = comp.component_id) res
            ORDER BY sort_order";
            //echo $sql;
            return $this->db->query($sql)->getResultArray();
    }

    public function getBudgetSummeryByBlock($filter=[]) {

        $sql = "SELECT
            b.budget_plan_id,
            bp.district_id,
            bp.block_id,
            COALESCE(sb.name, sd.name) AS block_name,
            bp.fund_agency_id,
            sfa.name fund_agency,
            sy.id year_id,
            sy.name year,
            SUM(b.physical) phy,
            SUM(b.financial) fin,
            bp.status
            FROM soe_budgets_plan bp
            LEFT JOIN soe_budgets b
            ON b.budget_plan_id = bp.id
            LEFT JOIN soe_fund_agency sfa
            ON bp.fund_agency_id = sfa.id
            LEFT JOIN soe_years sy
            ON bp.year = sy.id
            LEFT JOIN soe_districts sd
            ON bp.district_id = sd.id
            LEFT JOIN soe_blocks sb
            ON bp.block_id = sb.id
        WHERE bp.year = ".$filter['year']."
        AND bp.fund_agency_id = ".$filter['fund_agency_id']."
        AND bp.district_id = ".$filter['district_id']."
        AND b.deleted_at IS NULL
        AND bp.deleted_at IS NULL
        GROUP BY bp.block_id
        ORDER BY bp.block_id";
       //echo $sql;
        return $this->db->query($sql)->getResultArray();

    }


}
