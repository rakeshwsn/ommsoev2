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

    public function getWeeks()
    {
        $builder = $this->db->table('ac_crop_weeks');
        $seasons = $builder->get()->getResult();

        $season_data = [];
        foreach ($seasons as $season) {
            $season_data[] = [
                'id' => $season->id,
                'year' => $season->year,
                'season' => $season->season,
                'start_date' => $season->start_date,

                'end_date' => $season->end_date,

            ];
        }
        return $season_data;

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

    public function getCurrentYearDates()
    {

        $this->settings = new \Config\Settings();

        $kharif_start_month = getMonthById((int)$this->settings->kharif_start_month);
        $kharif_end_month = getMonthById((int)$this->settings->kharif_end_month);

        $rabi_start_month = getMonthById((int)$this->settings->rabi_start_month);
        $rabi_end_month = getMonthById((int)$this->settings->rabi_end_month);

        $given_date = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'));

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
            $season_end_date = \DateTime::createFromFormat('Y-m-d', $given_date->format('Y') . '-' . $rabi_end_month_number . '-01');
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

    public function getAreaCoverage($filter = [])
    {
        if (!empty($filter['block_id'])) {
            $sql = "SELECT cc.*,gp.name gp FROM vw_area_coverage_gpwise cc 
                    LEFT JOIN soe_grampanchayats gp ON cc.gp_id=gp.id 
                    WHERE cc.block_id=" . $filter['block_id'] .
                ' AND cc.year_id=' . $filter['year_id'] .
                " AND cc.season='" . $filter['season']."'";

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
}
