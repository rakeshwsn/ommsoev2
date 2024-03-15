<?php

namespace Admin\Users\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'member';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name' => 'trim|required|max_length[100]',
        'designation' => 'required|trim|max_length[100]'
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

    public function getAll($data = [])
    {
        $builder = $this->db->table($this->table);
        $this->filter($builder, $data);

        $builder->select('*');

        $sort = isset($data['sort']) ? $data['sort'] : 'name';
        $order = isset($data['order']) && $data['order'] == 'desc' ? 'desc' : 'asc';
        $builder->orderBy($sort, $order);

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 10;
            }
            $builder->limit((int)$data['limit'], (int)$data['start']);
        }

        if (!empty($data['filter_search'])) {
            $builder->where("name LIKE '%" . $data['filter_search'] . "%'");
        }

        $res = $builder->get()->getResult();

        return $res;
    }

    public function getTotal($data = [])
    {
        $builder = $this->db->table($this->table);
        $this->filter($builder, $data);

        $count = $builder->countAllResults();

        return $count;
    }

    private function filter($builder, $data)
    {
        // Add any additional filters here
    }
}
