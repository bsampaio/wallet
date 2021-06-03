<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 03/06/2021
 * Time: 16:48
 */

namespace App\Utils;


class Date
{
    const BRAZILIAN_DATE = 'd/m/Y';
    const BRAZILIAN_DATETIME = self::BRAZILIAN_DATE . ' H:i:s';
    const COMPETENCE = 'Y-m';
    const UTC_DATE = 'Y-m-d';
    const UTC_DATETIME = self::UTC_DATE . 'H:i:s';

    public static function getMonthName($month)
    {
        switch ($month) {
            case 1:
                return "Janeiro";
                break;
            case 2:
                return "Fevereiro";
                break;
            case 3:
                return "Março";
                break;
            case 4:
                return "Abril";
                break;
            case 5:
                return "Maio";
                break;
            case 6:
                return "Junho";
                break;
            case 7:
                return "Julho";
                break;
            case 8:
                return "Agosto";
                break;
            case 9:
                return "Setembro";
                break;
            case 10:
                return "Outubro";
                break;
            case 11:
                return "Novembro";
                break;
            case 12:
                return "Dezembro";
                break;
        }
    }

    public static function getWeekName($month)
    {
        switch ($month) {
            case 0:
                return "Domingo";
                break;
            case 1:
                return "Segunda";
                break;
            case 2:
                return "Terça";
                break;
            case 3:
                return "Quarta";
                break;
            case 4:
                return "Quinta";
                break;
            case 5:
                return "Sexta";
                break;
            case 6:
                return "Sábado";
                break;
        }
    }

    public static function secondsToFormattedHours($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;
        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }

    public static function dateToBrazilianDate($date, $format = 'Y-m-d', $ifEmpty = '')
    {
        if(empty($date)) {
            return '';
        }

        return Carbon::createFromFormat($format, $date)->format(self::BRAZILIAN_DATE);
    }
}