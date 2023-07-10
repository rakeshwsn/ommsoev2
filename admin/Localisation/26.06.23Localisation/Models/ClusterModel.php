<?php
namespace Admin\Localisation\Models;

use CodeIgniter\Model;

class ClusterModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'cluster';
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
            'label' => 'GP',
            'rules' => 'trim|is_cluster[cluster_to_gp.gp_id,cluster_id,{cluster_id}]',
            'errors' => [
                'is_cluster' => 'This {field} already Used'
            ],
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
	protected $beforeInsert         = ['getClusterCode','resetBlock'];
	protected $afterInsert          = [];
	protected $beforeUpdate         = ['resetBlock'];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];
	
	
	public function getAll($data = array()){
		//printr($data);
		$builder=$this->db->table("{$this->table} c");
		$this->filter($builder,$data);
		
		$builder->select("c.*,d.name as district,b.name as block,b.code as bcode,(select GROUP_CONCAT(g.name SEPARATOR '<br>') from cluster_to_gp cg left join grampanchayat g on (cg.gp_id=g.id) where cg.cluster_id=c.id) as gp");
		
		if (isset($data['sort']) && $data['sort']) {
			$sort = $data['sort'];
		} else {
			$sort = "c.name";
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
        //echo $this->db->getLastQuery();
		return $res;
	}
	
	public function getTotals($data = array()) {
		$builder=$this->db->table("{$this->table} c");
		$this->filter($builder,$data);
		$count = $builder->countAllResults();
		return $count;
	}
	
	private function filter($builder,$data){
		$builder->join('district d', 'c.district_id = d.id');
        $builder->join('block b', 'c.block_id = b.id');
        
		if(!empty($data['filter_district'])){
            $builder->where("c.district_id  = '".$data['filter_district']."'");
        }
		
		if(!empty($data['filter_cluster'])){
            $builder->where("c.name  LIKE '%{$data['filter_cluster']}%'");
        }
		if (!empty($data['filter_search'])) {
			$builder->where("
				c.name LIKE '%{$data['filter_search']}%'	
				OR c.code LIKE '%{$data['filter_search']}%'"
			);
		}
    }

    protected function resetBlock(array $data)
    {
        unset($data['data']['gp_id']);
        return $data;
    }

    protected  function getClusterCode(array $data){

		$builder=$this->db->table("{$this->table} c");
        $builder->join('district d', 'c.district_id = d.id');
        $builder->select("c.code");
		$builder->where("c.district_id  = '".$data['data']['district_id']."'");
		$builder->orderBy('c.code', 'desc');
		$builder->limit(1);
		$res = $builder->get()->getRow();
		
		
		if($res){
			$laststr=$res->code;
			$larr=str_split($laststr, strlen($laststr) - 2);
			$inumber=sprintf("%02d", $larr[1]+1);
			$data['data']['code']=$larr[0].$inumber;
		}else{
		    $blockModel=new BlockModel();
		    $block=$blockModel->find($data['data']['block_id']);
			$data['data']['code']=$block->code.'C'."01";
		}
		//printr($data);
		//exit;
		return $data;
	}

  
	public function getClustersByDistrict($district) {
		$builder=$this->db->table("{$this->table} c");
		$builder->where("district",$district);
		$res = $builder->get()->getResult();
		return $res;
	}

    public function getClustersByBlock($block) {
        $builder=$this->db->table("{$this->table} c");
        $builder->join('block b', 'c.block_id = b.id');
        $builder->select("c.*");
        $builder->where("b.code",$block);
        $res = $builder->get()->getResult();
        //echo $this->db->getLastQuery();
        return $res;
    }
}
?>
