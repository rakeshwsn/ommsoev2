<?php

namespace Admin\Components\Models;

use CodeIgniter\Model;

class ComponentsModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'soe_components';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'description' => 'trim|required|max_length[500]',
    ];

    public function getBaseQuery($filter = [])
    {
        $builder = $this->db->table("{$this->table} sc");

        if (!empty($filter['filter_search'])) {
            $builder->where("(
                sc.description LIKE '%{$filter['filter_search']}%'
                OR sc.slug LIKE '%{$filter['filter_search']}%'
                OR sc.row_type LIKE '%{$filter['filter_search']}%'
                OR sc.tags LIKE '%{$filter['filter_search']}%')"
            );
        }

        if (isset($filter['sort']) && $filter['sort']) {
            $sort = $filter['sort'];
        } else {
            $sort = "sc.slug";
        }

        if (isset($filter['order']) && ($filter['order'] == 'desc')) {
            $order = "desc";
        } else {
            $order = "asc";
        }

        $builder->orderBy($sort, $order);

        if (isset($filter['start']) || isset($filter['limit'])) {
            if ($filter['start'] < 0) {
                $filter['start'] = 0;
            }

            if ($filter['limit'] < 1) {
                $filter['limit'] = 10;
            }
            $builder->limit((int)$filter['limit'], (int)$filter['start']);
        }

        return $builder;
    }

    public function getTotal($data = [])
    {
        $builder = $this->getBaseQuery($data);
        return $builder->countAllResults();
    }

    public function getAll($data = [])
    {
        $builder = $this->getBaseQuery($data);
        $builder->select("sc.*");

        $res = $builder->get()->getResult();
        return $res;
    }

    public function getComponents($filter = [])
    {
        $filter['fund_agency_id'] = isset($filter['fund_agency_id']) ? $filter['fund_agency_id'] : 0;

        $builder = $this->db->table("soe_components sc");
        $builder->select("sca.id assign_id, sc.id component_id, sca.number, sc.description, sca.parent, sca.sort_order, sc.row_type, sc.category, sca.fund_agency_id, sb.agency_type_id");
        $builder->join('soe_components_assign sca', 'sca.component_id = sc.id', 'left');
        $builder->join('soe_budgets sb', 'sb.component_id = sc.id AND sb.fund_agency_id = sc.fund_agency_id', 'left');

        if (!empty($filter['user_group'])) {
            $user_group = (array)$filter['user_group'];
            $builder->whereIn('sb.agency_type_id', $user_group);
        } else {
            $builder->where('sb.agency_type_id IS NULL');
        }

        $builder->where('sc.fund_agency_id', $filter['fund_agency_id']);
        $builder->groupBy('sca.component_id');

        try {
            $res = $builder->get()->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error in ComponentsModel::getComponents: ' . $e->getMessage());
            return [];
        }

        return $res;
    }
}
