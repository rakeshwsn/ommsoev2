<?php
namespace Admin\Localisation\Models;

use CodeIgniter\Model;

class BlockModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'block';
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
	protected $beforeInsert         = ['getBlockCode'];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];
	
	
	public function getAll($data = array()){
		//printr($data);
		$builder=$this->db->table("{$this->table} b");
		$this->filter($builder,$data);
		
		$builder->select("b.*,d.name as district,d.code as dcode");
		
		if (isset($data['sort']) && $data['sort']) {
			$sort = $data['sort'];
		} else {
			$sort = "b.name";
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
	
	public function getTotals($data = array()) {
		$builder=$this->db->table("{$this->table} b");
		$this->filter($builder,$data);
		$count = $builder->countAllResults();
		return $count;
	}
	
	private function filter($builder,$data){
		$builder->join('district d', 'b.district_id = d.id');
        
		if(!empty($data['filter_district'])){
            $builder->where("b.district_id  = '".$data['filter_district']."'");
        }
		
		/*if(!empty($data['filter_block'])){
            $builder->where("b.name  LIKE '%{$data['filter_block']}%'");
        }*/
		if (!empty($data['filter_search'])) {
			$builder->where("
				b.name LIKE '%{$data['filter_search']}%'	
				OR b.code LIKE '%{$data['filter_search']}%'"
			);
		}
    }

	protected  function getBlockCode(array $data){
		//printr($data);
		$builder=$this->db->table("{$this->table} b");
		$builder->select("b.code");
		$builder->where("b.district_id  = '".$data['data']['district_id']."'");
		$builder->orderBy('b.code', 'desc');
		$builder->limit(1);
		$res = $builder->get()->getRow();
		
		
		if($res){
			$laststr=$res->code;
			$larr=str_split($laststr, strlen($laststr) - 2);
			$inumber=sprintf("%02d", $larr[1]+1);
			$data['data']['code']=$larr[0].$inumber;
		}else{
            $districtModel=new DistrictModel();
            $district=$districtModel->find($data['data']['district']);

            $data['data']['code']=$district->code."B01";
		}
		//printr($data);
		//exit;
		return $data;
	}

	public function getBlocksByDistrict($district) {
		$builder=$this->db->table("{$this->table} b");
		$builder->where("district_id",$district);
		$res = $builder->get()->getResult();
		return $res;
	}
    public function getBlockByCode($code) {
        $builder=$this->db->table("{$this->table} b");
        $builder->where("code",$code);
        $res = $builder->get()->getRowArray();
        return $res;
    }
}
?>
