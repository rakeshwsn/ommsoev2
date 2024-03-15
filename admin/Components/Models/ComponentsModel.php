<?php

namespace Admin\Components\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\RawSql;

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

        $this->applyFilterSearch($builder, $filter);
        $this->applySorting($builder, $filter);
        $this->applyPagination($builder, $filter);

        return $builder;
    }

    private function applyFilterSearch( $builder, $filter ) {
        if ( !empty($filter['filter_search']) ) {
            $searchTerm = $filter['filter_search'];
            $builder->where(
                new RawSql("sc.description LIKE '%?%' OR sc.slug LIKE '%?%' OR sc.row_type LIKE '%?%' OR sc.tags LIKE '%?%'", [$searchTerm, $searchTerm, $searchTerm, $searchTerm])
            );
        }
    }

    private function applySorting( $builder, $filter ) {
        $sort = isset($filter['sort']) && $filter['sort'] ? $filter['sort'] : "sc.slug";
        $order = isset($filter['order']) && ($filter['order'] == 'desc') ? "desc" : "asc";

        $builder->orderBy($sort, $order);
    }

    private function applyPagination( $builder, $filter ) {
        if ( isset($filter['start']) || isset($filter['limit']) ) {
            $start = isset($filter['start']) ? (int)$filter['start'] : 0;
            $limit = isset($filter['limit']) ? (int)$filter['limit'] : 10;

            if ( $start < 0 ) {
                $start = 0;
            }

            if ( $limit < 1 ) {
                $limit = 10;
            }

            $builder->limit($limit, $start);
        }
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

        $this->applyUserGroupFilter($builder, $filter);
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

    private function applyUserGroupFilter( $builder, $filter ) {
        if ( !empty($filter['user_group']) ) {
            $user_group = (array)$filter['user_group'];
            $builder->whereIn('sb.agency_type_id', $user_group);
        } else {
            $builder->where('sb.agency_type_id IS NULL');
        }
    }
}
