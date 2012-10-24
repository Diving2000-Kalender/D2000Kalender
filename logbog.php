<?php
require_once ("function.php");
require_once ("mysqlConfig.php");



sec_session_start();

if(login_check() == false)
{
	header('Location: ./login.php');	
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Logbog</title>
	<link rel="stylesheet" href="style.css" />
	<link rel="shortcut icon" href="favicon.ico">
	
</head>
<body>

<?php include ("menu.php"); ?>

<h3>Logbog</h3>

<br />
Tiløj dine log i din personlige logbog.
<br />
<a href="addDive.php">Tilføj et log</a>
<br /><br />

<br />

<table border="0" cellpadding="4" cellspacing="0" width="500px">
 <tr>
  <td><b>Dato</b></td>
  <td><b>Sted</b></td>
  <td><b>Bundtid</b></td>
  <td><b>Luftforbrug</b></td>
  <td><b>Dybde</b></td>
  <td><b>Makker</b></td>
 </tr>

<?php

$sql = "SELECT * FROM T_DiDiveProfile WHERE DiCustID = " . $_SESSION["eveID"] . " order by DiStartDate_N DESC";
	
	$row=odbc_exec(connectToDB(), $sql);
	
$count = 0;
$dybde = 0;
$bundtid = 0;
$maksBundtid = 0;
while(odbc_fetch_row($row))
{
	if($count % 2)
		echo "<tr>";
	else
		echo "<tr bgcolor=\"#184698\">";
		
	if ($maksBundtid < beregnBundtid(odbc_result($row, "DiStartDate_N"), odbc_result($row, "DiEndDate_N")))
		$maksBundtid = beregnBundtid(odbc_result($row, "DiStartDate_N"), odbc_result($row, "DiEndDate_N"));
	
	$dybde += str_replace(",",".", odbc_result($row, "DiMaxDepthTx_N"));
	$bundtid += beregnBundtid(odbc_result($row, "DiStartDate_N"), odbc_result($row, "DiEndDate_N"));
	
	echo "
	
	 <td><a href=\"viewDive.php?diveID=" . odbc_result($row, "DiDiveProfileID") . "\">" . convertDateForPrint(substr(odbc_result($row, "DiStartDate_N"),0,16)) . "</a></td>
	 <td>" . getLocationToDivelog(odbc_result($row, "DiDiveProfileID")) . "</td>
	 <td>" . beregnBundtid(odbc_result($row, "DiStartDate_N"), odbc_result($row, "DiEndDate_N")) . "</td>
	 <td>" . beregnLuftforbrug(odbc_result($row, "DiAirInTx_N"), odbc_result($row, "DiAirOutTx_N")) . "</td>
	 <td>" . odbc_result($row, "DiMaxDepthTx_N") . "</td>
	 <td>" . getMakkerToDivelog(odbc_result($row, "DiDiveProfileID")) . "</td>
	 
	 </tr>
	 ";
	
$count++;	
}
?>
</table>

<?php

	echo "<br /><br />";
	echo "	<font class=\"textInfo\">Statistik</font><br /><br />";

	echo "
	<div class=\"style_form\">
  		<label for=\"dyk\" style=\"width:160px\"><b>Antal dyk:</b></label>
  		<label for=\"dyk\">" . $count . "</label>
	</div>
	<br />";

	echo "
	<div class=\"style_form\">
  		<label for=\"dyk\" style=\"width:160px\"><b>Maks dybde:</b></label>
  		<label for=\"dyk\">" . getMaxDybde($_SESSION["eveID"]) . "</label>
	</div>
	<br />";
	
	if($count == 0)
	{
		$avgDybde = 0;
		$avgBundtid = 0;
	}
	else
	{
		$avgDybde = $dybde / $count;
		$avgBundtid = $bundtid / $count;	
	}
	echo "
	<div class=\"style_form\">
  		<label for=\"dyk\" style=\"width:160px\"><b>Gennemsnitlig dybde:</b></label>
  		<label for=\"dyk\">" . $avgDybde . "</label>
	</div>
	<br />";
	
	echo "
	<div class=\"style_form\">
  		<label for=\"dyk\" style=\"width:160px\"><b>Længste dyk:</b></label>
  		<label for=\"dyk\">" . $maksBundtid . "</label>
	</div>
	<br />";
	
	echo "
	<div class=\"style_form\">
  		<label for=\"dyk\" style=\"width:160px\"><b>Gennemsnitlig bundtid:</b></label>
  		<label for=\"dyk\">" . $avgBundtid . "</label>
	</div>";

	

include("buttonMenu.php"); 

?>

</body>
</html>