<?php

namespace App\Traits;

use Admin\Reports\Models\ReportsModel;
use Config\ExcelStyles;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

trait ReportTrait
{
    /**
     * @var int
     */
    private $totBudPhy = 0;

    /**
     * @var int
     */
    private $totBudFin = 0;

    // ... other total variables

    /**
     * Generate the table HTML for the given array and action.
     *
     * @param array $array
     * @param string $action
     * @return string
     */
    protected function getTable(array $array, string $action): string
    {
        $this->initVars();

        $html = $this->generateTable($array, $action);

        if ($action === 'view' || $action === 'download') {
            $html .= $this->generateGrandTotal($action);
        }

        return $html;
    }

    /**
     * Generate the table rows for the given array and action.
     *
     * @param array $array
     * @param string $action
     * @return string
     */
    private function generateTable(array $array, string $action): string
    {
        $html = '';

        foreach ($array as $item) {
            if ($item['row_type'] === 'heading' && !isset($item['children'])) {
                continue;
            }

            if ($item['row_type'] !== 'heading') {
                $this->calcTotals($item);
            }

            if (isset($item['children']) && $item['children']) {
                $html .= $this->addSubtotal($item['children'], true);
            }

            if (!isset($item['children'])) {
                $html .= $this->generateRow($item, $action);
            }
        }

        return $html;
    }

    /**
     * Generate the table row HTML for the given item and action.
     *
     * @param array $item
     * @param string $action
     * @return string
     */
    private function generateRow(array $item, string $action): string
    {
        // Generate the table row HTML based on the item and action
    }

    /**
     * Generate the grand total row HTML for the given action.
     *
     * @param string $action
     * @return string
     */
    private function generateGrandTotal(string $action): string
    {
        // Generate the grand total row HTML based on the action
    }

    // ... other methods
}
