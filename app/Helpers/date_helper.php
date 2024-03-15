<?php

declare(strict_types=1);

use Admin\Common\Models\AllowuploadModel;
use Admin\Common\Models\MonthModel;
use Admin\Common\Models\YearModel;

function ymdToDmy(string $ymd): string
{
    if (empty($ymd) || substr($ymd, 0, 4) === '0000') {
        return '';
    }

    return date('d/m/Y', strtotime($ymd));
}

function dmyToYmd(string $dmy, string $separator = '/'): string
{
    if ($dmy) {
        $dt = DateTime::createFromFormat("d{$separator}m{$separator}Y", $dmy);
        return $dt ? $dt->format('Y-m-d') : '';
    }

    return $dmy;
}

function dobToAge(?string $dob): int
{
    if (!$dob) {
        return 0;
    }

    $from = new DateTime($dob);
    $to = new DateTime('today');

    return $from->diff($to)->y;
}

function my_date_range(string $start_date, ?string $end_date = null): string
{
    if (!$end_date) {
        $end_date = $start_date;
    }

    $start_day = (int) date('d', strtotime($start_date));
    $end_day = (int) date('d', strtotime($end_date));
    $start_month = (int) date('m', strtotime($start_date));
    $end_month = (int) date('m', strtotime($end_date));
    $start_year = (int) date('Y', strtotime($start_date));
    $end_year = (int) date('Y', strtotime($end_date));

    if ($start_year === $end_year) {
        if ($start_month === $end_month) {
            return date("M d, Y", strtotime($start_date)) . ' - ' . date("d, Y", strtotime($end_date));
        }

        return date("M d, Y", strtotime($start_date)) . ' - ' . date("M d, Y", strtotime($end_date));
    }

    return date('M d, Y', strtotime($start_date)) . ' - ' . date('M d, Y', strtotime($end_date));
}

function time_ago(string $date): string
{
    if (empty($date)) {
        return "No date provided";
    }

    $periods = ["second", "minute", "hour", "day", "week", "month", "year", "decade"];
    $lengths = [60, 60, 24, 7, 4.35, 12, 10];
    $now = time();
    $unix_date = strtotime($date);

    if (empty($unix_date)) {
        return "Bad date";
    }

    if ($now > $unix_date) {
        $difference = $now - $unix_date;
        $tense = "ago";
    } else {
        $difference = $unix_date - $now;
        $tense = "from now";
    }

    for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
        $difference /= $lengths[$j];
    }

    $difference = round($difference);

    if ($difference !== 1) {
        $periods[$j] .= "s";
    }

    return "$difference {$periods[$j]} {$tense}";
}

$monthModel = new MonthModel();

function getMonths(): array
{
    global $monthModel;

    try {
        return $monthModel->getMonths()->getResultArray();
    } catch (\Throwable $th) {
        return [];
    }
}

function getMonthById(int $month_id): array
{
    global $monthModel;

    try {
        return $monthModel->getMonthById($month_id)->getRowArray();
    } catch (\Throwable $th) {
        return [];
    }
}

function getMonthIdByMonth(int $month_num): int
{
    global $monthModel;

    try {
        $month = $monthModel->getMonthIdByMonth($month_num);
        return $month ? (int) $month['id'] : 0;
    } catch (\Throwable $th) {
        return 0;
    }
}

function getMonthsUpto(int $upto_month): array
{
    global $monthModel;

    try {
        return $monthModel->getMonthsUpto($upto_month)->getResultArray();
    } catch (\Throwable $th) {
        return [];
    }
}

function previousMonth(int $month): int
{
    global $monthModel;

    try {
        return $monthModel->getPreviousMonth($month);
    } catch (\Throwable $th) {
        return 0;
    }
}

$yearModel = new YearModel();
$allowUpload = new AllowuploadModel();

function getAllYears(bool $asArray = true): array
{
    global $yearModel;

    if ($asArray) {
        return $yearModel->asArray()->findAll();
    }

    return $yearModel->findAll();
}

function getYear
