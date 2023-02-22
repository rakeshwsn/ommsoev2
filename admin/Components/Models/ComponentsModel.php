<?php

namespace Admin\Components\Models;

use CodeIgniter\Model;

class ComponentsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'soe_components';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'description' => array(
            'label' => 'Description',
            'rules' => 'trim|required|max_length[500]'
        )
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    public function getTotal($data = array()) {
        $builder=$this->db->table("{$this->table} sc");
        $this->filter($builder,$data);
        $count = $builder->countAllResults();
        return $count;
    }

    public function getAll($data = array()){
        $builder=$this->db->table("{$this->table} sc");
        $this->filter($builder,$data);

        $builder->select("sc.*");

        if (isset($data['sort']) && $data['sort']) {
            $sort = $data['sort'];
        } else {
            $sort = "sc.slug";
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
        $res = $builder->get()->getResult();
        return $res;

    }

    private function filter($builder,$data){
        if (!empty($data['filter_search'])) {
            $builder->where("(
                sc.description LIKE '%{$data['filter_search']}%'
				or sc.slug LIKE '%{$data['filter_search']}%'
				or sc.row_type LIKE '%{$data['filter_search']}%'
				or sc.tags LIKE '%{$data['filter_search']}%')"
            );
        }
    }

}
