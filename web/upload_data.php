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

/*
 * STEPS:
 *   1) Query user table for int_id associated with id and eml.
 *     a) No Record: Create user, get result, and continue.
 *     b) Record Found: Continue.
 */

/*
"SELECT int_id FROM users WHERE eml='eml' AND  id='id'"
IF NOT RESULT: 
  "INSERT INTO users ( eml, id ) VALUES ('eml', 'id')"
  IF SUCCESS: $userid = MySQLi::insert_id
  ELSE: ERROR
ELSE: $userid = RESULT['int_id')
 */

/*   2) Query session headers for session id based on user int_id, v, and session.
 *     a) No Record: Create record, get result, and continue.
 *     b) Record Found: Continue.
 */
 
/*
"SELECT int_id FROM session_header WHERE user_id=$userid AND v='v' AND session='session'"
IF NOT RESULT:
  "INSERT INTO session_header (user_id, v, session) VALUES ($userid, 'v', 'session')"
  IF SUCCESS: $sessid = MySQLi::insert_id
  ELSE: ERROR
ELSE: $sessid = RESULT('int_id')
 */
  
/*   3) Process Data received based on the following starting substrings:
 *     a) "default": Session Query 1 - Default Units
 *       i) Replace "defaultUnit" with "k" for all keys.
 *       ii) Check if session id exists in key_info_temp. NB: Just use select query and check if we have results.
 *         a) Not Found: Insert data into key_info_temp.
 *         b) Found: Merge data with incoming data, insert into key_info, delete all records for session.
 */
 
/*
"SELECT key_id, long_name, short_name, default_unit, user_unit FROM key_info_temp WHERE session_id=$sessid"
IF NOT RESULT:
  $data = arrray();
  FOR EACH key, value IN $_GET
    $data_key = substitute("k" for "defaultUnit" in key)
    $data[$data_key] = array( session_id => $sessid, default_unit => value )
  CONVERT $data TO INDEXED ARRAY
  "INSERT INTO key_info_temp (session_id, key_id, default_unit) VALUES $data"
  IF NOT SUCCESS: ERROR
ELSE
  $data = array()
  FOR EACH row IN RESULT
    $data[row[key_id] = array( session_id => row[session_id], long_name => row[long_name], short_name => row[short_name], user_unit => row[user_unit])
  FOR EACH key, value IN $_GET
    $data_key = substitute("k" for "defaultUnit" in key)
	$data[$data_key][default_unit] = value
  CONVERT $data TO INDEXED ARRAY
  TRANSACTION:
    "INSERT INTO key_info (session_id, key_id, long_name, short_name, default_unit, user_unit) VALUES $data"
	"DELETE FROM key_info_temp WHERE session_id=$sessid"
  IF NOT SUCCESS: ERROR
 */


/*     b) "profile": Session Query 2 - Vehicle Profile
 *       i) Check if vehicle profile exists. NB Just use select query and check if we have results.
 *         a) Not Found: Create Record, get result, and continue.
           b) Found: Continue.
		 ii) Link profile to session in vehicle_info
 */
 
/*
"SELECT int_id FROM vehicle_profile WHERE owner=$userid AND name='profileName' AND fuel_type = profileFuelType AND fuel_cost = profileFuelCost AND weight=profileWeight AND ve=profileVe"
IF NOT RESULT:
  "INSERT INTO vehicle_profile (owner, name, fuel_type, fuel_cost, weight, ve) VALUES ($userid, 'profileName', profileFuelType, profileFuelCost, profileWeight, profileVe)"
  IF SUCCESS: $carid = MySQLi::insert_id
  ELSE: ERROR
ELSE: $carid = RESULT('int_id')

INSERT INTO vehicle_info (session_id, vehicle_id) VALUES ($sessid, $carid)
IF NOT SUCCESS: ERROR
 */
 
/*     c) "user": Session Query 3 - User Info for Keys
 *       i) Replace "userUnit," "userShortName," and "userFullName" with "k" for all keys. 
 *         a) Transform into array ( key => array ( user_unit => <userUnit>, user_short_name => <userShortName>, user_full_name => <userFullName> ) ).
 *         b) Replace any '+' except the any that are the first character with a space in all name entries. This is to account for Bolt EV cell voltages unless or until the prefix character is changed.
 *       ii) Check if session id exists in key_info_temp.  NB: Just use select query and check if we have results.
 *         a) Not Found: Insert data into key_info_temp.
 *         b) Found: Merge data with incoming data, insert into key_info, delete all records for session.
 */

/*
"SELECT key_id, long_name, short_name, default_unit, user_unit FROM key_info_temp WHERE session_id=$sessid"
IF NOT RESULT:
  $data = arrray();
  FOR EACH key, value IN $_GET
    $data_key = ""
    $value_key = ""
    IF FIND "userUnit" IN key
        $data_key = substitute("k" for "userUnit" in key)
        $value_key = "user_unit"
    IF FIND "userShortName" IN key
        $data_key = substitute("k" for "userShortName" in key)
        $value_key = "short_name"
    IF FIND "userFullName" IN key
        $data_key = substitute("k" for "userFullName" in key)
        $value_key = "full_name"
    ELSE
        ERROR
    
    IF $data[$data_key] IS NEW
        $data[$data_key] = array( session_id => $sessid )
    ELSE
        $data[$data_key][$value_key] = value
        
  CONVERT $data TO INDEXED ARRAY
  "INSERT INTO key_info_temp (session_id, key_id, long_name, short_name, user_unit) VALUES $data"
  IF NOT SUCCESS: ERROR
ELSE
  $data = array()
  FOR EACH row IN RESULT
    $data[row[key_id]] = array( session_id => row[session_id], default_unit => row[default_unit])

  FOR EACH key, value IN $_GET
    $data_key = ""
    $value_key = ""
    IF FIND "userUnit" IN key
        $data_key = substitute("k" for "userUnit" in key)
        $value_key = "user_unit"
    IF FIND "userShortName" IN key
        $data_key = substitute("k" for "userShortName" in key)
        $value_key = "short_name"
    IF FIND "userFullName" IN key
        $data_key = substitute("k" for "userFullName" in key)
        $value_key = "full_name"
    ELSE
        ERROR
    
    IF $data[$data_key] IS NEW
        $data[$data_key] = array( session_id => $sessid )
    ELSE
        $data[$data_key][$value_key] = value
        
  CONVERT $data TO INDEXED ARRAY
  TRANSACTION:
    "INSERT INTO key_info (session_id, key_id, long_name, short_name, default_unit, user_unit) VALUES $data"
	"DELETE FROM key_info_temp WHERE session_id=$sessid"
  IF NOT SUCCESS: ERROR
 */

/*     d) "k": Session Query 4 - Log Data (No documentation created at this time). - Model on old code.
 */
 
// Return the response required by Torque
echo "OK!";

/*
 * EVERYTHING BELOW WILL NO LONGER FUNCTION
 *
 * DELETE OR COMMENT BEFORE TESTING
 */
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


?>
