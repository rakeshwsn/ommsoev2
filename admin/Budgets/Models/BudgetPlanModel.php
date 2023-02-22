<?php
namespace Admin\Budgets\Models;
use CodeIgniter\Model;
class BudgetPlanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'soe_budgets_plan';
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

    /*public function updateBudgetPlan($id,$data){
        $builder=$this->db->table("{$this->table}");

        $budgetPlandata=array(
            "year"=>$data['year'],
            "fund_agency_id"=>$data['fund_agency_id']
        );

        $builder->where("id",$id);
        $builder->update($budgetPlandata);

        /*$builder=$this->db->table("soe_budgets_phase");
        $builder->where("budget_plan_id",$id);
        $builder->delete();

        if (isset($data['phase'])) {

            foreach ($data['phase'] as $phase) {
                $budget_phase_data=array(
                    "budget_plan_id"=>$id,
                    "total_block"=>$phase['image'],
                    "phase"=>$phase['phase'],
                );
                $builder->insert($budget_phase_data);

            }
        }

        //return "success";
    }*/
    public function getTotal($data = array()) {
        $builder=$this->db->table("{$this->table} sbp");
        $this->filter($builder,$data);
        $count = $builder->countAllResults();
        return $count;
    }

    public function getAll($data = array()){
        $builder=$this->db->table("{$this->table} sbp");
        $builder->join("soe_fund_agency sfa","sbp.fund_agency_id=sfa.id","left");
        $builder->join("soe_blocks sb","sb.fund_agency_id=sfa.id","left");

        $this->filter($builder,$data);

        $builder->select("sbp.*,sfa.name as fund_agency,count(sb.id) total_block");

        if (isset($data['sort']) && $data['sort']) {
            $sort = $data['sort'];
        } else {
            $sort = "sbp.id";
        }

        if (isset($data['order']) && ($data['order'] == 'desc')) {
            $order = "desc";
        } else {
            $order = "asc";
        }
        $builder->orderBy($sort, $order);

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 10;
            }
            $builder->limit((int)$data['limit'],(int)$data['start']);
        }
        $builder->groupBy("sbp.fund_agency_id");
        $res = $builder->get()->getResult();
        return $res;

    }

    public function getBudgetPhase($budget_plan_id){
        $builder=$this->db->table("soe_budgets_phase sbp");
        $builder->select("sbp.*");
        $builder->where("sbp.budget_plan_id",$budget_plan_id);
        $res = $builder->get()->getResultArray();
        return $res;
    }
    public function getBudgetPlan($budget_plan_id){
        $builder=$this->db->table("{$this->table} sbp");
        $builder->join("soe_fund_agency sfa","sbp.fund_agency_id=sfa.id","left");
        $builder->select("sbp.*,sfa.name as fund_agency");
        $builder->where("sbp.id",$budget_plan_id);
        $res = $builder->get()->getRow();
        return $res;

    }
    private function filter($builder,$data){

        if (!empty($data['filter_search'])) {
            $builder->where("(
                sfa.name LIKE '%{$data['filter_search']}%')"
            );
        }
    }

}
