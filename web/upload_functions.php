<?php

// Loads session information.
function process_session_data($prefix, $get_keys, $session_data, $is_key = true)
{
    $regex_str = '/^' . $prefix . '/'
    $key_list = preg_grep($regex_str, $get_keys);

    if (count($key_list) == 0) {
        return false;
    }

    foreach ($key_list as $get_key) {
        $key = preg_replace($regex_str, ($is_key ? 'k' : ''), $get_key);
        $value = $_GET[$get_key];

        if ($is_key) {
            if (!isset($session_data[$key])) {
                $session_data[$key] = array($prefix => $value);
            } else {
                $session_data[$key][$prefix] => $value];
            }
        } else {
            if (!isset($session_data[$prefix])) {
                $session_data[$prefix] = array($key => $value);
            } else {
                $session_data[$prefix][$key] = $value;
            }
        }
    }

    return true;
}

// Hyphenates the Torque ID.
// Would work for any 32 character string.
function format_id_as_uuid($torque_id)
{
    $grps = array(
        substr($torque_id, 0, 8),
        substr($torque_id, 8, 4),
        substr($torque_id, 12, 4),
        substr($torque_id, 16, 4),
        substr($torque_id, 20, 12)
    );

    return implode("-", $grps);
}

function abrp_build_query_array($prefix, $session_data, $isKeys = true)
{
    $query_data = array();
    if ($isKeys) {
        $keys = preg_grep('/^k/', array_keys($session_data));
        foreach ($keys as $key) {
            $queryKey = preg_replace('/^k/', $prefix, $key);
            $query_data[$queryKey] = $session_data[$key][$prefix];
        }
    } else {
        $values = $session_data[$prefix];
        foreach ($values as $key => $value) {
            $queryKey = $prefix . $key;
            $query_data[$queryKey] = $value;
        }
    }

    return $query_data;
}

function abrp_query_1($query_header, $session_data)
{
    $query_data = abrp_build_query_array('defaultUnit', $session_data);

    return array_merge($query_header, $query_data);
}

function abrp_query_2($query_header, $session_data)
{
    $query_data = abrp_build_query_array('profile', $session_data, false);
    return array_merge($query_header, $query_data);
}

function abrp_query_3($query_header, $session_data)
{
    $query_data_unit = abrp_build_query_array('userUnit', $session_data);
    $query_data_short = abrp_build_query_array('userShortName', $session_data);
    $query_data_full = abrp_build_query_array('userFullName', $session_data);

    return array_merge(
        $query_header,
        $query_data_unit,
        $query_data_short,
        $query_data_full
    );
}

function abrp_query_4($query_header, $session_data)
{
    $query_data = $session_data['values'];
    return array_merge($query_header, $query_data);
}

function abrp_forward($session_id, $session_data, $abrp_id, $init = false)
{
    // Rebuild the queries and forward to ABRP.
    $query_header = array(
        'eml' => $abrp_id,
        'v' => $session_id['v'],
        'session' => $session_id['session'],
        'id' => $session_id['id'],
        'time' => $session_data['time'];
    );

    if ($init) {
        $server_url = "http://api.iternio.com/1/tlm/torque?"
        $query_1 = abrp_query_1($query_header, $session_data);
        $query_2 = abrp_query_2($query_header, $session_data);
        $query_3 = abrp_query_3($query_header, $session_data);

        file_get_contents($server_url . http_build_query($query_1));
        file_get_contents($server_url . http_build_query($query_2));
        file_get_contents($server_url . http_build_query($query_3));
    } else {
        $query_4 = abrp_query_4($query_header, $session_data);

        file_get_contents($server_url . http_build_query($query_4));
    }
}
