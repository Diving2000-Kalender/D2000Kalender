<?php
require_once ("function.php");
require_once ("mysqlConfig.php");
@$tripID = $_GET["tripID"];


sec_session_start();

if(login_check() == false)
{
	header('Location: ./info.php?tripID=' . $tripID . '');	
}

$conn = connectToDB();




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
	<title>Diving 2000 turtilmelding</title>
	<link rel="stylesheet" href="style.css" />
	<link rel="shortcut icon" href="favicon.ico">
	
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" /> 
</head>


<body>

<?php include ("menu.php"); ?>

<?php
echo "<h2>Udstyrs profil</h2>";
echo "I forbindelse med din tilmelding bliver der automatisk booket det udstyr, som er valgt i din profil. Hvis du ønsker at 
ønsker at ændre dette, så kan det ske på din <a href=\"equipmentProfil.php\">profilside</a>.<br /><br />";




//Tjek om personen allerede er tilmeldt turen - hvis han er så skal tilmelding formen ikke vises
//select COUNT(*) as 'Antal' from EVE.dbo.T_CpCustTrip WHERE CpCustID = " + customerID + " AND CpTripID = " + tripID + "";

if (!isEnrolled($_SESSION["eveID"], $tripID))
{
?>



<form action="bookMeIn.php" method="post" name="myForm">

<div class="style_form">
  <label for="navn">Navn:</label>
  <label for="dbNavn"><?php echo "" . getNameFromEVE($_SESSION["eveID"]) . ""; ?></label>
</div>
<br />

<div class="style_form">
  <label for="tlf">Telefon:</label>
 <label for="dbTLF"><?php echo "" . getMobileFromEVE($_SESSION["eveID"]) . ""; ?></label>
</div>
<br />

<div class="style_form">
  <label for="meetingPlace">Mødested</label>
  <select name="sted">
    <option value="turSted">Ved turens mødested</option>
	<option value="butik">Ved Diving 2000</option>
  </select>
</div>
<br />

<div class="style_form">
  <label for="klub">Klub medlem?</label>
  <label for="klub2" style="width: auto;"><?php echo "" . getMedlemsStatus($_SESSION["eveID"]) . ""; ?></label>
</div> 
<br />
<br /><br /><br />

<h3>Leje af udstyr</h3>


<table width="700">
 <tr>
  <td style="border-bottom: 1px solid #000000;">Maske/snorkel</td>
  <td style="border-bottom: 1px solid #000000;"><input type="checkbox" value="1" name="maske" <?php echo "" . getEquipmentProfile($_SESSION["eveID"], "maske") . ""; ?>></td>
  <td style="border-bottom: 1px solid #000000;"><?php echo "" . getEquipmentPrice("Maske", getMedlemsStatusBool($_SESSION["eveID"])) . " kr."; ?></td>
 </tr>
 <tr>
  <td style="border-bottom: 1px solid #000000;">Tank inkl. luft</td>
  <td style="border-bottom: 1px solid #000000;">
  <?php
  	echo "" . buildDropDown("tank", 1008, "tank", $_SESSION["eveID"]) . ""; ?>
  
  </td>
  <td style="border-bottom: 1px solid #000000;"><?php echo "" . getEquipmentPrice("Tank", getMedlemsStatusBool($_SESSION["eveID"])) . " kr."; ?></td>
  <td>
  Ønsker ekstra tank* <input type="checkbox" name="ekstraTank">
  </td>
  
 </tr>
 <tr>
  <td style="border-bottom: 1px solid #000000;">Finner</td>
  <td style="border-bottom: 1px solid #000000;">
  <?php
  	echo "" . buildDropDown("finner", 1045, "finner", $_SESSION["eveID"]) . ""; ?>
	
  </td>
  <td style="border-bottom: 1px solid #000000;"><?php echo "" . getEquipmentPrice("Finner", getMedlemsStatusBool($_SESSION["eveID"])) . " kr."; ?></td>
 </tr>
 <tr>
   <td style="border-bottom: 1px solid #000000;">Dragt</td>
   <td style="border-bottom: 1px solid #000000;">
    
	<?php
	echo "" . buildDropDown("dragt", 1034, "dragt", $_SESSION["eveID"]) . ""; 
	?>
	
	
   </td>
   <td style="border-bottom: 1px solid #000000;"><?php echo "" . getEquipmentPrice("Dragt", getMedlemsStatusBool($_SESSION["eveID"])) . " kr."; ?></td>
  </tr>
  <tr>
   <td style="border-bottom: 1px solid #000000;">Handsker</td>
   <td style="border-bottom: 1px solid #000000;">

	<?php
	echo "" . buildDropDown("handsker", 1001, "handsker", $_SESSION["eveID"]) . ""; 
	?>
	
   </td>
   <td style="border-bottom: 1px solid #000000;"><?php echo "" . getEquipmentPrice("Handsker", getMedlemsStatusBool($_SESSION["eveID"])) . " kr."; ?></td>
  </tr>
  <tr>
   <td style="border-bottom: 1px solid #000000;">Støvler</td>
   <td style="border-bottom: 1px solid #000000;">

	<?php
	echo "" . buildDropDown("boots", 1000, "boots", $_SESSION["eveID"]) . ""; 
	?>

	</td> 
   <td style="border-bottom: 1px solid #000000;"><?php echo "" . getEquipmentPrice("Boots", getMedlemsStatusBool($_SESSION["eveID"])) . " kr."; ?></td>
  </tr>
  <tr>
   <td style="border-bottom: 1px solid #000000;">Bly</td>
   <td style="border-bottom: 1px solid #000000;">
   
    <?php
	echo "" . buildDropDown("bly", 1002, "bly", $_SESSION["eveID"]) . ""; 
	?>
   
   </td>
   <td style="border-bottom: 1px solid #000000;"><?php echo "" . getEquipmentPrice("Bly", getMedlemsStatusBool($_SESSION["eveID"])) . " kr."; ?></td>
  </tr>
  <tr>
   <td style="border-bottom: 1px solid #000000;">Regulator</td>
   <td style="border-bottom: 1px solid #000000;"><input type="checkbox" value="1" name="regulator" <?php echo "" . getEquipmentProfile($_SESSION["eveID"], "reg") . ""; ?>></td>
   <td style="border-bottom: 1px solid #000000;"><?php echo "" . getEquipmentPrice("Regulator", getMedlemsStatusBool($_SESSION["eveID"])) . " kr."; ?></td>
  </tr>
  <tr>
   <td style="border-bottom: 1px solid #000000;">BCD</td>
   <td style="border-bottom: 1px solid #000000;">

	<?php
	echo "" . buildDropDown("bcd", 1037, "bcd", $_SESSION["eveID"]) . ""; 
	?>
	
   </td>
   <td style="border-bottom: 1px solid #000000;"><?php echo "" . getEquipmentPrice("BCD", getMedlemsStatusBool($_SESSION["eveID"])) . " kr."; ?></td>
  </tr>
  
  <tr>
   <td style="border-bottom: 1px solid #000000;">Tilbehør</td>
   <td style="border-bottom: 1px solid #000000;">

	<?php
	echo "" . buildDropDown("Tilbehør", 1214, "Tilbehør", $_SESSION["eveID"]) . ""; 
	?>
	
   </td>
   <td style="border-bottom: 1px solid #000000;"></td>
  </tr>

  
  
  <tr>
   <td colspan="2" align="left">
   <input type="hidden" name="tripID" value="<?php echo "" . $tripID . ""; ?>">
   <input type="submit" value="Tilmeld" ></td>
      
  </tr>
</table>
<br />
<b>*</b>Leje af ekstra tank koster 100kr for ikke medlemmer og 90kr for medlemmer. Tanken skal afleves i Diving 2000 seneste 
dagen efter. 


</form>
<?php
}
else
	echo "<div class=\"fejl\">Du er allerede tilmeldt denne tur. Er du forhindret i at deltage eller ønsker du at rette i det udstyr du skal leje. Bedes du kontakte butikken på telefon 6613 0049</div>";
?>


<?php include("buttonMenu.php"); ?>

</body>
</html>