<?php
namespace Admin\CropCoverage\Controllers;

use App\Controllers\AdminController;
use Admin\CropCoverage\Models\TargetModel;


class TargetVsAchievement extends AdminController
{
    private $targetModel;
    function __construct()
    {
        $this->targetModel = new TargetModel();

    }
    public function index()
    {

        $this->template->set_meta_title(lang('TargetVsAchievement'));
        $data['years'] = (getAllYears());

        $seasons = array(
            array(
                'id' => '1',
                'name' => 'Rabi'
            ),
            array(
                'id' => '2',
                'name' => 'Kharif'
            )
        );
        $data['seasons'] = $seasons;


        return $this->getChart();
    }
    public function getChart()
    {
        $data['years'] = getAllYears();

        $seasons = [
            ['id' => '1', 'name' => 'Rabi'],
            ['id' => '2', 'name' => 'Kharif']
        ];
        $data['seasons'] = $seasons;

        $view = '';

        if ($this->user->block_id) {
            $data['milletchart_url'] = admin_url('areacoverage/targetVsAchievement/milletchart');

            $view = 'target_vs_achievement_block';

        } elseif ($this->user->district_id) {
            $data['chart_url'] = admin_url('areacoverage/targetVsAchievement/chart');
            $filter = [
                'district_id' => $this->user->district_id,
                'year_id' => getCurrentYearId(),
                'season' => getCurrentSeason()
            ];

            // Fetch district target vs. achievement data
            $data['districtdata'] = $this->targetModel->getDistrictWiseTarget($filter);

            $view = 'target_vs_achievement_district';
        }

        return $this->template->view('Admin\CropCoverage\Views\\' . $view, $data);
    }
    public function milletChart()
    {
        $data['milletchart_url'] = admin_url('areacoverage/targetVsAchievement/milletchart');
        $filter = [
            'block_id' => $this->user->block_id,
            'year_id' => getCurrentYearId(),
            'season' => getCurrentSeason()
        ];

        // Fetch millets target data
        $milletstarget = $this->targetModel->getMilletWiseTarget($filter);

        if (!empty($milletstarget)) {
            $millets = $series_target = $series_achievement = [];

            foreach ($milletstarget as $millettarget) {
                $millets[] = $millettarget['crop'];
                $series_target[] = (int) $millettarget['target_area'];

                $series_achievement[] = (int) $millettarget['achievement_area']; // Ensure values are cast to integers
            }

            $data['xaxis'] = $millets;

            $data['series_target'] = $series_target;

            $data['series_achievement'] = $series_achievement;

            header('Content-Type: application/json');
            echo json_encode($data, JSON_NUMERIC_CHECK);


        }
    }
    public function chart()
    {
        $data = [];
        $year_id = $this->request->getGet('year_id');

        // echo $year_id;
        // exit;
        $season = $this->request->getGet('season');

        $filter = [
            'year_id' => $year_id,
            'season' => $season
        ];

        $distwisetarget = $this->targetModel->getDistrictWiseTarget($filter);
        $districts = $series_target = $series_achievement = [];

        foreach ($distwisetarget as $disttarget) {
            $districts[] = $disttarget['district'];
            $series_target[] = $disttarget['target_area'];
            $series_achievement[] = $disttarget['ach_area'];
        }

        $data['xaxis'] = $districts;
        $data['series_target'] = $series_target;
        $data['series_achievement'] = $series_achievement;

        header('content-type:application/json');
        echo json_encode($data, JSON_NUMERIC_CHECK);
    }

}
?>