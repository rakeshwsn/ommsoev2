<?php

namespace App\Models;

use CodeIgniter\Model;

class ComponentsAssignModel extends Model
{
    protected $table = 'soe_components_assign';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'component_id', 'number', 'parent', 'sort_order', 'fund_agency_id', 'row_type'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [];

    public function getAssignComponent($fundAgencyId)
    {
        $builder = $this->builder();
        $builder->select('sca.id, sca.component_id, sca.number, sc.description, sca.parent, sca.sort_order, sca.fund_agency_id, sc.row_type');
        $builder->join('soe_components sc', 'sca.component_id = sc.id', 'left');
        $builder->where('sca.fund_agency_id', $fundAgencyId);
        $builder->where('sca.deleted_at', null);
        $builder->orderBy('sca.sort_order', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function getMaxSortorder($id)
    {
        return $this->where('fund_agency_id', $id)
            ->selectMax('sort_order')
            ->get()
            ->getRow()
            ->sort_order ?? 0;
    }
}
