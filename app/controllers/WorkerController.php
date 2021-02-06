<?php
declare(strict_types=1);

use Phalcon\Mvc\View;
use Phalcon\Http\Request;
use Service\Google;
use Service\iOS;
use System\Helper;
use Phalcon\Mvc\Model\Manager;

class WorkerController extends ControllerBase
{

    public function checkAction($os = 'iOS')
    {
        $this->view->disableLevel(
            View::LEVEL_MAIN_LAYOUT
        );

        $date = date('Y-m-d H:i:s');

        $params = [
            'where' => [
                'expire_date <=' => $date,
                'os' => $os,
                'status' => 1,
            ],
            'limit' => [
                'offset' => 0,
                'limit' => 100000
            ]
        ];

        if ($os == 'Google')
            $mock = new Google();
        else
            $mock = new iOS();

        $helper = new Helper();
        $params = $helper->CreateFilter($params);

        $find_subsciption = Subscription::find($params);

        $subs_ids = [1];

        foreach ($find_subsciption as $subs) {
            $result = $mock->Check($subs->receipt_hash);

            if ((!isset($result) || empty($result) || !isset($result['result']) || empty($result['result']) || !$result['result']) ||
                (isset($result['expire_date']) && !empty($result['expire_date']) && $result['expire_date'] < $date))
            {
                $subs_ids[] = $subs->id;
                /*

                ### bu yöntemle 100.000 kayıt update'i 01:56 sürdü ###
                $subs->status = 2;
                $subs->save();

                */

                $params = [
//                'where_in' => [
//                    'subscription.id' => $subs_ids // çok uzun - bitmedi (foreach dışında in ile tek sorguyla - mongoda çok işe yarıyor. bu işlemin mongodaki süresi 100.000 kayıt için: 1-5 sn)
//                ],
//                    'where' => [ // mock doğrulaması olmazsa 200.000 kayıt update'i (foreach dışında kullanılır, döngü olmaz): 39 sn
//                        'subscription.expire_date <=' => $date,
//                        'subscription.os' => $os,
//                        'subscription.status' => 1,
//                    ],
                    'where' => [
                        'subscription.id' => $subs->id // bu yöntemle 100.000 kayıt update'i 01:27 sürdü
                    ]
                ];

                $params = $helper->CreateFilter($params);

                $data = ['status' => 2];

                if (isset($params) && !empty($params) && isset($params[0]) && !empty($params[0]) && isset($params['bind']) && !empty($params['bind']) && isset($data) && !empty($data))
                {
                    $updated_columns = [];
                    foreach ($data as $key => $value) {
                        $updated_columns[] = 'subscription.' . $key . ' = :' . $key . ':';
                        $params['bind'][$key] = $value;
                    }

                    if (isset($updated_columns) && !empty($updated_columns))
                    {
                        $phql = 'UPDATE subscription SET ' . implode(', ', $updated_columns) . ' WHERE ' . $params[0];

                        $this->modelsManager->executeQuery($phql, $params['bind']);
                    }
                }
            }
        }
    }

}

