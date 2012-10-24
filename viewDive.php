<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Min logbog</title>
	<link rel="stylesheet" href="style.css" />
	<script language="javascript" src="cal2.js"></script>
	<script language="javascript" src="cal_conf2.js"></script>
	<link rel="shortcut icon" href="favicon.ico">

	<script type="text/javascript">

		function addLoadEvent(func) {
		  var oldonload = window.onload;
		  if (typeof window.onload != 'function') {
		    window.onload = func;
		  } else {
		    window.onload = function() {
		      oldonload();
		      func();
		    }
		  }
		}

		function prepareInputsForHints() {
			var inputs = document.getElementsByTagName("input");
			for (var i=0; i<inputs.length; i++){
				// test to see if the hint span exists first
				if (inputs[i].parentNode.getElementsByTagName("span")[0]) {
					// the span exists!  on focus, show the hint
					inputs[i].onfocus = function () {
						this.parentNode.getElementsByTagName("span")[0].style.display = "inline";
					}
					// when the cursor moves away from the field, hide the hint
					inputs[i].onblur = function () {
						this.parentNode.getElementsByTagName("span")[0].style.display = "none";
					}
				}
			}
			// repeat the same tests as above for selects
			var selects = document.getElementsByTagName("select");
			for (var k=0; k<selects.length; k++){
				if (selects[k].parentNode.getElementsByTagName("span")[0]) {
					selects[k].onfocus = function () {
						this.parentNode.getElementsByTagName("span")[0].style.display = "inline";
					}
					selects[k].onblur = function () {
						this.parentNode.getElementsByTagName("span")[0].style.display = "none";
					}
				}
			}
		}
		addLoadEvent(prepareInputsForHints);

</script>



</head>
<body>

<?php 
require_once ("function.php");
require_once ("mysqlConfig.php");



@$diveID = $_GET["diveID"];


sec_session_start();

if(login_check() == false)
{
	header('Location: ./login.php');	
}


include ("menu.php");


$sql = "SELECT * from T_DiDiveProfile where DiDiveProfileID = " . $diveID . " AND DiCustID = " . $_SESSION["eveID"] . "";

$row=odbc_exec(connectToDB(), $sql);

if(odbc_num_rows($row) == 0)
	header('Location: logbog.php?test=' . odbc_num_rows($row) . '');
		
while(odbc_fetch_row($row))
{

	$startDate = odbc_result($row, "DiStartDate_N");
	$endDate = odbc_result($row, "DiEndDate_N");
	$airIn = odbc_result($row, "DiAirInTx_N");
	$airOut = odbc_result($row, "DiAirOutTx_N");
	$dybde = odbc_result($row, "DiMaxDepthTx_N");
	$note = odbc_result($row, "DiNotesTx_N");
	
}

?>

<div class="style_form">
  <label for="date"><b>Dato:</b></label>
  <label for="date"><?php echo "" . convertDateForPrint(substr($startDate, 0, 10)) . ""; ?> </label>
</div>
<br />
<div class="style_form">
  <label for="tidNed"><b>Tid ned:</b></label>
  <label for="tidNed"><?php echo "" . substr($startDate, 11, 5) . ""; ?></label>
</div>
<br />
<div class="style_form">
  <label for="bundtid"><b>Bundtid:</b></label>
  <label for="bundtid"><?php echo "" . calcBottomTime($startDate, $endDate) . ""; ?></label>
</div>
<br />

<div class="style_form">
  <label for="tidNed"><b>Tid op:</b></label>
  <label for="tidNed"><?php echo "" . substr($endDate, 11, 5) . ""; ?></label>
</div>

<br />


<div class="style_form">
  <label for="luft"><b>Tank tryk start:</b></label>
  <label for="luft"><?php echo "" . $airIn . ""; ?></label>
</div>
<br />
<div class="style_form">
  <label for="luft"><b>Tank tryk slut:</b></label>
  <label for="luft"><?php echo "" . $airOut . ""; ?></label>
</div>
<br />

<div class="style_form">
  <label for="luft"><b>Lutforbrug:</b></label>
  <label for="luft"><?php echo "" . calcAirConsumption($airIn, $airOut) . ""; ?></label>
</div>
<br />


<div class="style_form">
  <label for="dybde"><b>Maks dybde:</b></label>
  <label for="dybde"><?php echo "" . $dybde . ""; ?></label>
</div>
<br />
<div class="style_form">
  <label for="dybde"><b>Makker:</b></label>
  <label for="dybde"><?php echo "" . getMakkerToDive($diveID) . ""; ?></label>
</div>
<br />

<div class="style_form">
  <label for="note"><b>Kommentar:</b></label>
  <?php echo "" . stripCommondFromEVE($note) . ""; ?>
  
  
</div>



<?php include("buttonMenu.php"); ?>


</body>
</html>