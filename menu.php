<?php
require_once ("function.php");
require_once ("mysqlConfig.php");
?>

<style type="text/css">
body {
padding: 0;

margin-left: 10px;
margin-right: 10px;
}

#navigation {
position: fixed;
top: 0;
width: 98%;
color: #ffffff;
height: 25px;
text-align: center;
padding-top: 10px;
/* Adds shadow to the bottom of the bar */
-webkit-box-shadow: 0px 0px 8px 0px #000000;
-moz-box-shadow: 0px 0px 8px 0px #000000;
box-shadow: 0px 0px 8px 0px #000000;
/* Adds the transparent background */
background-color: rgba(17, 80, 177, 0.8);
color: rgba(30, 90, 1, 0.8);
}
#navigation a {
font-size: 12px;
padding-left: 15px;
padding-right: 15px;
color: white;
text-decoration: none;
}

#navigation a:hover {
color: grey;
} 
</style>
 
 
 <div id="navigation">
   <a href="/kalender.php">| Kalender |</a>
   <a href="/profil.php">| Mit Diving 2000 |</a>
   
   
<?php



if(login_check() == true)
{
	echo "
	<a href=\"/editUserData.php\">| Profil |</a>
	<a href=\"/equipmentProfil.php\">| Udstyrsprofil |</a>
	<a href=\"/logbog.php\">| Logbog |</a>
	";
	
	
	if(isAdmin($_SESSION["eveID"]))
	{
		echo "
		<a href=\"" . createLink("indstillinger.php") . "\">| Indstillinger |</a>";
		
		
		//<a href=\"" . createLink("userList.php") . "\">| Rediger bruger |</a>
		//<a href=\"" . createLink("turBeskrivelse.php") . "\">| Turbeskrivelse |</a>
		echo "
		<a href=\"" . createLink("hitliste.php") . "\">| Hitlisten |</a>
		<a href=\"" . createLink("EVE_kalender.pdf") . "\">| Manual |</a>
		";		
	}
	
	
	echo "
	<a href=\"/logOut.php\">| Logud |</a>";
}
else
{
	echo "
	<a href=\"newUser.php\">| Opret profil |</a>
	";
	
}
/*
 
	$currentFile = $_SERVER["PHP_SELF"];
	$parts = Explode('/', $currentFile);
	
	echo "<a href=\"help.php#" . $parts[count($parts) - 1] . "\">Hjælp</a>";
 */
 
 
 ?>
 </div>
 <br /><br /><br /><br />