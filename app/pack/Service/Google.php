<?php

namespace Service;
use \System\Helper;

class Google{
    var $month;

    public function Send($receipt = NULL)
    {
        $response = [
            'result' => FALSE,
        ];

        if (isset($receipt) && !empty($receipt) && self::ReceiptControl($receipt))
        {
            $expire_date = date('Y-m-d H:i:s', strtotime('+ ' . $this->month . ' months'));

            $response = [
                'result' => 'OK',
                'status' => TRUE,
                'expire-date' => Helper::GetUtcTime($expire_date),
            ];
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

    protected function ReceiptControl($receipt = '')
    {
        $this->month = substr($receipt,-1);
        return (intval($this->month) % 2) == 1 ? TRUE : FALSE;
    }

}