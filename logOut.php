<?php
include ("function.php");
include ("mysqlConfig.php");
sec_session_start();

//createLog(16, $_SESSION["eveID"]);

// Unset all session values
$_SESSION = array();
// get session parameters 
$params = session_get_cookie_params();
// Delete the actual cookie.
setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
setcookie("auth_key", "", time() - 3600);


// Destroy session
session_destroy();
header('Location: ./kalender.php');
?>