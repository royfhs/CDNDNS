<?php

require_once('__global.php');
require_once('__ping.php');
require_once('__curl.php');

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
    
    $mysqli->query('lock tables targets write;');
    $result = $mysqli->query("select id, domain_id, addr from targets where ts_lastprobe = 0 or timestampdiff(second, ts_lastprobe, now()) > {$INTERVAL_PROBE} order by ts_lastprobe asc limit 1;");
    if ($result && $result->num_rows > 0) {
        $task_target = $result->fetch_object();
        $result->free();
        $mysqli->query("update targets set ts_lastprobe = now() where id = {$task_target->id};");
        printf("Got task, id: %d, domain_id: %s, addr: %s\n", $task_target->id, $task_target->domain_id, $task_target->addr);
    } else {
        printf("No task\n");
    }
    $mysqli->query('unlock tables;');
    
    $result = $mysqli->query("select domain, testmethod, port, path, criteria from domains where id = {$task_target->domain_id};");
    if ($result && $result->num_rows > 0) {
        $task_domain = $result->fetch_object();
        $result->free();
        $task = (object)array_merge((array)$task_target, (array)$task_domain);
    }
    
    if ($task !== false) {
        $status = false;
        switch ($task->testmethod) {
            case 'ping':
                if (is_ipv6($task->addr)) {
                    $status = ping($task->addr);
                }
                break;
            case 'tcping':
                if (is_ipv6($task->addr) && is_port($task->port)) {
                    $status = tcping($task->addr, intval($task->port));
                }
                break;
            case 'http_get':
                if (is_domain($task->domain) && is_ipv6($task->addr) && is_port($task->port) && is_string($task->path)) {
                    $status = http_get($task->domain, $task->addr, intval($task->port), $task->path);
                }
                break;
            case 'https_get':
                if (is_domain($task->domain) && is_ipv6($task->addr) && is_port($task->port) && is_string($task->path)) {
                    $status = https_get($task->domain, $task->addr, intval($task->port), $task->path);
                }
                break;
            case 'http_get_code':
                if (is_domain($task->domain) && is_ipv6($task->addr) && is_port($task->port) && is_string($task->path)) {
                    $criteria = is_string($task->criteria) ? $task->criteria : false;
                    $status = http_get_code($task->domain, $task->addr, intval($task->port), $task->path, $criteria);
                }
                break;
            case 'https_get_code':
                if (is_domain($task->domain) && is_ipv6($task->addr) && is_port($task->port) && is_string($task->path)) {
                    $criteria = is_string($task->criteria) ? $task->criteria : false;
                    $status = https_get_code($task->domain, $task->addr, intval($task->port), $task->path, $criteria);
                }
                break;
            case 'http_get_header_match':
                if (is_domain($task->domain) && is_ipv6($task->addr) && is_port($task->port) && is_string($task->path) && is_string($task->criteria)) {
                    $status = http_get_header_match($task->domain, $task->addr, intval($task->port), $task->path, $task->criteria);
                }
                break;
            case 'https_get_header_match':
                if (is_domain($task->domain) && is_ipv6($task->addr) && is_port($task->port) && is_string($task->path) && is_string($task->criteria)) {
                    $status = https_get_header_match($task->domain, $task->addr, intval($task->port), $task->path, $task->criteria);
                }
                break;
            case 'http_get_content_match':
                if (is_domain($task->domain) && is_ipv6($task->addr) && is_port($task->port) && is_string($task->path) && is_string($task->criteria)) {
                    $status = http_get_content_match($task->domain, $task->addr, intval($task->port), $task->path, $task->criteria);
                }
                break;
            case 'https_get_content_match':
                if (is_domain($task->domain) && is_ipv6($task->addr) && is_port($task->port) && is_string($task->path) && is_string($task->criteria)) {
                    $status = https_get_content_match($task->domain, $task->addr, intval($task->port), $task->path, $task->criteria);
                }
                break;
            default:
                printf("Unknown testmethod: %s\n", $task->testmethod);
        }
        if ($status !== false) {
            printf("Result: %s\n", print_r($status, true));
            $status_response = $status->response ? 'true' : 'false';
            $status_valid = $status->valid ? 'true' : 'false';
            $mysqli->query("update targets set ts_probefinish = now(), response = {$status_response}, response_pctg = {$status->response_pctg}, valid = {$status_valid}, valid_pctg = {$status->valid_pctg}, time_response = {$status->time_response}, time_valid = {$status->time_valid} where id = {$task->id};");
            if ($task->testmethod === 'ping') {
                $mysqli->query("update targets set ts_probefinish = now(), ts_lastprobe = now(), response = {$status_response}, response_pctg = {$status->response_pctg}, valid = {$status_valid}, valid_pctg = {$status->valid_pctg}, time_response = {$status->time_response}, time_valid = {$status->time_valid} where id <> {$task->id} and domain_id in (select id from domains where testmethod = 'ping' and addr = '{$task->addr}');");
            } else if ($task->testmethod === 'tcping') {
                $mysqli->query("update targets set ts_probefinish = now(), ts_lastprobe = now(), response = {$status_response}, response_pctg = {$status->response_pctg}, valid = {$status_valid}, valid_pctg = {$status->valid_pctg}, time_response = {$status->time_response}, time_valid = {$status->time_valid} where id <> {$task->id} and domain_id in (select id from domains where testmethod = 'tcping' and addr = '{$task->addr}' and port = {$task->port});");
            }
        } else {
            printf("No test result for: %s\n", print_r($task, true));
            $mysqli->query("update targets set ts_probefinish = now() where id = {$task->id};");
        }
    }
    
    $mysqli->close();
    if ($task === false) {
        sleep($SLEEP_IDLE);
    }
}

