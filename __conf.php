<?php

global $MYSQL_HOST, $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB;
$MYSQL_HOST = 'localhost';
$MYSQL_USER = 'cdndns';
$MYSQL_PASS = 'cdndns';
$MYSQL_DB = 'cdndns';

global $SLEEP_IDLE, $SLEEP_MYSQLERROR;
$SLEEP_IDLE = 30;
$SLEEP_MYSQLERROR = 60;

global $INTERVAL_DIG, $INTERVAL_PROBE, $PROBE_VALID_FOR;
$INTERVAL_DIG = 60;
$INTERVAL_PROBE = 300;
$PROBE_VALID_FOR = 3600;

global $PUBLIC_SERVERS;
$PUBLIC_SERVERS = array(
    'sjtu202' => '202.120.2.100',
    'sjtu112' => '202.112.26.33',
    'ustc' => '202.38.64.1',
    '114' => '114.114.114.114',
    'baidu' => '180.76.76.76',
    'dnspod' => '119.29.29.29',
    'ali' => '223.5.5.5',
    'cnnic' => '1.2.4.8',
    'cernet' => '101.7.8.9',
    'dnspai' => '101.226.4.6',
    'onedns1' => '112.124.47.27',
    'onedns2' => '114.215.126.16',
    'google' => '8.8.8.8',
    'opendns' => '208.67.222.222',
    'verisign' => '64.6.64.6',
    'he' => '74.82.42.42',
    'yandex' => '77.88.8.8',
    'dyn' => '216.146.35.35',
    'comodo' => '8.26.56.26',
    'level3' => '209.244.0.3',
    'dnswatch' => '84.200.69.80',
    'quad9' => '9.9.9.10',
);

global $ECS_SUBNETS;
$ECS_SUBNETS = array(
//    'cernet_sjtu' => '202.120.1.1',
//    'cernet_tsinghua' => '166.111.1.1',
//    'tel_sh' => '180.160.1.1',
//    'tel_bj' => '180.149.128.1',
//    'tel_gd' => '219.128.1.1',
//    'tel_wh' => '119.96.1.1',
//    'tel_cq' => '222.176.1.1',
//    'uni_sh' => '223.166.1.1',
//    'uni_bj' => '111.192.1.1',
//    'uni_gd' => '112.88.1.1',
//    'uni_wh' => '210.5.130.1',
//    'cmcc_sh' => '211.136.110.1',
//    'cmcc_bj' => '221.130.45.1',
    'cmcc_gd' => '211.139.145.1',
//    'cmcc_wh' => '120.202.17.1',
    'hk' => '27.111.160.1',
//    'tw' => '49.158.1.1',
//    'jp' => '14.192.32.1',
    'us' => '23.253.1.1',
    'fr' => '77.192.1.1',
//    'de' => '2.200.1.1',
);

