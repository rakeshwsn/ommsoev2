<?php

namespace Admin\Enterprises\Controllers;

use Admin\Enterprises\Models\BlockModel;
use Admin\Enterprises\Models\DistrictModel;
use Admin\Enterprises\Models\EnterprisesBudgetModel;
use Admin\Enterprises\Models\EnterprisesModel;
use Admin\Enterprises\Models\EnterprisesUnitModel;
use Admin\Enterprises\Models\GpModel;
use Admin\Enterprises\Models\VillagesModel;
use Admin\Enterprises\Models\YearModel;
use App\Controllers\AdminController;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



class DistWiseThr extends AdminController
{
	public function index()
	{
		$this->template->add_package(array('datatable', 'select2', 'uploader', 'jquery_loading'), true);
		$this->template->set('header', true);
		helper('form');
		
		return $this->template->view('Admin\Enterprises\Views\distwisethresher');
	}
	public function download_thr(){
		return $this->template->view('Admin\Enterprises\Views\threxcelform');

	}


}
