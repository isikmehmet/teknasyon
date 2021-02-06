<?php
declare(strict_types=1);
date_default_timezone_set('Europe/Istanbul');

use Phalcon\Di\FactoryDefault;

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

try {

    /**
     * The FactoryDefault Dependency Injector automatically registers
     * the services that provide a full stack framework.
     */
    $di = new FactoryDefault();

    /**
     * Read services
     */
    include APP_PATH . '/config/services.php';

    /**
     * Handle routes
     */
    include APP_PATH . '/config/router.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';

    if (!isset($_GET['_url']) || empty($_GET['_url']))
    {
        if (isset($_SERVER['argv']) && !empty($_SERVER['argv']))
        {
            $_url = [];
            foreach ($_SERVER['argv'] as $key => $argv) {
                if ($key > 0)
                {
                    $_url[] = $argv;
                }
            }

            $_GET['_url'] = '/' . implode('/', $_url);

        }
    }

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

//    echo $application->handle($_SERVER['REQUEST_URI'])->getContent();
    echo $application->handle($_GET['_url'] ?? '/')->getContent();
} catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
