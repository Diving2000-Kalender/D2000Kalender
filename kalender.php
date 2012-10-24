<?php
require_once ("function.php");
require_once ("mysqlConfig.php");

sec_session_start();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Kalender</title>
	<link rel="stylesheet" href="style.css" />
	<link rel="shortcut icon" href="favicon.ico">

	
</head>
<body>

<?php 

include ("menu.php");


@ $month = $_GET['month'];
@ $year = $_GET['year'];

if (!isset($month))
{
	$month = date("n");
	$year = date("Y");
}	

$id = 23;
$days = 44;

if (@$_GET["trip"] == "true")
	echo "<div class=\"godkendt\">Din tilmelding er registret</div><br /><br />";
if (@$_GET["create"] == "true")
	echo "<div class=\"godkendt\">Din profil blev aktiveret</div><br /><br />";
if (@$_GET["create"] == "false")
	echo "<div class=\"fejl\">Din profil kunne ikke aktiveres. Prøv at kopier linket i stedet.</div><br /><br />";
if (@$_GET["create"] == "false_1")
	echo "<div class=\"fejl\">Kontoen er blevet aktiveret</div><br /><br />";
if (@$_GET["create"] == "false_2")
	echo "<div class=\"fejl\">Kontoen er blevet deaktiveret. Kontakt Diving 2000</div><br /><br />";
	
	
	
	
echo generate_calendar($id, $year, $month, $days);

echo "<br /><br />";

echo "<h3>Vi har fået nyt tilmeldingssystem</h3>
Vi har skiftet vores gamle tilmeldingssystem ud med et nyt, som gør det lettere for dig og giver dig nogle flere muligheder.
Det nye system snakker også sammen med vores interne systemer, så du kan skifte mail, telefonnummer, udstyrsprofil mv.<br />
<br />
<b>Første gang du skal bruge det nye system</b>, skal du oprette en konto. Dette skal kun gøres første gang.<br /><br />";

echo "Log på <a href=\"login.php\">Mit Diving 2000</a><br />";
echo "Endnu ikke oprettet, så gør det nu. <a href=\"newUser.php\">Du kan oprette dig her - det tager under 1 minut</a>";


include("buttonMenu.php");
?>

</body>

</html>

