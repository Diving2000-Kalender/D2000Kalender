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
	<title>Mit Diving 2000</title>
	<link rel="stylesheet" href="style.css" />
	<link rel="shortcut icon" href="favicon.ico">
	
</head>
<body>

<?php include ("menu.php"); ?>

<h3>Velkommen til Mit Diving 2000</h3>

På Mit Diving 2000 har du en masse muligheder. Du kan bl.a se de kommende dykkerture og dykkerkurser, som du er tilmeldt. 
Du kan rette dine kontakt oplysninger og udstyrsprofil, så du let og hurtigt at tilmelde dig til dykkerture. 
<br /><br />

<?php 
function getMedlemsStatus2 ($eveID)
{
	$conn = connectToDB();
	$sql = "SELECT C4CustID, C4CustTypeID, C4LastUpdatedDate FROM T_C4CustCustType WHERE (C4CustID = " . $eveID . ") AND (C4CustTypeID = 10)";		
	$row=odbc_exec($conn, $sql);
	
	if(odbc_num_rows($row) == 1)
	{
		return "Du er ansat, så du er medlem";	
	}
	
	
	
	
	$sql = "select TOP 1 CfCardValidToDate_N from EVE.dbo.T_CfCustClub where CfClubID = 1 AND CfCustID = " . $eveID . "";		
	$row=odbc_exec($conn, $sql);
	
	$date = "";
	$expireDate = "1";
	
	
	while(odbc_fetch_row($row))
	{
		$date = odbc_result($row, "CfCardValidToDate_N");

	}
	
	if (strlen($date) > 8) //Er medlem eller har været
	{
		$expireDate = substr($date, 0, 10);
		
		$dateDiff = dateDiff(date("Y-m-d"), $expireDate);
		
		if ($dateDiff < 30 && $dateDiff > 0)
			return "<div class=\"klubExpire\">JA! Medlemskabet udløber den " . convertDateForPrint($expireDate) . " (Om " . $dateDiff . " dage). <a href=\"http://www.shop-diving2000.dk/group.asp?group=127\">Forlæng dit medlemsskab her</a></div>";
		if($dateDiff > 0)
			return "JA! Medlemskabet udløber den " . convertDateForPrint($expireDate) . " (Om " . $dateDiff . " dage)";
		else
			return "Du er ikke medlem. <a href=\"http://www.shop-diving2000.dk/product.asp?product=5897\">Køb dit medlemsskab her.</a>";
		
	}
	else
		return "Du er ikke medlem. <a href=\"http://www.shop-diving2000.dk/product.asp?product=5897\">Køb dit medlemsskab her.</a>";
	
	
}





echo "Medlemsstatus: " .  getMedlemsStatus($_SESSION["eveID"]) . "<br /><br /><br />";



echo "
<table width=\"900\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">
 <tr>
  <td width=\"25%\" style=\"border-right: 2px solid black;\"><b><u><font size=\"3\">Dine kommende ture</font></u></b></td>
  
  <td width=\"25%\" style=\"border-right: 2px solid black;\"><b><u><font size=\"3\">Tidligere ture</font></u></b></td>
  <td width=\"25%\"><b><u><font size=\"3\">Certifikater*</font></u></b></td>
 </tr>
 
 <tr>
  <td style=\"border-right: 2px solid black; vertical-align: top;\">" . getUpcomingTrips($_SESSION["eveID"]) . "<br /><br /><b><u><font size=\"3\">Kommende kurser:</font></u></b><br />" . getUpcommingCourses($_SESSION["eveID"]) . "</td>
  
  <td style=\"border-right: 2px solid black; vertical-align: top;\">" . getLastTrips($_SESSION["eveID"], 10) . "</td>
  <td style=\"vertical-align: top;\">" . getCertification($_SESSION["eveID"], 10) . "</td>
 </tr>";

?>

</table>
<?php

if(isDM($_SESSION["eveID"]) || isAdmin($_SESSION["eveID"]))
{
	echo "

<span class=\"textInfo\">Staff</span><br />
<table width=\"600\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">

 <tr>
  <td style=\"border-right: 2px solid black; vertical-align: top; width: 50%\">" . getUpcomingTripsStaff($_SESSION["eveID"]) . "</td>
  <td style=\"border-right: 2px solid black; vertical-align: top; width: 50%;\">" . getUpcommingCoursesStaff($_SESSION["eveID"]) . "</td>
  
 </tr>";
}

?>


</table>
<br /><br />
<i>* Bemærk at selvom dit certifikatnummer er listet, så kan der gå op til 4 uger inden du vil modtage dit certifikat.<br />
Ikke alle certifikatnummer kan vises i listen.</i>


</body>
</html>