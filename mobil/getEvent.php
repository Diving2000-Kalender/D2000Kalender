
<?php
include ("../function.php");
include ("../mysqlConfig.php");

$count = 0;

$today = date("Y-m-d");
$day = 0;

$output = "";

while($count < 15 && $day < 120)
{
	
	$date = date("Y-m-d",strtotime(date("Y-m-d", strtotime($today)) . " +" . $day . " day"));
    
	
	
	if(getCourseForMobile($date) != "")
	{
		$output .= "" . getCourseForMobile($date, true) . "";
		$count++;
	}
	
	
	if(getTripForMobile($date) != "")
	{
		$output .= "" . getTripForMobile($date, true) . "";
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


echo "" . printBeskrivelseForMobile($newArray[$_GET["navn"]]) . "";

?>
