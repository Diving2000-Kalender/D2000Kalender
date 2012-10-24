<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Mit Diving 2000 - Ny log</title>
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






sec_session_start();

if(login_check() == false)
{
	header('Location: ./login.php');	
}


include ("menu.php");

if(@$_REQUEST["date"] && @$_REQUEST["dybde"] && @$_REQUEST["bundtid"] && @$_REQUEST["tankStart"] && @$_REQUEST["tankSlut"])
{
	$tankStart = null;
	$tankSlut = null;
	$beskrivelse = null;
	$makker = null;
	$dybde = 0;
	$sted = null;
	
	
	if($_REQUEST["dybde"] != "")
	{
		$dybde = str_replace(",", ".", $_REQUEST["dybde"]);	
	}
	
	if($_REQUEST["beskrivelse"] != "")
		$beskrivelse = $_REQUEST["beskrivelse"];
	
	if($_REQUEST["makker"] != "")
	{
		$beskrivelse .= "\r\n\r\n{" . $_REQUEST["makker"] . "}";
		$makker = $_REQUEST["makker"];
	}
	
	if($_REQUEST["sted"] != "")
	{
		$beskrivelse .= "\r\n\r\n{" . $_REQUEST["sted"] . "}";
		$sted = $_REQUEST["sted"];	
		
	}
		
	if($_REQUEST["tankStart"] != "")
		$tankStart = $_REQUEST["tankStart"];
	
	if($_REQUEST["tankSlut"] != "")
		$tankSlut = $_REQUEST["tankSlut"];
		
	$date = date("Y-m-d H:i:s");
		
	$tidNed = $_REQUEST["date"] . " " . $_REQUEST["timer"] . ":" . $_REQUEST["minutter"] . "";
	
	$tidOp = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($tidNed)) . " +" . $_REQUEST["bundtid"] . " minutes"));
		
		
	
	$conn = connectToDB();
	
	$sql = "INSERT INTO [eve].[dbo].[T_DiDiveProfile] ([DiCustID],[DiEmpID],[DiStartDate_N],[DiEndDate_N]
           ,[DiAirInTx_N],[DiAirOutTx_N],[DiMaxDepthTx_N],[DiNotesTx_N],[DiCreationDate],[DiLastUpdatedDate]) 
	VALUES (" . $_SESSION["eveID"] . ",3,'" . mysql_real_escape_string($tidNed) . "','" . mysql_real_escape_string($tidOp) . "','" . mysql_real_escape_string($tankStart) . "','" . mysql_real_escape_string($tankSlut) . "','" . mysql_real_escape_string($dybde) . "','" . $beskrivelse . "','" . $date . "','" . $date . "')";
	
	$row=odbc_exec($conn, $sql);		
	
	
	$sql = "select TOP (1) DiDiveProfileID from EVE.dbo.T_DiDiveProfile where DiCustID = " . $_SESSION["eveID"] . " order by DiCreationDate desc";		
	$row=odbc_exec($conn, $sql);
	while(odbc_fetch_row($row))
	{
		$diveID = odbc_result($row, "DiDiveProfileID");
	}
	
	
	
	$insertSQL = "INSERT into diveLog (`diveID`, `makker`, `sted`) values (" . $diveID . ", '" . mysql_real_escape_string($makker) . "', '" . mysql_real_escape_string($sted) . "')";
	mysql_query($insertSQL) or die(mysql_error());
	//var_dump($insertSQL);
	
	$uds = "<div class=\"godkendt\">Dit dyk blev logget</div>";
}
else
	$uds = "<div class=\"fejl\">Alle felterne skal udfyldes</div>";




if($uds)
	echo "" . $uds . "<br /><br />";
?>


<form action="<?php echo "" . $_SERVER["PHP_SELF"] . ""; ?>" method="post" name="addLog">

<div class="style_form">
  <label for="date">Dato:</label>
  <input type="text" name="date" size=10 readonly="true"><a href="javascript:showCal('Calendar')">Vælg dato</a>
  <span class="hint">Vælg datoen for dykket<span class="hint-pointer"> </span></span>
</div>

<div class="style_form">
  <label for="tidNed">Tid ned:</label>
  
  <select name="timer" class="form_element">
  <?php
  	for($i = 0; $i < 24; $i++)
	{
		if($i < 10)
			echo "<option value=\"" . $i . "\">0" . $i . "</option>";
		else
			echo "<option value=\"" . $i . "\">" . $i . "</option>";
	}
  
  
  ?>
  </select> 

    <select name="minutter" class="form_element">
  <?php
  	for($i = 0; $i < 60; $i++)
	{
		if($i < 10)
			echo "<option value=\"" . $i . "\">0" . $i . "</option>";
		else
			echo "<option value=\"" . $i . "\">" . $i . "</option>";
	}
  
  
  ?>
  </select> 
  
  
  <span class="hint">Vælg hvornår dykket startede<span class="hint-pointer"> </span></span>
</div>

<div class="style_form">
  <label for="bundtid">Bundtid:</label>
  <input type="text" name="bundtid">
  <span class="hint">Indtast din bund i minutter<span class="hint-pointer"> </span></span>
</div>

<div class="style_form">
  <label for="luft">Tank tryk start:</label>
  <input type="text" name="tankStart">
  <span class="hint">Indtast tanktrykket ved start<span class="hint-pointer"> </span></span>
</div>

<div class="style_form">
  <label for="luft">Tank tryk slut:</label>
  <input type="text" name="tankSlut">
  <span class="hint">Indtast tanktrykket ved slut<span class="hint-pointer"> </span></span>
</div>

<div class="style_form">
  <label for="dybde">Maks dybde:</label>
  <input type="text" name="dybde">
  <span class="hint">Indtast maks dybde på dykket<span class="hint-pointer"> </span></span>
</div>

<div class="style_form">
  <label for="dybde">Makker:</label>
  <input type="text" name="makker">
  <span class="hint">Indtast navnet på din makker<span class="hint-pointer"> </span></span>
</div>

<div class="style_form">
  <label for="dybde">Sted:</label>
  <input type="text" name="sted">
  <span class="hint">Indtast navnet på dykkerstedet<span class="hint-pointer"> </span></span>
</div>


<div class="style_form">
  <label for="note">Kommentar:</label>
  <textarea name="beskrivelse" rows="15" cols="60"></textarea>
  <span class="hint">Indtast maks dybde på dykket<span class="hint-pointer"> </span></span>
</div>


<input type="submit" value="Opret" />
</form>




<?php include("buttonMenu.php"); ?>


</body>
</html>