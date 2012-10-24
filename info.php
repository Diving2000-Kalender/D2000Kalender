<?php

require_once ("function.php");
require_once ("mysqlConfig.php");
sec_session_start();

$conn = connectToDB();


@$tripID = $_GET["tripID"];

if ($tripID == "")
	header('Location: ./kalender.php');	
	

$sql="SELECT * FROM EVE.dbo.T_TpTrip WHERE TpTripID = " . $tripID . "";

$row=odbc_exec($conn, $sql);
while(odbc_fetch_row($row))
{
	
	$destinationID = odbc_result($row, "TpDestinationID_N");
	$maksDeltager = odbc_result($row, "TpMaxNoIn");
	$sted = getDestinationByID(odbc_result($row,"TpDestinationID_N"));
	$Dato = odbc_result($row,"TpStartDate_N");
	$endDate = odbc_result($row,"TpEndDate_N");
	$Pris = getPrice(odbc_result($row,"TpPrivateNotesTx_N"));
	$beskrivelse = getDescription(odbc_result($row, "TpDestinationID_N"));
	
	$beskrivelse .= "<br /><br />Vi kører fra Diving 2000 kl. " . getTimeAtDiving2000(odbc_result($row, "TpStartDate_N"), odbc_result($row, "TpArrivalMinsIn")) . "<br />";
	$beskrivelse .= "Kører du selv, så mødes vi på stedet kl. " . substr(odbc_result($row, "TpStartDate_N"), 11,5) . "<br /><br />";
	
	@$moreInfo = odbc_result($row, "TpMoreInfoTx_N");
	
	$status = odbc_result($row,"TpTripStatusID");
}
	
odbc_close($conn); 




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
	<title>Dykkertur med Diving 2000</title>
	<link rel="stylesheet" href="style.css" />
	<link rel="shortcut icon" href="favicon.ico">
	
<meta name="description" content="Jeg har lige tilmeldt mig denne dykkertur hos Diving 2000. Skal du ikke med?" />
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" /> 
<meta property="og:title" content="Dykkertur til <?php echo "" . $sted . ""; ?> med Diving 2000"/>
<meta property="og:image" content="http://diving2000.dk/data/media/diving2000logo5.png"/>
<meta property="og:description" content="Jeg har lige tilmeldt mig dykkerturen til <?php echo "" . $sted . ""; ?> hos Diving 2000. Skal du ikke med?"/>

</head>


<body>
<div id="fb-root"></div>

<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) {return;}
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/da_DK/all.js#xfbml=1&appId=166978863373833";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<?php 

include ("menu.php"); 


echo "<h2>" . $sted . " den " . convertDateForPrint(substr($Dato, 0, 10)) . "</h2>
<b>Del på Facebook:</b> <div class=\"fb-like\" data-send=\"true\" data-layout=\"button_count\" data-width=\"200\" data-show-faces=\"false\" data-colorscheme=\"light\"></div>

&nbsp;&nbsp;

" . addToGoogleCalendar($Dato, $endDate, "Dykkertur med Diving 2000 til " . $sted . "", $sted, "Vi ses under overfladen") . "

&nbsp;&nbsp;<a href=\"addToOutlook.php?tripID=" . $tripID . "\"><img src=\"images/outlook.png\" title=\"Tilføj til Outlook\" alt=\"Tilføj til Outlook\"></a>
&nbsp;&nbsp;

<a href=\"\" onclick=\"window.open('routeBeskrivelse.php?distID=" . $destinationID . "','Diving 2000 Routebeskrivelse', 'menubar=no , toolbar=no , status=no , resizable=yes , width=450 , height=300 , scrollbars=yes'); return false\"><img src=\"images/gm_button.png\" title=\"Routebeskrivelse\"></a>


<br />


";




echo "
<br />";



echo "" . nl2br($beskrivelse) . "<br /><br />";


if (!$moreInfo)
	echo "<b>Pris: " . getPriceFromTripID($tripID, "normal") . "/" . getPriceFromTripID($tripID, "medlem") . "</b> (normal/medlem)<br /><br />";


?>



<?php
if(@$_REQUEST["error"] == 1)
	echo "<div class=\"fejl\">Brugernavn eller adgangskoden er forkerte. Prøv igen.</div><br /><br />";

if(@$moreInfo)
	echo "Tilmelding og mere info kan findes her: <a href=\"" . $moreInfo . "\">" . $moreInfo . "</a>";
else
{
	if (friePladser($tripID) > 0)
	{

		if (!login_check())
		{

			

		?>

		Login for at tilmelde dig turen. Har du ikke noget logind? <a href="newUser.php">Så oprettet det her - tager under 1 minut</a>
		<br />
		<br />


		<form action="logMeIn.php" method="post" name="myForm">


		<div class="style_form">
		  <label for="brugernavn">Brugernavn:</label>
		  <input type="text" name="username" class="form_element">
		</div>

		<div class="style_form">
		  <label for="adgangskode">Adgangskode:</label>
		  <input type="password" name="password" class="form_element">
		</div>

		 <div class="style_form">
		   <input type="hidden" value="<?php echo "book.php?tripID=" . $tripID . ""; ?>" name="urlOK" >
		   <input type="hidden" value="<?php echo "info.php?tripID=" . $tripID . "&error=1"; ?>" name="urlError" >
		   <input type="submit" value="Login"  class="form_element" >
		</div>  

		</form>

		<?php
		}
		else
		{
			
			echo "<a href=\"book.php?tripID=" . $_GET["tripID"] . "\">Klik her for at tilmelde</a>";
			
		}
	}
	else
	{
		echo "<h2>Der er ikke flere ledigede pladser - kontakt butikken</h2>";
		
	}
}
?>

<?php include("buttonMenu.php"); ?>

</body>
</html>