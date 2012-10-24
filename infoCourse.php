<?php

require_once ("function.php");
require_once ("mysqlConfig.php");

$conn = connectToDB();

//Tidsplanen findes i T_MoModule og T_MTModuleType
//Beskrivelse af kursus og URL til at købe

@$courseID = $_GET["courseID"];

if($courseID == "")
{
	header('Location: ./kalender.php');	
	
}


$sql="SELECT * FROM EVE.dbo.T_CsCourse WHERE CsCourseID = " . $courseID . "";

$row=odbc_exec($conn, $sql);
while(odbc_fetch_row($row))
{
	$startDato = convertDateForPrint(substr(odbc_result($row, "CsStartDate"),0,10));
	$kursus = getCourseTypeFromID(odbc_result($row, "CsCourseTypeID"));
	$beskrivelse = getDescriptionCourse(odbc_result($row, "CsCourseTypeID"));
	
	if(odbc_result($row, "CsMoreInfoTx_N") == "")
	{
		$link = "http://www.shop-diving2000.dk/search.asp?keyword=" . str_replace(" ", "+",$kursus) ."&submit1=S%F8g";	
	}
	else
		$link = odbc_result($row, "CsMoreInfoTx_N");
}
	
odbc_close($conn); 




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
	<title>Tilmelding til dykkerkursus</title>
	<link rel="stylesheet" href="style.css" />
	<link rel="shortcut icon" href="favicon.ico">

<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" /> 


</head>


<body>

<?php include ("menu.php"); ?>

<?php

echo "<h2>" . $kursus . " den " . $startDato . "</h2>
<br />";
?>
Book dit dykkerkursus her: <a href="<?php echo "" . $link . ""; ?>">Diving 2000 shop</a><br /><br />

<?php
echo "" . @nl2br($beskrivelse) . "<br /><br />";

///////////////  Tider //////////////////

echo "<h2>Tidsplan</h2>";

$sql="SELECT * FROM EVE.dbo.T_MoModule WHERE MoCourseID = " . $courseID . " ORDER BY MoStartDate_N";

$row=odbc_exec($conn, $sql);

echo "<table width=\"600\">";
echo " <tr>";
echo "  <td>Modul</td>";
echo "  <td>Dato</td>";
echo "  <td>Tid</td>";
echo "  <td>Sted</td>";
echo " </tr>";
while(odbc_fetch_row($row))
{
	echo "<tr>";
	echo " <td>" . getModuleNameFromID(odbc_result($row, "MoModuleTypeID")) . "</td>";
	echo " <td>" . convertDateForPrint(odbc_result($row, "MoStartDate_N")) . "</td>";
	echo " <td>" . substr(odbc_result($row, "MoStartDate_N"),11,5) . " - " . substr(odbc_result($row, "MoEndDate_N"), 11,5) . "</td>";
	echo "<td>" . getModuleLocationFromID(odbc_result($row, "MoLocationID_N")) . "</td>";	
	echo "</tr>";
}

echo "</table>";


?>
<br />
<i>Bemærk at alle tiderne er vejledende og at stedet kan ændres. </i>
<br />
<br /><br />

Book dit dykkerkursus her: <a href="<?php echo "" . $link . ""; ?>">Diving 2000 shop</a>

<?php include("buttonMenu.php"); ?>

</body>
</html>