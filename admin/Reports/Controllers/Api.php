<?php

namespace Admin\Reports\Controllers;

use Admin\Common\Models\YearModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Reports\Models\AreaModel;
use Admin\Reports\Models\ReportsModel;
use CodeIgniter\API\ResponseTrait;

class Api extends \CodeIgniter\RESTful\ResourceController
{
    use ResponseTrait;

    public function __construct()
    {
        helper(['aio', 'date', 'general', 'number']);
    }

    public function mpr()
    {
        $rules = [
            'year' => 'permit_optional|is_natural_no_zero',
            'month' => 'permit_optional|is_natural_no_zero',
            'agency_type_id' => 'permit_optional|is_natural_no_zero',
            'fund_agency_id' => 'permit_optional|is_natural_no_zero',
            'district_id' => 'permit_optional|is_natural_no_zero',
            'block_id' => 'permit_optional|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $filter = [
            'year_id' => getCurrentYearId(),
            'month_id' => getCurrentMonthId(),
            'agency_type_id' => '',
            'fund_agency_id' => 1,
            'block_users' => [5, 6],
            'block_user' => false,
        ];

        if ($this->request->getGet('year')) {
            $filter['year_id'] = $this->request->getGet('year');
        }

        if ($this->request->getGet('month')) {
            $filter['month_id'] = $this->request->getGet('month');
        }

        if ($this->request->getGet('agency_type_id')) {
            $filter['agency_type_id'] = $this->request->getGet('agency_type_id');
        }

        if ($this->request->getGet('fund_agency_id')) {
            $filter['fund_agency_id'] = $this->request->getGet('fund_agency_id');
        }

        if ($this->request->getGet('district_id')) {
            $filter['district_id'] = $this->request->getGet('district_id');
        }

        if ($this->request->getGet('block_id')) {
            $filter['block_id'] = $this->request->getGet('block_id');
        }

        $reportModel = new ReportsModel();
        $components = $reportModel->getMpr($filter);

        $components = $this->buildTree($components, 'parent', 'scomponent_id');

        return $this->respond($components);
    }

    public function mprFilters()
    {
        $data['districts'] = (new DistrictModel())->getDistrictsByFundAgency(1);
        $data['months'] = getAllMonths();
        $data['years'] = getAllYears();
        $data['seasons'] = getSeasons();

        return $this->respond($data);
    }

    public function getBlocks()
    {
        $district_id = $this->request->getGet('district_id');
        $data['blocks'] = (new BlockModel())->getBlocksByDistrict($district_id);

        return $this->respond($data);
    }

    public function getAreaCoverage()
    {
        $areaModel = new AreaModel();
        $yearModel = new YearModel();

        $filter = [
            'year' => $areaModel->getYearByDate(date('Y-m-d'))->id,
            'season' => $areaModel->getCurrentSeason(),
            'district_id' => '',
            'block_id' => '',
        ];

        if ($this->request->getGet('year')) {
            $year = $this->request->getGet('year');
            $year_sdate = $yearModel->find($year)->start_date;
            $filter['year'] = $areaModel->getYearByDate($year_sdate)->id;
        }

        if ($this->request->getGet('season')) {
            $filter['season'] = $this->request->getGet('season');
        }

        if ($this->request->getGet('district_id')) {
            $filter['district_id'] = $this->request->getGet('district_id');
        }

        if ($this->request->getGet('block_id')) {
            $filter['block_id'] = $this->request->getGet('block_id');
        }

        $data = $areaModel->getAreaCoverage($filter);

        return $this->respond($data);
    }
}
