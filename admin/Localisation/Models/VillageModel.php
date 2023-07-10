<?php

namespace Admin\Localisation\Models;

use CodeIgniter\Model;

class VillageModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'village';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;
	protected $allowedFields        = [];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	// Validation
	protected $validationRules      = [
		'district_id' => array(
			'label' => 'District', 
			'rules' => 'trim|required|max_length[100]'
		),
		'block_id' => array(
			'label' => 'Block', 
			'rules' => 'trim|required|max_length[100]'
		),
		'gp_id' => array(
			'label' => 'Grampanchayat', 
			'rules' => 'trim|required|max_length[100]'
		),
		'name' => array(
			'label' => 'Name', 
			'rules' => "trim|required|max_length[255]"
		)
	];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = ['getVillageCode'];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];
	
	
	public function getAll($data = array()){
		//printr($data);
		$builder=$this->db->table("{$this->table} v");
		$this->filter($builder,$data);
		
		//$builder->select("v.*,(select d.name from district d where d.id=v.district_id) as district,(select b.name from block b where b.id=v.block_id) as block,(select g.name from grampanchayat g where g.id=v.gp_id) as grampanchayat");
        $builder->select("v.*,d.name as district,b.name as block,g.name as grampanchayat,g.code as gcode");

        if (isset($data['sort']) && $data['sort']) {
			$sort = $data['sort'];
		} else {
			$sort = "v.name";
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
        //$builder->where('v.tcode', null);
		$res = $builder->get()->getResult();
        //echo $this->db->getLastQuery();
		return $res;
	}
	
	public function getTotals($data = array()) {
		$builder=$this->db->table("{$this->table} v");
		$this->filter($builder,$data);
		$count = $builder->countAllResults();
		return $count;
	}
	
	private function filter($builder,$data){
		$builder->join('grampanchayat g', 'v.gp_id = g.id','left');
		$builder->join('block b', 'g.block_id = b.id','left');
		$builder->join('district d', 'b.district_id = d.id','left');
		
	 
		if(!empty($data['filter_district'])){
            $builder->where("v.district_id  = '".$data['filter_district']."'");
        }

        if(!empty($data['filter_block'])){
            $builder->where("v.block_id  = '".$data['filter_block']."'");
        }
		
		if(!empty($data['filter_grampanchayat'])){
            $builder->where("v.gp_id  = '".$data['filter_grampanchayat']."'");
        }
		
		if(!empty($data['filter_village'])){
            $builder->where("v.name  LIKE '%{$data['filter_village']}%'");
        }
		if (!empty($data['filter_search'])) {
			$builder->where("
				v.name LIKE '%{$data['filter_search']}%'	
				OR v.code LIKE '%{$data['filter_search']}%'"
			);
		}
    }

	protected  function getVillageCode(array $data){
		//printr($data);
		$builder=$this->db->table("{$this->table} v");
		$builder->select("v.code");
		$builder->where("v.district_id  = '".$data['data']['district_id']."'");
        $builder->where("v.block_id  = '".$data['data']['block_id']."'");
		$builder->where("v.gp_id  = '".$data['data']['gp_id']."'");
		$builder->orderBy('v.code', 'desc');
		$builder->limit(1);
		$res = $builder->get()->getRow();

		
		if($res){
			$laststr=$res->code;
			$larr=str_split($laststr, strlen($laststr) - 2);
			$inumber=sprintf("%02d", $larr[1]+1);
			$data['data']['code']=$larr[0].$inumber;
		}else{
            $gpModel=new GrampanchayatModel();
            $gp=$gpModel->find($data['data']['gp_id']);
            $data['data']['code']=$gp->code."V01";
		}

		return $data;
	}
	
	
	
	public function getVillageByGP($gp) {
		$builder=$this->db->table("{$this->table} v");
        $builder->where("gp_id",$gp);
        $res = $builder->get()->getResult();
        return $res;
    }
	
}
