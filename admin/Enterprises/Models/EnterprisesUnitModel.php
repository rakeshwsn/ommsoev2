<?php

namespace Admin\Enterprises\Models;

use CodeIgniter\Model;

class EnterprisesUnitModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'enterprises_units';
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

    // Validation
    protected $validationRules      = [
        'name' => array(
            'label' => 'Name',
            'rules' => 'trim|required|max_length[100]'
        ),
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

    public function getAll_bk($data = array())
    {
        $builder = $this->db->table("{$this->table} eu");
        $builder->join('enterprises e', 'eu.id=e.unit_id', 'left');

        $this->filter($builder, $data);

        $builder->select("eu.id,
                        eu.name,
                        eu.sort_order,
                        eu.group_unit,
                        COUNT(e.unit_id) as total_ent");

        // Set the sorting order
        $sort = isset($data['sort']) && $data['sort'] ? $data['sort'] : 'eu.name';
        $order = isset($data['order']) && $data['order'] === 'desc' ? 'desc' : 'asc';
        $builder->orderBy($sort, $order);

        // Set limit and offset if provided
        if (isset($data['start']) || isset($data['limit'])) {
            $start = isset($data['start']) && $data['start'] >= 0 ? (int)$data['start'] : 0;
            $limit = isset($data['limit']) && $data['limit'] >= 1 ? (int)$data['limit'] : 15;
            $builder->limit($limit, $start);
        }

        $builder->where("eu.{$this->deletedField} IS NULL");

        $builder->groupBy('e.unit_id');

        $query = $builder->getCompiledSelect(); echo $query; exit;

        $res = $builder->get()->getResult();
        return $res;
    }

    public function getAll($data = array()){
        $sql = "SELECT
        `eu`.`id`,
        `eu`.`name`,
        `eu`.`group_unit`,
        e.total_units
      FROM `enterprises_units` `eu`
        LEFT JOIN (SELECT
        eu.id unit_id,
        COALESCE(cnt.total_units, 0) total_units
      FROM enterprises_units eu
        LEFT JOIN (SELECT
            unit_id,
            COUNT(e.id) total_units
          FROM enterprises e
          GROUP BY unit_id) cnt
          ON cnt.unit_id = eu.id) `e`
          ON `eu`.`id` = `e`.`unit_id`
      WHERE `eu`.`deleted_at` IS NULL
      ORDER BY `eu`.`name` ASC";

      return $this->db->query($sql)->getResultArray();
    }

    public function getTotal($data = array())
    {
        $builder = $this->db->table("{$this->table} eu");
        $this->filter($builder, $data);
        $count = $builder->countAllResults();
        return $count;
    }

    private function filter($builder, $data)
    {

        if (!empty($data['filter_search'])) {
            $builder->where(
                "
				name LIKE '%{$data['filter_search']}%'"
            );
        }
    }
}
