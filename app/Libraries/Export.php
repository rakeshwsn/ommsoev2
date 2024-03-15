<?php

namespace App\Libraries;

use Config\ExcelStyles;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use DOMDocument;

class Export
{
    public static function createExcelFromHTML($html, $filename, $return = false)
    {
        $reader = new Html();
        $html = preg_replace("/&(?!\S+;)/", "&amp;", $html);
        $spreadsheet = $reader->loadFromString($html);
        $spreadsheet->setActiveSheetIndex(0);

        $table = new DOMDocument();
        @$table->loadHTML($html);
        $rows = $table->getElementsByTagName('tr');

        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex < 2) { // Skip the first two rows (header and subheader)
                continue;
            }

            $className = $row->getAttribute('class');
            if (preg_match('/highlight-(\w+)/', $className, $matches)) {
                $highlightClass = $matches[1];

                $fillColor = null;
                switch ($highlightClass) {
                    case 'heading1':
                        $fillColor = ExcelStyles::heading1();
                        break;
                    case 'heading2':
                        $fillColor = ExcelStyles::heading2();
                        break;
                    case 'heading3':
                        $fillColor = ExcelStyles::heading3();
                        break;
                    case 'heading4':
                        $fillColor = ExcelStyles::fill_yellow();
                        break;
                }

                if ($fillColor) {
                    $cellRange = 'A' . ($rowIndex + 1) . ':' . $worksheet->getHighestColumn() . ($rowIndex + 1);
                    $worksheet->getStyle($cellRange)->applyFromArray($fillColor);
                }
            }
        }

        // Set auto-size column widths for all columns
        foreach ($worksheet->getColumnIterator() as $column) {
            $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        // Set page orientation and margins
        $worksheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $worksheet->getPageMargins()->setTop(0.75);
        $worksheet->getPageMargins()->setBottom(0.75);
        $worksheet->getPageMargins()->setLeft(0.75);
        $worksheet->getPageMargins()->setRight(0.75);

        if ($return) {
            return $spreadsheet;
        } else {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit();
        }
    }
}
