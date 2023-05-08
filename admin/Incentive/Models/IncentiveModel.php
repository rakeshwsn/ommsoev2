<?php

namespace Admin\Incentive\Models;

use CodeIgniter\Model;

class IncentiveModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'detailed_incentive_data';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'object';
    protected $useSoftDeletes       = false;
    protected $protectFields        = false;
    //protected $allowedFields        = [];

    // Dates
    protected $useTimestamps        = true;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = '';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name' => array(
            'label' => 'Name',
            'rules' => 'trim|required|max_length[100]|is_unique[module.name,id,{id}]'
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


    public function getAll($data){
        // echo "<pre>";
        // print_r($data); exit;
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
     // echo $this->db->getLastQuery();
        return $res;
    }

    public function getTotal($data = array()) {
       // print_r($data); exit;
        $builder=$this->db->table($this->table);
        $this->filter($builder,$data);
        $count = $builder->countAllResults();
        return $count;
    }

    
    public function getcheckExsists($data) {
         //print_r($data); exit;
         $builder=$this->db->table('incetive_main_details');
         $builder->where("district_id" , $data['district_id']);
         $builder->where("block_id", $data['block_id']);
         $builder->where("year", $data['year']);
         $builder->where("season", $data['season']);
         $count = $builder->countAllResults();
         //echo $this->db->getLastQuery();
         return $count;
         
     }

     public function addInceitive_main($data)
     {
 
        $builder=$this->db->table('incetive_main_details');
        $builder->insert($data);
     
        return $this->db->InsertID();
         
     }

     public function addInceitive($data)
	{


        $this->db->table("detailed_incentive_data")->insertBatch($data);
	
		
		
	}

    private function filter($builder,$data){
      //print_r($data) ; exit;
        // if (!empty($data['filter_search'])) {
        //     $builder->where("
		// 		year LIKE '%{$data['filter_search']}%'"
        //     );
        // }
        if (!empty($data['mainincetiveid'])) {
            $builder->where("incetive_id", $data['mainincetiveid']);
        }
        // if (!empty($data['filter_year'])) {
        //     $builder->where("
		// 		year LIKE '%{$data['filter_year']}%'"
        //     );
        // }
        // if (!empty($data['filter_season'])) {
        //     $builder->where("
		// 		season LIKE '%{$data['filter_season']}%'"
        //     );
        // }
    }


    public function deleteincentive($selected = [])
	{
		$builder = $this->db->table('incetive_main_details');
		$builder->whereIn("id", $selected)->delete();

		$builder = $this->db->table('detailed_incentive_data');
		$builder->whereIn("incetive_id", $selected)->delete();
	}

}
