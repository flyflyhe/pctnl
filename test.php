<?php

$redisConfig = [
    'REDIS_HOST'=>'10.0.3.203',
    'REDIS_PASSWORD'=>'e30ce5c05eee807018f4810fcf7ccf65',
    'REDIS_PORT'=> 6379,
    'REDIS_DATABASE'=> 2
];

$getRedis = function () use ($redisConfig)
{
    $redis = new Redis();
    $redis->connect($redisConfig['REDIS_HOST'], $redisConfig['REDIS_PORT']);
    $redis->auth($redisConfig['REDIS_PASSWORD']);
    $redis->select($redisConfig['REDIS_DATABASE']);
    return $redis;
};

$redis = $getRedis();
$pid = pcntl_fork();
if ($pid === -1) {
    die('fork failed');
} elseif ($pid === 0) {
    while (1) {
        sleep(1);
        echo posix_getpid().PHP_EOL;
    }
} elseif ($pid > 0) {
    while (1) {
        sleep(1);
        echo posix_getpid().PHP_EOL;
    }
}