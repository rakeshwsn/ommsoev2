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

    public function GetSeasons()
    {

        $builder = $this->db->table("ac_seasons a");
        $builder->join('years y', 'a.year = y.id', 'left');
        $builder->join('district d', 'a.districts = d.id', 'left');

        $seasons = $builder->get()->getResult();
        // print_r($seasons);
        // exit;
        $seasons_data = [];
        foreach ($seasons as $season) {
            $seasons_data[] = [
                'year' => $season->year,
                'season' => $season->season,
                'name' => $season->name,
                'week' => $season->week,
                'date_added' => $season->date_added,

            ];
            return $seasons_data;
        }
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
    ON acp.practice_id = ap.id ORDER BY acp.crop_id";

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
        $builder = $this->db->table('ac_followup_crop');
        $builder->insertBatch($fCrop);
    }

    public function deleteNursery($crop_coverage_id)
    {
        $builder = $this->db->table('ac_nursery');
        $builder->where('crop_coverage_id',$crop_coverage_id)->delete();
    }

    public function deleteArea($crop_coverage_id)
    {
        $builder = $this->db->table('ac_area_practices');
        $builder->where('crop_coverage_id',$crop_coverage_id)->delete();
    }

    public function deleteFupCrops($crop_coverage_id)
    {
        $builder = $this->db->table('ac_followup_crop');
        $builder->where('crop_coverage_id',$crop_coverage_id)->delete();
    }

    public function getCurrentYearDates($date=null)
    {

        $this->settings = new \Config\Settings();

        $kharif_start_month = getMonthById((int)$this->settings->kharif_start_month);
        $kharif_end_month = getMonthById((int)$this->settings->kharif_end_month);

        $rabi_start_month = getMonthById((int)$this->settings->rabi_start_month);
        $rabi_end_month = getMonthById((int)$this->settings->rabi_end_month);

        $date = $date ?: date('Y-m-d');
        $given_date = \DateTime::createFromFormat('Y-m-d', $date);

        $kharif_start_month_number = $kharif_start_month['number'];
        $kharif_end_month_number = $kharif_end_month['number'];
        $rabi_start_month_number = $rabi_start_month['number'];
        $rabi_end_month_number = $rabi_end_month['number'];
        if ($given_date->format('n') >= $kharif_start_month_number
            && $given_date->format('n') <= $kharif_end_month_number
        ) {
            $current_season = 'Kharif';
            $season_start_date = \DateTime::createFromFormat('Y-m-d', $given_date->format('Y') . '-' . $kharif_start_month_number . '-01');
            $season_end_date = \DateTime::createFromFormat('Y-m-d', $given_date->format('Y') . '-' . $kharif_end_month_number . '-30');
        } elseif ($given_date->format('n') >= $rabi_start_month_number
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

    public function getWeeks(){

        if(isset($date)){
            $dates = $this->getCurrentYearDates($date);
        } else {
            $dates = $this->getCurrentYearDates();
        }

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

    public function getWeekDate($date = 'today') {
        $output = $this->getWeeks();

        foreach ($output as $dates) {
            $today = strtotime($date);
            if($today >= strtotime($dates['start_date']) && $today <= strtotime($dates['end_date'])){
                return $dates;
            }
        }
    }

    public function getAreaCoverage($filter = []) {
        if (!empty($filter['block_id'])) {
            $sql = "SELECT cc.*,gp.name gp FROM vw_area_coverage_gpwise cc 
                    LEFT JOIN soe_grampanchayats gp ON cc.gp_id=gp.id 
                    WHERE cc.block_id=" . $filter['block_id'];
            if(!empty($filter['year_id'])){
                $sql .= " AND cc.year_id=" . $filter['year_id'];
            }
            if(!empty($filter['season'])){
                $sql .= " AND cc.season='" . $filter['season']."'";
            }
            if(!empty($filter['start_date'])){
                $sql .= " AND DATE(cc.start_date)=date('" . $filter['start_date']."')";
            }

            return $this->db->query($sql)->getResult();
        }

        if (!empty($filter['district_id'])) {
            $sql = "SELECT ac.*,
  b.name block,bgps.gps FROM soe_blocks b 
  LEFT JOIN (SELECT * FROM vw_blockwise_gps) bgps ON bgps.block_id=b.id
  LEFT JOIN (SELECT * FROM vw_area_coverage_blockwise cc 
                    WHERE cc.year_id=" . $filter['year_id'] .
                " AND cc.season='" . $filter['season']."') ac ON ac.block_id=b.id 
                WHERE b.district_id=".$filter['district_id']." ORDER BY date(ac.start_date) DESC,b.name ASC";

            return $this->db->query($sql)->getResult();
        }
    }

    public function getPracticeArea($crop_coverage_id=0) {
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

    public function getNursery($crop_coverage_id=0){
        $b = $this->db->table('ac_nursery');
        return $b->where('crop_coverage_id',$crop_coverage_id)->get()->getFirstRow('array');
    }

    public function getFupCrops($crop_coverage_id=0){
        $sql = "SELECT
  afc.crop_id,
  afc.area,
  ac.crops crop
FROM ac_followup_crop afc
  LEFT JOIN ac_crops ac
    ON afc.crop_id = ac.id WHERE afc.crop_coverage_id=".$crop_coverage_id;

        return $this->db->query($sql)->getResultArray();
    }

    public function setStatus($filter = [],$status=0,$remarks='') {
        $builder = $this->db->table($this->table);
        $builder->set('status',$status);
        $builder->set('remarks',$remarks);
        $builder->where('block_id',$filter['block_id'])
            ->where('start_date',$filter['start_date'])
            ->update();
    }
}
