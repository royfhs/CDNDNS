<?php

require_once('__global.php');
require_once('__ping.php');
require_once('__curl.php');

//var_dump(ping('8.8.8.8'));
//var_dump(tcping('ftp.sjtu.edu.cn', 21));
var_dump(http_get_header_match('www.taobao.com', '123.129.215.236', 80, '/', 'Strict-Transport-Security'));


