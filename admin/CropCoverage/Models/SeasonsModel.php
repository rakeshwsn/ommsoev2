<?php
namespace Admin\CropCoverage\Models;

use CodeIgniter\Model;

class SeasonsModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'ac_seasons';
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

	public function AddSeasons($data) {
    // dd($data);
		$seasonsdata=array(

			"year"=>$data['year_id'],
			"season"=>$data['season'],
			"districts"=>$data['districts_id'],

		
		);
		$this->db->table('ac_seasons')->insert($seasonsdata);
	
    }

    public function GetSeasons() {

		$builder=$this->db->table("{$this->table} a");
    	$builder->join('years y', 'a.year = y.id','left');
        $builder->join('district d', 'a.districts = d.id','left');
        
        $seasons   = $builder->get()->getResult();
		// print_r($seasons);
		// exit;
		$seasons_data = [];
        foreach($seasons as $season){
            $seasons_data[] = [
            	'year'=> $season->year,
            	'season'=> $season->season,
				'name'=> $season->name,
				'date_added'=> $season->date_added,

            ];
			return $seasons_data;
        }
		

        // $season_data = [];
        // foreach($seasons as $season){
        //     $season_data[] = [
        //     	'id'=> $season->id,
        //     	'year' => $season->year,
        //     	'season' => $season->season,
        //     	'district' => $season->date_added,

        //     ];
        // }
       return $season_data;
    
    }	
	
}
?>
