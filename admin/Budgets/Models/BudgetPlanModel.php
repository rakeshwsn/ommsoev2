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
       //echo $this->db->getLastQuery();
        return $res;

    }

    public function approveBudget($data,$condition){
        $builder=$this->db->table($this->table);
        $builder->update($data, $condition);
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
        $builder->where("b.is_program",1);
        $res = $builder->get()->getRow();
        return $res;

    }
    private function filter($builder,$data){
        $builder->join("soe_fund_agency sfa","sbp.fund_agency_id=sfa.id","left");
        $builder->join("soe_blocks sb","sb.id=sbp.block_id","left");
        $builder->join("soe_districts sd","sd.id=sbp.district_id","left");
        
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
            $builder->where("sb.name LIKE '%{$data['filter_search']}%'");
        }
       
        $builder->where("sbp.deleted_at IS NULL");
        $builder->where("sb.is_program=1");


    }

    public function getBudgetPlanByBlock($data){
        $builder=$this->db->table("{$this->table} sbp");
        $builder->where("sbp.year",$data['year']);
        $builder->where("sbp.fund_agency_id",$data['fund_agency_id']);
        if($data['district_id']){
            $builder->where("sbp.district_id",$data['district_id']);
        }
       
        $builder->where("sbp.block_id",$data['block_id']);
        
        $builder->where("sbp.deleted_at",null);
        $res = $builder->get()->getRow();
        return $res;

    }

    public function getCumulativeBudget($data){
        $sql="SELECT
        bud.budget_plan_id,
        sd.district_id,
        sd.district,
        bud.fund_agency_id,
        bud.fund_agency,
        bud.year,
        bud.phy,
        bud.fin,
        CASE WHEN bud.status IS NULL THEN 3 ELSE bud.status END AS status
      FROM vw_district_fund_agency sd
        LEFT JOIN (SELECT
            b.budget_plan_id,
            bp.district_id,
            bp.fund_agency_id,
            sfa.name fund_agency,
            sy.id year_id,
            sy.name year,
            SUM(b.physical) phy,
            SUM(financial) fin,
            bp.status
          FROM soe_budgets_plan bp
            LEFT JOIN soe_budgets b
              ON b.budget_plan_id = bp.id
            LEFT JOIN soe_fund_agency sfa
              ON bp.fund_agency_id = sfa.id
            LEFT JOIN soe_years sy
                ON bp.year=sy.id
          WHERE b.deleted_at IS NULL AND bp.deleted_at IS NULL and bp.year = ".$data['filter_year']."
          GROUP BY bp.district_id,bp.fund_agency_id) bud
          ON bud.district_id = sd.district_id
          AND sd.fund_agency_id = bud.fund_agency_id where 1=1";

        if(!empty($data['filter_fund_agency_id'])){
            $sql .= " AND sd.fund_agency_id = ".$data['filter_fund_agency_id'];
        }
        if(!empty($data['filter_district_id'])){
            $sql .= " AND bud.district_id = ".$data['filter_district_id'];
        }
        //echo $sql;
        return $this->db->query($sql)->getResultArray();
    }

}
