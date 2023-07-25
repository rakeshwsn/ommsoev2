<?php
namespace Admin\Letters\Models;

use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Localisation\Models\GrampanchayatModel;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class LetteruserModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'letter_users';
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
        // 'username' =>array(
        //         'label' => 'Username',
        //         'rules' => "required|is_unique[user.username,id,{id}]"
        // )
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

    public function getLetter($data = array()){
		$builder=$this->db->table("{$this->table} b");
		$this->filter($builder,$data);
		
		$builder->select("b.*");
		
		if (isset($data['sort']) && $data['sort']) {
			$sort = $data['sort'];
		} else {
			$sort = "b.user_name";
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
	
	public function getTotalLetters($data = array()) {
		$builder=$this->db->table("{$this->table} b");
		$this->filter($builder,$data);
		$count = $builder->countAllResults();
		return $count;
	}
	
	
	private function filter($builder,$data){
		
		
		if (!empty($data['filter_search'])) {
			$builder->where("
				b.title LIKE '%{$data['filter_search']}%'"
			);
		}
    }
	
	public function deleteLetterUser($selected=[]){
		$builder = $this->db->table('letter_users');
		$builder->whereIn("id", $selected)->delete();
	}
}
