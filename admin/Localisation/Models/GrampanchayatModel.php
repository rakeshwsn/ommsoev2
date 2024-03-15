<?php
namespace Admin\Localisation\Models;

use CodeIgniter\Model;

class GrampanchayatModel extends Model
{
	protected $DBGroup = 'default';
	protected $table = 'soe_grampanchayats';
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
	protected $validationRules = [
        'id' => 'permit_empty|integer|greater_than[0]',
		'district_id' => array(
			'label' => 'District',
			'rules' => 'trim|max_length[100]'
		),
		'block_id' => array(
			'label' => 'Block',
			'rules' => 'trim|required|max_length[100]'
		),
		'name' => array(
			'label' => 'Name',
			'rules' => "trim|required|max_length[255]|is_unique_gp[soe_grampanchayats.name,id,{id}]",
            'errors' => ['is_unique_gp' => 'This Grampanchayat already exists.']
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
		$builder = $this->db->table("{$this->table} sg");
		$this->filter($builder, $data);

		$builder->select("sg.*,sd.name as district,sb.name as block");

		if (isset($data['sort']) && $data['sort']) {
			$sort = $data['sort'];
		} else {
			$sort = "sg.name";
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

		$res = $builder->get()->getResult();
		//		echo $this->db->getLastQuery();
		return $res;
	}

	public function getTotals($data = array())
	{
		$builder = $this->db->table("{$this->table} sg");
		$this->filter($builder, $data);
		$count = $builder->countAllResults();
		return $count;
	}

	private function filter($builder, $data)
	{
		$builder->join('soe_blocks sb', 'sg.block_id = sb.id', 'left');
		$builder->join('soe_districts sd', 'sb.district_id = sd.id', 'left');

		if (!empty($data['filter_district'])) {
//			$builder->where("sg.district_id  = '" . $data['filter_district'] . "'");
		}

		if (!empty($data['filter_block'])) {
			$builder->where("sg.block_id  = '" . $data['filter_block'] . "'");
		}

		if (!empty($data['filter_grampanchayat'])) {
			$builder->where("sg.name  LIKE '%{$data['filter_grampanchayat']}%'");
		}
		if (!empty($data['filter_search'])) {
			$builder->where(
				"(
				sg.name LIKE '%{$data['filter_search']}%'	
				OR sd.name LIKE '%{$data['filter_search']}%'
				OR sb.name LIKE '%{$data['filter_search']}%')"
			);
		}
		$builder->where("sg.deleted_at", null);
		$builder->where("sb.is_program", 1);
	}

	public function getGPsByBlock($block_id)
	{
		// echo $block_id;
		// exit;
		$sql = "
SELECT
  sb.id AS block_id,
  sb.name AS block,
  sg.id AS gp_id,
  sg.name AS gp
FROM soe_grampanchayats sg
LEFT JOIN soe_blocks sb
  ON sg.block_id = sb.id
WHERE sg.deleted_at IS NULL AND sb.is_program=1";

		if ($block_id) {
			$sql .= " AND sg.block_id = " . $block_id;
		} else {
			$sql .= " AND sg.block_id = " . 0;
		}
		$sql .= " ORDER BY sg.name";
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