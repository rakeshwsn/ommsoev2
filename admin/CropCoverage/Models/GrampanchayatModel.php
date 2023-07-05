<?php
namespace Admin\CropCoverage\Models;

use CodeIgniter\Model;

class GrampanchayatModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'soe_grampanchayat';
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
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];
	
	
	public function getAll($data = array()){
		// printr($data);
		// exit;
		$builder=$this->db->table("{$this->table} g");
		$this->filter($builder,$data);
		
		
		$builder->select("g.*,d.name as district,b.name as block");
		
		if (isset($data['sort']) && $data['sort']) {
			$sort = $data['sort'];
		} else {
			$sort = "g.name";
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
        //$builder->where('g.tcode', null);
		$res = $builder->get()->getResult();

		//echo $this->db->getLastQuery();
		// print_r($res);
		// exit;

		return $res;
	}
	public function getTotals($data = array()) {
		$builder=$this->db->table("{$this->table} g");
		// print_r($this->filter($builder,$data));
		// exit;
		$count = $builder->countAllResults();
		return $count;
	}
	private function filter($builder,$data){
		$builder->join('block b', 'g.block_id = b.id','left');
        $builder->join('district d', 'b.district_id = d.id','left');
        
		if(!empty($data['filter_district'])){
            $builder->where("b.district_id  = '".$data['filter_district']."'");
        }
        if(!empty($data['filter_block'])){
            $builder->where("g.block_id  = '".$data['filter_block']."'");
        }
		if(!empty($data['filter_grampanchayat'])){
            $builder->where("g.name  LIKE '%{$data['filter_grampanchayat']}%'");
        }
		if (!empty($data['filter_search'])) {
			$builder->where("
				b.name LIKE '%{$data['filter_search']}%'	
				OR b.code LIKE '%{$data['filter_search']}%'"
			);
		}
		return $data;
    }
	// protected  function getGrampanchayatCode(array $data){
	// 	//printr($data);
	// 	$builder=$this->db->table("{$this->table} g");
	// 	$builder->select("g.code");
	// 	$builder->where("g.district_id  = '".$data['data']['district_id']."'");
    //     $builder->where("g.block_id  = '".$data['data']['block_id']."'");
	// 	$builder->orderBy('g.code', 'desc');
	// 	$builder->limit(1);
	// 	$res = $builder->get()->getRow();
	// 	if($res){
	// 		$laststr=$res->code;
	// 		$larr=str_split($laststr, strlen($laststr) - 2);
	// 		$inumber=sprintf("%02d", $larr[1]+1);
	// 		$data['data']['code']=$larr[0].$inumber;
	// 	}else{
    //         $blockModel=new BlockModel();
    //         $block=$blockModel->find($data['data']['block_id']);

    //         $data['data']['code']=$block->code."G01";
	// 	}
	// 	return $data;
	// }
    public function getGPsByBlock($block) {
        $builder=$this->db->table("{$this->table} b");
        $builder->where("block_id",$block);
        $res = $builder->get()->getResult();
        return $res;
    }
  
}
?>
