<?php

require_once('__global.php');

define('_CURL_TEST_NONE', 100);
define('_CURL_TEST_CODE', 200);
define('_CURL_TEST_REGEX', 300);

function http_get($domain, $ip, $port, $path) {
    return curl($domain, $ip, $port, "http://{$domain}:{$port}{$path}", _CURL_TEST_NONE, false);
}

function https_get($domain, $ip, $port, $path) {
    return curl($domain, $ip, $port, "https://{$domain}:{$port}{$path}", _CURL_TEST_NONE, false);
}

function http_get_code($domain, $ip, $port, $path, $code = false) {
    return curl($domain, $ip, $port, "http://{$domain}:{$port}{$path}", _CURL_TEST_CODE, false, $code);
}

function https_get_code($domain, $ip, $port, $path, $code = false) {
    return curl($domain, $ip, $port, "https://{$domain}:{$port}{$path}", _CURL_TEST_CODE, false, $code);
}

function http_get_header_match($domain, $ip, $port, $path, $regex) {
    return curl($domain, $ip, $port, "http://{$domain}:{$port}{$path}", _CURL_TEST_REGEX, false, $regex);
}

function https_get_header_match($domain, $ip, $port, $path, $regex) {
    return curl($domain, $ip, $port, "https://{$domain}:{$port}{$path}", _CURL_TEST_REGEX, false, $regex);
}

function http_get_content_match($domain, $ip, $port, $path, $regex) {
    return curl($domain, $ip, $port, "http://{$domain}:{$port}{$path}", _CURL_TEST_REGEX, true, $regex);
}

function https_get_content_match($domain, $ip, $port, $path, $regex) {
    return curl($domain, $ip, $port, "https://{$domain}:{$port}{$path}", _CURL_TEST_REGEX, true, $regex);
}

// === PRIVATE BELOW ===

function curl($domain, $ip, $port, $fullurl, $testmethod, $testrespbody = false, $testmatch = false) {
    $result = new ProbeResult();
    if (!is_domain($domain) || !is_ipv6($ip) || !is_port($port)) {
        error_log("curl: invalid \$domain({$domain}), \$ip({$ip}) or \$port({$port})");
        return $result;
    }
    $attempts = 3;
    $attempts_if_responsive = 5;
    $num_response = 0;
    $num_valid = 0;
    $total_time = 0.0;
    $total_time_valid = 0.0;
    for ($i = 0; $i < $attempts; $i++) {
        $outputparam = '/dev/null';
        if ($testrespbody) {
            $outputparam = '-';
        }
        exec("curl -v -k -s -S -m 15 --connect-timeout 5 -o {$outputparam} -w '\n\nCURL_RESULT   http_code %{http_code} num_connects %{num_connects} num_redirects %{num_redirects} size_request %{size_request} size_header %{size_header} size_download %{size_download} speed_download %{speed_download} time_connect %{time_connect} time_appconnect %{time_appconnect} time_pretransfer %{time_pretransfer} time_redirect %{time_redirect} time_starttransfer %{time_starttransfer} time_total %{time_total}   EOL\n' --resolve {$domain}:{$port}:{$ip} {$fullurl} 2>&1", $output, $retval);
        $test_ok = false;
        if ($testmethod === _CURL_TEST_NONE) {
            $test_ok = true;
        }
        foreach($output as $line) {
            if ($testmethod === _CURL_TEST_REGEX && preg_match("/{$testmatch}/", $line) === 1) {
                $test_ok = true;
            }
            if (preg_match('/^CURL_RESULT .* http_code ([0-9]+) .* time_total ([0-9\.]+) .* EOL$/', $line, $matches) === 1) {
                $code = intval($matches[1]);
                $time = floatval($matches[2]);
                if ($testmethod === _CURL_TEST_CODE) {
                    if ($testmatch !== false && intval($testmatch) === $code) {
                        $test_ok = true;
                    } else if ($testmatch === false && ($code >= 200 && $code < 400)) {
                        $test_ok = true;
                    }
                }
                if ($code != 0) {
                    $num_response += 1;
                    $total_time += $time * 1000;
                    if ($test_ok) {
                        $num_valid += 1;
                        $total_time_valid += $time * 1000;
                    }
                    $attempts = $attempts_if_responsive;
                }
                break 1;
            }
        }
        sleep(1);
    }
    if ($num_response > 0) {
        $result->response = true;
        $result->response_pctg = $num_response / $attempts;
        $result->time_response = $total_time / $num_response;
        if ($num_valid > 0) {
            $result->valid = true;
            $result->valid_pctg = $num_valid / $attempts;
            $result->time_valid = $total_time_valid / $num_valid;
        }
    }
    return $result;
}
