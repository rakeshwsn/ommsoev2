<?php 
namespace Admin\Reports\Controllers;
use Admin\Common\Models\YearModel;
use Admin\Localisation\Models\BlockModel;
use Admin\Localisation\Models\DistrictModel;
use Admin\Reports\Models\AreaModel;
use Admin\Reports\Models\ReportsModel;
use App\Traits\PreloaderTrait;
use App\Traits\ReportTrait;
use App\Traits\TreeTrait;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Api extends ResourceController
{
    use ResponseTrait,TreeTrait,ReportTrait {
        ReportTrait::getTable insteadof TreeTrait;
        ReportTrait::generateTable insteadof TreeTrait;
    }

    function __construct() {
        helper(['aio','date','general','number']);
    }

    public function mpr() {

        $data = [];

        $filter['year_id'] = getCurrentYearId();
        if($this->request->getGet('year')){
            $filter['year_id'] = $this->request->getGet('year');
        }

        $filter['month_id'] = getCurrentMonthId();
        if($this->request->getGet('month')){
            $filter['month_id'] = $this->request->getGet('month');
        }

        $filter['agency_type_id'] = '';
//        if($this->request->getGet('agency_type_id')){
//            $filter['agency_type_id'] = $this->request->getGet('agency_type_id');
//        }

        $filter['fund_agency_id'] = 1;
        $filter['block_users'] = [5,6];
        $filter['block_user'] = false;

        if($this->request->getGet('fund_agency_id')){
            $filter['fund_agency_id'] = $this->request->getGet('fund_agency_id');
        }

        $filter['district_id'] = '';
        if($this->request->getGet('district_id')){
            $filter['district_id'] = $this->request->getGet('district_id');
        }

        $filter['block_id'] = '';
        if($this->request->getGet('block_id')){
            $filter['block_id'] = $this->request->getGet('block_id');
        }

        $reportModel = new ReportsModel();
        $data['components'] = [];

        $components = $reportModel->getMpr($filter);

        $components = $this->buildTree($components, 'parent', 'component_id');

//        $components = $this->getTable($components,'array');

        $data['components'] = $components;

        return $this->respond($data);
    }

    public function mprFilters() {
        $data['districts'] = (new DistrictModel())->getDistrictsByFundAgency(1);
        $data['months'] = getAllMonths();
        $data['years'] = getAllYears();
        $data['seasons'] = getSeasons();

        return $this->respond($data);
    }

    public function getBlocks() {
        $district_id = 0;
        if($this->request->getGet('district_id')){
            $district_id = $this->request->getGet('district_id');
        }
        $data['blocks'] = (new BlockModel())->getBlocksByDistrict($district_id);

        return $this->respond($data);
    }

    public function getAreaCoverage() {
        $areaModel = new AreaModel();
        $yearModel = new YearModel();

        $filter['year'] = $areaModel->getYearByDate(date('Y-m-d'))->id;
        if($this->request->getGet('year')){
            $year = $this->request->getGet('year');
            $year_sdate = $yearModel->find($year)->start_date;
            $filter['year'] = $areaModel->getYearByDate($year_sdate)->id;
        }

        $filter['season'] = $areaModel->getCurrentSeason();
        if($this->request->getGet('season')){
            $filter['season'] = $this->request->getGet('season');
        }

        $filter['district_id'] = '';
        if($this->request->getGet('district_id')){
            $filter['district_id'] = $this->request->getGet('district_id');
        }

        $filter['block_id'] = '';
        if($this->request->getGet('block_id')){
            $filter['block_id'] = $this->request->getGet('block_id');
        }

        $data = $areaModel->getAreaCoverage($filter);

        return $this->respond($data);
    }
}
