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
Du kan rette dine kontakt oplysninger og udstyrsprofil, så du let og hurtigt kan tilmelde dig til dykkerture og dykkerkurser
<br /><br />

<?php 

echo "Medlemsstatus: " .  getMedlemsStatus($_SESSION["eveID"]) . "<br /><br /><br />";



echo "
<table width=\"950\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">
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
<br /><br />
<i>* Bemærk at selvom dit certifikatnummer er listet, så kan der gå op til 4 uger inden du vil modtage dit certifikat.<br />
Ikke alle certifikatnummer kan vises i listen.</i>


<?php include("buttonMenu.php"); ?>

</body>
</html>