<?php

namespace Admin\Components\Models;

use CodeIgniter\Model;

class ComponentsModel extends Model
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
    protected $validationRules      = [
        'description' => array(
            'label' => 'Description',
            'rules' => 'trim|required|max_length[500]'
        )
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


    public function getTotal($data = array()) {
        $builder=$this->db->table("{$this->table} sc");
        $this->filter($builder,$data);
        $count = $builder->countAllResults();
        return $count;
    }

    public function getAll($data = array()){
        $builder=$this->db->table("{$this->table} sc");
        $this->filter($builder,$data);

        $builder->select("sc.*");

        if (isset($data['sort']) && $data['sort']) {
            $sort = $data['sort'];
        } else {
            $sort = "sc.slug";
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
        $res = $builder->get()->getResult();
        return $res;

    }

    private function filter($builder,$data){
        if (!empty($data['filter_search'])) {
            $builder->where("(
                sc.description LIKE '%{$data['filter_search']}%'
				or sc.slug LIKE '%{$data['filter_search']}%'
				or sc.row_type LIKE '%{$data['filter_search']}%'
				or sc.tags LIKE '%{$data['filter_search']}%')"
            );
        }
    }

    public function getComponents($filter=[]) {
        $filter['fund_agency_id'] = isset($filter['fund_agency_id']) ? $filter['fund_agency_id']:0;

        $sql = "SELECT
      sca.id assign_id,
      sc.id component_id,
      sca.number,
      sc.description,
      sca.parent,
      sca.sort_order,
      sc.row_type,
      sc.category
    FROM (SELECT
        *
      FROM soe_components_assign
      WHERE deleted_at IS NULL
      AND fund_agency_id = 1) sca
      LEFT JOIN soe_components sc
        ON sca.component_id = sc.id
      LEFT JOIN (SELECT
          sb.agency_type_id,
          sb.component_id
        FROM soe_budgets sb
          LEFT JOIN soe_budgets_plan sbp
            ON sb.budget_plan_id = sbp.id
        WHERE sbp.fund_agency_id = ".(int)$filter['fund_agency_id']."
        GROUP BY sb.component_id) sb
        ON sb.component_id = sc.id
    WHERE 1 = 1";
        if(!empty($filter['user_group'])){
            $user_group = (array)$filter['user_group'];
            $sql .= "
    AND (sb.agency_type_id IN (".implode(',',$user_group).")
    OR sb.agency_type_id IS NULL
    OR sb.agency_type_id = 0)";
        }
        if (!empty($filter['component_category'])) {
            if(is_array($filter['component_category'])){
                $sql .= " AND sc.category IN ('" . implode('\',\'',$filter['component_category']) . "')";
            } else {
                $sql .= " AND sc.category = '".$filter['component_category']."'";
            }
        }

        $sql .= " ORDER by sort_order";
//echo $sql;exit;
        return $this->db->query($sql)->getResultArray();
    }

}
