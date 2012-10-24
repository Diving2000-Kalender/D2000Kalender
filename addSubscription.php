<?php
require_once ("function.php");
require_once ("mysqlConfig.php");


sec_session_start();

if(login_check() == false)
{
	header('Location: ./login.php');	
}

	$url = "http://diving2000.ithansen.net/ubivox/addSubscription.php?mail=" . getMailFromEVE($_SESSION["eveID"]) . "";
	$fp = fopen($url, 'r');
	
	$content = "";
	while($l = fread($fp, 1024))
		$content .= $l;
	fclose($fp);
	
	header('Location: ./editUserData.php');


?>