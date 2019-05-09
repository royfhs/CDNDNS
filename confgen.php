<?php

require_once('__global.php');

__RELOAD_CONF();

$mysqli = new mysqli($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);
if (!$mysqli) {
    error_log("MySQL connection error");
    die();
}
$mysqli->set_charset('utf8');

echo "server:\n\n";

function generate_zone($domain, $num_addrs, $num_response, $num_valid, $avg_time_response, $avg_time_valid, $addrs, $response_pctgs, $valid_pctgs, $time_responses, $time_valids) {
    $selected = array();
    $comments = array();
    arsort($response_pctgs, SORT_NUMERIC);
    arsort($valid_pctgs, SORT_NUMERIC);
    asort($time_responses, SORT_NUMERIC);
    asort($time_valids, SORT_NUMERIC);
    if ($num_valid > 0) {
        foreach($time_valids as $addr => $delay) {
            if ($delay <= ($avg_time_valid * 0.5) && $valid_pctgs[$addr] > 0.99) {
                $selected[] = $addr;
                $comments[$addr] = "perfect: valid {$valid_pctgs[$addr]}, delay {$delay}";
                unset($time_valids[$addr]);
                if (count($selected) >= 10) {
                    goto done;
                }
            }
        }
        if (count($selected) < 3) {
            foreach($time_valids as $addr => $delay) {
                if ($delay <= $avg_time_valid && $valid_pctgs[$addr] > 0.99) {
                    $selected[] = $addr;
                    $comments[$addr] = "verygood: valid {$valid_pctgs[$addr]}, delay {$delay}";
                    unset($time_valids[$addr]);
                    if (count($selected) >= 10) {
                        goto done;
                    }
                }
            }
        }
        if (count($selected) < 2) {
            foreach($time_valids as $addr => $delay) {
                if ($delay <= ($avg_time_valid * 2) && $valid_pctgs[$addr] > 0.75) {
                    $selected[] = $addr;
                    $comments[$addr] = "good: valid {$valid_pctgs[$addr]}, delay {$delay}";
                    unset($time_valids[$addr]);
                    if (count($selected) >= 10) {
                        goto done;
                    }
                }
            }
        }
        if (count($selected) < 2) {
            foreach($time_valids as $addr => $delay) {
                if ($valid_pctgs[$addr] > 0.49) {
                    $selected[] = $addr;
                    $comments[$addr] = "ok: valid {$valid_pctgs[$addr]}, delay {$delay}";
                    unset($time_valids[$addr]);
                    if (count($selected) >= 10) {
                        goto done;
                    }
                }
            }
        }
        if (count($selected) < 1) {
            foreach($time_valids as $addr => $delay) {
                if ($valid_pctgs[$addr] > 0.01) {
                    $selected[] = $addr;
                    $comments[$addr] = "poor: valid {$valid_pctgs[$addr]}, delay {$delay}";
                    unset($time_valids[$addr]);
                    if (count($selected) >= 10) {
                        goto done;
                    }
                }
            }
        }
    } else if ($num_response > 0) {
        foreach($time_responses as $addr => $delay) {
            if ($delay <= $avg_time_response && $response_pctgs[$addr] > 0.99) {
                $selected[] = $addr;
                $comments[$addr] = "invalid-fast: response {$response_pctgs[$addr]}, delay {$delay}";
                unset($time_responses[$addr]);
                if (count($selected) >= 10) {
                    goto done;
                }
            }
        }
        if (count($selected) < 2) {
            foreach($time_responses as $addr => $delay) {
                if ($delay <= ($avg_time_response * 2) && $response_pctgs[$addr] > 0.75) {
                    $selected[] = $addr;
                    $comments[$addr] = "invalid-average: response {$response_pctgs[$addr]}, delay {$delay}";
                    unset($time_responses[$addr]);
                    if (count($selected) >= 10) {
                        goto done;
                    }
                }
            }
        }
        if (count($selected) < 2) {
            foreach($time_responses as $addr => $delay) {
                if ($response_pctgs[$addr] > 0.49) {
                    $selected[] = $addr;
                    $comments[$addr] = "invalid-poor: response {$response_pctgs[$addr]}, delay {$delay}";
                    unset($time_responses[$addr]);
                    if (count($selected) >= 10) {
                        goto done;
                    }
                }
            }
        }
        if (count($selected) < 1) {
            foreach($time_responses as $addr => $delay) {
                if ($response_pctgs[$addr] > 0.01) {
                    $selected[] = $addr;
                    $comments[$addr] = "invalid-worst: response {$response_pctgs[$addr]}, delay {$delay}";
                    unset($time_responses[$addr]);
                    if (count($selected) >= 10) {
                        goto done;
                    }
                }
            }
        }
    }
done:
    if (count($selected) > 0) {
        if (substr($domain, -1) !== '.') {
            $domain .= '.';
        }
        echo "\tlocal-zone: \"{$domain}\" typetransparent\n";
        foreach ($selected as $addr) {
            echo "\tlocal-data: \"{$domain} 60 IN AAAA {$addr}\"  # {$comments[$addr]}\n";
        }
        echo "\t# total addrs: {$num_addrs}, responsive: {$num_response}, valid: {$num_valid}, selected: " . count($selected) . ", avg delay: resp {$avg_time_response} valid {$avg_time_valid} \n\n";
    } else {
        echo "\t### skipped: {$current_domain}, no addr selected ({$num_addrs} addrs, {$num_response} responsive, {$num_valid} valid)\n\n";
        error_log("Unable to generate zone for {$domain}, no addr selected");
    }
}

$query = $mysqli->prepare("select high_priority domain_id, domain, addr, response, valid, response_pctg, valid_pctg, time_response, time_valid from targets inner join domains on domain_id = domains.id where /*response = true and*/ timestampdiff(second, ts_probefinish, now()) <= {$PROBE_VALID_FOR} order by domain_id;");
$query->execute();
$query->bind_result($domain_id, $domain, $addr, $response, $valid, $response_pctg, $valid_pctg, $time_response, $time_valid);
$current_id = 0;
$current_domain = false;
while ($query->fetch()) {
    if ($domain_id !== $current_id) {
        if ($current_id !== 0) {
            if ($num_addrs > 0 && $num_response > 0) {
                generate_zone($current_domain, $num_addrs, $num_response, $num_valid, ($num_response ? intval($total_time_response / $num_response) : -1), ($num_valid ? intval($total_time_valid / $num_valid) : -1), $addrs, $response_pctgs, $valid_pctgs, $time_responses, $time_valids);
            } else {
                echo "\t### skipped: {$current_domain}, no addr selected ({$num_addrs} addrs, {$num_response} responsive, {$num_valid} valid)\n\n";
                error_log("Unable to generate zone for {$current_domain} ({$num_addrs} addrs, {$num_response} responsive, {$num_valid} valid)");
            }
        }
        $current_id = $domain_id;
        $current_domain = $domain;
        $num_addrs = 0;
        $num_response = 0;
        $num_valid = 0;
        $total_time_response = 0;
        $total_time_valid = 0;
        $addrs = array();
        $response_pctgs = array();
        $valid_pctgs = array();
        $time_responses = array();
        $time_valids = array();
    }
    $num_addrs += 1;
    $addrs[] = $addr;
    if ($response) {
        $num_response += 1;
        $response_pctgs[$addr] = $response_pctg;
        $time_responses[$addr] = $time_response;
        $total_time_response += $time_response;
    }
    if ($valid) {
        $num_valid += 1;
        $valid_pctgs[$addr] = $valid_pctg;
        $time_valids[$addr] = $time_valid;
        $total_time_valid += $time_valid;
    }
    
}
if ($current_id !== 0) {
    if ($num_addrs > 0 && $num_response > 0) {
        generate_zone($current_domain, $num_addrs, $num_response, $num_valid, ($num_response ? intval($total_time_response / $num_response) : -1), ($num_valid ? intval($total_time_valid / $num_valid) : -1), $addrs, $response_pctgs, $valid_pctgs, $time_responses, $time_valids);
    } else {
        echo "\t### skipped: {$current_domain}, no addr selected ({$num_addrs} addrs, {$num_response} responsive, {$num_valid} valid)\n\n";
        error_log("Unable to generate zone for {$current_domain} ({$num_addrs} addrs, {$num_response} responsive, {$num_valid} valid)");
    }
}

$mysqli->close();

echo "### last update: " . date('Y-m-d H:i:s') . "\n";

