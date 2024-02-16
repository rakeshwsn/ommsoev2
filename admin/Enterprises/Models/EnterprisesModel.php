<?php

namespace Admin\Enterprises\Models;

use CodeIgniter\Model;

class EnterprisesModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'enterprises';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $protectFields = false;
    protected $allowedFields = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
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

    public function getAll($filter = [])
    {
        $sql = "SELECT
        e.id,
        e.unit_id,
        e.district_id,
        e.block_id,
        e.gp_id,
        e.village_id,
        e.budget_fin_yr_id,
        e.management_unit_type,
        e.managing_unit_name,
        e.contact_person,
        e.contact_mobile,
        e.date_estd,
        e.mou_date,
        e.own_share,
        e.unit_budget,
        e.addl_budget,
        e.unit_budget_amount,
        e.is_support_basis_infr,
        e.is_support_basis_infr,
        e.purpose_infr_support,
        e.support_infr_amount,
        e.address,
        e.pincode,
        date(e.created_at) created_at,
        sd.name districts,
        sb.name blocks,
        v.name villages,
        sg.name gps,
        eu.name unit_name,
        eu.unit_group_id,
        YEAR(e.date_estd) doeyear
        FROM enterprises e
        LEFT JOIN soe_districts sd ON sd.id = e.district_id
        LEFT JOIN soe_blocks sb ON sb.id = e.block_id
        LEFT JOIN villages v ON v.id = e.village_id
        LEFT JOIN soe_grampanchayats sg  ON sg.id = e.gp_id
        LEFT JOIN enterprises_units eu ON eu.id = e.unit_id
        WHERE e.deleted_at IS NULL ";

        if (!empty($filter['district_id'])) {
            $sql .= " AND e.district_id = " . $filter['district_id'];
        }
        if (!empty($filter['block_id'])) {
            $sql .= " AND e.block_id = " . $filter['block_id'];
        }
        if (!empty($filter['unit_id'])) {
            $sql .= " AND e.unit_id = " . $filter['unit_id'];
        }
        if (!empty($filter['doeyear'])) {
            $sql .= " AND YEAR(e.date_estd) = " . $filter['doeyear'];
        }

        if (!empty($filter["management_unit_type"])) {
            $sql .= " AND e.management_unit_type = '" . $filter["management_unit_type"] . "'";
        }
        if (!empty($filter['filter_search'])) {
            $search = $filter['filter_search'];
            $sql .= "AND
                (sd.name LIKE '%$search%' OR
                sb.name LIKE '%$search%' OR
                v.name LIKE '%$search%' OR
                eu.name LIKE '%$search%' OR
                e.managing_unit_name LIKE '%$search%')
            ";
        }
      
        if (!empty($filter['sort']) && $filter['sort']) {
            $sort = $filter['sort'];
        } else {
            $sort = "sd.name,sb.name,e.created_at";
        }

        if (!empty($filter['order']) && ($filter['order'] == 'desc')) {
            $order = "desc";
        } else {
            $order = "asc";
        }
        $sql .= " ORDER BY " . $sort . " " . $order;

        if (!empty($filter['start']) || isset($filter['limit'])) {
            if ($filter['start'] < 0) {
                $filter['start'] = 0;
            }

            if ($filter['limit'] < 1) {
                $filter['limit'] = 10;
            }
            $sql .= " LIMIT " . (int)$filter['start'] . "," . (int)$filter['limit'];
        }
       
        
     
        return $this->db->query($sql)->getResult();
    }

    /**
     * Get total rows
     *
     * @param datatype $paramname description
     * @return Number of rows
     */

    public function getTotals($data = array())
    {
        $builder = $this->db->table("{$this->table} e");
        $this->filter($builder, $data);

        // Add condition to check if deleted_at is not null
        $builder->where('e.deleted_at IS  NULL');

        $count = $builder->countAllResults();


        return $count;
    }
    private function filter($builder, $data)
    {
        $builder->join('soe_districts sd', 'e.district_id = sd.id', 'left');
        $builder->join('soe_blocks sb', 'e.block_id = sb.id', 'left');
        $builder->join('villages v', 'e.district_id = v.id', 'left');
        $builder->join('soe_grampanchayats sg', 'e.gp_id = sg.id', 'left');
        $builder->join('enterprises_units eu', 'e.unit_id = eu.id', 'left');

        if (!empty($data['district_id'])) {
            $builder->where("e.district_id  = '" . $data['district_id'] . "'");
        }
        if (!empty($data['block_id'])) {
            $builder->where("e.block_id  = '" . $data['block_id'] . "'");
        }
        if (!empty($data['unit_id'])) {
            $builder->where("e.unit_id  = '" . $data['unit_id'] . "'");
        }
        if (!empty($data['doeyear'])) {
            $builder->where("YEAR(e.date_estd) = '" . $data['doeyear'] . "'");
        }
        if (!empty($data['management_unit_type'])) {
            $builder->where("e.management_unit_type  = '" . $data['management_unit_type'] . "'");
        }
        //add to filter
        // $builder->where("sb.is_program  = 1");

        if (!empty($data['filter_search'])) {
            $builder->where("
				sd.name LIKE '%{$data['filter_search']}%' OR
                sb.name LIKE '%{$data['filter_search']}%' OR
                v.name LIKE '%{$data['filter_search']}%' OR
               eu.name LIKE '%{$data['filter_search']}%' OR
                eu.name LIKE '%{$data['filter_search']}%' OR
                e.managing_unit_name LIKE '%{$data['filter_search']}%'
			");
        }
    }

    public function yearWise($district_id)
    {
        $sql = "SELECT
        e.id,
        e.district_id,
        YEAR(e.date_estd) year
        FROM enterprises e
        WHERE e.district_id = $district_id  AND YEAR(e.date_estd) > 2000
        GROUP BY year
        ORDER BY year ";

        return $this->db->query($sql)->getResult();
    }

    /**
     * This method is called from establishemntreport for districtwise report generation.
     *
     * @param array $filter
     *
     * @return array $result
     */

    public function districtwiseUnits($filter)
    {
        $sql = "SELECT
        disunit.unit_id,
        disunit.unit,
        disunit.district_id,
        disunit.district,
        COALESCE(res.total_units, 0) total_units,
        res.management_unit_type,
        res.date_estd,
        res.year_id,
        res.month
        FROM (SELECT
        eu.id unit_id,
        eu.name unit,
        sd.id district_id,
        sd.name district
        FROM soe_districts sd
        CROSS JOIN (SELECT * FROM enterprises_units WHERE deleted_at IS NULL) eu) disunit
        LEFT JOIN (SELECT
        unit_id,
        COUNT(e.unit_id) total_units,
        e.district_id,
        e.management_unit_type,
        e.date_estd,
        dy.id year_id,
        MONTH(e.date_estd) month
      FROM enterprises e
        LEFT JOIN dashboard_years dy
          ON DATE(e.date_estd) BETWEEN DATE(dy.start_date) AND DATE(dy.end_date)
          LEFT JOIN soe_months sm
        ON MONTH(e.date_estd) = sm.number
      WHERE e.deleted_at IS NULL";
        if (!empty($filter['year_id']) && $filter['year_id']) {
            $sql .= " AND dy.id = " . $filter['year_id'];
        }
        if (!empty($filter['month']) && $filter['month']) {
            $sql .= " AND sm.id = " . $filter['month'];
        }
        if (!empty($filter["management_unit_type"]) && $filter["management_unit_type"] !== 'all') {
            $sql .= " AND e.management_unit_type = '" . $filter["management_unit_type"] . "'";
        }
        if (!empty($filter['unit_type'])) {
            if ($filter['unit_type'] == 'without_establishment_date') {
                $sql .= " AND (YEAR(e.date_estd) < 2000 ) ";
            }
            if ($filter['unit_type'] == 'without_mou_date') {
                $sql .= " AND (YEAR(e.mou_date) < 2000 ) ";
            }
            if ($filter['unit_type'] == 'only_establishment_date') {
                $sql .= " AND (YEAR(e.date_estd) > 2000 ) ";
            }
        }
        $sql .= " GROUP BY e.unit_id, e.district_id
      ORDER BY e.district_id) res
      ON disunit.district_id = res.district_id
      AND disunit.unit_id = res.unit_id ";

        $sql .= " ORDER BY unit_id, district";
        // echo $sql;exit;
        return $this->db->query($sql)->getResult();
    }

    /**
     * This method is called from establishemntreport for blockwise report generation.
     *
     * @param array $filter
     *
     * @return array $result
     */
    public function blockwiseUnits($filter)
    {
        $sql = "SELECT
        blkunit.block_id,
        blkunit.block,
        blkunit.district_id,
        blkunit.unit,
        blkunit.unit_id,
        COALESCE(res.total_units, 0) total_units,
        res.management_unit_type,
        res.date_estd,
        res.year_id,
        res.month
     FROM (SELECT
        eu.id unit_id,
        eu.name unit,
        sb.id block_id,
        sb.name block,
        sb.district_id,
        sb.is_program
      FROM soe_blocks sb
        CROSS JOIN (SELECT * FROM enterprises_units WHERE deleted_at IS NULL) eu) blkunit
        LEFT JOIN (SELECT
        e.unit_id,
        COUNT(e.unit_id) total_units,
        e.block_id,
        e.management_unit_type,
        e.date_estd,
        dy.id year_id,
        MONTH(e.date_estd) month
      FROM enterprises e
        LEFT JOIN dashboard_years dy
          ON DATE(e.date_estd) BETWEEN DATE(dy.start_date) AND DATE(dy.end_date)
          LEFT JOIN soe_months sm
        ON MONTH(e.date_estd) = sm.number
      WHERE e.deleted_at IS NULL";
        if (!empty($filter['year_id'])) {
            $sql .= " AND dy.id = " . $filter['year_id'];
        }
        if (!empty($filter['month'])) {
            $sql .= " AND sm.id = " . $filter['month'];
        }
        if (!empty($filter["management_unit_type"]) && $filter["management_unit_type"] !== 'all') {
            $sql .= " AND e.management_unit_type = '" . $filter["management_unit_type"] . "'";
        }

        if (!empty($filter['unit_type'])) {
            if ($filter['unit_type'] == 'without_establishment_date') {
                $sql .= " AND (YEAR(e.date_estd) < 2000 ) ";
            }
            if ($filter['unit_type'] == 'without_mou_date') {
                $sql .= " AND (YEAR(e.mou_date) < 2000 ) ";
            }
            if ($filter['unit_type'] == 'only_establishment_date') {
                $sql .= " AND (YEAR(e.date_estd) > 2000 ) ";
            }
        }
        $sql .= " GROUP BY e.unit_id,
               e.block_id) res
      ON blkunit.block_id = res.block_id
      AND blkunit.unit_id = res.unit_id
      and blkunit.is_program = 1";

        if (!empty($filter['district_id'])) {
            $sql .= " WHERE blkunit.district_id = " . $filter['district_id'];
        }

        $sql .= " ORDER BY unit_id, block";
        //   echo $sql;exit;
        return $this->db->query($sql)->getResult();
    }

    /**
     * This method is called from establishemntreport for gpwise report generation.
     *
     * @param array $filter
     *
     * @return array $result
     */
    public function gpwiseUnits($filter)
    {
        $sql = "SELECT
        gpunits.gp_id,
        gpunits.gp,
        gpunits.block_id,
        gpunits.unit_id,
        gpunits.unit,
        COALESCE(res.total_units, 0) total_units,
        res.management_unit_type,
        res.date_estd,
        res.year_id,
        res.month
     FROM (SELECT
      gp.id gp_id,
      gp.name gp,
      gp.block_id,
      eu.id unit_id,
      eu.name unit
     FROM (SELECT
      id,
      name,
      block_id,
      lgd_code
    FROM soe_grampanchayats
    UNION
    SELECT
      NULL id,
      'Other' name,
      block_id,
      NULL lgd_code
    FROM (SELECT DISTINCT
        block_id
      FROM soe_grampanchayats) AS blocks) gp
      CROSS JOIN enterprises_units eu WHERE eu.deleted_at IS NULL";
        if (!empty($filter['block_id'])) {
            $sql .= " AND block_id = " . $filter['block_id'];
        }
        $sql .= ") gpunits
     LEFT JOIN (SELECT
        e.unit_id,
        COUNT(e.unit_id) total_units,
        e.gp_id, e.block_id,
        e.management_unit_type,
        e.date_estd,
        dy.id year_id,
        MONTH(e.date_estd) month
      FROM enterprises e
      LEFT JOIN dashboard_years dy
          ON DATE(e.date_estd) BETWEEN DATE(dy.start_date) AND DATE(dy.end_date)
          LEFT JOIN soe_months sm
        ON MONTH(e.date_estd) = sm.number
      WHERE e.deleted_at IS NULL";
        if (!empty($filter['block_id'])) {
            $sql .= " AND e.block_id = " . $filter['block_id'];
        }
        if (!empty($filter['year_id'])) {
            $sql .= " AND dy.id = " . $filter['year_id'];
        }
        if (!empty($filter['month'])) {
            $sql .= " AND sm.id = " . $filter['month'];
        }
        if (!empty($filter["management_unit_type"]) && $filter["management_unit_type"] !== 'all') {
            $sql .= " AND e.management_unit_type = '" . $filter["management_unit_type"] . "'";
        }
        if (!empty($filter['unit_type'])) {
            if ($filter['unit_type'] == 'without_establishment_date') {
                $sql .= " AND (YEAR(e.date_estd) < 2000 ) ";
            }
            if ($filter['unit_type'] == 'without_mou_date') {
                $sql .= " AND (YEAR(e.mou_date) < 2000 ) ";
            }
            if ($filter['unit_type'] == 'only_establishment_date') {
                $sql .= " AND (YEAR(e.date_estd) > 2000 ) ";
            }
        }
        $sql .= " GROUP BY e.unit_id,
               e.gp_id) res
      ON  coalesce(res.gp_id,0) = coalesce(gpunits.gp_id,0)
      AND gpunits.block_id=res.block_id AND gpunits.unit_id=res.unit_id
      ORDER BY unit_id, gp";

//         echo $sql;exit;
        return $this->db->query($sql)->getResult();
    }

    public function getMainCenters($district_id, $unit_id, $block_id = '')
    {
        $sql = "SELECT
      e.id ent_id,
      e.managing_unit_name,
      e.management_unit_type,
      e.main_center_id
      FROM enterprises e
      INNER JOIN enterprises_units eu
        ON e.unit_id = eu.id
      WHERE e.deleted_at IS NULL
      AND eu.deleted_at IS NULL AND e.center_type = 'main_center' AND main_center_id IS NOT NULL AND unit_id = $unit_id";
        if ($district_id) {
            $sql .= " AND e.district_id = " . (int)$district_id;
        }
        if ($block_id) {
            $sql .= " AND e.block_id = " . (int)$block_id;
        }
        return $this->db->query($sql)->getResult();
    }

    public function equipment($id)
    {
        $sql = "SELECT
        e.ent_id,
        e.equipment_id,
        e.quantity,
        eq.name equipment
     FROM enterprise_equipment e
     LEFT JOIN equipment eq
      ON e.equipment_id = eq.id  WHERE e.ent_id = $id";
        // printr($sql);
        // exit;
        return $this->db->query($sql)->getResult();
    }

    public function getBy($filter)
    {
        $sql = "SELECT e.id enterprise_id,e.block_id,b.name `block`,e.gp_id,gp.name grampanchayat,e.village_id,v.name village,
        e.management_unit_type,e.managing_unit_name shg_name,eu.id unit_id,
        eu.name unit_name FROM enterprises e
        LEFT JOIN enterprises_units eu ON e.unit_id=eu.id
        LEFT JOIN soe_blocks b ON e.block_id=b.id
        LEFT JOIN soe_grampanchayats gp ON e.gp_id=gp.id
        LEFT JOIN villages v ON e.village_id=v.id
        WHERE e.deleted_at IS NULL AND b.is_program=1";

        if (!empty($filter['district_id'])) {
            $sql .= " AND e.district_id= " . (int)$filter['district_id'];
        }

        if (!empty($filter['unit_id'])) {
            $sql .= " AND e.unit_id= " . (int)$filter['unit_id'];
        }
        // echo $sql;exit;
        return $this->db->query($sql)->getResult();
    }
}
