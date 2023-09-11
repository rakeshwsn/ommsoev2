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

        $data['milletchart_url'] = admin_url('areacoverage/targetVsAchievement/milletchart');
        $data['distchart_url'] = admin_url('areacoverage/targetVsAchievement/distChart');
        $data['block_id'] = $this->user->block_id;
        $data['district_id'] = $this->user->district_id;

        return $this->template->view('Admin\CropCoverage\Views\target_vs_achievement', $data);
    }

    public function milletChart()
    {
        $filter = [
            'block_id' => $this->user->block_id,
            'year_id' => getCurrentYearId(),
            'season' => getCurrentSeason()
        ];

        $milletstarget = $this->targetModel->getMilletWiseTarget($filter);

        if (!empty($milletstarget)) {
            $millets = $series_target = $series_achievement = [];

            foreach ($milletstarget as $millettarget) {
                $millets[] = $millettarget['crop'];
                $series_target[] = (int) $millettarget['target_area'];

                $series_achievement[] = (int) $millettarget['achievement_area'];
            }

            $data['xaxis'] = $millets;

            $data['series_target'] = $series_target;

            $data['series_achievement'] = $series_achievement;

            header('Content-Type: application/json');
            echo json_encode($data, JSON_NUMERIC_CHECK);

        }
    }
    public function distTarVsAchChart()
    {

        $filter = [
            'district_id' => $this->user->district_id,
            'year_id' => getCurrentYearId(),
            'season' => getCurrentSeason()
        ];

        $blockwisetarvsach = $this->targetModel->getDistTargetVsAchievement($filter);
        if (!empty($blockwisetarvsach)) {
            $blocks = $series_target = $series_achievement = [];

            foreach ($blockwisetarvsach as $blocktarvsach) {
                $blocks[] = $blocktarvsach['block'];
                $series_target[] = (int) $blocktarvsach['target_area'];
                $series_achievement[] = (int) $blocktarvsach['ach_area'];
            }

            $data['xaxis'] = $blocks;
            $data['series_target'] = $series_target;
            $data['series_achievement'] = $series_achievement;

            header('content-type:application/json');
            echo json_encode($data, JSON_NUMERIC_CHECK);
        }
    }

}
?>