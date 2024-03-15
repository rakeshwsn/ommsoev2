<?php

namespace Admin\Incentive\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ rawSql;
use CodeIgniter\Validation\ Rules;

class IncentiveModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'detailed_incentive_data';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'object';
    protected $useSoftDeletes       = false;
    protected $protectFields        = false;

    // Dates
    protected $useTimestamps        = true;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = '';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name' => [
            'label' => 'Name',
            'rules' => 'trim|required|max_length[100]|is_unique[module.name,id,{id}]',
            'sanitize' => 'string'
        ],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    public function getAll($data)
    {
        $builder = $this->db->table($this->table);
        $this->filter($builder, $data);

        $builder->select("*");

        $sort = !empty($data['sort']) ? $data['sort'] : 'name';
        $order = !empty($data['order']) && $data['order'] == 'desc' ? 'desc' : 'asc';
        $builder->orderBy($sort, $order);

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
              
