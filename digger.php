<?php

require_once('__global.php');
require_once('__dig.php');

__RELOAD_CONF();

while (true) {
    
    __RELOAD_CONF();
    
    $mysqli = new mysqli($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);
    if (!$mysqli) {
        printf("MySQL connection error\n");
        sleep($SLEEP_MYSQLERROR);
        continue;
    }
    $mysqli->set_charset('utf8');
    
    $task = false;
    
    $mysqli->query('lock tables domains write;');
    $result = $mysqli->query("select id, domain from domains where ts_lastdig = 0 or timestampdiff(second, ts_lastdig, now()) > {$INTERVAL_DIG} order by ts_lastdig asc limit 1;");
    if ($result && $result->num_rows > 0) {
        $task = $result->fetch_object();
        $result->free();
        $mysqli->query("update domains set ts_lastdig = now() where id = {$task->id};");
        printf("Got task, id: %d, domain: %s\n", $task->id, $task->domain);
    } else {
        printf("No task\n");
    }
    $mysqli->query('unlock tables;');
    
    $addr_cache = array();
    
    if ($task !== false) {
        $mysqli->query("delete from targets where id = {$task->id};");
        foreach($PUBLIC_SERVERS as $name => $server) {
            $addrs = dig_at($task->domain, $server);
            foreach($addrs as $addr) {
                echo "{$task->domain} @{$name}({$server}): {$addr}\n";
                if (!in_array($addr, $addr_cache, TRUE)) {
                    $addr_cache[] = $addr;
                    $mysqli->query("insert into targets (id, domain_id, addr, ts_lastfound) values (uuid_short(), {$task->id}, '{$addr}', now()) on duplicate key update ts_lastfound = now();");
                }
            }
        }
//        foreach($ECS_SUBNETS as $name => $subnet) {
//            $addrs = dig_ecs($task->domain, $subnet);
//            foreach($addrs as $addr) {
//                echo "{$task->domain} +subnet={$name}({$subnet}): {$addr}\n";
//                if (!in_array($addr, $addr_cache, TRUE)) {
//                    $addr_cache[] = $addr;
//                    $mysqli->query("insert into targets (id, domain_id, addr, ts_lastfound) values (uuid_short(), {$task->id}, '{$addr}', now()) on duplicate key update ts_lastfound = now();");
//                }
//            }
//        }
        $mysqli->query("update domains set ts_digfinish = now() where id = {$task->id};");
        $mysqli->query("delete from targets where domain_id = {$task->id} and timestampdiff(second, ts_lastfound, now()) > {$INTERVAL_DIG};");
    }
    
    $mysqli->close();
    if ($task === false) {
        sleep($SLEEP_IDLE);
    }
}

