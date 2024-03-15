<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'soe_transactions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'year', 'month', 'block_id', 'district_id', 'agency_type_id', 'user_id', 'transaction_type', 'status', 'fund_agency_id'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect('default');
    }

    public function getAll($filter = [])
    {
        $builder = $this->db->table('soe_transactions st');
        $builder->select('st.*, y.name year, b.name block, d.name district, ug.name agency_type');
        $builder->join('soe_years y', 'y.id = st.year', 'left');
        $builder->join('soe_blocks b', 'b.id = st.block_id', 'left');
        $builder->join('soe_districts d', 'd.id = st.district_id', 'left');
        $builder->join('user_group ug', 'ug.id = st.agency_type_id', 'left');

        if (isset($filter['transaction_type']) && !empty($filter['transaction_type'])) {
            $builder->where('st.transaction_type', $filter['transaction_type']);
        } else {
            $builder->where('st.transaction_type !=', 'refund');
        }

        if (isset($filter['filter_search']) && !empty($filter['filter_search'])) {
            $builder->like('y.name', $filter['filter_search']);
            $builder->orLike('c.name', $filter['filter_search']);
        }

        if (isset($filter['agency_type_id']) && !empty($filter['agency_type_id'])) {
            $builder->where('st.agency_type_id', $filter['agency_type_id']);
        }

        if (isset($filter['district_id']) && !empty($filter['district_id'])) {
            $builder->where('st.district_id', $filter['district_id']);
        }

        if (isset($filter['user_id']) && !empty($filter['user_id'])) {
            $builder->where('st.user_id', $filter['user_id']);
        }

        if (isset($filter['sort']) && !empty($filter['sort'])) {
            $builder->orderBy($filter['sort'], $filter['order']);
        } else {
            $builder->orderBy('st.year DESC, st.month');
        }

        if (isset($filter['start']) || isset($filter['limit'])) {
            if (isset($filter['start'])) {
                $builder->where('st.id >=', $filter['start']);
            }
            if (isset($filter['limit'])) {
                $builder->limit($filter['limit']);
            }
        }

        $query = $builder->get();
        return $query->getResult();
    }

    public function getTotal($filter = [])
    {
        $builder = $this->db->table('soe_transactions st');
        $builder->select('COUNT(*) total');

        if (isset($filter['transaction_type']) && !empty($filter['transaction_type'])) {
            $builder->where('st.transaction_type', $filter['transaction_type']);
        } else {
            $builder->where('st.transaction_type !=', 'refund');
        }

        if (isset($filter['agency_type_id']) && !empty($filter['agency_type_id'])) {
            $builder->where('st.agency_type_id', $filter['agency_type_id']);
        }

        if (isset($filter['user_id']) && !empty($filter['user_id'])) {
            $builder->where('st.user_id', $filter['user_id']);
        }

        if (isset($filter['filter_search']) && !empty($filter['filter_search'])) {
            $builder->like('y.name', $filter['filter_search']);
            $builder->orLike('c.name', $filter['filter_search']);
        }

        $query = $builder->get();
        return $query->getRow()->total;
    }

    // Add other methods as needed
}
