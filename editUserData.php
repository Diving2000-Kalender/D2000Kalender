<?php
require_once ("function.php");
require_once ("mysqlConfig.php");


sec_session_start();

if(login_check() == false)
{
	header('Location: ./login.php');	
}


if(@$_REQUEST["subm"])
{
	
	if(@$_REQUEST["passwordChange"])
	{
		if(@$_REQUEST["password"] == @$_REQUEST["password2"] && strlen(@$_REQUEST["password"]) > 5 )
		{
			$passwordEn = encryption($_REQUEST["password"]);
			
			$insertSQL = "UPDATE `users` SET `password`='" . md5($passwordEn) . "' WHERE `eveID`=" . $_SESSION["eveID"] . " LIMIT 1";
			mysql_query($insertSQL) or die(mysql_error());	
			
			createLog(10, $_SESSION["eveID"]);
			
			$uds = "<div class=\"godkendt\">Din adgangskode blev ændret</div>";
		}
		else
			$uds = "<div class=\"fejl\">Adgangskoden passer ikke sammen eller er kortere end 6 tegn. Ingen ændre fortaget</div>";
	
		
	}
	if(@$_REQUEST["update"])
	{
		if(checkEmailAddress($_REQUEST["mail"]))
		{
			if(isZipCode($_REQUEST["postnr"]) && isZipCode($_REQUEST["postnrE"]))
			{
				if(isTelephone($_REQUEST["mobil"]) && isTelephone($_REQUEST["mobilE"]) && isTelephone($_REQUEST["fastnet"]))
				{
					
					$conn = connectToDB();
					$sql = "UPDATE [eve].[dbo].[T_CuCust] SET " . "CuEMailTx_N = '" . mysql_real_escape_string($_REQUEST["mail"]) . "', " . "CuTelMobileTx_N = '" . mysql_real_escape_string($_REQUEST["mobil"]) . "', " . "CuTelHomeTx_N = '" . mysql_real_escape_string($_REQUEST["fastnet"]) . "', " . "CuAddress1Tx_N = '" . mysql_real_escape_string($_REQUEST["adresse"]) . "', " . "CuPostcodeTx_N = '" . mysql_real_escape_string($_REQUEST["postnr"]) . "', " . "CuAddress3Tx_N = '" . mysql_real_escape_string($_REQUEST["by"]) . "', " . "CuNOKNameTx_N = '" . mysql_real_escape_string($_REQUEST["navnE"]) . "', " . "CuNOKTelTx_N = '" . mysql_real_escape_string($_REQUEST["mobilE"]) . "', " . "CuNOKRelationshipTx_N = '" . mysql_real_escape_string($_REQUEST["forholdE"]) . "', " . "CuNOKAddress1Tx_N = '" . mysql_real_escape_string($_REQUEST["adresseE"]) . "', " . "CuNOKPostcodeTx_N = '" . mysql_real_escape_string($_REQUEST["postnrE"]) . "', " . "CuNOKAddress3Tx_N = '" . mysql_real_escape_string($_REQUEST["byE"]) . "' " . "WHERE CuCustID = " . $_SESSION["eveID"] . ";";
					
					$row = odbc_exec($conn, $sql);
					
					createLog(3, $_SESSION["eveID"]);
					
					$uds = "<div class=\"godkendt\">Dine stamdata blev opdateret</div>";
				}
				else
					$uds = "<div class=\"fejl\">Et af telefonnummerne er ugyldige. Undlad +45 eller 0045 foran nummeret</div>";
			}
			else
				$uds = "<div class=\"fejl\">Postnr. er ugyldigt</div>";
		}
		else
			$uds = "<div class=\"fejl\">Mailadressen er ugyldig</div>";
	}
	
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Mit Diving 2000 - Profil</title>
	<link rel="stylesheet" href="style.css" />
	<link rel="shortcut icon" href="favicon.ico">
	
	
<script language="javascript">
function checkEmail() {
var email = document.getElementById('mail');
var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
if (!filter.test(email.value)) {
alert('Please provide a valid email address');
email.focus;
return false;
}
}
</script>
	
	
</head>
<body>

<?php include ("menu.php"); ?>

<h3>Min profil</h3>
<br /><br />
Her kan du rette dine stamdata og ændre din adgangskode til systemet. 
<br /><br />



<?php

if(@$uds)
	echo "" . $uds . "<br /><br />";

?>


<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post" name="myForm">

<table border="0" class="text">
 <tr>
  <td valign="top">
<b>Kontakt oplysninger</b>
<br /><br />
	<div class="style_form">
	  <label for="mail">Mail:</label>
	  <input type="text" name="mail" class="form_element" value="<?php echo "" . getMailFromEVE($_SESSION["eveID"]) . ""; ?>">
	</div>

	<div class="style_form">
	  <label for="nyhedsbrev">Nyhedsbrev:</label>
	  <label for="nyhedsbrev" style="height:40px; width: 221px; border:0;padding:4px 8px; margin-bottom:0px;"><?php echo "" . getUbivoxStatus(getMailFromEVE($_SESSION["eveID"])) . ""; ?></label>
	  
	</div>

	
		<div class="style_form">
	  <label for="klub">Medlem af Odense Dykkerklub:</label>
	  <label for="nyhedsbrev" style="height:40px; width: 221px; border:0;padding:4px 8px; margin-bottom:0px;"><?php echo "" .  getMedlemsStatus($_SESSION["eveID"]) . ""; ?></label>
	  
	</div>

	



	


	<div class="style_form">
	  <label for="mobil">Mobil:</label>
	  <input type="text" name="mobil" class="form_element" value="<?php echo "" . stripPhoneNumber(getMobileFromEVE($_SESSION["eveID"])) . ""; ?>">
	</div>

	<div class="style_form">
	  <label for="fastnet">Fastnet:</label>
	  <input type="text" name="fastnet" class="form_element" value="<?php echo "" . stripPhoneNumber(getFastnetFromEVE($_SESSION["eveID"])) . ""; ?>">
	</div>


	<div class="style_form">
	  <label for="Adresse">Adresse:</label>
	  <input type="text" name="adresse" class="form_element" value="<?php echo "" . getAdresseFromEVE($_SESSION["eveID"]) . ""; ?>">
	</div>

	<div class="style_form">
	  <label for="postnr">Postnr.:</label>
	  <input type="text" name="postnr" class="form_element" value="<?php echo "" . getPostnrFromEVE($_SESSION["eveID"]) . ""; ?>">
	</div>


	<div class="style_form">
	  <label for="by">By:</label>
	  <input type="text" name="by" class="form_element" value="<?php echo "" . getByFromEVE($_SESSION["eveID"]) . ""; ?>">
	</div>
  
  
  </td>
  <td width="20px">&nbsp;</td>
  <td valign="top">

<b>Ændre adgangskode</b>
<br /><br />
  
  
  	<div class="style_form">
	  <label for="pass">Adgangskode:</label>
	  <input type="password" name="password" class="form_element">
	</div>


  	<div class="style_form">
	  <label for="pass2">Gentag adgangskode:</label>
	  <input type="password" name="password2" class="form_element">
	</div>


<div class="style_form">
  <input type="hidden" name="subm" value="1" />
  <input type="submit" name="passwordChange" value="Opdater adgangskode"  class="form_element" />
</div>

  
  
  
  
  
  </td>
  </tr>
</table>


<br /><br />
<b>Emergency Contact</b>
<br />

<div class="style_form">
  <label for="nav">Navn:</label>
  <input type="text" name="navnE" value="<?php echo "" . getANameFromEVE($_SESSION["eveID"]) . ""; ?>" class="form_element">
</div>

<div class="style_form">
  <label for="mobilE">Mobil:</label>
  <input type="text" name="mobilE" value="<?php echo "" . stripPhoneNumber(getAMobileFromEVE($_SESSION["eveID"])) . ""; ?>" class="form_element">
</div>

<div class="style_form">
  <label for="forhold">Forhold:</label>
  <input type="text" name="forholdE" value="<?php echo "" . getAForholdFromEVE($_SESSION["eveID"]) . ""; ?>" class="form_element">
</div>

<div class="style_form">
  <label for="adresseE">Adresse:</label>
  <input type="text" name="adresseE" value="<?php echo "" . getAAdresseFromEVE($_SESSION["eveID"]) . ""; ?>" class="form_element">
</div>

<div class="style_form">
  <label for="postnr">Postnr.:</label>
  <input type="text" name="postnrE" value="<?php echo "" . getAPostnrFromEVE($_SESSION["eveID"]) . ""; ?>" class="form_element">
</div>


<div class="style_form">
  <label for="by">By:</label>
  <input type="text" name="byE" value="<?php echo "" . getAByFromEVE($_SESSION["eveID"]) . ""; ?>" class="form_element">
</div>

<div class="style_form">
  <input type="hidden" name="subm" value="1" />
  <input type="submit" name="update" value="Opdater"  class="form_element" />
</div>


</form>

<?php include("buttonMenu.php"); ?>

</body>
</html>