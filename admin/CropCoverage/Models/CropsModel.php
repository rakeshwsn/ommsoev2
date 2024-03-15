<?php
namespace Admin\CropCoverage\Models;

use CodeIgniter\Model;

class CropsModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'ac_crops';
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

	public function AddCrops($data) {
    
    $cropsdata=array(
        "crops"=>$data['crops'],
     
    );
    $this->db->table('ac_crops')->insert($cropsdata);
}
    public function GetCrops() {
        $builder = $this->db->table('ac_crops');
        $crops   = $builder->get()->getResult();

        $season_data = [];
        foreach($crops as $crop){
            $season_data[] = [
            	'id'=> $crop->id,
            	'crops' => $crop->crops,
            ];
        }
       return $season_data;
    
    }	
	
}
?>
