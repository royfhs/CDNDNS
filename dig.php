<?php

require_once('__global.php');
require_once('__dig.php');

__RELOAD_CONF();

$domain = 'non-existant-domain.sjtu.edu.cn';

foreach($PUBLIC_SERVERS as $name => $server) {
    $addrs = dig_at($domain, $server);
    foreach($addrs as $addr) {
        echo "{$domain} @{$name}({$server}): {$addr}\n";
    }
}
/*
foreach($ECS_SUBNETS as $name => $subnet) {
    $addrs = dig_ecs($domain, $subnet);
    foreach($addrs as $addr) {
        echo "{$domain} +subnet={$name}({$subnet}): {$addr}\n";
    }
}

