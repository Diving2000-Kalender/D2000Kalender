<?php
include ("function.php");
include ("mysqlConfig.php");

sec_session_start();

if(login_check() == true)
{
	header('Location: ./profil.php');	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Log på Mit Diving 2000</title>
<link rel="stylesheet" href="style.css" />
<link rel="shortcut icon" href="favicon.ico">

</head>
<body>

<?php include ("menu.php"); ?>

<br /><br />
<?php


if(@$_GET["error"] == 1)
{
	if ($_GET["error"] == "1_afventer")
		echo "<div class=\"fejl\">Kontoen mangler at blive aktiveret. Du har modtaget en mail, da du oprettede kontoen</div><br /><br />";	
	else if ($_GET["error"] == "1_deaktiveret")
		echo "<div class=\"fejl\">Din konto er deaktiveret. Kontakt Diving 2000 for yderligere</div><br /><br />";	
	else
		echo "<div class=\"fejl\">Brugernavn og adgangskode passer ikke sammen. Prøv igen</div><br /><br />";	
}


?>


Fra Mit Diving 2000 kan du se dine kommende ture og kurser, men også rediger dine oplysninger og din udstyrsprofil. 
<br /><br />
<form action="logMeIn.php" method="post" name="myForm">


<div class="style_form">
  <label for="brugernavn">Brugernavn:</label>
  <input type="text" name="username" class="form_element">
</div>

<div class="style_form">
  <label for="adgangskode">Adgangskode:</label>
  <input type="password" name="password" class="form_element">
</div>

<?php

if($_SERVER["REMOTE_ADDR"] == "212.130.94.12" || $_SERVER["REMOTE_ADDR"] == "212.130.93.57" || $_SERVER["REMOTE_ADDR"] == "2.104.236.186" || $_SERVER["REMOTE_ADDR"] == "127.0.0.1")
{
	echo "
	<div class=\"style_form\">
  <label for=\"adgangskode\">Log ind som:</label>
  " . getUserlist() . "
  
</div>
";	
	
}

?>

<div class="style_form">
  <label for="husk">Forbliv logget ind:</label>
  <input type="checkbox" name="husk" >
</div>



<input type="hidden" value="profil.php" name="urlOK" >
   <input type="hidden" value="login.php?error=1" name="urlError" >

 <div class="style_form">
   <input type="submit" value="Login"  class="form_element" />
</div>

<br />

<a href="glemtPassword.php">Glemt dit kodeord?</a>
</form>

<?php include("buttonMenu.php"); ?>

</body>
</html>