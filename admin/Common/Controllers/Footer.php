<?php

namespace App\Controllers\Admin\Common;

use App\Controllers\AdminController;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;

class FooterController extends AdminController
{
    public function index()
    {
        $data = [
            'show_old_portal' => true,
        ];

        if ($this->user->isLogged()) {
            $blockModel = new BlockModel();
            $districtModel = new DistrictModel();

            $newBlocks = $blockModel->asArray()->where(['year' => 6])->findAll();
            $newDistricts = $districtModel->getNewDistricts();

            $userDistrictId = $this->user->district_id ?? null;
            $userBlockId = $this->user->block_id ?? null;

            if ($userDistrictId && in_array($userDistrictId, array_column($newDistricts, 'district_id'))) {
                $data['show_old_portal'] = false;
            }

            if ($userBlockId && in_array($userBlockId, array_column($newBlocks, 'id'))) {
                $data['show_old_portal'] = false;
            }
        }

        return view('Admin\Common\Views\footer', $data);
    }
}
