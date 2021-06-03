<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 03/06/2021
 * Time: 18:00
 */

namespace App\Utils;


class Number
{
    public static function money($input, $currency = 'BRL', $prefix = true)
    {
        if($prefix) {
            $p = self::getCurrencyPrefix($currency) . " ";
        }
        return $p . number_format($input, 2, ",", ".");
    }

    public static function moneyReverse($input)
    {
        $input = str_replace("R$ ", '', $input);
        $input = str_replace(".", '', $input);
        $input = str_replace(",", '.', $input);
        $input = number_format($input, 2, ".", "");

        return floatval($input);
    }

    public static function decimal($input, $decimals = 2)
    {
        return number_format($input, $decimals, ",", ".");
    }

    public static function getCurrencyPrefix($currency = 'BRL')
    {
        $symbols = [
            "ALL" => "Lek",
            "AFN" => "؋",
            "ARS" => "$",
            "AWG" => "ƒ",
            "AUD" => "$",
            "AZN" => "₼",
            "BSD" => "$",
            "BBD" => "$",
            "BYN" => "Br",
            "BZD" => "BZ$",
            "BMD" => "$",
            "BOB" => "\$b",
            "BAM" => "KM",
            "BWP" => "P",
            "BGN" => "лв",
            "BRL" => "R$",
            "BND" => "$",
            "KHR" => "៛",
            "CAD" => "$",
            "KYD" => "$",
            "CLP" => "$",
            "CNY" => "¥",
            "COP" => "$",
            "CRC" => "₡",
            "HRK" => "kn",
            "CUP" => "₱",
            "CZK" => "Kč",
            "DKK" => "kr",
            "DOP" => "RD$",
            "XCD" => "$",
            "EGP" => "£",
            "SVC" => "$",
            "EUR" => "€",
            "FKP" => "£",
            "FJD" => "$",
            "GHS" => "¢",
            "GIP" => "£",
            "GTQ" => "Q",
            "GGP" => "£",
            "GYD" => "$",
            "HNL" => "L",
            "HKD" => "$",
            "HUF" => "Ft",
            "ISK" => "kr",
            "INR" => "",
            "IDR" => "Rp",
            "IRR" => "",
            "IMP" => "£",
            "ILS" => "₪",
            "JMD" => "J$",
            "JPY" => "¥",
            "JEP" => "£",
            "KZT" => "лв",
            "KPW" => "₩",
            "KRW" => "₩",
            "KGS" => "лв",
            "LAK" => "₭",
            "LBP" => "£",
            "LRD" => "$",
            "MKD" => "ден",
            "MYR" => "RM",
            "MUR" => "₨",
            "MXN" => "$",
            "MNT" => "₮",
            "MZN" => "MT",
            "NAD" => "$",
            "NPR" => "₨",
            "ANG" => "ƒ",
            "NZD" => "$",
            "NIO" => "C$",
            "NGN" => "₦",
            "NOK" => "kr",
            "OMR" => "",
            "PKR" => "₨",
            "PAB" => "B/.",
            "PYG" => "Gs",
            "PEN" => "S/.",
            "PHP" => "₱",
            "PLN" => "zł",
            "QAR" => "",
            "RON" => "lei",
            "RUB" => "₽",
            "SHP" => "£",
            "SAR" => "",
            "RSD" => "Дин.",
            "SCR" => "₨",
            "SGD" => "$",
            "SBD" => "$",
            "SOS" => "S",
            "ZAR" => "R",
            "LKR" => "₨",
            "SEK" => "kr",
            "CHF" => "CHF",
            "SRD" => "$",
            "SYP" => "£",
            "TWD" => "NT$",
            "THB" => "฿",
            "TTD" => "TT$",
            "TRY" => "",
            "TVD" => "$",
            "UAH" => "₴",
            "GBP" => "£",
            "USD" => "$",
            "UYU" => "\$U",
            "UZS" => "лв",
            "VEF" => "Bs",
            "VND" => "₫",
            "YER" => "",
            "ZWD" => "Z$"
        ];

        if(!isset($symbols[$currency])) {
            return "";
        }

        return $symbols[$currency];
    }
}