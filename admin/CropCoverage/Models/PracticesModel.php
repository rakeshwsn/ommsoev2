<?php
namespace Admin\CropCoverage\Models;

use CodeIgniter\Model;

class PracticesModel extends Model
{
	protected $DBGroup = 'default';
	protected $table = 'ac_practices';
	protected $primaryKey = 'id';
	protected $useAutoIncrement = true;
	protected $insertID = 0;
	protected $returnType = 'object';
	protected $useSoftDelete = false;
	protected $protectFields = true;
	protected $allowedFields = [];

	// Dates
	protected $useTimestamps = false;
	protected $dateFormat = 'datetime';
	protected $createdField = 'created_at';
	protected $updatedField = 'updated_at';
	protected $deletedField = 'deleted_at';

	// Validation
	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks = true;
	protected $beforeInsert = [];
	protected $afterInsert = [];
	protected $beforeUpdate = [];
	protected $afterUpdate = [];
	protected $beforeFind = [];
	protected $afterFind = [];
	protected $beforeDelete = [];
	protected $afterDelete = [];


	public function GetPractices()
	{
		$builder = $this->db->table('ac_practices');
		$practices = $builder->get()->getResult();

		$practice_data = [];
		foreach ($practices as $practice) {
			$practice_data[] = [
				'id' => $practice->id,
				'practices' => $practice->name,

			];
		}
		return $practice_data;
	}



}
?>