<?php

require_once('__global.php');

function dig_at($domain, $server) {
    $addrs = array();
    if (!is_domain($domain) || (!is_ipv4($server) && !is_domain($server))) {
        error_log("dig_at: invalid \$domain({$domain}) or \$server({$server})");
        return $addrs;
    }
    $output = dig("{$domain} AAAA @{$server}");
    $answers = dig_get_answers($output);
    $rrs = dig_parse_rrs($answers);
    foreach ($rrs as $rr) {
        if ($rr['type'] === 'AAAA') {
            $addrs[] = $rr['data'];
        }
    }
    return $addrs;
}

//function dig_ecs($domain, $subnet) {
//    $addrs = array();
//    if (!is_domain($domain) || !is_subnet($subnet)) {
//        error_log("dig_ecs: invalid \$domain({$domain}) or \$subnet({$subnet})");
//        return $addrs;
//    }
//    do {
//        $output = dig("{$domain} +trace +subnet={$subnet}");
//        $answers = dig_get_last_trace($output);
//        $rrs = dig_parse_rrs($answers);
//        $has_a = false;
//        $has_cname = false;
//        foreach ($rrs as $rr) {
//            if ($rr['type'] === 'A') {
//                $addrs[] = $rr['data'];
//                $has_a = true;
//            } else if ($rr['type'] === 'CNAME') {
//                $domain = $rr['data'];
//                $has_cname = true;
//            }
//        }
//        if (!$has_cname && !$has_a) {
//            error_log("dig_ecs: trace failed for {$domain} ecs {$subnet}");
//        }
//    } while($has_cname && !$has_a);
//    return $addrs;
//}

// === PRIVATE BELOW ===

function dig($param) {
    exec("dig +tries=2 +time=3 {$param}", $output, $retval);
    if ($retval !== 0) {
        error_log("dig: dig (param: {$param}) exit code $retval");
    }
    return $output;
}

function dig_get_answers($output) {
    $answers = array();
    if (!is_array($output)) {
        error_log("dig_get_answers: input not array");
        return $answers;
    }
    $flag = false;
    foreach($output as $line) {
        if ($flag) {
            if ($line === '' || substr($line, 0, 2) === ';;') {
                $flag = false;
            } else {
                $answers[] = $line;
            }
        } else if ($line === ';; ANSWER SECTION:') {
            $flag = true;
        }
    }
    return $answers;
}

function dig_get_last_trace($output) {
    $answers = array();
    if (!is_array($output)) {
        error_log("dig_get_last_trace: input not array");
        return $answers;
    }
    $temp = array();
    foreach($output as $line) {
        if ($line === '') {
            $temp = array();
        } else if (substr($line, 0, 2) === ';;') {
            $answers = $temp;
        } else {
            $temp[] = $line;
        }
    }
    return $answers;
}

function dig_parse_rrs($rrset) {
    $rrs = array();
    if (!is_array($rrset)) {
        error_log("dig_parse_rrs: input not array");
        return $rrs;
    }
    foreach($rrset as $line) {
        if (preg_match('/^([^\s]+)\s+([0-9]+)\s+([^\s]+)\s+([^\s]+)\s+(.+)$/', $line, $matches) === 1) {
            $rrs[] = array(
                'name' => $matches[1],
                'ttl' => $matches[2],
                'class' => $matches[3],
                'type' => $matches[4],
                'data' => $matches[5],
            );
        } else {
            error_log("dig_parse_rrs: cannot parse resource record: {$line}");
        }
    }
    return $rrs;
}

