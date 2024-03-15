<?php

namespace App\Models;

use CodeIgniter\Model;

class BudgetPlanModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'soe_budgets_plan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $protectFields = false;
    protected $allowedFields = [
        'year', 'fund_agency_id', 'district_id', 'block_id', 'created_at', 'updated_at', 'deleted_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'year' => 'required|integer',
        'fund_agency_id' => 'required|integer',
        'district_id' => 'integer',
        'block_id' => 'integer'
    ];
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

    public function getTotal($data = [])
    {
        return $this->where($data)->countAllResults();
    }

    public function getAll($data = [])
    {
        $this->select('sbp.*, sfa.name as fund_agency, sd.name as district, sb.name as block');
        $this->join('soe_fund_agency sfa', 'sbp.fund_agency_id = sfa.id', 'left');
        $this->join('soe_districts sd', 'sbp.district_id = sd.id', 'left');
        $this->join('soe_blocks sb', 'sbp.block_id = sb.id', 'left');

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

        $this->orderBy($sort, $order);

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 10;
            }

            $this->limit((int)$data['limit'], (int)$data['start']);
        }

        $this->groupStart();
        $this->where('sbp.deleted_at', null);
        $this->groupEnd();

        $res = $this->findAll();

        return $res;
    }

    public function getBudgetPhase($budget_plan_id)
    {
        $builder = $this->db->table("soe_budgets_phase sbp");
        $builder->select("sbp.*");
        $builder->where("sbp.budget_plan_id", $budget_plan_id);
        $res = $builder->get()->getResultArray();
        return $res;
    }

    public function getBudgetPlan($budget_plan_id)
    {
        $builder = $this->db->table("{$this->table} sbp");
        $builder->join("soe_fund_agency sfa", "sbp.fund_agency_id = sfa.id", "left");
        $builder->join("soe_districts d", "sbp.district_id = d.id", "left");
        $builder->join("soe_blocks b", "sbp.block_id = b.id", "left");
        $builder->join("soe_years y", "sbp.year = y.id", "left");
        $builder->select("sbp.*, d.name as district, b.name as block, y.name as yname, sfa.name as fund_agency");
        $builder->where("sbp.id", $budget_plan_id);
        $res = $builder->get()->getRow();
        return $res;
    }

    public function filter($builder, $data)
    {
        $builder->join("soe_fund_agency sfa", "sbp.fund_agency_id = sfa.id", "left");
        $builder->join("soe_blocks sb", "sb.id = sbp.block_id", "left");
        $builder->join("soe_districts sd", "sd.id = sbp.district_id", "left");

        if (isset($data['filter_district_id'])) {
            $builder->where("sbp.district_id = " . $data['filter_district_id']);
        }

        if (isset($data['filter_block_id'])) {
            $builder->where("sbp.block_id = " . $data['filter_block_id']);
        }

        if (isset($data['filter_year'])) {
            $builder->where("sbp.year = " . $data['filter_year']);
        }

        if (isset($data['filter_fund_agency_id'])) {
            $builder->where("sbp.fund_agency_id = " . $data['filter_fund_agency_id']);
        }

       
