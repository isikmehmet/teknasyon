<?php
declare(strict_types=1);

use Phalcon\Mvc\View;
use Phalcon\Http\Request;
use Service\Google;
use Service\iOS;
use System\Helper;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Cache\Adapter\Redis;

class ApiController extends ControllerBase
{

    public function registerAction()
    {
        $this->view->disableLevel(
            View::LEVEL_MAIN_LAYOUT
        );

        try{
            $allowed_keys = ['uid', 'appId', 'language', 'os'];
            $allowed_filter_keys = ['uid', 'appId', 'os'];

            $request = new Request();

            $response = [
                'Register' => FALSE,
            ];

            if ($request->isPost() === TRUE)
            {
                $post = $request->getPost();
                $helper = new Helper();

                if (isset($post['uid'])         && !empty($post['uid']) &&
                    isset($post['appId'])       && !empty($post['appId']) &&
                    isset($post['language'])    && !empty($post['language']) &&
                    isset($post['os'])          && !empty($post['os']))
                {
                    $params = [
                        'where' => []
                    ];

                    foreach ($post as $key => $value) {
                        if (in_array($key, $allowed_filter_keys))
                        {
                            $params['where'][$key] = $value;
                        }
                    }

                    $params = $helper->CreateFilter($params);

                    $find_device = Devices::find($params);

                    if (isset($find_device[0]) && !empty((array)$find_device[0]))
                    {
                        $device = $find_device[0];
                        $device->language = $post['language'];
                        $device->update_date = date('Y-m-d H:i:s');
                        $device->save();

                        $response = [
                            'Register' => 'OK',
                            'client-token' => $device->client_token
                        ];
                    }
                    else
                    {
                        $device = new Devices();

                        foreach ($post as $key => $value) {
                            if (in_array($key, $allowed_keys))
                            {
                                $device->{$key} = $value;
                            }
                        }

                        do{
                            $token = $helper->CreateToken(32);
                            $token_params = [
                                'where' => [
                                    'client_token' => $token
                                ]
                            ];
                            $token_params = $helper->CreateFilter($token_params);
                            $find_device = Devices::find($token_params);

                            $unique_token = isset($find_device[0]) && !empty($find_device[0]) ? TRUE : FALSE;
                        }
                        while($unique_token);

                        if (isset($token) && !empty($token))
                        {
                            $device->client_token = $token;
                            $device->register_date = date('Y-m-d H:i:s');

                            $save = $device->save();

                            if ($save)
                            {
                                $response = [
                                    'Register' => 'OK',
                                    'client-token' => $token
                                ];
                            }
                        }
                    }
                }
                else
                {
                    throw new \Exception('Zorunlu alanlar: uid, appId, language, os');
                }
            }
            else
            {
                throw new \Exception('Post edilen veri bulunamadı');
            }

            echo json_encode($response);
        }
        catch(Exception $e)
        {
            $response = [
                'Register' => FALSE,
                'Message' => $e->getMessage()
            ];

            echo json_encode($response);
        }
    }

    public function purchaseAction()
    {
        $this->view->disableLevel(
            View::LEVEL_MAIN_LAYOUT
        );

        try{
            $request = new Request();

            $response = [
                'Response' => FALSE,
            ];

            if ($request->isPost() === TRUE)
            {
                $post = $request->getPost();
                $helper = new Helper();

                if (isset($post['client-token'])    && !empty($post['client-token']) &&
                    isset($post['receipt'])         && !empty($post['receipt']))
                {
                    $params = [
                        'where' => [
                            'client_token' => $post['client-token']
                        ]
                    ];

                    $params = $helper->CreateFilter($params);

                    $find_device = Devices::find($params);

                    if (isset($find_device[0]) && !empty((array)$find_device[0]))
                    {
                        $device = $find_device[0];

                        $service_response = [];

                        switch ($device->os){

                            case 'Google':
                                $google = new Google();
                                $service_response = $google->Send($post['receipt']);
                                break;

                            case 'iOS':
                                $ios = new iOS();
                                $service_response = $ios->Send($post['receipt']);
                                break;

                            default:
                                break;

                        }

                        if (isset($service_response) && !empty($service_response) &&
                            isset($service_response['status']) && !empty($service_response['status']) &&
                            isset($service_response['expire-date']) && !empty($service_response['expire-date']))
                        {
                            $purchase = new Purchase();

                            $purchase->client_token = $device->client_token;
                            $purchase->receipt_hash = $post['receipt'];
                            $purchase->platform = $device->os;
                            $purchase->platform_response = json_encode($service_response);
                            $purchase->status = $service_response['status'] ? 1 : 0;
                            $purchase->expire_date = $service_response['expire-date'];
                            $purchase->expire_date_utc = 'UTC -6';
                            $purchase->request_date = date('Y-m-d H:i:s');

                            $save = $purchase->save();

                            if ($save)
                            {
                                $params = [
                                    'where' => [
                                        'client_token' => $post['client-token']
                                    ]
                                ];

                                $params = $helper->CreateFilter($params);

                                $find_subsciption = Subscription::find($params);

                                if (isset($find_subsciption[0]) && !empty($find_subsciption[0]))
                                {
                                    $finded = $find_subsciption[0];
                                    $finded->expire_date = $service_response['expire-date'];
                                    $finded->status = $service_response['status'] ? 1 : 0;
                                    $finded->update_date = date('Y-m-d H:i:s');
                                    $finded->save();
                                }
                                else
                                {
                                    $new_subs = new Subscription();
                                    $new_subs->client_token = $post['client-token'];
                                    $new_subs->receipt_hash = $post['receipt'];
                                    $new_subs->os = $device->os;
                                    $new_subs->expire_date = $service_response['expire-date'];
                                    $new_subs->status = $service_response['status'] ? 1 : 0;
                                    $new_subs->create_date = date('Y-m-d H:i:s');
                                    $new_subs->update_date = date('Y-m-d H:i:s');
                                    $new_subs->save();
                                }

                                if ($service_response['status'])
                                {
                                    $response = [
                                        'Response' => 'OK',
                                        'service_response' => $service_response
                                    ];
                                }
                            }

                            $device->update_date = date('Y-m-d H:i:s');
                            $save = $device->save();
                        }
                    }
                }
                else
                {
                    throw new \Exception('Zorunlu alanlar: client-token, receipt');
                }
            }
            else
            {
                throw new \Exception('Post edilen veri bulunamadı');
            }

            echo json_encode($response);
        }
        catch(Exception $e)
        {
            $response = [
                'Register' => FALSE,
                'Message' => $e->getMessage()
            ];

            echo json_encode($response);
        }
    }

    public function checkSubscriptionAction()
    {
        $this->view->disableLevel(
            View::LEVEL_MAIN_LAYOUT
        );

        try{
            $request = new Request();

            $response = [
                'Response' => FALSE,
            ];

            if ($request->isPost() === TRUE)
            {
                $post = $request->getPost();
                $helper = new Helper();

                if (isset($post['client-token'])    && !empty($post['client-token']))
                {
                    $params = [
                        'where' => [
                            'client_token' => $post['client-token']
                        ]
                    ];

                    $params = $helper->CreateFilter($params);

                    $subsciption = Subscription::find($params);

                    if (isset($subsciption[0]) && !empty((array)$subsciption[0]))
                    {
                        $subsciption = $subsciption[0];

                        if (strtotime($subsciption->expire_date) >= time())
                        {
                            $response = [
                                'Response' => TRUE
                            ];
                        }
                    }
                }
                else
                {
                    throw new \Exception('Zorunlu alan: client-token');
                }
            }
            else
            {
                throw new \Exception('Post edilen veri bulunamadı');
            }

            echo json_encode($response);
        }
        catch(Exception $e) {
            $response = [
                'Register' => FALSE,
                'Message' => $e->getMessage()
            ];

            echo json_encode($response);
        }
    }

}

