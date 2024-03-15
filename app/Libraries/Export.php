<?php
namespace App\Libraries;

use Config\ExcelStyles;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Export
{
    public static function createExcelFromHTML($html,$filename,$return = false){

        $reader = new Html();

        $html = preg_replace("/&(?!\S+;)/", "&amp;", $html);

        $spreadsheet = $reader->loadFromString($html);
        $spreadsheet->setActiveSheetIndex(0);
        $worksheet = $spreadsheet->getActiveSheet();

        // Load HTML content into a DOM object
        $table = new \DOMDocument();
        $table->loadHTML($html);

        $rows = $table->getElementsByTagName('tr');

        foreach ($worksheet->getRowIterator() as $row) {
            // Find the corresponding row element in the HTML table
            $rowIndex = $row->getRowIndex();

            $rowElement = $rows->item($rowIndex - 1); // -1 because row indices start at 1 in PhpSpreadsheet

            // Get the class name of the row element
            $className = $rowElement->getAttribute('class');

            // Check if the class name matches a highlight class from the HTML table
            if (preg_match('/highlight-(\w+)/', $className, $matches)) {
                $highlightClass = $matches[1];

                // Set the fill color based on the highlight class
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
                    $lastColumnIndex = $worksheet->getHighestColumn();
                    $range = 'A' . $rowIndex . ':' . $lastColumnIndex . $rowIndex;
                    $worksheet->getStyle($range)->applyFromArray($fillColor);

                }
            }
        }

        // Set auto-size column widths for all columns
        foreach ($spreadsheet->getActiveSheet()->getColumnIterator() as $column) {
            $spreadsheet->getActiveSheet()
                ->getColumnDimension($column->getColumnIndex())
                ->setAutoSize(false);
        }

    
        if($return){
            return $spreadsheet;
        }else{
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'. $filename .'"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
        }
    }

    public static function createExcelFromHTML2(String $html,$title){

        
    
        return $worksheet;
        
    }
}
