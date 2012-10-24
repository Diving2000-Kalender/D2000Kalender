<?php
include ("../function.php");
include ("../mysqlConfig.php");

function getCourseForMobileIphone ($dato, $returnID = false)
{
	$temp = "";
	
	$conn = connectToDB();
	
	$sql = "select * from EVE.dbo.T_CsCourse WHERE (CsStartDate = '" . $dato . "') AND (CsCourseStatusID = 1)";		
	
	$row=odbc_exec($conn, $sql);

	while(odbc_fetch_row($row))
 	{
		if(odbc_result($row, "CsStartDate") != "")
		{
			if($returnID)
				$temp .= "K" . convertDateForPrint(substr(odbc_result($row, "CsStartDate"), 0, 10)) . " - " . getCourseTypeFromID(odbc_result($row, "CsCourseTypeID")) . "§";
			else
				$temp .= "" . convertDateForPrint(substr(odbc_result($row, "CsStartDate"), 0, 10)) . "|" . getCourseTypeFromID(odbc_result($row, "CsCourseTypeID")) . "|--|" . getDescriptionCourse(odbc_result($row, "CsCourseTypeID")) . "|" . getGPS(odbc_result($row, "CsCourseTypeID")) . "§";
		}
	}
	
	
	
	return $temp;
	
}



function getTripForMobileIphone ($dato, $returnID = false)
{
	$temp = "";

	$conn = connectToDB();
	
	$dateToMorrow = date("Y-m-d",strtotime(date("Y-m-d", strtotime($dato)) . " +1 day"));
	$sql = "select * from EVE.dbo.T_TpTrip WHERE (TpStartDate_N BETWEEN '" . $dato . "' AND '" . $dateToMorrow . "') AND (TpTripStatusID = 1) ORDER BY TpStartDate_N";		
	
	$row=odbc_exec($conn, $sql);

	$temp = "";
	$tripID = 0;
	
	while(odbc_fetch_row($row))
 	{
		
		$pris = explode("/", odbc_result($row, "TpPrivateNotesTx_N"));
		
		if(odbc_result($row, "TpStartDate_N") != "")
		{
			if($returnID)
				$temp .= "T" . convertDateForPrint(substr(odbc_result($row, "TpStartDate_N"), 0, 10)) . " - " . getDestinationByID(odbc_result($row, "TpDestinationID_N")) . "§";
			else
			{
				$temp .= "" . convertDateForPrint(substr(odbc_result($row, "TpStartDate_N"), 0, 10)) . "|" . getDestinationByID(odbc_result($row, "TpDestinationID_N")) . "|" . $pris[0] . "|" . getDescription(odbc_result($row, "TpDestinationID_N")) . "";
				
				
				$temp .= "<br /><br />Vi kører fra Diving 2000 kl. " . getTimeAtDiving2000(odbc_result($row, "TpStartDate_N"), odbc_result($row, "TpArrivalMinsIn")) . ". <br />";
				$temp .= "Kører du selv, så mødes vi på stedet kl. " . substr(odbc_result($row, "TpStartDate_N"), 11,5) . "<br /><br />";
				$temp .= "|" . getGPS(odbc_result($row, "TpDestinationID_N")) . "§";
				
			}
		}
	}
	
	return $temp;
}



$count = 0;

$today = date("Y-m-d");
$day = 0;

$output = "";

while($count < 15 && $day < 120)
{
	
	$date = date("Y-m-d",strtotime(date("Y-m-d", strtotime($today)) . " +" . $day . " day"));
    
	
	
	if(getCourseForMobileIphone($date) != "")
	{
		$output .= "" . strip_tags(getCourseForMobileIphone($date)) . "";
		$count++;
	}
	
	
	if(getTripForMobileIphone($date) != "")
	{
		$output .= "" . strip_tags(getTripForMobileIphone($date)) . "";
		$count++;
	}
	
	$day++;
	
}

$array = explode("§", $output);

for($i = 0; $i < sizeof($array); $i++)
{
	if($array[$i] != "")
		$newArray[] = $array[$i];
}

$output = "";
for($i = 0; $i < sizeof($newArray); $i++)
{
//	echo "(" . $i . ") " . $newArray[$i] . "<br />";
	$output .= "" . $newArray[$i] . "§";
}

echo "" . substr($output, 0, strlen($output) - 1) . "";


?>