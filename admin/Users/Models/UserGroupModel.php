<?php

namespace Admin\Users\Models;

use CodeIgniter\Model;

class UserGroupModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'user_group';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
    protected $useSoftDeletes        = true;
    protected $protectFields        = false;
//	protected $allowedFields        = [];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
    protected $validationRules      = [
        'name' => array(
            'label' => 'Name',
            'rules' => 'trim|required|max_length[100]'
        ),
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

    public function getAll($data = array()){
        //printr($data);
        $builder=$this->db->table($this->table);
        $this->filter($builder,$data);

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
            $builder->limit((int)$data['limit'],(int)$data['start']);
        }
        //$builder->where($this->deletedField, null);

        $res = $builder->get()->getResult();

        return $res;
    }

    public function getTotal($data = array()) {
        $builder=$this->db->table($this->table);
        $this->filter($builder,$data);
        $count = $builder->countAllResults();
        return $count;
    }

    private function filter($builder,$data){

        if (!empty($data['filter_search'])) {
            $builder->where("
				name LIKE '%{$data['filter_search']}%'"
            );
        }
    }

    public function addUserGroupPermission($id,$data){
       
        $builder=$this->db->table("user_group_permission");
        $builder->where("user_group_id",$id);
        $builder->delete();

        if (isset($data)) {
            foreach ($data as $key => $value) {
                $array = array(
                    'permission_id'=>$value,
                    'user_group_id'=>$id
                );
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

    /* Get agency types (created by Niranjan)*/
    public function getAgencyTypeByFundAgecny($filter){
        $builder=$this->db->table("fund_flow_chart");
        $builder->where('fund_agency_id', $filter['fund_agency_id']);
        if(isset($filter['agency_type_id'])){
            
            $builder->where("(parent_id IN (".implode(',',$filter['agency_type_id']).") or user_group_id in (".implode(',',$filter['agency_type_id'])."))");
        }
        $res=$builder->get()->getResult();
       
        return $res;
    }

    public function getAgencyTree($filter=[]){
        $sql="WITH RECURSIVE ParentChildTree AS (
            SELECT id, user_group_id, parent_id, fund_agency_id, 0 AS level
            FROM fund_flow_chart
            WHERE 1=1";
            if(is_array($filter['user_group_id'])){
                $sql.=" and user_group_id IN (".implode(',',$filter['user_group_id']).")"; 
            }else{
                $sql.=" and user_group_id = ".$filter['user_group_id']; 
            }
            
            $sql.=" UNION ALL
          
            SELECT c.id, c.user_group_id, c.parent_id, c.fund_agency_id, pct.level + 1
            FROM fund_flow_chart c
            INNER JOIN ParentChildTree pct ON c.parent_id = pct.user_group_id
          )
          SELECT * FROM ParentChildTree WHERE 1=1 ";
          if($filter['agency_type_id']){
            $sql.=" and user_group_id =".$filter['agency_type_id'];
          }
          $sql.=" and fund_agency_id=".$filter['fund_agency_id']."  GROUP BY user_group_id";
         
        return $this->db->query($sql)->getResultArray();  
    }

    public function getAgencyChild($filter=[]){
        $sql="SELECT
        *
      FROM fund_flow_chart
      WHERE parent_id=".$filter['user_group_id']." 
      AND fund_agency_id = ".$filter['fund_agency_id'];
      return $this->db->query($sql)->getResultArray(); 
    }

    public function getAgencyTypes(){
        $builder=$this->db->table($this->table);
        $builder->where('agency', 1);
        $res = $builder->get()->getResult();
        return $res;
    }
}
