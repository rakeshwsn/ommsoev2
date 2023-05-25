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
        $builder->join("soe_blocks sb","sb.id=sbp.block_id","left");
        $builder->join("soe_districts sd","sd.id=sbp.district_id","left");
        $this->filter($builder,$data);

        $builder->select("sbp.*,sfa.name as fund_agency,sd.name as district,sb.name as block");

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
       // $builder->groupBy("sbp.fund_agency_id");
        $res = $builder->get()->getResult();
       // echo $this->db->getLastQuery();
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
        $builder->join("soe_districts d","sbp.district_id=d.id","left");
        $builder->join("soe_blocks b","sbp.block_id=b.id","left");
        $builder->join("soe_years y","sbp.year=y.id","left");
        $builder->select("sbp.*,d.name as district,b.name as block,y.name as yname, sfa.name as fund_agency");
        $builder->where("sbp.id",$budget_plan_id);
        $res = $builder->get()->getRow();
        return $res;

    }
    private function filter($builder,$data){
        $builder->join("soe_fund_agency fa","sbp.fund_agency_id=fa.id","left");
        $builder->join('soe_districts d','d.id=sbp.district_id','left');
        $builder->join('soe_blocks b','b.id=sbp.block_id','left');
        
        if(!empty($data['filter_district_id'])){
            $builder->where("sbp.district_id  = '".$data['filter_district_id']."'");
        }

        if(!empty($data['filter_block_id'])){
            $builder->where("sbp.block_id  = '".$data['filter_block_id']."'");
        }
		
		if(!empty($data['filter_year'])){
            $builder->where("sbp.year  = '".$data['filter_year']."'");
        }
		
        if(!empty($data['filter_fund_agency_id'])){
            $builder->where("sbp.fund_agency_id  = '".$data['filter_fund_agency_id']."'");
        }
        
        if (!empty($data['filter_search'])) {
            $builder->where("(
                sfa.name LIKE '%{$data['filter_search']}%')"
            );
        }

    }

    public function getBudgetPlanByBlock($data){
        $builder=$this->db->table("{$this->table} sbp");
        $builder->where("sbp.year",$data['year']);
        $builder->where("sbp.fund_agency_id",$data['fund_agency_id']);
        $builder->where("sbp.district_id",$data['district_id']);
        $builder->where("sbp.block_id",$data['block_id']);
        $builder->where("sbp.deleted_at",null);
        $res = $builder->get()->getRow();
        return $res;

    }

}
