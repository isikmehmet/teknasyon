<?php

namespace System;

class Helper{

    private static function crypto_rand_secure($min = 0, $max)
    {
        $range = $max - $min;
        if ($range < 1)
            return $min;

        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1;
        $bits = (int) $log + 1;
        $filter = (int) (1 << $bits) - 1;

        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter;
        }
        while ($rnd > $range);

        return ($min + $rnd);
    }

    public function CreateToken($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[self::crypto_rand_secure(0, $max-1)];
        }

        return $token;
    }

    public function CreateFilter($filter = NULL)
    {
        $response = [];
        $filter_arr = [];
        $limit = "";
        $bind = [];

        if (isset($filter) && !empty($filter))
        {
            foreach ($filter as $key => $value) {
                if ($key == 'where')
                {
                    foreach ($value as $column => $val) {
                        $equal = '=';

                        if (strpos($column, ' '))
                        {
                            $equal = '';
                        }

                        $bind_str =  self::Perma($column);
                        $filter_arr[] = $column . ' ' . $equal . ' :' . $bind_str . ':';
                        $bind[$bind_str] = $val;
                    }
                }
                else if ($key == 'where_in')
                {
                    foreach ($value as $column => $val) {
                        $pfx = 'in_';
                        $in = [];
                        foreach ($val as $index => $item) {
                            $bind_str = $pfx .  self::Perma($item);
                            if (!array_key_exists($bind_str, $bind))
                            {
                                $in[] = ':' . $bind_str . ':';
                                $bind[$bind_str] = $item;
                            }
                        }

                        if (isset($in) && !empty($in) && count($in) > 0)
                        {
                            $filter_arr[] = $column . ' in(' . implode(',', $in) . ')';
                        }
                    }
                }
                else if ($key == 'limit')
                {
                    $limit = " limit " . $value['offset'] . ', ' . $value['limit'];
                }
                // join, group, having, order buradan genişletilebilir.
            }

            $query_string = implode(' and ', $filter_arr) . $limit;
            $response = [
                $query_string,
                'bind' => $bind,
            ];
        }

        return $response;
    }

    private static function Perma($str = NULL)
    {
        $deny = ["ş", "Ş", "ı", "ü", "Ü", "ö", "Ö", "ç", "Ç", "ğ", "Ğ", "İ", "(", ")", "", " ", "/", "*", "?"];
        $allow = ["s", "s", "i", "u", "u", "o", "o", "c", "c", "g", "g", "i", "", "", "", "", "", "", ""];

        $str = str_replace($deny, $allow, $str);

        $str = preg_replace("@[^A-Za-z0-9-_]+@i", "", $str);

        return $str;
    }

    public static function GetUtcTime($date = NULL, $utc = -6)
    {
        if (!isset($date) || empty($date))
            $date = date('Y-m-d H:i:s');

        $gmdate = gmdate('Y-m-d H:i:s', strtotime($date));

        $time = strtotime($gmdate) + (3600 * (date('I') + $utc));
        // yaz saati uygulaması varken UTC -5, yokken UTC -6 olarak çalışır.

        $date = date('Y-m-d H:i:s', $time);

        return $date;
    }

}