<?php

namespace Admin\Localisation\Models;

use CodeIgniter\Model;

class LgdVillagesModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'lgd_villages';
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
		'gp_id' => array(
			'label' => 'Grampanchayat',
			'rules' => 'trim|required|max_length[100]'
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
		$builder = $this->db->table("{$this->table} lv");
		$this->filter($builder, $data);

		//$builder->select("v.*,(select d.name from district d where d.id=v.district_id) as district,(select b.name from block b where b.id=v.block_id) as block,(select g.name from grampanchayat g where g.id=v.gp_id) as grampanchayat");
		$builder->select("lv.*,d.name as district,lb.name as block,lg.name as grampanchayat");

		if (isset($data['sort']) && $data['sort']) {
			$sort = $data['sort'];
		} else {
			$sort = "lv.name";
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
		$builder = $this->db->table("{$this->table} lv");
		$this->filter($builder, $data);
		$count = $builder->countAllResults();
		// echo $this->db->getLastQuery();exit;
		
		return $count;
	}

	private function filter($builder, $data)
	{
		$builder->join('lgd_gps lg', 'lv.gp_lgd_code = lg.lgd_code', 'left');
		$builder->join('lgd_blocks lb', 'lg.block_lgd_code = lb.lgd_code', 'left');
		$builder->join('soe_districts d', 'lb.district_lgd_code = d.lgd_code', 'left');


		if (!empty($data['filter_district'])) {
			$builder->where("lb.district_lgd_code  = '" . $data['filter_district'] . "'");
		}

		if (!empty($data['filter_block'])) {
			$builder->where("lg.block_lgd_code  = '" . $data['filter_block'] . "'");
		}

		if (!empty($data['filter_grampanchayat'])) {
			$builder->where("lv.gp_lgd_code  = '" . $data['filter_grampanchayat'] . "'");
		}

		if (!empty($data['filter_village'])) {
			$builder->where("lv.name  LIKE '%{$data['filter_village']}%'");
		}
		if (!empty($data['filter_search'])) {
			$builder->where(
				"
				lv.name LIKE '%{$data['filter_search']}%'"
			);
		}
		// echo $this->db->getLastQuery();
		// exit;
	}

	protected  function getVillageCode(array $data)
	{
		//printr($data);
		$builder = $this->db->table("{$this->table} lv");
		$builder->where("lv.gp_lgd_code  = '" . $data['data']['gp_lgd_code'] . "'");
		$builder->limit(1);
		$res = $builder->get()->getRow();

		return $data;
	}

	public function getVillageByGP($gp)
	{
		$builder = $this->db->table("{$this->table} lv");
		$builder->where("gp_lgd_code", $gp);
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
