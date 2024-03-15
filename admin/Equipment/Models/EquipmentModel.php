<?php

namespace Admin\Equipment\Models;

use CodeIgniter\Model;

class EquipmentModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'equipment';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'object';
    protected $useSoftDeletes       = true;
    protected $protectFields        = false;
    protected $allowedFields        = [];

    // Dates
    protected $useTimestamps        = true;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    // Validation
    protected $validationRules      = [
        'id'    => 'permit_empty|integer|greater_than[0]',
        'name' => array(
            'label' => 'Name',
            'rules' => "trim|required|max_length[255]|is_unique[equipment.name,id,{id}]'",
            'errors' => [
                'is_unique_gp' => 'This field already exit.',
            ],
        )
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

    public function getAll($data = array())
    {
        $builder = $this->db->table($this->table);
        $this->filter($builder, $data);

        $builder->select("*");

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
            $builder->limit((int)$data['limit'], (int)$data['start']);
        }
        // $builder->where($this->deletedField, null);

        $res = $builder->get()->getResult();
	// echo $this->db->getLastQuery();exit;
        return $res;
    }

    public function getTotals($data = array())
    {
        $builder = $this->db->table($this->table);
        $this->filter($builder, $data);
        $count = $builder->countAllResults();
        
        return $count;
    }
    private function filter($builder,$data){
		// $builder->join('soe_districts d', 'lb.district_lgd_code = d.lgd_code');
        
		if(!empty($data['center_type'])){
            $builder->where("center_type  = '".$data['center_type']."'");
        }

		if (!empty($data['filter_equipment'])) {
			$builder->where("
				name LIKE '%{$data['filter_equipment']}%' OR
			");
		}
        // echo $this->db->getLastQuery();exit;
    }

  



}
