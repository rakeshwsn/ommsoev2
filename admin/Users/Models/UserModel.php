<?php

namespace Admin\Users\Models;

use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class UserModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'user';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'object';
    protected $useSoftDeletes        = true;
    protected $protectFields        = false;
    protected $allowedFields        = [];

    // Dates
    protected $useTimestamps        = true;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'firstname' => [
            'label' => 'Name',
            'rules' => 'trim|required|max_length[100]',
        ],

        'email' => [
            'label' => 'Email',
            'rules' => 'required|trim|valid_email|max_length[255]|is_unique[user.email,id,{id}]',
        ],

        'username' => [
            'label' => 'Username',
            'rules' => 'required|is_unique[user.username,id,{id}]',
        ],
        'password' => [
            'label' => 'Password',
            'rules' => 'required',
        ]
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = ['setPassword', 'gparray', 'setLocalisation', 'resetAssign'];
    protected $afterInsert          = [];
    protected $beforeUpdate         = ['setPassword', 'gparray', 'setLocalisation', 'resetAssign'];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = ['setUserType'];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    public function getAll($filter = [])
    {
        // Sanitize search query
        $filter['filter_search'] = $this->db->escapeString($filter['filter_search'] ?? '');

        $sql = "SELECT
                  u.*, d.name district, b.name block, ug.name role
                FROM user u
                  LEFT JOIN soe_districts d ON d.id = u.district_id
                  LEFT JOIN soe_blocks b ON b.id = u.block_id
                  LEFT JOIN user_group ug ON ug.id = u.user_group_id
                WHERE u.deleted_at IS NULL";

        if (!empty($filter['filter_search'])) {
            $sql .= " AND (
                          CONCAT_WS(' ', u.firstname, u.lastname) LIKE '%{$filter['filter_search']}%'
                          OR u.email LIKE '%{$filter['filter_search']}%'
                          OR u.username LIKE '%{$filter['filter_search']}%'
                          OR d.name LIKE '%{$filter['filter_search']}%'
                          OR b.name LIKE '%{$filter['filter_search']}%'
                          OR ug.name LIKE '%{$filter['filter_search']}%'
                        )";
        }

        // Order by
        if (isset($filter['sort']) && $filter['sort']) {
            $sort = $filter['sort'];
        } else {
            $sort = "d.name, b.name";
        }

        if (isset($filter['order']) && ($filter['order'] == 'desc')) {
            $order = "desc";
        } else {
            $order = "asc";
        }
        $sql .= " ORDER BY $sort $order ";

        // Pagination
        if (isset($filter['start']) || isset($filter['limit'])) {
            if ($filter['start'] < 0) {
                $filter['start'] = 0;
            }

            if ($filter['limit'] < 1) {
                $filter['limit'] = 10;
            }
        }
        $sql .= " LIMIT " . $filter['start'] . ', ' . $filter['limit'];

        $result = $this->db->query($sql);

        if ($result->getNumRows() > 0) {
            return $result->getResult();
        } else {
            return [];
        }
    }

    public function getTotal($filter = [])
    {
        // Validate input
        if (!isset($filter['agency_type_id']) && !isset($filter['district_id']) && !isset($filter['month']) && !isset($filter['year'])) {
            return 0;
        }

        $sql = "SELECT
                  COUNT(u.id) total
                FROM user u
                  LEFT JOIN soe_districts d ON d.id = u.district_id
                  LEFT JOIN soe_blocks b ON b.id = u.block_id
                  LEFT JOIN user_group ug ON ug.id = u.user_group_id
                WHERE user_group_id != 1 AND u.deleted_at IS NULL";

        if (isset
