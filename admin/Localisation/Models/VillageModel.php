<?php

namespace Admin\Localisation\Models;

use CodeIgniter\Model;

class VillageModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'villages';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $protectFields        = false;
	protected $allowedFields =  [];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	// Validation
	protected $validationRules      = [
        'id' => 'permit_empty|integer|greater_than[0]',
		'gp_id' => array(
			'label' => 'Grampanchayat',
			'rules' => 'trim|required|max_length[100]'
		),
		'name' => array(
			'label' => 'Name',
			'rules' => "trim|required|max_length[255]|is_unique_village[villages.name,id,{id}]",
            'errors' => ['is_unique_gp' => 'This Grampanchayat already exists.']
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


	public function getAll($data = array())
	{
		//printr($data);
		$builder = $this->db->table("{$this->table} v");
		$this->filter($builder, $data);

		//$builder->select("v.*,(select d.name from district d where d.id=v.district_id) as district,(select b.name from block b where b.id=v.block_id) as block,(select g.name from grampanchayat g where g.id=v.gp_id) as grampanchayat");
		$builder->select("v.*,d.name as district,b.name as block,g.name as grampanchayat");

		if (isset($data['sort']) && $data['sort']) {
			$sort = $data['sort'];
		} else {
			$sort = "v.name";
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
			$builder->limit((int)$data['limit'], (int)$data['start']);
		}
		//$builder->where($this->deletedField, null);
		//$builder->where('v.tcode', null);
		$res = $builder->get()->getResult();
		// echo $this->db->getLastQuery();exit;
		return $res;
	}

	public function getTotals($data = array())
	{
		$builder = $this->db->table("{$this->table} v");
		$this->filter($builder, $data);
		$count = $builder->countAllResults();

		return $count;
	}

	private function filter($builder, $data)
	{
		$builder->join('soe_grampanchayats g', 'v.gp_id = g.id', 'left');
		$builder->join('soe_blocks b', 'g.block_id = b.id', 'left');
		$builder->join('soe_districts d', 'b.district_id = d.id', 'left');
		
		$builder->where("b.is_program=1");

		if (!empty($data['filter_district'])) {
			$builder->where("b.district_id  = '" . $data['filter_district'] . "'");
		}

		if (!empty($data['filter_block'])) {
			$builder->where("g.block_id  = '" . $data['filter_block'] . "'");
		}

		if (!empty($data['filter_grampanchayat'])) {
			$builder->where("v.gp_id  = '" . $data['filter_grampanchayat'] . "'");
		}

		if (!empty($data['filter_village'])) {
			$builder->where("v.name  LIKE '%{$data['filter_village']}%'");
		}
		if (!empty($data['filter_search'])) {
			$builder->where(
				"v.name LIKE '%{$data['filter_search']}%'"
			);
		}
		// echo $this->db->getLastQuery();exit;
	}
	
	protected  function getVillageCode(array $data)
	{
		//printr($data);
		$builder = $this->db->table("{$this->table} v");
		$builder->where("v.gp_id  = '" . $data['data']['gp_id'] . "'");
		$builder->limit(1);
		$res = $builder->get()->getRow();

		return $data;
	}

	public function getVillageByGP($gp)
	{
		$builder = $this->db->table("{$this->table} v");
		$builder->where("gp_id", $gp);
		$res = $builder->get()->getResult();
		return $res;
	}
	public function getDataById($id)
	{
		$sql = "SELECT
		sg.id gp_id,
		sg.name gp_name,
		sg.district_id,
		sg.block_id,
		sb.name blocks,
		v.name village_name,
		sd.name districts,
		v.id village_id
	  FROM soe_grampanchayats sg
		LEFT JOIN villages v
		  ON sg.id = v.gp_id
		LEFT JOIN soe_blocks sb
		  ON sg.block_id = sb.id
		LEFT JOIN soe_districts sd
		  ON sg.district_id = sd.id
	where sb.is_program=1 AND v.id = $id
	  GROUP BY districts";
		return $this->db->query($sql)->getRow();
	}
}
