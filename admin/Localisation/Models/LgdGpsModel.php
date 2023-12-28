<?php
namespace Admin\Localisation\Models;

use CodeIgniter\Model;

class LgdGpsModel extends Model
{
	protected $DBGroup = 'default';
	protected $table = 'lgd_gps';
	protected $primaryKey = 'id';
	protected $useAutoIncrement = true;
	protected $insertID = 0;
	protected $returnType = 'object';
	protected $useSoftDeletes = true;
	protected $protectFields = false;
	protected $allowedFields = [];

	// Dates
	protected $useTimestamps = false;
	protected $dateFormat = 'datetime';
	protected $createdField = 'created_at';
	protected $updatedField = 'updated_at';
	protected $deletedField = 'deleted_at';

	// Validation
	// Validation
	protected $validationRules = [
		'district_id' => array(
			'label' => 'District',
			'rules' => 'trim|required|max_length[100]'
		),
		'block_id' => array(
			'label' => 'Block',
			'rules' => 'trim|required|max_length[100]'

		),
		'name' => array(
			'label' => 'Name',
			'rules' => "trim|required|max_length[255]|regex_match[/^[A-Za-z\s.]+$/]"
		)
	];
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


	public function getAll($data = array())
	{
		//printr($data);
		$builder = $this->db->table("{$this->table} lg");
		$this->filter($builder, $data);

		$builder->select("lg.*,sd.name as district,lb.name as block");

		if (isset($data['sort']) && $data['sort']) {
			$sort = $data['sort'];
		} else {
			$sort = "lg.name";
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
			$builder->limit((int) $data['limit'], (int) $data['start']);
		}
		//$builder->where($this->deletedField, null);
		//$builder->where('g.tcode', null);
		$res = $builder->get()->getResult();
		//		echo $this->db->getLastQuery();
		return $res;
	}

	public function getTotals($data = array())
	{
		$builder = $this->db->table("{$this->table} lg");
		$this->filter($builder, $data);
		$count = $builder->countAllResults();
		return $count;
	}

	private function filter($builder, $data)
	{
		$builder->join('lgd_blocks lb', 'lg.block_lgd_code = lb.lgd_code', 'left');
		$builder->join('soe_districts sd', 'lb.district_lgd_code = sd.lgd_code', 'left');

		if (!empty($data['filter_district'])) {
			$builder->where("lb.district_lgd_code  = '" . $data['filter_district'] . "'");
		}

		if (!empty($data['filter_block'])) {
			$builder->where("lg.block_lgd_code  = '" . $data['filter_block'] . "'");
		}

		if (!empty($data['filter_grampanchayat'])) {
			$builder->where("lg.name  LIKE '%{$data['filter_grampanchayat']}%'");
		}
		if (!empty($data['filter_search'])) {
			$builder->where(
				"(
				lg.name LIKE '%{$data['filter_search']}%'	
				OR sd.name LIKE '%{$data['filter_search']}%'
				OR lb.name LIKE '%{$data['filter_search']}%')"
			);
		}
		
	}

	public function getGPsByBlock($block_id)
	{
		// echo $block_id;
		// exit;
		$sql = "
			SELECT
			lb.id AS block_id,
			lb.name AS block,
			lg.id AS gp_id,
			lg.name AS gp
			FROM lgd_gps lg
			LEFT JOIN lgd_blocks lb
			ON lg.block_lgd_code = lb.lgd_code
			";

		if ($block_id) {
			$sql .= " AND lg.block_lgd_code = " . $block_id;
		} else {
			$sql .= " AND lg.block_lgd_code = " . 0;
		}

		// echo $sql;
		// exit;
		$res = $this->db->query($sql)->getResult();
		return $res;
	}

	public function getGPsByCluster($cluster)
	{
		$builder = $this->db->table("cluster_to_gp cg");
		$builder->join('cluster c', 'cg.cluster_id = c.id');
		$builder->join('soe_grampanchayats g', 'cg.gp_id = g.id');
		$builder->select("g.*");
		$builder->where("c.code", $cluster);
		$res = $builder->get()->getResult();
		return $res;
	}

	public function getGPByCode($code)
	{

		$builder = $this->db->table("{$this->table} sg");
		$builder->select("GROUP_CONCAT(g.id) AS ids");
		$builder->whereIn("sg.code", explode(",", $code));
		$res = $builder->get()->getRowArray();
		//echo $this->db->getLastQuery();
		//exit;
		return $res;
	}

}