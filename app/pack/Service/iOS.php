<?php

namespace Service;
use \System\Helper;

class iOS{
    var $month;
    protected static $redis;

    public function __construct()
    {
        self::$redis = new \Redislib();
    }

    public function Send($receipt = NULL)
    {
        $response = [
            'result' => FALSE,
        ];

        if (isset($receipt) && !empty($receipt) && self::CreateExpireDateByReceiptHash($receipt))
        {
            $expire_date = date('Y-m-d H:i:s', strtotime('+ ' . $this->month . ' months'));

            $response = [
                'result' => 'OK',
                'status' => TRUE,
                'expire-date' => Helper::GetUtcTime($expire_date),
            ];

            self::$redis->cache->set($receipt, $expire_date);
        }
        else
        {
            $response = [
                'result' => FALSE,
                'status' => FALSE,
                'expire-date' => NULL,
            ];
        }

        return $response;
    }

    public function Check($receipt = NULL)
    {
        $response = [
            'result' => FALSE,
        ];

        if (isset($receipt) && !empty($receipt))
        {
            $expire_date = self::$redis->cache->get($receipt);
            if (isset($expire_date) && !empty($expire_date))
            {
                $response = [
                    'result' => 'OK',
                    'status' => TRUE,
                    'expire-date' => Helper::GetUtcTime($expire_date),
                ];
            }
        }

        return $response;
    }

    protected function CreateExpireDateByReceiptHash($receipt = '')
    {
        $this->month = substr($receipt,-1);
        return (intval($this->month) % 2) == 1 ? TRUE : FALSE;
    }

}