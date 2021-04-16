<?php
require_once("./creds.php");

session_start();

if (isset($_POST["deletesession"])) {
    $deletesession = preg_replace('/\D/', '', $_POST['deletesession']);
}
elseif (isset($_GET["deletesession"])) {
    $deletesession = preg_replace('/\D/', '', $_GET['deletesession']);
}

if (isset($deletesession) && !empty($deletesession)) {
    // Connect to Database
    $con = mysqli_connect($db_host, $db_user, $db_pass, $db_name) or die(mysqli_error());

    $delresult = mysqli_query($con, "DELETE FROM $db_table
                          WHERE session=$deletesession;") or die(mysqli_error());

    mysqli_free_result($delresult);
    mysqli_close($con);
}

?>
