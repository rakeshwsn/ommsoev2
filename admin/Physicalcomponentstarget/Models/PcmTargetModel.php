<?php

namespace Admin\Physicalcomponentstarget\Models;

use CodeIgniter\Model;

class PcmTargetModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'mpr_components_target_master';
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
        'components_name' => array(
            'label' => 'components_name',
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

    public function getAll($data = array())
    {
        //printr($data);
        $builder = $this->db->table($this->table);
        $this->filter($builder, $data);

        $builder->select("*");

        if (isset($data['sort']) && $data['sort']) {
            $sort = $data['sort'];
        } else {
            $sort = "components_name";
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

        $res = $builder->get()->getResult();

        return $res;
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

    public function addPhysicaltargetdata($data)
    {
        $yearid = $data['year_id'];


        if (isset($data['component'])) {
            $masterTableData = array();
            $targetData = array();

            foreach ($data['component'] as $districtId => $targetMultiple) {
                $masterTableData = array(
                    "year_id" => $yearid,
                    "district_id" => $districtId,
                );

                // $masterTableId = array();

                $this->db->table("mpr_components_target_master")->insert($masterTableData);
                $masterTableId =  $this->db->insertID();

                foreach ($targetMultiple as $componentId => $targetValue) {
                    $targetData = array(
                        "mprcomponents_master_id" => $masterTableId,
                        "mpr_component_id" => $componentId,
                        "no_total" => $targetValue ? $targetValue : 0,
                    );
                    $targetTable = $this->db->table("mpr_components_target_data")->insert($targetData);
                }
            }
        }

        //  printr($masterTableData); exit;
    }

    public function updateMasterData($data)
    {
        $yearId = $data['year_id'];

        if (isset($data['component'])) {
            foreach ($data['component'] as $districtId => $targetMultiple) {
                $masterTableIds = $this->getMasterDataId($yearId, $districtId);

                // Delete existing data for each master table ID
                foreach ($masterTableIds as $masterTableId) {
                    $this->db->table('mpr_components_target_data')
                        ->where('mprcomponents_master_id', $masterTableId)
                        ->delete();
                }

                // Insert new data
                foreach ($targetMultiple as $componentId => $targetValue) {
                    foreach ($masterTableIds as $masterTableId) {
                        $insertData = array(
                            'mprcomponents_master_id' => $masterTableId,
                            'mpr_component_id' => $componentId,
                            'no_total' => $targetValue ?: 0,
                        );

                        $this->db->table('mpr_components_target_data')
                            ->insert($insertData);
                    }
                }
            }
        }
    }


    public function getMasterDataId($yearId, $districtId)
    {
        $results = $this->db->table('mpr_components_target_master')
            ->select('id')
            ->where('year_id', $yearId)
            ->where('district_id', $districtId)
            ->get()
            ->getResult();

        $ids = [];
        if (is_array($results)) {
            foreach ($results as $result) {
                $ids[] = $result->id;
            }
        }

        return $ids;
    }


    public function showTargetComponents($filter = [])
    {
        // printr($filter); exit;
        $sql = "SELECT
        mc_dist.district_id,
        mc_dist.district,
        mc_dist.mc_id,
        mc_dist.description,
        mcm.year_id,
        mctd.mprcomponents_master_id,
        CASE WHEN mctd.no_total IS NULL THEN 0 ELSE mctd.no_total END AS total
      FROM (SELECT
          sd.id district_id,
          sd.name district,
          mc.id mc_id,
          mc.description
        FROM soe_districts sd
          CROSS JOIN mpr_components mc) mc_dist
        LEFT JOIN (SELECT * FROM mpr_components_target_master mctm ";

        if (empty($filter['district_id'])) {
            $sql .= "WHERE mctm.year_id = " . $filter['year_id'];
        }

        $sql .= ") mcm
          ON mc_dist.district_id = mcm.district_id
        LEFT JOIN mpr_components_target_data mctd
          ON mctd.mprcomponents_master_id = mcm.id
          AND mctd.mpr_component_id = mc_dist.mc_id
      WHERE mcm.deleted_at IS NULL ";
        if ($filter['district_id']) {
            $sql .= " AND mc_dist.district_id = " . $filter['district_id'] . " AND mcm.year_id = " . $filter['year_id'];
        }
        $sql .= " ORDER BY mc_dist.district ASC, mc_dist.description ASC";
        return $this->db->query($sql)->getResultArray();
    }


    public function getTargetcomponent()
    {
        $sql = "SELECT
        mc_dist.district_id,
        mc_dist.district,
        mc_dist.mc_id,
        mc_dist.description,
        mcm.year_id,
        mctd.mprcomponents_master_id,
        CASE WHEN mctd.no_total IS NULL THEN 0 ELSE mctd.no_total END AS total
      FROM (SELECT
          sd.id district_id,
          sd.name district,
          mc.id mc_id,
          mc.description
        FROM soe_districts sd
          CROSS JOIN mpr_components mc) mc_dist
        LEFT JOIN mpr_components_target_master mcm
          ON mc_dist.district_id = mcm.district_id
        LEFT JOIN mpr_components_target_data mctd
          ON mctd.mprcomponents_master_id = mcm.id
          AND mctd.mpr_component_id = mc_dist.mc_id
      WHERE mcm.deleted_at IS NULL
      ORDER BY mc_dist.district ASC, mc_dist.description ASC";

        return $this->db->query($sql)->getResultArray();
    }

    public function getTargetData($id)
    {
        $builder = $this->db->table('mpr_components_target_data')
            ->Where(['mprcomponents_master_id' => $id])
            ->get();

        $componentData = $builder->getResultArray();
        return $componentData;
    }


    public function showCheckExistsData($filter = [])
    {
        $builder = $this->db->table($this->table);

        if (isset($filter['district_id'])) {
            $builder->where('district_id', $filter['district_id']);
        }

        if (isset($filter['year_id'])) {
            $builder->where('year_id', $filter['year_id']);
        }

        $count = $builder->countAllResults();
        return $count;
    }

    public function showGetExsistsData($filter = [])
    {
        $builder = $this->db->table($this->table);

        if (isset($filter['district_id'])) {
            $builder->where('district_id', $filter['district_id']);
        }

        if (isset($filter['year_id'])) {
            $builder->where('year_id', $filter['year_id']);
        }

        $query = $builder->select('id')->get();
        $result = $query->getRow();
        return $result;
    }

    // public function deleteenterprises($selected = [])
    // {
    //     $builder = $this->db->table('enterprises');
    //     $builder->whereIn("id", $selected)->delete();
    // }
}
