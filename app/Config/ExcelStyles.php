<?php

namespace Config;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExcelStyles {

    protected static $style = [
        'heading1'=>[
            'font' => [
                'bold' => true,
                'size' => 12,
                'color'=>['rgb' => 'ffffff'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '2C3B49'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ],
        'heading2'=>[
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'C0C0C0'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ],
        'heading3'=>[
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'C0C0C0'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ],
        'border' => [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ],
        'fill_yellow' => [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFF00',
                ]
            ],
        ],
        'fill_grey' => [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFA9A9A9',
                ]
            ],
        ],
        'fill_blue' => [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF2C3B49',
                ]
            ],
            'font' => [
                'color' => ['argb' => 'FFFFFFFF'],
            ],
        ],
    ];

    public static function heading1(){
        return self::$style['heading1'];
    }

    public static function heading2(){
        return self::$style['heading2'];
    }

    public static function heading3(){
        return self::$style['heading3'];
    }

    public static function border(){
        return self::$style['border'];
    }

    public static function fill_yellow(){
        return self::$style['fill_yellow'];
    }

    public static function fill_grey(){
        return self::$style['fill_grey'];
    }

    public static function fill_blue(){
        return self::$style['fill_blue'];
    }
}
