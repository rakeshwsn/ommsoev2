<?php
namespace Admin\CropCoverage\Models;

use CodeIgniter\Model;

class AreaCoverageWeeksModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'ac_crop_weeks';
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

	//protected $db;
	
	public function __construct(){
		parent::__construct();
		//$this->db = \Config\Database::connect();
	}
	public function addseason($week_days) {
    
    $seasondata=array(
      "year"=>$week_days['year'],
      "season"=>$week_days['season'],
      "start_date"=>$week_days['start_date'],
      "end_date"=>$week_days['end_date'],
    );
    $this->db->table('ac_crop_weeks')->insert($seasondata);

}
 public function getWeeks() {
        $builder = $this->db->table('ac_crop_weeks');
        $seasons   = $builder->get()->getResult();

        $season_data = [];
        foreach($seasons as $season){
            $season_data[] = [
            	'id'=> $season->id,
            	'year' => $season->year,
                'season' => $season->season,
                'start_date' => $season->start_date,

                'end_date' => $season->end_date,

            ];
        }
       return $season_data;
    
    }

	// public function insertData($data){
	// 	$builder=$this->db->table('crop_coverage');
	// 	$builder->insert($data);
	// 	echo $this->db->getLastQuery();
	// 	exit;
	// }
	
	
	
}
?>
