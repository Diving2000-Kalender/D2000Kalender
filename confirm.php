<?php
require_once ("mysqlConfig.php");

@$eveID = $_GET["eID"];
@$auth = $_GET["auth"];

if (!$eveID && !$auth)
	header('Location: ./kalender.php');

$data = mysql_query("select * from users where `eveID` = " . $eveID . "") or die(mysql_error());
while($row = mysql_fetch_array($data))
{
	$status = $row["status"];
	
}


if ($status == "aktiv")
	header('Location: ./kalender.php?create=false_1');
else if ($status == "deaktiveret")
	header('Location: ./kalender.php?create=false_2');
else
{
	$insertSQL = "UPDATE `users` SET `status`='aktiv' WHERE `eveID`=" . $eveID . " AND activationKey='" . $auth . "'";
	$out = mysql_query($insertSQL) or die(mysql_error());
	
	
	if($out == 1)
		header('Location: ./kalender.php?create=true');
	else
		header('Location: ./kalender.php?create=false');
	
}


?>