<?php
require_once ("function.php");
require_once ("mysqlConfig.php");
@$tripID = $_GET["tripID"];


sec_session_start();

if(login_check() == false)
{
	header('Location: ./login.php');	
}


@$maske = $_REQUEST["maske"];
@$tank = $_REQUEST["tank"];
@$finner = $_REQUEST["finner"];
@$dragt = $_REQUEST["dragt"];

@$handsker = $_REQUEST["handsker"];
@$boots = $_REQUEST["boots"];

@$bly = $_REQUEST["bly"];
@$regulator = $_REQUEST["regulator"];
@$bcd = $_REQUEST["bcd"];

@$place = $_REQUEST["place"];
@$save = $_REQUEST["save"];

if($save)
{
//Fjerner alt tidligere udstyr
	resetEquipmentProfil($_SESSION["eveID"]);
/////////////////    Indsætter det nye   ///////////////////////


	updateEquipment(1008, $tank, $_SESSION["eveID"]); //tank
	updateEquipment(1037, $bcd, $_SESSION["eveID"]); //bcd
	updateEquipment(1002, $bly, $_SESSION["eveID"]); //bly
	updateEquipment(1045, $finner, $_SESSION["eveID"]); //finner
	updateEquipment(1001, $handsker, $_SESSION["eveID"]); //handsker
	
	if ($maske)
		updateEquipment(1046, "0", $_SESSION["eveID"]); //maske
	
	if ($regulator)
		updateEquipment(1038, "0", $_SESSION["eveID"]); //reg
	
	updateEquipment(1000, $boots, $_SESSION["eveID"]); //boots
	updateEquipment(1034, $dragt, $_SESSION["eveID"]); //dragt

	createLog(4, $_SESSION["eveID"]);

	$uds = "<div class=\"godkendt\">Din profil blev opdateret</div>";
}




?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Udstyrsprofil</title>
	<link rel="stylesheet" href="style.css" >
	<link rel="shortcut icon" href="favicon.ico">
	
</head>
<body>

<?php include ("menu.php"); ?>

<h3>Leje af udstyr</h3>

<br />
<br />
Når du tilmelder dig en tur, så vil din udstyrsprofil automatisk sendt med din bestilling. Det er derfor vigtigt at, 
du holder din profil opdateret - både hvad angå størrelser og hvilket udstyr du behøver. 
<br /><br />
<?php



if($save)
	echo "" . $uds . "<br /><br />";

?>

<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post" name="myForm">




<table width="500">
 <tr>
  <td style="border-bottom: 1px solid #000000;">Maske/snorkel</td>
  <td style="border-bottom: 1px solid #000000;"><input type="checkbox" value="1" name="maske" <?php echo "" . getEquipmentProfile($_SESSION["eveID"], "maske") . ""; ?>></td>
  <td style="border-bottom: 1px solid #000000;">15 Kr. (10 kr.)</td>
 </tr>
 <tr>
  <td style="border-bottom: 1px solid #000000;">Tank inkl. luft</td>
  <td style="border-bottom: 1px solid #000000;">
  <?php
  	echo "" . buildDropDown("tank", 1008, "tank", $_SESSION["eveID"]) . ""; ?>
  </td>
  
  <td style="border-bottom: 1px solid #000000;">50 Kr. (0 kr.)</td>
  
 </tr>
 <tr>
  <td style="border-bottom: 1px solid #000000;">Finner</td>
  <td style="border-bottom: 1px solid #000000;">
  <?php
  	echo "" . buildDropDown("finner", 1045, "finner", $_SESSION["eveID"]) . ""; ?>
	
  </td>
  <td style="border-bottom: 1px solid #000000;">15 Kr. (10 kr.)</td>
 </tr>
 <tr>
   <td style="border-bottom: 1px solid #000000;">Dragt</td>
   <td style="border-bottom: 1px solid #000000;">
    
	<?php
	echo "" . buildDropDown("dragt", 1034, "dragt", $_SESSION["eveID"]) . ""; 
	?>
	
	
   </td>
   <td style="border-bottom: 1px solid #000000;">110 Kr. (80 kr.)</td>
  </tr>
  <tr>
   <td style="border-bottom: 1px solid #000000;">Handsker</td>
   <td style="border-bottom: 1px solid #000000;">

	<?php
	echo "" . buildDropDown("handsker", 1001, "handsker", $_SESSION["eveID"]) . ""; 
	?>
	
   </td>
   <td style="border-bottom: 1px solid #000000;">6 Kr. (5 kr.)</td>
  </tr>
  <tr>
   <td style="border-bottom: 1px solid #000000;">Støvler</td>
   <td style="border-bottom: 1px solid #000000;">

	<?php
	echo "" . buildDropDown("boots", 1000, "boots", $_SESSION["eveID"]) . ""; 
	?>

	</td> 
   <td style="border-bottom: 1px solid #000000;">6 Kr. (5 kr.)</td>
  </tr>
  <tr>
   <td style="border-bottom: 1px solid #000000;">Bly</td>
   <td style="border-bottom: 1px solid #000000;">
   
    <?php
	echo "" . buildDropDown("bly", 1002, "bly", $_SESSION["eveID"]) . ""; 
	?>
   
   </td>
   <td style="border-bottom: 1px solid #000000;">15 Kr. (10 kr.)</td>
  </tr>
  <tr>
   <td style="border-bottom: 1px solid #000000;">Regulator</td>
   <td style="border-bottom: 1px solid #000000;"><input type="checkbox" value="1" name="regulator" <?php echo "" . getEquipmentProfile($_SESSION["eveID"], "reg") . ""; ?>></td>
   <td style="border-bottom: 1px solid #000000;">120 Kr. (90 kr.)</td>
  </tr>
  <tr>
   <td style="border-bottom: 1px solid #000000;">BCD</td>
   <td style="border-bottom: 1px solid #000000;">

	<?php
	echo "" . buildDropDown("bcd", 1037, "bcd", $_SESSION["eveID"]) . ""; 
	?>
	
   </td>
   <td style="border-bottom: 1px solid #000000;">120 Kr. (90 kr.)</td>
  </tr>
  
  <tr>
   <td colspan="2" align="left">
   <input type="hidden" value="1" name="save">
   <input type="submit" value="Opdater profil" ></td>
      
  </tr>
</table>

</form>

<?php include("buttonMenu.php"); ?>

</body>
</html>