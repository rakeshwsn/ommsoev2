<?php

namespace Admin\Users\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ rawSql;

class UserGroupModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'user_group';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $returnType           = 'object';
    protected $useSoftDeletes        = true;
    protected $protectFields        = false;
    protected $allowedFields        = ['name'];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name' => 'trim|required|max_length[100]',
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

    public function getAll($data = [])
    {
        $this->filter($data);

        $builder = $this->builder();

        $builder->select('*');

        if (isset($data['sort']) && $data['sort']) {
            $sort = $data['sort'];
        } else {
            $sort = "name";
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
        $builder->whereNull($this->deletedField);

        $res = $builder->get()->getResult();

        return $res;
    }

    public function getTotal($data = [])
    {
        $this->filter($data);

        $builder = $this->builder();

        $count = $builder->countAllResults();

        return $count;
    }

    private function filter(&$data){

        if (!empty($data['filter_search'])) {
            $data['where'][] = [
                'name' => [
                    'like' => '%'.$data['filter_search'].'%',
                ],
            ];
        }
    }

    public function addUserGroupPermission($id,$data){
        $builder=$this->db->table("user_group_permission");
        $builder->where("user_group_id",$id);
        $builder->delete();

        if (isset($data)) {
            foreach ($data as $key => $value) {
                $array = [
                    'permission_id'=>$value,
                    'user_group_id'=>$id
                ];
                $builder->insert($array);
            }
        }
        return "success";
    }

    public function getBlockUsers() {
        $this->settings = new \Config\Settings();
        return $this->whereIn('id',[$this->settings->block_user,$this->settings->cbo_user])
            ->asArray()->find();
    }

    protected function builder()
    {
        return $this->db->table($this->table);
    }
}
