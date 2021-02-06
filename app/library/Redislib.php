<?php

use Phalcon\Storage\SerializerFactory;
use Phalcon\Cache\Adapter\Redis;

class Redislib
{
    var $cache;
    var $frontCache;

    public function __construct()
    {
        $this->frontCache = new SerializerFactory(
            [
                "lifetime" => 172800,
            ]
        );

        $this->cache = new Redis(
            $this->frontCache,
            [
                "host"       => "127.0.0.1",
                "port"       => 6379,
            ]
        );
    }
}