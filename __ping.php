<?php

require_once('__global.php');

function ping($target) {
    $result = new ProbeResult();
    if (!is_ipv6($target) && !is_domain($target)) {
        error_log("ping: invalid \$target({$target})");
        return $result;
    }
    exec("ping6 -c 10 -i 1 -W 3 {$target}", $output, $retval);
    $ok = false;
    foreach($output as $line) {
        if (preg_match('/^([0-9]+) packets transmitted, ([0-9]+) received, /', $line, $matches) === 1) {
            $ok = true;
            $pkts = intval($matches[1]);
            $rcvs = intval($matches[2]);
            if ($rcvs > 0) {
                $result->response = true;
                $result->valid = true;
                $result->response_pctg = $rcvs / $pkts;
                $result->valid_pctg = $rcvs / $pkts;
            }
        } else if (preg_match('/^rtt min\/avg\/max\/mdev = [0-9\.]+\/([0-9\.]+)\/[0-9\.]+\/[0-9\.]+ ms/', $line, $matches) === 1) {
            $avg = floatval($matches[1]);
            $result->time_response = $avg;
            $result->time_valid = $avg;
        }
    }
    if (!$ok) {
        error_log("ping: invalid output, check ping command");
    }
    return $result;
}

function tcping($target, $port) {
    $result = new ProbeResult();
    if ((!is_ipv6($target) && !is_domain($target)) || !is_port($port)) {
        error_log("tcping: invalid \$target({$target}) or \$port({$port})");
        return $result;
    }
    exec("nping -6 -c 10 --delay 1s -p {$port} {$target}", $output, $retval);
    $ok = false;
    foreach($output as $line) {
        if (preg_match('/^TCP connection attempts: ([0-9]+) \| Successful connections: ([0-9]+) \|/', $line, $matches) === 1) {
            $ok = true;
            $pkts = intval($matches[1]);
            $rcvs = intval($matches[2]);
            if ($rcvs > 0) {
                $result->response = true;
                $result->valid = true;
                $result->response_pctg = $rcvs / $pkts;
                $result->valid_pctg = $rcvs / $pkts;
            }
        } else if (preg_match('/^Max rtt: [0-9\.]+ms \| Min rtt: [0-9\.]+ms \| Avg rtt: ([0-9\.]+)ms/', $line, $matches) === 1) {
            $avg = floatval($matches[1]);
            $result->time_response = $avg;
            $result->time_valid = $avg;
        }
    }
    if (!$ok) {
        error_log("tcping: invalid output, check nping command");
    }
    return $result;
}

