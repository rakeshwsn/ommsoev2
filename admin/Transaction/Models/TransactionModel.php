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

    protected $joinTables = [
        'soe_years' => 'year',
        'soe_blocks' => 'block_id',
        'soe_districts' => 'district_id',
        'user_group' => 'agency_type_id'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect('default');
    }

    public function getAll($filter = [])
    {
        $builder = $this->db->table($this->table);

        $builder->select('st.*, y.name year, b.name block, d.name district, ug.name agency_type');
        $builder->join($this->joinTables, $builder->getCompiledSelect(), 'left');

        $this->applyFilters($builder, $filter);

        $query = $builder->get();
        return $query->getResult();
    }

    public function getTotal($filter = [])
    {
        $builder = $this->db->table($this->table);

        $builder->select('COUNT(*) total');
        $this->applyFilters($builder, $filter);

        $query = $builder->get();
        return $query->getRow()->total;
    }

    protected function applyFilters(Builder $builder, array $filter)
    {
        if (isset($filter['transaction_type']) && !empty($filter['transaction_type'])) {
            $builder->where('st.transaction_type', $filter['transaction_type']);
        } else {
            $builder->where('st.transaction_type !=', 'refund');
        }

        if (isset($filter['filter_search']) && !empty($filter['filter_search'])) {
            $builder->groupStart()
                ->like('y.name', $filter['filter_search'])
                ->orLike('c.name', $filter['filter_search'])
                ->groupEnd();
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
    }

    // Add other methods as needed
}

