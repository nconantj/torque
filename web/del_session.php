<?php
require_once ('creds.php');
require_once ('db_functions.php');

session_start();

if (isset($_POST["deletesession"])) {
    $deletesession = preg_replace('/\D/', '', $_POST['deletesession']);
}
elseif (isset($_GET["deletesession"])) {
    $deletesession = preg_replace('/\D/', '', $_GET['deletesession']);
}

if (isset($deletesession) && !empty($deletesession)) {
    // Connect to Database
    $db = new DBAccess($db_host, $db_user, $db_pass, $db_name);

    //$delresult = mysqli_query($con, "DELETE FROM $db_table
    //                      WHERE session=$deletesession;") or die(mysqli_error());
	
	$delresult = $db->delete_data($db_table, "session=$deletesession");
}

?>
