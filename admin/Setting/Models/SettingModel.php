<?php

namespace App\Models;

use CodeIgniter\Database\MySQLi\BaseBuilder;
use CodeIgniter\Session\Session;

class SettingModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'config';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = false;
    protected $allowedFields = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name' => [
            'label' => 'Subject',
            'rules' => 'trim|required|max_length[100]'
        ],
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

    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table($this->table);
    }

    public function group()
    {
        $this->builder->select('id, name');
        $this->builder->from('groups');

        if ($this->session->get('type') != 'SUPERADMIN') {
            $this->builder->where('type !=', 'SUPERADMIN');
        }

        $res = $this->builder->get()->getResultArray();
        return $res;
    }

    public function getUsers($data = [])
    {
        $this->builder->select('u.*, gp.name as group_name');
        $this->builder->from('users u');
        $this->builder->join('groups gp', 'gp.id = u.group_id');

        if ($this->session->get('type') != 'SUPERADMIN') {
            $this->builder->where('gp.type !=', 'SUPERADMIN');
        }

        if (!empty($data['filter_search'])) {
            $this->builder->where("CONCAT_WS(' ', u.first_name, u.last_name) LIKE '%{$data['filter_search']}%' OR u.email LIKE '%{$data['filter_search']}%'");
        }

        if (!empty($data['filter_groupid'])) {
            $this->builder->where('u.group_id', $data['filter_groupid']);
        }

        $sort_data = [
            'u.first_name',
            'u.last_name',
            'u.email',
            'gp.name',
            'u.last_login'
        ];

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sort = $data['sort'];
        } else {
            $sort = "u.first_name";
        }

        if (isset($data['order']) && ($data['order'] == 'desc')) {
            $order = "desc";
        } else {
            $order = "asc";
        }

        $this->builder->orderBy($sort, $order);

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 10;
            }
            $this->builder->limit((int)$data['limit'], (int)$data['start']);
        }

        $res = $this->builder->get()->getResultArray();
        return $res;
    }

    public function getTotalUsers($data = [])
    {
        $this->builder->select('COUNT(*) as count');
        $this->builder->from('users u');
        $this->builder->join
