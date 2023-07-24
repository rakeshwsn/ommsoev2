<?php

namespace Admin\Physicalcomponents\Models;

use CodeIgniter\Model;

class ComponentPhyModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'mpr_components';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'object';
    protected $useSoftDeletes        = false;
    protected $protectFields        = false;
    //	protected $allowedFields        = [];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'description' => array(
            'label' => 'description',
            'rules' => 'trim|required'
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

    public function getAll($data = array())
    {
        //printr($data);
        $builder = $this->db->table($this->table);
        $this->filter($builder, $data);

        $builder->select("*");

        if (isset($data['sort']) && $data['sort']) {
            $sort = $data['sort'];
        } else {
            $sort = "id";
        }

        if (isset($data['order']) && ($data['order'] == 'desc')) {
            $order = "asc";
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

        $res = $builder->get()->getResultArray();

        return $res;
    }

    public function insertMprComponents($data)
    {

        if (isset($data['componentsdata'])) {

            foreach ($data['componentsdata'] as $componentsdatas) {

                $target_data = array(
                    "componentid" => $componentsdatas['componentid'],
                    "description" => $componentsdatas['description'],
                    "year_id" => 1,
                );
                $this->db->table("mpr_components")->insert($target_data);
            }
        }
    }

    public function updateComponentData($id, $data)
    {
        if (isset($data['componentsdata'])) {

            foreach ($data['componentsdata'] as $componentsdatas) {

                $target_data = array(
                    "componentid" => $componentsdatas['componentid'],
                    "description" => $componentsdatas['description'],
                    "year_id" => 1,
                );
                $this->db->table("mpr_components")->where("id", $id)->update($target_data);
            }
        }
    }

    public function getTotal($data = array())
    {
        $builder = $this->db->table($this->table);
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

    public function deleteenterprises($selected = [])
    {
        $builder = $this->db->table('enterprises');
        $builder->whereIn("id", $selected)->delete();
    }


    public function getComponentsSearch($data = array())
    {

        $builder = $this->db->table('soe_components');
        $builder->join('soe_components_assign', 'soe_components_assign.component_id = soe_components.id', 'left');
        $this->filtercomponent($builder, $data);
        $builder->select('soe_components.id, CONCAT(soe_components.description, " (Code-", soe_components_assign.number, ")") AS component');
        $res = $builder->get()->getResult();
        return $res;
    }

    private function filtercomponent($builder, $data)
    {

        if (!empty($data)) {
            $builder->where('soe_components.description LIKE', "%{$data}%");
        }
    }




    public function getComponents()
    {
        $builder = $this->db->table('mpr_components');
        $components   = $builder->get()->getResult();

        $season_data = [];
        foreach ($components as $component) {
            $componentsData[] = [
                'id' => $component->id,
                'description' => $component->description,

            ];
        }
        return $componentsData;
    }
}
