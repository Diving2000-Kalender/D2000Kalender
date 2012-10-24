
<?php
include ("../function.php");
include ("../mysqlConfig.php");

$count = 0;

$today = date("Y-m-d");
$day = 0;
$id = "";

$output = "";


if(strpos($_GET["date"], "/"))
{
	$dato = substr($_GET["date"], 6, 4);
	$dato .= "-" . substr($_GET["date"],3,2);
	$dato .= "-" . substr($_GET["date"],0,2);
	$dato .= " 00:00:00";
	
}

else
{
	$dateArray = explode("-", $_GET["date"]);

	if($dateArray[0] < 10)
		$dateArray[0] = "0" . $dateArray[0];
	if($dateArray[1] < 10)
		$dateArray[1] = "0" . $dateArray[1];

	$dato = $dateArray[2] . "-" . $dateArray[1] . "-" . $dateArray["0"] . " 00:00:00";
}

$sted = str_replace("Ã¥", "å", $_GET["heading"]);


$sql="SELECT * FROM EVE.dbo.T_TpTrip WHERE (TpStartDate_N > '" . $dato . "') AND (TpDestinationID_N = " . getDestinationIDFromName($sted) . ") ORDER BY TpStartDate_N";

@$row=odbc_exec(connectToDB(), $sql);
@$count = odbc_num_rows($row);

if($count)
{

	
	while(odbc_fetch_row($row))
	{
		$id = odbc_result($row, "TpTripID");	
	}		
	
	echo "http://www.kalender.diving2000.dk/info.php?tripID=" . $id . "";
	
	
	
}
else
{

	$sql = "select * from EVE.dbo.T_CsCourse WHERE (CsStartDate = '" . $dato . "') AND (CsCourseStatusID = 1)";		

	$row=odbc_exec(connectToDB(), $sql);	
	while(odbc_fetch_row($row))
	{
		
		$id = odbc_result($row, "CsCourseID");
	}
	
	echo "http://www.kalender.diving2000.dk/infoCourse.php?courseID=" . $id . "";
}



//header('Location: ../book.php?tripID=' . getLinkForMobile($newArray[$_GET["navn"]]) . '');	

?>
