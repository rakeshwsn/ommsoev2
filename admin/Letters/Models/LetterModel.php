<?php
namespace Admin\Letters\Models;

use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Localisation\Models\GrampanchayatModel;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class LetterModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'letters';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes        = true;
	protected $protectFields        = false;
//	protected $allowedFields        = [];

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

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
        $builder = $this->db->table('letters l');
        $builder->select("l.*,lu.user_name");

        $this->filter($data,$builder);

        if (isset($data['order']) && isset($data['sort']) && $data['order']) {
            $builder->orderBy($data['sort'], $data['order']);
        } else {
            $builder->orderBy("l.letter_no");
        }
        if (isset($data['start']) && isset($data['limit'])) {
            $builder->limit($data['limit'], $data['start']);
        }

        $results = $builder->get()->getResult();

        return $results;
    }

    public function getTotal($data = array()) {
        $builder = $this->db->table('letters l');
        $this->filter($data,$builder);

        $count = $builder->countAllResults();

        return $count;

    }

    private function filter($data,$builder){
        $builder->join('letter_users lu','l.user_id=lu.id','LEFT');
        if (!empty($data['filter_search'])) {
            $builder->where("
				(l.letter_no LIKE '%{$data['filter_search']}%'
				OR l.subject LIKE '%{$data['filter_search']}%'
				OR m.name LIKE '%{$data['filter_search']}%')
			");
        }

        $builder->where("l.deleted_at IS NULL");
    }

    public function getNewLetterNo() {
        $sql = "SELECT COALESCE((MAX(letter_no)+1),1) letter_no FROM vw_letters WHERE letter_no <6000 AND year_id=".getCurrentYearId();

        return $this->db->query($sql)->getFirstRow()->letter_no;
    }

    public function isLetterExists($letter_no=0,$year_id) {
        $sql = "SELECT * FROM vw_letters WHERE letter_no=".$letter_no." AND year_id=".$year_id;

        return $this->db->query($sql)->getFirstRow();
    }
}
