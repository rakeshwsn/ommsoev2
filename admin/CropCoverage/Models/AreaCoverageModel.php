<?php
namespace Admin\CropCoverage\Models;

use CodeIgniter\Model;

class AreaCoverageModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'ac_crop_coverage';
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

    //protected $db;


    public function addAC($data)
    {

        $areadata = array(
            "districts" => $data['districts_id'],
            "year" => $data['year_id'],
            "season" => $data['season'],
            "week" => $data['week'],

        );
        $this->db->table('ac_seasons')->insert($areadata);
    }

    public function getSeasons()
    {

        $seasons = [
            'rabi' => 'Rabi',
            'kharif' => 'Kharif',
        ];

        return $seasons;
    }

    public function getCropPractices()
    {
        $sql = "SELECT
  acp.crop_id,
  acp.practice_id,
  ac.crops,
  ap.name practice
FROM ac_crop_practices acp
  LEFT JOIN ac_crops ac
    ON acp.crop_id = ac.id
  LEFT JOIN ac_practices ap
    ON acp.practice_id = ap.id ORDER BY acp.crop_id,practice_id";

        $result = $this->db->query($sql)->getResultArray();

        $crops = [];
        // Loop through the result and group the practices by crop_id
        foreach ($result as $row) {
            $cropId = $row['crop_id'];
            $practice = strtolower($row['practice']);

            // If the crop_id is not yet in the $crops array, create a new entry
            if (!isset($crops[$cropId])) {
                $crops[$cropId] = [];
            }

            // Add the practice to the corresponding crop_id entry
            $crops[$cropId][] = $practice;
        }

        return $crops;
    }

    public function addNursery($nursery)
    {
        $builder = $this->db->table('ac_nursery');
        $builder->insert($nursery);
    }

    public function addArea($areas)
    {
        $builder = $this->db->table('ac_area_practices');
        foreach ($areas as $area) {
            $builder->insert($area);
        }
    }

    public function addFupCrops($fCrop)
    {
        $builder = $this->db->table('ac_area_follow_up');
        $builder->insertBatch($fCrop);
    }

    public function deleteNursery($crop_coverage_id)
    {
        $builder = $this->db->table('ac_nursery');
        $builder->where('crop_coverage_id', $crop_coverage_id)->delete();
    }

    public function deleteArea($crop_coverage_id)
    {
        $builder = $this->db->table('ac_area_practices');
        $builder->where('crop_coverage_id', $crop_coverage_id)->delete();
    }

    public function deleteFupCrops($crop_coverage_id)
    {
        $builder = $this->db->table('ac_area_follow_up');
        $builder->where('crop_coverage_id', $crop_coverage_id)->delete();
    }

    public function getCurrentYearDates($date = null)
    {

        $this->settings = new \Config\Settings();

        $kharif_start_month = getMonthById((int) $this->settings->kharif_start_month);
        $kharif_end_month = getMonthById((int) $this->settings->kharif_end_month);

        $rabi_start_month = getMonthById((int) $this->settings->rabi_start_month);
        $rabi_end_month = getMonthById((int) $this->settings->rabi_end_month);

        $date = $date ?: date('Y-m-d');
        $given_date = \DateTime::createFromFormat('Y-m-d', $date);

        $kharif_start_month_number = $kharif_start_month['number'];
        $kharif_end_month_number = $kharif_end_month['number'];
        $rabi_start_month_number = $rabi_start_month['number'];
        $rabi_end_month_number = $rabi_end_month['number'];
        if (
            $given_date->format('n') >= $kharif_start_month_number
            && $given_date->format('n') <= $kharif_end_month_number
        ) {
            $current_season = 'Kharif';
            $season_start_date = \DateTime::createFromFormat('Y-m-d', $given_date->format('Y') . '-' . $kharif_start_month_number . '-01');
            $season_end_date = \DateTime::createFromFormat('Y-m-d', $given_date->format('Y') . '-' . $kharif_end_month_number . '-30');
        } elseif (
            $given_date->format('n') >= $rabi_start_month_number
            || $given_date->format('n') <= $rabi_end_month_number
        ) {
            $current_season = 'Rabi';
            $season_start_date = \DateTime::createFromFormat('Y-m-d', $given_date->format('Y') . '-' . $rabi_start_month_number . '-01');
            $season_end_date = \DateTime::createFromFormat('Y-m-d', $given_date->format('Y') . '-' . $rabi_end_month_number . '-30');
            if ($given_date->format('n') <= 3) {
                $season_start_date->modify('-1 year');
                $season_end_date->modify('-1 year');
            }
        }

        return [
            'season' => $current_season,
            'start_date' => $season_start_date,
            'end_date' => $season_end_date,
        ];

    }

    public function getWeeks()
    {

        $dates = $this->getCurrentYearDates();

        $start = $dates['start_date'];
        $end = $dates['end_date'];
        $week_start = $this->settings->start_week;
        $week_start_index = array_search(strtolower($week_start), array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'));

        $output = [];

        while ($start <= $end) {
            $day_of_week = (int) $start->format('w');
            if ($day_of_week === $week_start_index && $start >= $start && $start <= $end) {
                $output[] = [
                    'start_date' => $start->format('Y-m-d'),
                    'end_date' => min($start->modify('+6 days'), $end)->format('Y-m-d')
                ];
            }
            $start->modify('+1 day');
        }

        return $output;
    }

    public function getWeekDate($date = 'today')
    {
        $output = $this->getWeeks();
        $date = strtotime($date);
        foreach ($output as $dates) {
            if ($date >= strtotime($dates['start_date']) && $date <= strtotime($dates['end_date'])) {
                return $dates;
            }
        }
        return [];
    }

    public function getAreaCoverage($filter = [])
    {
        if (!empty($filter['block_id'])) {
            $sql = "SELECT cc.*,gp.name gp FROM vw_area_coverage_gpwise cc 
                    LEFT JOIN soe_grampanchayats gp ON cc.gp_id=gp.id 
                    WHERE cc.block_id=" . $filter['block_id'];
            if (!empty($filter['year_id'])) {
                $sql .= " AND cc.year_id=" . $filter['year_id'];
            }
            if (!empty($filter['season'])) {
                $sql .= " AND cc.season='" . $filter['season'] . "'";
            }
            if (!empty($filter['start_date'])) {
                $sql .= " AND DATE(cc.start_date)=date('" . $filter['start_date'] . "')";
            }
            $sql .= " ORDER BY date(cc.start_date) DESC,gp.name ASC";

        } else if (!empty($filter['district_id'])) {
            $sql = "SELECT ac.*,b.id block_id,
  b.name block,bgps.gps total_gps FROM soe_blocks b 
  LEFT JOIN (SELECT * FROM vw_blockwise_gps) bgps ON bgps.block_id=b.id
  LEFT JOIN (SELECT * FROM vw_area_coverage_blockwise cc 
                    WHERE cc.year_id=" . $filter['year_id'] .
                " AND cc.season='" . $filter['season'] . "'";
            if (!empty($filter['start_date'])) {
                $sql .= " AND DATE(cc.start_date)=date('" . $filter['start_date'] . "')";
            }
            $sql .= ") ac ON ac.block_id=b.id 
                WHERE b.district_id=" . $filter['district_id'];
            $sql .= " ORDER BY date(ac.start_date) DESC,b.name ASC";

        } else {
            $sql = "SELECT
  sd.id district_id,
  sd.name district,
  dbg.total_blocks,
  dbg.total_gps,
  ac.start_date,
  ac.end_date,
  ac.farmers_covered,
  ac.nursery_raised,
  ac.balance_smi,
  ac.balance_lt,
  ac.ragi_smi,
  ac.ragi_lt,
  ac.ragi_ls,
  ac.little_millet_lt,
  ac.little_millet_ls,
  ac.foxtail_ls,
  ac.sorghum_ls,
  ac.kodo_ls,
  ac.barnyard_ls,
  ac.pearl_ls,
  ac.fc_area,
  ac.status
FROM soe_districts sd
  LEFT JOIN (SELECT
      *
    FROM vw_area_coverage_districtwise vacd
    WHERE vacd.status = 1";
            if (!empty($filter['start_date'])) {
                $sql .= " AND DATE(vacd.start_date)=date('" . $filter['start_date'] . "')";
            }
            $sql .= ") ac
    ON ac.district_id = sd.id";
            $sql .= " LEFT JOIN vw_districtwise_blocks_gps dbg 
    ON sd.id=dbg.district_id ORDER BY sd.name";
        }
        //        echo $sql;exit;
        return $this->db->query($sql)->getResult();
    }

    public function getAreaCoverageReport($filter = [])
    {

        if (isset($filter['block_id'])) {

            return $this->getByBlock($filter);

        } else if (isset($filter['district_id'])) {

            return $this->getByDistrict($filter);

        } else {

            return $this->getAllDistricts($filter);

        }
    }

    public function getByDistrict($filter = [])
    {

        $sql = "SELECT
  district_id,
  d.name district,
  block_id,
  block,
  vacrd.gps total_gps,
  SUM(farmers_covered) AS farmers_covered,
  SUM(nursery_raised) AS nursery_raised,
  SUM(balance_smi) AS balance_smi,
  SUM(balance_lt) AS balance_lt,
  SUM(fc_area) AS fc_area,
  SUM(ragi_smi) AS ragi_smi,
  SUM(ragi_lt) AS ragi_lt,
  SUM(ragi_ls) AS ragi_ls,
  SUM(little_millet_lt) AS little_millet_lt,
  SUM(little_millet_ls) AS little_millet_ls,
  SUM(foxtail_ls) AS foxtail_ls,
  SUM(sorghum_ls) AS sorghum_ls,
  SUM(kodo_ls) AS kodo_ls,
  SUM(barnyard_ls) AS barnyard_ls,
  SUM(pearl_ls) AS pearl_ls
FROM vw_area_coverage_report_blockwise vacrd LEFT JOIN soe_districts d ON d.id=vacrd.district_id
WHERE (year_id IS NULL";
        if (!empty($filter['year_id'])) {
            $sql .= " OR year_id = " . $filter['year_id'];
        }
        $sql .= " ) AND (season IS NULL";
        if (!empty($filter['season'])) {
            $sql .= " OR LOWER(season) = '" . strtolower($filter['season']) . "'";
        }
        $sql .= ")";
        if (!empty($filter['district_id'])) {
            $sql .= " AND vacrd.district_id=" . $filter['district_id'];
        }
        $sql .= " GROUP BY block_id ORDER BY district_id,block";

        return $this->db->query($sql)->getResult();
    }

    public function getByBlock($filter = [])
    {

        $sql = "SELECT
  block_id,
  gp_id,
  gp,
  SUM(farmers_covered) AS farmers_covered,
  SUM(nursery_raised) AS nursery_raised,
  SUM(balance_smi) AS balance_smi,
  SUM(balance_lt) AS balance_lt,
  SUM(fc_area) AS fc_area,
  SUM(ragi_smi) AS ragi_smi,
  SUM(ragi_lt) AS ragi_lt,
  SUM(ragi_ls) AS ragi_ls,
  SUM(little_millet_lt) AS little_millet_lt,
  SUM(little_millet_ls) AS little_millet_ls,
  SUM(foxtail_ls) AS foxtail_ls,
  SUM(sorghum_ls) AS sorghum_ls,
  SUM(kodo_ls) AS kodo_ls,
  SUM(barnyard_ls) AS barnyard_ls,
  SUM(pearl_ls) AS pearl_ls
FROM vw_area_coverage_report_gpwise vacrd
WHERE (year_id IS NULL";
        if (!empty($filter['year_id'])) {
            $sql .= " OR year_id = " . $filter['year_id'];
        }
        $sql .= " ) AND (season IS NULL";
        if (!empty($filter['season'])) {
            $sql .= " OR LOWER(season) = '" . strtolower($filter['season']) . "'";
        }
        $sql .= ")";
        if (!empty($filter['block_id'])) {
            $sql .= " AND vacrd.block_id=" . $filter['block_id'];
        }
        $sql .= " GROUP BY gp_id ORDER BY gp";

        return $this->db->query($sql)->getResult();
    }

    public function getAllDistricts($filter = [])
    {

        $sql = "SELECT d.id district_id,d.name district,total_blocks,total_gps,start_date,
farmers_covered,nursery_raised,balance_smi,
balance_lt,
fc_area,
ragi_smi,
ragi_lt,
ragi_ls,
little_millet_lt,
little_millet_ls,
foxtail_ls,
sorghum_ls,
kodo_ls,
barnyard_ls,
pearl_ls FROM soe_districts d LEFT JOIN
 (SELECT
  district_id,district,total_blocks,total_gps,start_date,
  SUM(farmers_covered) AS farmers_covered,
SUM(nursery_raised) AS nursery_raised,
SUM(balance_smi) AS balance_smi,
SUM(balance_lt) AS balance_lt,
SUM(fc_area) AS fc_area,
SUM(ragi_smi) AS ragi_smi,
SUM(ragi_lt) AS ragi_lt,
SUM(ragi_ls) AS ragi_ls,
SUM(little_millet_lt) AS little_millet_lt,
SUM(little_millet_ls) AS little_millet_ls,
SUM(foxtail_ls) AS foxtail_ls,
SUM(sorghum_ls) AS sorghum_ls,
SUM(kodo_ls) AS kodo_ls,
SUM(barnyard_ls) AS barnyard_ls,
SUM(pearl_ls) AS pearl_ls
FROM vw_area_coverage_report_districtwise vacrd
WHERE (1=1";
        if (!empty($filter['year_id'])) {
            $sql .= " OR vacrd.year_id = " . $filter['year_id'];
        }
        $sql .= " ) AND (vacrd.season IS NULL";
        if (!empty($filter['season'])) {
            $sql .= " OR LOWER(vacrd.season) = '" . strtolower($filter['season']) . "'";
        }
        $sql .= ")";
        if (!empty($filter['start_date'])) {
            $sql .= " AND DATE(vacrd.start_date) = DATE('" . $filter['start_date'] . "')";
        }
        $sql .= " GROUP BY district_id) res ON d.id=res.district_id ORDER BY d.name";
        //echo $sql;exit;
        return $this->db->query($sql)->getResult();
    }

    public function getPracticeArea($crop_coverage_id = 0)
    {
        $sql = "WITH crop_practice
AS
(SELECT
      crprt.crop_id,
      crprt.crop,
      crprt.practice_id,
      crprt.practice,
      CASE WHEN ISNULL(acp.id) THEN 0 ELSE 1 END status
    FROM (SELECT
        ac.id crop_id,
        ac.crops crop,
        ap.id practice_id,
        ap.name practice
      FROM ac_crops ac
        CROSS JOIN ac_practices ap) crprt
      LEFT JOIN ac_crop_practices acp
        ON acp.crop_id = crprt.crop_id
        AND acp.practice_id = crprt.practice_id),
practice_area
AS
(SELECT
      ac.id crop_id,
      ac.crops,
      aap.smi,
      lt,
      ls
    FROM ac_area_practices aap
      LEFT JOIN ac_crops ac
        ON aap.crop_id = ac.id
    WHERE aap.crop_coverage_id = $crop_coverage_id)
SELECT
  t1.crop_id,
  t1.crop,
  t1.practice_id,
  t1.practice,
  t1.status,
  CASE t1.practice_id WHEN 1 THEN t2.smi WHEN 2 THEN t2.lt WHEN 3 THEN t2.ls ELSE 0.0 END AS area
FROM crop_practice AS t1
  JOIN practice_area AS t2
    ON t1.crop_id = t2.crop_id
ORDER BY t1.crop_id, t1.practice_id";

        return $this->db->query($sql)->getResultArray();
    }

    public function getNursery($crop_coverage_id = 0)
    {
        $b = $this->db->table('ac_nursery');
        return $b->where('crop_coverage_id', $crop_coverage_id)->get()->getFirstRow('array');
    }

    public function getFupCrops($crop_coverage_id = 0)
    {
        $sql = "SELECT
  c.id crop_id,
  afc.area,
  c.crops crop
FROM ac_crops c LEFT JOIN (SELECT * FROM ac_area_follow_up fc 
WHERE fc.crop_coverage_id=" . $crop_coverage_id . ") afc ON c.id=afc.crop_id";

        return $this->db->query($sql)->getResultArray();
    }

    public function setStatus($filter = [], $status = 0, $remarks = '')
    {
        $builder = $this->db->table($this->table);
        $builder->set('status', $status);
        $builder->set('remarks', $remarks);
        $builder->where('block_id', $filter['block_id'])
            ->where('start_date', $filter['start_date'])
            ->update();
    }

    public function getUploadStatus($week_start_date)
    {
        $sql = "SELECT
  vdbg.*,
  COALESCE(ac.total_blocks,0) total_ac_blocks
FROM vw_districtwise_blocks_gps vdbg
  LEFT JOIN (SELECT
      district_id,
      COUNT(DISTINCT acc.block_id) total_blocks
    FROM ac_crop_coverage acc
    WHERE acc.deleted_at IS NULL
    AND acc.status = 1
    AND DATE(acc.start_date) = DATE('$week_start_date')
    GROUP BY acc.district_id) ac
    ON ac.district_id = vdbg.district_id ORDER BY vdbg.district";

        return $this->db->query($sql)->getResult();
    }

    public function deleteAll($filter = null)
    {
        $b = $this->db->table($this->table);
        $ac = $b->where($filter)->get()->getResult();

        foreach ($ac as $item) {
            $b = $this->db->table($this->table);
            $b->where('id', $item->id)->delete();
            $b = $this->db->table('ac_area_follow_up');
            $b->where('crop_coverage_id', $item->id)->delete();
            $b = $this->db->table('ac_area_practices');
            $b->where('crop_coverage_id', $item->id)->delete();
            $b = $this->db->table('ac_nursery');
            $b->where('crop_coverage_id', $item->id)->delete();
        }
    }
    public function getBlockWiseStatus($filter)
    {
        $sql = "SELECT
    sd.name AS district_name,
    sb.name AS block_name,
    COALESCE(acc.status, 3) AS status
FROM
    soe_blocks sb
LEFT JOIN
    ac_crop_coverage acc ON acc.block_id = sb.id 
        AND DATE(acc.start_date) = DATE('" . $filter['start_date'] . "')
        AND acc.deleted_at IS NULL
        AND (acc.status = 0 OR acc.status = 1 OR acc.status = 2)
LEFT JOIN
    soe_districts sd ON sb.district_id = sd.id
WHERE
    1 = 1";

        if (isset($filter['district_id'])) {
            $sql .= " AND sd.id = " . $filter['district_id'];
        }

        if (isset($filter['season'])) {
            $sql .= " AND acc.season = '" . $filter['season'] . "'";
        }

        if (isset($filter['year_id'])) {
            $sql .= " AND acc.year_id = " . $filter['year_id'];
        }

        $sql .= "
GROUP BY sb.id
ORDER BY
    sd.name ASC, sb.name ASC";

        $res = $this->db->query($sql)->getResult();
        return $res;
    }
}