<?php
namespace Admin\CropCoverage\Controllers;

use Admin\CropCoverage\Models\TargetModel;
use App\Controllers\AdminController;

class AreaCoverageDashboard extends AdminController
{
    private $data;
    private $error = array();
    private $targetModel;
    function __construct()
    {
        $this->targetModel = new TargetModel();

    }
    public function index()
    {
        $data['chart_url'] = admin_url('areacoverage/dashboard/chart');
        $data['milletchart_url'] = admin_url('areacoverage/dashboard/milletchart');

        $data['years'] = getAllYears();

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

        return $this->template->view('Admin\CropCoverage\Views\areacoverage_dashboard', $data);
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


    public function milletChart()
    {

        $year_id = $this->request->getGet('year_id');


        $season = $this->request->getGet('season');

        $filter = [
            'year_id' => $year_id,
            'season' => $season
        ];

        $milletstarget = $this->targetModel->getMilletWiseTarget($filter);
        // printr($distwisetarget);
        // exit;
        $millets = $series_target = $series_achievement = [];
        foreach ($milletstarget as $millettarget) {
            $millets[] = $millettarget['crop'];
            $series_target[] = $millettarget['target_area'];
            $series_achievement[] = $millettarget['achievement_area'];
        }
        $data['xaxis'] = $millets;


        /*$data['series'] = [
            ['name' => 'Millet Target', 'data' => $series_target],
            ['name' => 'Millet Achievement', 'data' => $series_achievement]
        ];*/
        $data['series_target'] = $series_target;
        $data['series_achievement'] = $series_achievement;

        header('content-type:application/json');
        echo json_encode($data, JSON_NUMERIC_CHECK);

    }

}