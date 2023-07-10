<?php
namespace Admin\CropCoverage\Models;

use CodeIgniter\Model;

class TargetModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'ac_target_master';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDelete        = false;
	protected $protectFields        = true;
	protected $allowedFields        = [];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [];
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

	public function AddTargets($data) {
    //crop master  table
    $masterdata=array(
        "block_id"=>$data['block_id'],
        "year_id" =>  getCurrentYearId(),
        "season" => getCurrentSeason(),
     
    );
        $target_id=$this->db->table('ac_target_master')->insert($masterdata);
        //crop coverage target table
        foreach ($data['crop'] as $crop_id=>$area) {
       
            $targetdata = array(
                "target_id" => $target_id,
                "crop_id" => $crop_id,
                // Add other columns and values from $values array as needed
                "smi" => $area['SMI'],
                "lt" => $area['LT'],
                "ls" => $area['LS']
            );
            
            $this->db->table('ac_target_area')->insert($targetdata);
        }
    }
   

    public function getPractices(){
        
        $builder = $this->db->table('ac_crop_practices cp');
        $builder->select('cp.crop_id, cp.practice_id, ac.crops, p.name as practice');
        $builder->join('ac_crops ac', 'ac.id = cp.crop_id', 'left');
        $builder->join('ac_practices p', 'cp.practice_id = p.id', 'left');
        $builder->orderBy('ac.id');
        $query = $builder->get();
        
        return $query->getResultArray();
      
    } 
   
	
}
?>
