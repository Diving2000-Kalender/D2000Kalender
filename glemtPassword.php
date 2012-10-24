<?php
include ("function.php");
include ("mysqlConfig.php");

@$username = $_REQUEST["username"];
@$subm = $_REQUEST["subm"];

if($username && $subm)
{
	
	$uds = resetPassword($username);
	
	//$uds = 
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


if(@$uds)
	echo "" . $uds . "<br /><br />";

?>


Har du glemt dit kodeord, kan du indtaste dit brugernavn og et nyt password til blive sendt til den e-mail, 
som du står registret med.
<br /><br />
<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post" name="myForm">


<div class="style_form">
  <label for="brugernavn">Brugernavn:</label>
  <input type="text" name="username" class="form_element">
</div>

 <div class="style_form">
 	<input type="hidden" value="1" name="subm" class="form_element">
   <input type="submit" value="Send"  class="form_element" />
</div>

<br />
</form>

<?php include("buttonMenu.php"); ?>

</body>
</html>