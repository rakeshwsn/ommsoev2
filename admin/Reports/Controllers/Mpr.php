<?php

namespace Admin\Reports\Controllers;

use App\Controllers\AdminController;
use App\Libraries\Export;
use App\Traits\SomeTrait;

class ReportController extends AdminController
{
    use SomeTrait;

    public function exportReport()
    {
        // Your code here

        $export = new Export();
        $export->exportData($data);
    }
}
