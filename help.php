<?php

require_once ("function.php");
require_once ("mysqlConfig.php");
sec_session_start();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Diving 2000 - Hjælp</title>
	<link rel="stylesheet" href="style.css" />
	<link rel="shortcut icon" href="favicon.ico">
	
</head>
<body>

<h1>Hjælp</h1>
<br /><br />



<b>Profil.php</b>
<a name="profil.php"></a>
<br />
<b>Der står jeg ikke medlem af Odense Dykkerklub, men det er jeg?</b><br />
Hvis dette skulle være tilfælde så er det beklageligt, men det skyldes at de oplysninger som vi har på dig 
ikke stemmer over ens med dem som du har opgivet. Men det kan vi hurtigt rette. Du bedes venligst sende en mail 
til <a href="mailto: info@diving2000.dk">info@diving2000.dk</a> hvor du beskriver problemet og venligst skrive 
dit medlemsnummer. Så får vi rettet det.

<br />



<b>Opret bruger</b>
<a name="newUser.php"></a>
<br />
<b>Hvorfor skal jeg have en bruger?</b> <br />
Det er nødvendigt at have en bruger for at kunne booke ture. Med brugeren slipper man for at skulle 
indtaste kontakt oplysninger og udstyrsprofil hver gang man booker en tur. <br />
Det er gratis at oprette en bruger at tage og tager mindre end et minut. Du kan oprette din <a href="newUser.php">bruger her</a>.
<br /><br />


<?php include("buttonMenu.php"); ?>

</body>
</html>