<?php

namespace Admin\Permission\Models;

use CodeIgniter\Database\Query;
use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'permission';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $allowedFields = [
        'name',
        'description',
    ];

    protected $validationRules = [
        'name' => 'trim|required|max_length[100]',
    ];

    public function getAll(array $data = []): array
    {
        $builder = $this->db->table($this->table);
        $this->filter($builder, $data);

        $builder->select('*');

        if (isset($data['sort']) && $data['sort']) {
            $sort = $data['sort'];
        } else {
            $sort = "name";
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

        try {
            $query = $builder->get();
            return $query->getResultArray();
        } catch (DatabaseException $e) {
            log_message('error', $e->getMessage());
            return [];
        }
    }

    public function getTotal(array $data = []): int
    {
        $builder = $this->db->table($this->table);
        $this->filter($builder, $data);

        try {
            return $builder->countAllResults();
        } catch (DatabaseException $e) {
            log_message('error', $e->getMessage());
            return 0;
        }
    }

    private function filter(Query $builder, array $data): void
    {
        if (!empty($data['filter_search'])) {
            $builder->where("name LIKE '%" . $data['filter_search'] . "%'");
        }
    }

    public function getModulesWithPermission($id = null): array
    {
        $builder = $this->db->table($this->table);
        $alias = $builder->getAliasedBuilder()->getAlias();

        $subquery = $this->db->table('user_group_permission')
            ->select('permission_id')
            ->where('user_group_id', $id);

        return $builder
            ->select("$alias.id, $alias.name, $alias.description, (CASE WHEN $alias.id IN ($subquery) THEN 'yes' ELSE 'no' END) as active")
            ->get()
            ->getResultArray();
    }
}
