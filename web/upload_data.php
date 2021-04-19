<?php
require_once ('creds.php');
require_once ('auth_app.php');
require_once ('db_functions.php');

if ($abrp_forward) {
    $params = $_SERVER['QUERY_STRING'];
    $result = file_get_contents("http://api.iternio.com/1/tlm/torque?$params");
}

// Connect to Database
$db = new DBAccess($db_host, $db_user, $db_pass, $db_name);

// Create an array of all the existing fields in the database
$db_query_result = $db->get_fields($db_table) or $db->db_die();
$dbfields[] = $db->enumerate_rows($db_query_result);

// Iterate over all the k* _GET arguments to check that a field exists
if (sizeof($_GET) > 0) {
    $keys = array();
    $values = array();

    foreach ($_GET as $key => $value) {
        // Keep columns starting with k
        if (preg_match("/^k/", $key)) {
            $keys[] = $key;
            $values[] = $value;
            $submitval = 1;
        }
        else if (in_array($key, array("v", "eml", "time", "id", "session"))) {
            $keys[] = $key;
            $values[] = "'".$value."'";
            $submitval = 1;
        }

        // Skip columns matching userUnit*, defaultUnit*, and profile*
        else if (preg_match("/^userUnit/", $key) or preg_match("/^defaultUnit/", $key) or (preg_match("/^profile/", $key) and (!preg_match("/^profileName/", $key)))) {
            $submitval = 0;
        }

        else {
            $submitval = 0;
        }
        // NOTE: Use the following "else" statement instead of the one above
        //       if you want to keep anything else.
        //else {
        //    $keys[] = $key;
        //    $values[] = "'".$value."'";
        //    $submitval = 1;
        //}
        // If the field doesn't already exist, add it to the database
        if (!in_array($key, $dbfields) and $submitval == 1) {
            $db->add_column($db_table, $key, 'VARCHAR(255)', false, '0');
        }
    }

    if ((sizeof($keys) === sizeof($values)) && sizeof($keys) > 0) {
        // Now insert the data for all the fields
        $db->insert_data($db_table, $keys, $values);
    }
} else {
    die ( "No URL parameters." );
}

// Return the response required by Torque
echo "OK!";

?>
