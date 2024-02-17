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

    public function getAll($filter = array())
    {
        $sql = "SELECT
        `eu`.`id`,
        `eu`.`name`,
        `eu`.`unit_group_id`,
        e.total_units,eg.name unit_group
      FROM `enterprises_units` `eu`
        LEFT JOIN (SELECT
        eu.id unit_id,
        COALESCE(cnt.total_units, 0) total_units
      FROM enterprises_units eu
        LEFT JOIN (SELECT
            unit_id,
            COUNT(e.id) total_units
          FROM enterprises e WHERE e.deleted_at IS NULL 
          GROUP BY unit_id) cnt
          ON cnt.unit_id = eu.id) `e`
          ON `eu`.`id` = `e`.`unit_id` LEFT JOIN enterprise_unit_group eg ON eu.unit_group_id = eg.id  
      WHERE `eu`.`deleted_at` IS NULL";
        if (!empty($filter['unit_group_id'])) {
            $sql .= " AND unit_group_id=" . $filter['unit_group_id'];
        }
        $sql .= " ORDER BY `eu`.`name` ASC";
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
