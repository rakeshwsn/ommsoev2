<?php

namespace Admin\Physicalachievement\Models;

use CodeIgniter\Model;

class PhysicalachievementModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'mpr_components_achive_data';
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
    public function getComponentsAllData($filter = [])
    {
        $monthid = $filter['month_id'] - 1;
        $sql = "SELECT
        mc.id,
        mc.description AS component,
        target.no_total targettotal,
        IFNULL(SUM(upto_prev.no_total), 0) AS ach_total,
        cur_ach.cur_total
      FROM mpr_components mc
        LEFT JOIN (
          SELECT
            mctd.mpr_component_id,
            mctd.no_total,
            mctd.mprcomponents_master_id
          FROM mpr_components_target_data mctd
            LEFT JOIN mpr_components_target_master mctm
              ON mctd.mprcomponents_master_id = mctm.id
          WHERE mctm.district_id = " . $filter['district_id'] . "
          AND mctm.year_id = " . $filter['year_id'] . "
        ) target ON target.mpr_component_id = mc.id
        LEFT JOIN (
          SELECT
            mcad.mprcomponents_master_id,
            mcad.mpr_component_id,
            mcad.no_total
          FROM mpr_components_achive_data mcad
          WHERE mcad.month_id BETWEEN 4 AND " . $monthid . "
        ) upto_prev ON upto_prev.mpr_component_id = mc.id AND upto_prev.mprcomponents_master_id = target.mprcomponents_master_id
        LEFT JOIN (
          SELECT
            mcad.mpr_component_id,
            mcad.mprcomponents_master_id,
            mcad.no_total AS cur_total
          FROM mpr_components_achive_data mcad
          WHERE mcad.month_id = " . $filter['month_id'] . "
        ) cur_ach ON cur_ach.mpr_component_id = mc.id AND cur_ach.mprcomponents_master_id = target.mprcomponents_master_id
        WHERE mc.year_id <= " . $filter['year_id'] . "
      GROUP BY mc.id, mc.description, target.no_total, cur_ach.cur_total";
        return $this->db->query($sql)->getResultArray();
    }



    public function showTargetAchData($data=[])
    {
        $preMonth = $data['month_id'] -1;
        $currentYearStartMonth = 4;
        $currentMonth = date('n');
        $currentYear = 1;
        $nextYearAprilMonth = 4;

        $sql = "SELECT
        dist.id AS district_id,
        dist.district,
        dist.total_block";
        $componets=$this->getMprComponents($data['year_id']);
        foreach($componets as $comp){
        $sql .= " , comp_target{$comp['id']}.target{$comp['id']},upto_ach{$comp['id']}.upto_ach{$comp['id']},curr_ach{$comp['id']}.cur_ach{$comp['id']} ";
        }
        $sql .= " FROM (SELECT
          sd.id,
          sd.name AS district,
          COUNT(sb.id) total_block
        FROM soe_districts sd
          LEFT JOIN soe_blocks sb
            ON sb.district_id = sd.id where 1=1";
            if (!empty($data['district_id'])) {
                $sql .= " and sb.district_id = " . $data['district_id'];
            }
            $sql .= "
        GROUP BY sb.district_id) dist";

          foreach($componets as $comp){
            $sql.=" LEFT JOIN (SELECT
            mctm.district_id,
            SUM(mctd.no_total) AS target{$comp['id']}
          FROM mpr_components_target_master mctm
            LEFT JOIN mpr_components_target_data mctd
              ON mctm.id = mctd.mprcomponents_master_id
          WHERE mctm.year_id = " . $data['year_id'] . "
          AND mctd.mpr_component_id = {$comp['id']}
          GROUP BY mctm.district_id) comp_target{$comp['id']}
          ON comp_target{$comp['id']}.district_id = dist.id
         LEFT JOIN (SELECT
            mctm.district_id,
            SUM(mcad.no_total) AS upto_ach{$comp['id']}
          FROM mpr_components_target_master mctm
            LEFT JOIN mpr_components_achive_data mcad
              ON mctm.id = mcad.mprcomponents_master_id
          WHERE mctm.year_id = " . $data['year_id'] . "
          AND mcad.mpr_component_id = {$comp['id']}
          AND mcad.month_id BETWEEN 4 AND " . $preMonth . "
          GROUP BY mctm.district_id) upto_ach{$comp['id']}
          ON upto_ach{$comp['id']}.district_id = dist.id
        LEFT JOIN (SELECT
            mctm.district_id,
            SUM(mcad.no_total) AS cur_ach{$comp['id']}
          FROM mpr_components_target_master mctm
            LEFT JOIN mpr_components_achive_data mcad
              ON mctm.id = mcad.mprcomponents_master_id
          WHERE mctm.year_id = " . $data['year_id'] . "
          AND mcad.mpr_component_id = {$comp['id']}
          AND mcad.month_id = " . $data['month_id'] . "
          GROUP BY mctm.district_id) curr_ach{$comp['id']}
          ON curr_ach{$comp['id']}.district_id = dist.id";
        }
        $sql .= " ORDER BY dist.district ASC";
       // echo $sql; exit;
        return $this->db->query($sql)->getResultArray();
    }

    protected function getMprComponents($data){
        $sql="SELECT * FROM mpr_components WHERE year_id <= " . $data . " ORDER BY id ASC";
        return $this->db->query($sql)->getResultArray();
    }

    public function getMprComponentsall($data = []){
        $sql="SELECT * FROM mpr_components WHERE year_id <= " . $data['year_id'] . " ORDER BY id ASC";
        return $this->db->query($sql)->getResultArray();
    }

    public function addPhysicalachData($data)
    {
        $monthid = $data['monthid'];
        $masterid = $data['componentidmain'];

        // Check if $monthid and $masterid exist inside the table
        $existingData = $this->db->table("mpr_components_achive_data")
            ->where('mprcomponents_master_id', $masterid)
            ->where('month_id', $monthid)
            ->get()
            ->getResult();

        if (!empty($existingData)) {
            // $monthid and $masterid exist in the table, delete the existing rows
            $this->db->table("mpr_components_achive_data")
                ->where('mprcomponents_master_id', $masterid)
                ->where('month_id', $monthid)
                ->delete();
        }

        // Insert the new data
        if (isset($data['component'])) {
            foreach ($data['component'] as $componentid => $achmultiple) {
                $masterTableData = array(
                    "mprcomponents_master_id" => $masterid,
                    "mpr_component_id" => $componentid,
                    "month_id" => $monthid,
                    "no_total" => $achmultiple ? $achmultiple : 0,
                );

                $this->db->table("mpr_components_achive_data")->insert($masterTableData);
            }
        }
    }



    public function showGetExsistsMonthData($filter = [])
    {

        $builder = $this->db->table('mpr_components_target_master');
        $builder->join('mpr_components_achive_data', 'mpr_components_achive_data.mprcomponents_master_id = mpr_components_target_master.id', 'left');
        $builder->select('mpr_components_achive_data.id, mpr_components_achive_data.month_id');

        if (isset($filter['district_id'])) {
            $builder->where('mpr_components_target_master.district_id', $filter['district_id']);
        }
        if (isset($filter['year_id'])) {
            $builder->where('mpr_components_target_master.year_id', $filter['year_id']);
        }
        if (isset($filter['month_id'])) {
            $builder->where('mpr_components_achive_data.month_id', $filter['month_id']);
        }

        $result = $builder->get()->getRow();
        return $result;
    }
}
