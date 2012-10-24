<?php

function getSubjectForMail($type)
{
	$data = mysql_query("select * from mail where type = " . $type . " limit 1") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		return $row["emne"];
	}
}

function getMessageForMail($type)
{
	$data = mysql_query("select * from mail where type = " . $type . " limit 1") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		return $row["besked"];
	}
}

function getEquipmentPrice($udstyr, $isMember)
{
	if($isMember)
		$type = "medlemPris";
	else
		$type = "normalPris";
		
	$data = mysql_query("select * from udstyrs_priser where udstyr = '" . $udstyr . "' limit 1") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		return $row["" . $type . ""];
	}
	
}

function updateEquipmentPrice($udstyr, $normalPris, $klubPris)
{
	
	$insertSQL = "UPDATE `udstyrs_priser` SET `normalPris`=" . $normalPris . ", `medlemPris`=" . $klubPris . " WHERE `udstyr`='" . $udstyr . "'";
	mysql_query($insertSQL) or die(mysql_error());
}

function getUserlist ()
{
	$output = "";
	
	$output .= "<select name=\"loginAs\" class=\"form_element\">";
	$output .= "<option value=\"0\">Som mig selv</option>";
	$data = mysql_query("select * from users where accountType = 'user' ORDER BY created DESC") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		$output .= "<option value=\"" . $row["eveID"] . "\">" . getNameFromEVE($row["eveID"]) . "</option>";
	}
	
	$output .= "</select>";
	
	return $output;
}


function getUsernameFromEVEID ($eveID)
{
	$data = mysql_query("select * from users where eveID = " . $eveID . " LIMIT 1") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		return $row["username"];
	}
}

function isCreatedOrFound ($option)
{
	if($option == "found")
		return "<a href=\"#\" title=\"Personen fundet i EVE\">(f)</a>";
	if ($option == "created")
		return "<a href=\"#\" title=\"Personen blev oprettet\">(c)</a>";
	if ($option == "change")
		return "<a href=\"#\" title=\"EVEID er blevet skiftet\">(e)</a>";
	
}

function sendBookingMail($besked, $mail)
{
	$subject = getMailForm(2, true);
	
	//$subject = 'Diving 2000 - Turtilmelding';
	$headers = "From: Diving 2000 <hansen@diving2000.dk>\r\n";
	$headers .= "Reply-To: hansen@diving2000.dk\r\n";
//	$headers .= "CC: info@diving2000.dk\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";


	$message = '<html><body>';
	
	
	//SYstem-data tag
	$body = getMailForm(2, false);
	$replace = $besked;
	$body = str_replace("%system-data%", $replace, $body);
	
	$replace = "<a href=\"" . getLastNewsletterLink() . "\"><img src=\"http://kalender.diving2000.dk/images/nyhedsbrev.jpg\" alt=\"Seneste nyhedsbrev fra Diving 2000\"></a>";
	$body = str_replace("%sidste-nyhedsbrev-data%", $replace, $body);
		
	
	
	$message .= $body;
	
	$message .= '</body></html>';

	mail($mail, $subject, $message, $headers);	
	
	
	
}


function getLastNewsletterLink()
{
	$url = "http://diving2000.ithansen.net/nyhedsbrev.php";
	$fp = fopen($url, 'r');
	$content = '';
	
	while($l = fread($fp, 1024))
		$content .= $l;
	fclose($fp);
	
	return $content;
	
}

function resetPassword ($username)
{
	if(isUsernameAvailab($username))
		return "<div class=\"fejl\">Brugernavnet kunne ikke findes</div>";

	$result = mysql_query("SELECT * FROM users WHERE `status` != 'aktiv' AND `username` = '" . mysql_real_escape_string($username) . "'") or die (mysql_error()); 
	$num = mysql_num_rows($result);
	if ($num)
		return "<div class=\"fejl\">Kontoen er ikke blevet aktiveret. Der blev sendt en mail da din konto blev oprettet</div>";

	$newPassword = makePassword(8);
	
	$passwordEn = encryption($newPassword);
	
	
	$insertSQL = "UPDATE `users` SET `password`='" . md5($passwordEn) . "' WHERE `username`='" . mysql_real_escape_string($username) . "' LIMIT 1";
	mysql_query($insertSQL) or die(mysql_error());

	$toMail = getMailFromEVE(getEveIDFromUsername($username));

	//$subject = 'Diving 2000 - Adgangskode reset';
	$subject = getMailForm(3, true);
	
	$headers = "From: Diving 2000 <hansen@diving2000.dk>\r\n";
	$headers .= "Reply-To: hansen@diving2000.dk\r\n";
	//$headers .= "CC: susan@example.com\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";


	$message = '<html><body>';
	
	
	$besked = getMailForm(3, false);
	$replace = ' ' . $newPassword . '';
	$besked = str_replace("%system-data%", $replace, $besked);
	
	$message .= $besked;
	$message .= '</body></html>';

	mail($toMail, $subject, $message, $headers);	
		
	//return $message;	
	return "<div class=\"godkendt\">Din adgangskode er blevet ændret. Vi har sendt din nye adgangskode til din mail<br /><a href=\"login.php\">Klik her for at logge ind</a></div>";
}


function getMailForm ($type, $emne = false)
{
	$data = mysql_query("select * from mail where `type` = " . $type . " limit 1") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		if ($emne)
			return $row["emne"];
		else
			return $row["besked"];
	}		
	
}


function welcomeMail ($toMail, $eveID, $auth)
{
	

	//$subject = 'Velkommen til Diving 2000';
	$subject = getMailForm(1, true);
	
	$headers = "From: Diving 2000 <hansen@diving2000.dk>\r\n";
	$headers .= "Reply-To: hansen@diving2000.dk\r\n";
	//$headers .= "CC: susan@example.com\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";


	$message = '<html><body>';
	
	$besked = getMailForm(1, false);
	$replace = '<a href="http://kalender.diving2000.dk/confirm.php?eID=' . $eveID . '&auth=' . $auth . '">http://kalender.diving2000.dk/confirm.php?eID=' . $eveID . '&auth=' . $auth . '</a>';
	$besked = str_replace("%system-data%", $replace, $besked);
	
	$message .= $besked;
	$message .= '</body></html>';

	mail($toMail, $subject, $message, $headers);
		
}


function getMaxDybde ($eveID)
{
	$dybde = "";
	
	$sql = "SELECT TOP (1) * FROM T_DiDiveProfile WHERE DiCustID = " . $_SESSION["eveID"] . " order by DiMaxDepthTx_N DESC";
	$row=odbc_exec(connectToDB(), $sql);
	while(odbc_fetch_row($row))
	{
		$dybde = odbc_result($row, "DiMaxDepthTx_N");	
	}
	
	if($dybde == "")
		$dybde = 0;
	
	return $dybde;
	
}

function calcBottomTime ($startDate, $endDate)
{
	$to = strtotime($startDate);
	$from = strtotime($endDate);
	return round(abs($to - $from) / 60,2). "";
	
	
}

function calcAirConsumption ($start, $slut)
{
	return $start - $slut;
}


function getMakkerToDive ($diveID)
{
	$data = mysql_query("select * from divelog where `diveID` = " . $diveID . " limit 1") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		return $row["makker"];
	}	
}

function stripCommondFromEVE ($note)
{
	
	return substr($note, 0, strpos($note, "{"));
}

function getAdresseInfoToDestination ($desID, $value)
{
	$data = mysql_query("select * from tur_beskrivelse where `turID` = " . $desID . " limit 1") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		return $row[$value];
	}
	
}

function getMakkerToDivelog ($diveID)
{
	$data = mysql_query("select * from diveLog where `diveID` = " . $diveID . " limit 1") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		return $row["makker"];
	}
}

function getLocationToDivelog ($diveID)
{
	$data = mysql_query("select * from diveLog where `diveID` = " . $diveID . " limit 1") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		return $row["sted"];
	}
}


/**
 * Henter beskrivelse af det valgte kursus.
 * 
 * @param KursusID
 * @return Beskrivelse eller ikke beskrivelse
 * 
 */
function getDescriptionCourse ($courseID)
{
	$description = "";
	$count = 0;
	$data = mysql_query("select `description` from kursus_beskrivelse where `id` = " . $courseID . "") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		$description = nl2br($row["description"]);
		$count++;
	}
	
	if($count == 1)
		return $description;
	else
		return "<i>Der kunne desværre ikke finde nogen beskrivelse</i>";
	
	
}

/**
 * Henter GPS koordinaterne for mødestedet
 */
function getGPS ($destinationID)
{

	$sql = "SELECT *  FROM `tur_beskrivelse` WHERE `turID` = " . $destinationID . "";	
	
	$data = mysql_query("" . $sql . "") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		$lat = $row["lat"];
		$ing = $row["ing"];
	}
	if(@$lat == "")
		return "55.398122,10.387235"; //default - Diving 2000
	return "" . $lat . "," . $ing . "";
		
}

function replaceChar ($input)
{
	$input = str_replace("æ", "Ã¦", $input);
	$input = str_replace("ø", "Ã¸", $input);
	$input = str_replace("å", "Ã¥", $input);
	
	return $input;
}

function getMettingPoint ($destinationID)
{
	$sql = "SELECT *  FROM `tur_beskrivelse` WHERE `turID` = " . $destinationID . "";	
	
	$data = mysql_query("" . $sql . "") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		$adresse = $row["adresse"];
		$post = $row["postnr"];
		$lat = $row["lat"];
		$ing = $row["ing"];
	}
	if(@$lat == "")
		return "Vi har ingen adresse på dette sted. Kontakt butikken.<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />";
	return "" . $adresse . "<br />" . $post . " " . getCityName($post) . "<br />Koordinater: " . $lat . "," . $ing . "";
	
}

function getCityName ($postnr)
{
	$sql = "SELECT *  FROM `postnummer` WHERE `postnr` = " . $postnr . "";
	
	$data = mysql_query("" . $sql . "") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		$by = $row["by"];		
	}
	
	return $by;
	
	
}



/**
 * Beregner antallet af minutter mellem 2 dateTime
 * 
 * @param start dato
 * @param slut dato
 * 
 * @return Antal minutter
 * 
 */
function beregnBundtid ($startDate, $endDate)
{
	$to_time = strtotime(substr($startDate,0,16));
	$from_time = strtotime(substr($endDate,0,16));
	
	return round(abs($to_time - $from_time) / 60,2);	
}

/**
 * Beregner hvor meget luft, at der er blevet brugt på dykket
 * 
 * @param luftforbrug start
 * @param luftforbrug slut
 * 
 * @return LuftForbrug
 * 
 */
function beregnLuftforbrug ($luftStart, $luftSlut)
{
	return $luftStart - $luftSlut;
}



/**
 * Henter statussen for en mail fra Ubivox. Dog kun for listen Diving 2000
 * 
 *  @param Mail
 *  @return Statusen for mailen
 * 
 */
function getUbivoxStatus ($mail)
{
	include('ubivox/init.php'); 
	try
	{
		
		$response = $client->call('ubivox.get_subscriber', $mail);
		
		$listID = 4208;
		
		for($i = 0; $i < sizeof($response["subscriptions"]); $i++)
		{
			if($response["subscriptions"][$i]["list_id"] == $listID)
			{
				switch($response["subscriptions"][$i]["state"])
				{
					case "pending":
						return "Afventer bekræftigelse fra dig. <a href=\"addSubscription.php\">Gensend mail</a>";
						break;
					case "active":
						return "Er tilmeldt";
						break;
						
					case "suspended":
						return "Mailadresse ugyldig - Afvist af server. <a href=\"addSubscription.php\">Tilmeld mail</a>";
						break;
						
					case "deleted":
						return "Slettet";
						break;
					default:
						return "Du er ikke tilmeldt vores nyhedsbrev. <a href=\"addSubscription.php\">Tilmeld din mail</a>";
				}
				
				break;
			}
		}
		
	}
	catch(Zend_XmlRpc_Client_FaultException $e)
	{
		return "Du er ikke tilmeldt vores nyhedsbrev. <a href=\"addSubscription.php\">Tilmeld din mail</a>";
	}
		
}	
	

/**
 * Funktion til at tilføje en mailadresse til vores liste i Ubivox. Brugeren skal dog selv bekræfte 
 * at man ønsker at tilmelde sig nyhedsbrevet ved at klikke på det link, som man får en mail med.
 * 
 * @param mailen som skal tilføjes
 * 
 * @return OK eller Fejlbeskrivelse
 */
function addSubscriptionToUbivox ($mail)
{
	include('init.php'); 

	try 
	{
	// $client->call('ubivox.create_subscription', array("" . $mail . "", 4208, 0));
		$client->call('ubivox.create_subscription', array($mail, 4208, 1));
	} 
	catch(Zend_XmlRpc_Client_FaultException $e) 
	{
		if ($e->getCode() != 1003)
			return "fejl - " . $e->getCode() . "";
	}
	 

	return "ok";
	
	
}
					
					
/**
 * Denne funktion laver et link, som gør det muligt at tilføje en tur i ens Google kalender, med 
 * start dato, slut dato og hvor turen går til.
 * 
 * @param StartDate Start dato
 */				
function addToGoogleCalendar ($startDate, $endDate, $what, $where, $beskrivelse)
{


	$what = str_replace("æ", "%C3%A6", $what);
	$what = str_replace("ø", "%C3%B8", $what);
	$what = str_replace("å", "%C3%A5", $what);
	
	$where = str_replace("æ", "%C3%A6", $where);
	$where = str_replace("ø", "%C3%B8", $where);
	$where = str_replace("å", "%C3%A5", $where);
	
	$startDate = substr($startDate, 0, 16);
	
	$startDate = strtotime ( '-2 hour' , strtotime ( $startDate ) ) ;
	$startDate = date ( 'Y-m-d H:i' , $startDate );
	
	
	$startDate = str_replace("-", "", $startDate);
	$startDate = str_replace(":", "", $startDate);
	$startDate = str_replace(" ", "", $startDate);

	$endDate = strtotime ( '-2 hour' , strtotime ( $endDate ) ) ;
	$endDate = date ( 'Y-m-d H:i' , $endDate );
	
	$endDate = substr($endDate, 0, 16);
	$endDate = str_replace("-", "", $endDate);
	$endDate = str_replace(":", "", $endDate);
	$endDate = str_replace(" ", "", $endDate);
	
	
	$url = "http://www.google.com/calendar/event?action=TEMPLATE&text=" . $what . "&dates=" . substr($startDate,0,8) . "T" . substr($startDate,8,4) . "00Z/" . substr($endDate,0,8) . "T" . substr($endDate,8,4) . "00Z&details=" . $beskrivelse . "&location=" . $where . "&trp=false&sprop=http%3A%2F%2Fkalender.diving2000.dk%2Finfo.php&sprop=name:Diving%202000";
	
	
	return "<a href=\"" . $url . "\" target=\"_blank\"><img src=\"images/gc_button.png\" border=\"0\" alt=\"Tilføj turen til din Google Kalender\" title=\"Tilføj turen til din Google Kalender\" /></a>";	
}

/**
 * Funktion som gør at menuen indsætter de rigtige link
 * 
 */
function createLink ($page)
{
	$test = strpos($_SERVER["SCRIPT_NAME"], "admin");
	if($test > 0)
		return $page;
	else
		return "/admin/" . $page;
	
}


/**
 * Funktion som analyser om et tur sted har en beskrivelse.
 * @param navnet på stedet
 * 
 * @return Hvis en beskrivelse findes, så et link til at rediger denne beskrivelse ellers til at oprette en beskrivelse
 * 
 */
function harBeskrivelse($turID)
{
	
    $sql = "SELECT * FROM tur_beskrivelse WHERE `turID` = " . $turID . "";
    
	$result = mysql_query($sql) or die (mysql_error()); 
	$num = mysql_num_rows($result);
	
	
	if($num == 1)
		return "<a href=\"editTripDescription.php?desID=" . $turID . "\">Ændre/se beskrivelse</a>";
	else 
		return "<a href=\"newTripDescription.php?desID=" . $turID . "\">Tilføj beskrivelse</a><br />";
	
	
}

function harGPS($turID)
{
    $sql = "SELECT * FROM tur_beskrivelse WHERE `turID` = " . $turID . "";
    
	$result = mysql_query($sql) or die (mysql_error()); 
	$num = mysql_num_rows($result);
	
	
	if($num == 1)
	{
		$sql = "SELECT * FROM tur_beskrivelse WHERE `turID` = " . $turID . " AND adresse != ''";
	    
		$result = mysql_query($sql) or die (mysql_error()); 
		$num = mysql_num_rows($result);
		
		if ($num == 0)
			return "<a href=\"newTripGPS.php?desID=" . $turID . "\">Tilføj GPS koordinater</a>";
		else
			return "<a href=\"editTripGPS.php?desID=" . $turID . "\">Ændre/se GPS koordinater</a>";
	}
		
	else 
		return "<i>Opret venligst en beskrivelse ørst</i>";
	
	
}


////////////////////////////////////////////////////////////////////////////////////////////////////
//										BRUGER INDPUT TJEK										  //
////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Funktion til at tjekke om mailadresse en rigtigt. 
 * @param Mailadressen som skal valideres
 * 
 * @return true eller false
 * 
 */
function checkEmailAddress($email) 
{
	$pattern = "/^[\w-]+(\.[\w-]+)*@";
    $pattern .= "([0-9a-z][0-9a-z-]*[0-9a-z]\.)+([a-z]{2,4})$/i";
    
	if (preg_match($pattern, $email)) 
	{
        $parts = explode("@", $email);
        if (checkdnsrr($parts[1], "MX"))
		    return true;
        else
            return false;
    } 
	else 
		return false; 
}


/**
 * Tjekker om det indtastede post nummer opfylder kravende. 
 * Som er på 4 tal
 * 
 * 
 */
function isZipCode ($postnr)
{
	if($postnr == "")
		return true;
		
	if(strlen($postnr) == 4 && is_numeric($postnr))
		return true; 
	else
		return false;
	
}

function isTelephone ($nummer)
{		
	if($nummer == "")
		return true;
	
	$nummer = str_replace("+45", "", $nummer);
	$nummer = str_replace("0045", "", $nummer);
	
	if(strlen($nummer) == 8 && is_numeric($nummer))
		return true; 
	else
		return false;
	
	
}


////////////////////////////////////////////////////////////////////////////////////////////////////
//										GET FUNKTIONER											  //
////////////////////////////////////////////////////////////////////////////////////////////////////



function getMailFromUsername ($username)
{
	$data = mysql_query("select `eveID` from users where `username` = '" . $username . "' LIMIT 1") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		$eveID = $row["eveID"];
	}	
	
	return getMailFromEVE($eveID);
}


function getNameFromEVE ($eveID)
{
	$conn = connectToDB();
	$sql = "select top (1) CuFirstNameTx, CuLastNameTx from EVE.dbo.T_CuCust WHERE CuCustID = " . $eveID . "";		
	
	$row=odbc_exec($conn, $sql);
	$name = "";
	while(odbc_fetch_row($row))
	{
		$name .= odbc_result($row, "CuFirstNameTx");
		$name .= " " . odbc_result($row, "CuLastNameTx");
	}
	return $name;
}


function getUpcomingTrips ($eveID)
{
	$conn = connectToDB();
	$sql = "SELECT T_TpTrip.TpDestinationID_N as 'sted', T_TpTrip.TpStartDate_N as 'startDate', T_TpTrip.TpTripID as 'tripID'
	        FROM EVE.dbo.T_CpCustTrip INNER JOIN EVE.dbo.T_TpTrip ON T_CpCustTrip.CpTripID = T_TpTrip.TpTripID
			WHERE (T_CpCustTrip.CpCustID = " . $eveID . ") AND (T_TpTrip.TpStartDate_N >= '" . date("Y-m-d") . "')
			ORDER BY T_TpTrip.TpStartDate_N";
	
	
	
	$output = "<div id=\"wrap\">";
	
	$row=odbc_exec($conn, $sql);
	
	if(odbc_num_rows($row) == 0)
		return "<i>Ingen kommende ture. <br /><a href=\"kalender.php\">Book din næste tur her</a></i>";
	
	while(odbc_fetch_row($row))
	{
		$output .= "<div id=\"left_col\"><a href=\"info.php?tripID=" . odbc_result($row,"tripID") . "\">" . fixlength(getDestinationByID(odbc_result($row, "sted")),25,"...") . "</a></div>";
		$output .= "<div id=\"right_col\">" . convertDateForPrint(odbc_result($row, "startDate")) . "</div><br />";
	}
	
	$output .= "</div>";
	return $output;
}

function fixlength ($input, $length, $chars)
{

	if(strlen($input) >= $length)
		return substr($input, 0, $length) . "" . $chars;
	else
		return $input;
	
}

function getUpcommingCourses ($eveID)
{
	$conn = connectToDB();
	$sql = "SELECT T_CsCourse.CsCourseTypeID as 'type', T_CsCourse.CsCourseNoTx, T_CsCourse.CsStartDate as 'startDate'";
	$sql .= " FROM T_C1CustCourse INNER JOIN T_CsCourse ON T_C1CustCourse.C1CourseID_N = T_CsCourse.CsCourseID";
	$sql .= " WHERE (T_C1CustCourse.C1CustID = " . $eveID . ") AND (T_CsCourse.CsStartDate >= '" . date("Y-m-d") . "')";
	$sql .= " ORDER BY T_CsCourse.CsStartDate";
	
	
	$output = "<div id=\"wrap\">";
	
	
	$row=odbc_exec($conn, $sql);
	
	if(odbc_num_rows($row) == 0)
		return "<i>Ingen kommende kurser. <br /><a href=\"kalender.php\">Book dit næste kursus her</a></i>";
	
	while(odbc_fetch_row($row))
	{
		$output .= "<div id=\"left_col\">" . getCourseTypeFromID(odbc_result($row, "type")) . "</div>";
		$output .= "<div id=\"right_col\">" . convertDateForPrint(odbc_result($row, "startDate")) . "</div><br />";
	}
	
	$output .= "</div>";
	return $output;	
	
}



function getUpcomingTripsStaff ($eveID)
{
	
	$conn = connectToDB();
	$sql = "Select TOP (10) ETTripID, TpTripNoTx, TpDestinationID_N FROM T_ETEmpTrip
JOIN T_TpTrip ON T_ETEmpTrip.ETTripID = T_TpTrip.TpTripID
where ETEmpID = " . $eveID . " ORDER BY T_TpTrip.TpStartDate_N DESC";
	
	$output = "<div id=\"wrap\">";
	
	$row=odbc_exec($conn, $sql);
	
	if(odbc_num_rows($row) == 0)
		return "<i>Ingen kommende ture. <br /><a href=\"kalender.php\">Book din næste tur her</a></i>";
	
	$output .= "<div id=\"left_col\"><b><u>Turnavn</u></b></div>";
	$output .= "<div id=\"right_col\"><b><u>Antal</u></b></div><br /><br />";
	while(odbc_fetch_row($row))
	{
		$output .= "<div id=\"left_col\">" . odbc_result($row, "TpTripNoTx") . "</div>";
		$output .= "<div id=\"right_col\">" . countNumberOfCoumsterOnTrip(odbc_result($row, "ETTripID")) . "</div><br />";
	}
	
	$output .= "</div>";
	return $output; 
}


function countNumberOfCoumsterOnTrip ($tripID)
{
	$sql = "SELECT COUNT(*) AS 'antal' FROM T_CpCustTrip WHERE (CpTripID = " . $tripID . ")";

	$row=odbc_exec(connectToDB(), $sql);
	
	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "antal");	
	}
}


function getUpcommingCoursesStaff ($eveID)
{

	$sql = "select TOP 10 * from EVE.dbo.T_EeEmpCourse where EeEmpID = " . $eveID . " order by EeEmpCourseID DESC";


	$antal = 10;
/*	$sql = "SELECT TOP (" . $antal . ") T_TpTrip.TpDestinationID_N as sted, T_TpTrip.TpStartDate_N as startDate FROM T_TpTrip 
	INNER JOIN T_CpCustTrip ON T_TpTrip.TpTripID = T_CpCustTrip.CpCustTripID 
    WHERE (T_CpCustTrip.CpCustID = " . $eveID . ") ORDER BY T_TpTrip.TpStartDate_N DESC";
*/	
	
	$row=odbc_exec(connectToDB(), $sql);
	
	if(odbc_num_rows($row) == 0)
		return "<i>Ingen tidligere ture. <a href=\"kalender.php\">Book din næste tur her</a></i>";
	
	$output = "";

	$output .= "<div id=\"left_col\"><b><u>Kursusnavn</u></b></div>";
	$output .= "<div id=\"right_col\"><b><u>Antal</u></b></div><br /><br />";

	while(odbc_fetch_row($row))
	{
		$output .= "<div id=\"left_col\">" . fixlength(getCourseNameFromID(odbc_result($row, "EeCourseID")),25,"...") . "</div>";
		$output .= "<div id=\"right_col\">" . getNumberOfcustomerFromCourse(odbc_result($row, "EeCourseID")) . "</div><br />";
		
		
	}
	
	return $output;
}


function getNumberOfcustomerFromCourse($courseID)
{
	$sql = "select COUNT(*) as 'Antal' from T_C1CustCourse where C1CourseID_N = " . $courseID . "";
	
	$row=odbc_exec(connectToDB(), $sql);
	while(odbc_fetch_row($row))
	{
		
		return odbc_result($row, "Antal");
		
	}

}



function getLastTrips ($eveID, $antal)
{
	/*$sql = "SELECT TOP (" . $antal . ") T_TpTrip.TpDestinationID_N as sted, T_TpTrip.TpStartDate_N as startDate FROM T_TpTrip 
	INNER JOIN T_CpCustTrip ON T_TpTrip.TpTripID = T_CpCustTrip.CpCustTripID 
    WHERE (T_CpCustTrip.CpCustID = " . $eveID . ") ORDER BY T_TpTrip.TpStartDate_N DESC";
	*/
	
	$sql = "SELECT TOP (" . $antal . ") T_CpCustTrip.CpCustID, T_CpCustTrip.CpTripID, T_TpTrip.TpTripNoTx, 
	T_TpTrip.TpDestinationID_N as sted, T_TpTrip.TpStartDate_N as startDate FROM T_CpCustTrip INNER JOIN 
	T_TpTrip ON T_CpCustTrip.CpTripID = T_TpTrip.TpTripID 
	WHERE (T_CpCustTrip.CpCustID = " . $eveID . ") ORDER BY T_CpCustTrip.CpTripID DESC";
	
	$row=odbc_exec(connectToDB(), $sql);
	
	if(odbc_num_rows($row) == 0)
		return "<i>Ingen tidligere ture. <br /><a href=\"kalender.php\">Book din næste tur her</a></i>";
	
	$output = "";
	
	
	
	
	while(odbc_fetch_row($row))
	{
		$output .= "<div id=\"left_col\">" . fixlength(getDestinationByID(odbc_result($row, "sted")),25,"...") . "</div>";
		$output .= "<div id=\"right_col\">" . convertDateForPrint(odbc_result($row, "startDate")) . "</div><br />";
		
		
	}
	
	return $output;
}



function getCertification ($eveID)
{
	$conn = connectToDB();
	$sql = "select C1CourseTypeID, C1CertificationNoTx_N from EVE.dbo.T_C1CustCourse where (C1CustID = " . $_SESSION["eveID"] . ") AND (C1CertificationNoTx_N != '') ORDER BY C1CertificationDate_N DESC";		
	
	$row=odbc_exec($conn, $sql);
		
	if(odbc_num_rows($row) == 0)
		return "<i>Vi her ikke registret nogle certifikater</i>";
	
	$output = "";
	
	while(odbc_fetch_row($row))
	{
		$output .= "<div id=\"left_col\">" . fixlength(getCourseTypeFromID(odbc_result($row, "C1CourseTypeID")),25,"...")	 . "</div>";
		$output .= "<div id=\"right_col\">" . odbc_result($row, "C1CertificationNoTx_N") . "</div><br />";
		
		
	}
	
	return $output;
}




function getCustTripID ($eveID, $tripID)
{
	$conn = connectToDB();
	
	$sql = "select TOP (1) CpCustTripID from EVE.dbo.T_CpCustTrip WHERE CpCustID = " . $eveID . " AND CpTripID = " . $tripID . "";		
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "CpCustTripID");	
	}
	
}



function getMobileFromEVE ($eveID)
{
	$conn = connectToDB();
	$sql = "select top (1) CuTelMobileTx_N from EVE.dbo.T_CuCust WHERE CuCustID = " . $eveID . "";		
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "CuTelMobileTx_N");

	}

}

function getMailFromEVE ($eveID)
{
	$conn = connectToDB();
	$sql = "select top (1) CuEMailTx_N from EVE.dbo.T_CuCust WHERE CuCustID = " . $eveID . "";		
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "CuEMailTx_N");

	}	
}



function getFastnetFromEVE ($eveID)
{
	$conn = connectToDB();
	$sql = "select top (1) CuTelHomeTx_N from EVE.dbo.T_CuCust WHERE CuCustID = " . $eveID . "";		
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "CuTelHomeTx_N");

	}		
	
}

function getAdresseFromEVE ($eveID)
{
	$conn = connectToDB();
	$sql = "select top (1) CuAddress1Tx_N from EVE.dbo.T_CuCust WHERE CuCustID = " . $eveID . "";		
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "CuAddress1Tx_N");

	}		
	
}

function getPostnrFromEVE ($eveID)
{
	$conn = connectToDB();
	$sql = "select top (1) CuPostcodeTx_N from EVE.dbo.T_CuCust WHERE CuCustID = " . $eveID . "";		
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		return preg_replace("/[^0-9]+/","",odbc_result($row, "CuPostcodeTx_N"));

	}		
}


function getByFromEVE ($eveID)
{
	$conn = connectToDB();
	$sql = "select top (1) CuAddress3Tx_N from EVE.dbo.T_CuCust WHERE CuCustID = " . $eveID . "";		
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "CuAddress3Tx_N");

	}			
	
}

	
	
function getANameFromEVE ($eveID)
{
	$conn = connectToDB();
	$sql = "select top (1) CuNOKNameTx_N from EVE.dbo.T_CuCust WHERE CuCustID = " . $eveID . "";		
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "CuNOKNameTx_N");

	}			
	
}

function getAForholdFromEVE ($eveID)
{
	$conn = connectToDB();
	$sql = "select top (1) CuNOKRelationshipTx_N from EVE.dbo.T_CuCust WHERE CuCustID = " . $eveID . "";		
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "CuNOKRelationshipTx_N");

	}			
	
}

function getAMobileFromEVE ($eveID)
{
	$conn = connectToDB();
	$sql = "select top (1) CuNOKTelTx_N from EVE.dbo.T_CuCust WHERE CuCustID = " . $eveID . "";		
	
	$row=odbc_exec($conn, $sql);
	
	
	while(odbc_fetch_row($row))
	{
		 return odbc_result($row, "CuNOKTelTx_N");

	}	
	
	
}

function stripPhoneNumber ($telefon)
{
	$telefon = str_replace(" ", "", $telefon);
	
	return str_replace("+45","",$telefon);
}

function getAAdresseFromEVE ($eveID)
{
	$conn = connectToDB();
	$sql = "select top (1) CuNOKAddress1Tx_N from EVE.dbo.T_CuCust WHERE CuCustID = " . $eveID . "";		
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "CuNOKAddress1Tx_N");

	}			
}

function getAPostnrFromEVE ($eveID)
{
	$conn = connectToDB();
	$sql = "select top (1) CuNOKPostcodeTx_N from EVE.dbo.T_CuCust WHERE CuCustID = " . $eveID . "";		
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		return preg_replace("/[^0-9]+/","",odbc_result($row, "CuNOKPostcodeTx_N"));

	}			
}


function getAByFromEVE ($eveID)
{
	$conn = connectToDB();
	$sql = "select top (1) CuNOKAddress3Tx_N from EVE.dbo.T_CuCust WHERE CuCustID = " . $eveID . "";		
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "CuNOKAddress3Tx_N");

	}			
}


//TODO
function getLastCertifikatFromEVE ($eveID)
{

	$conn = connectToDB();
	$sql = "select top (1) CuEMailTx_N from EVE.dbo.T_CuCust WHERE CuCustID = " . $eveID . "";		
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		return "Certifikat"; //FIX dette

	}	
}





function getLastCertifikatNrFromEVE ($eveID)
{
	$conn = connectToDB();
	$sql = "select top (1) CuEMailTx_N from EVE.dbo.T_CuCust WHERE CuCustID = " . $eveID . "";		
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		return "Certifikat nr"; //FIX dette

	}		
}


function getEquipmentProfile ($eveID, $type)
{
	switch($type)
	{
		case "tank":
			$stockType = 1008;
			break;
		case "bcd":
			$stockType = 1037;
			break;
		case "bly":
			$stockType = 1002;
			break;
		case "finner":
			$stockType = 1045;
			break;
		case "handsker":
			$stockType = 1001;
			break;
		case "maske":
			$stockType = 1046;
			break;
		case "reg":
			$stockType = 1038;
			break;
		case "boots":
			$stockType = 1000;
			break;
		case "dragt":
			$stockType = 1034;	
			break;
		case "Tilbehør":
			$stockType = 1214;	
			break;
	}
	
	
	$conn = connectToDB();
	$sql = "SELECT TOP (1) PrCustID, PrStockTypeID, PrSizeID_N, PrNotesTx_N FROM T_PrProfile WHERE (PrCustID = " . $eveID . ") AND (PrStockTypeID = " . $stockType . ")";		
	
	$row=odbc_exec($conn, $sql);
	
	$size = "";
	$count = 0;
	
	while(odbc_fetch_row($row))
	{
		$size = odbc_result($row, "PrSizeID_N");
		$count++;
	}		
	
	if($size != null && $count == 1 && $size == 0 && ($type == "maske" || $type == "reg" || $type == "tank"))
		return "checked";
	else
		return $size;
	
}

function getDescription ($tripID)
{
	$description = "";
	$count = 0;
	$data = mysql_query("select `description` from tur_beskrivelse where `turID` = " . $tripID . "") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		$description = nl2br($row["description"]);
		$count++;
	}
	
	if($count == 1)
		return $description;
	else
		return "<i>Der kunne desværre ikke finde nogen beskrivelse</i>";

}

function getPrice ($input)
{
	if($input == "")
		return "100kr/0kr";
	return $input;	
	
}

function getPriceFromTripID ($tripID, $type)
{
	$sql="SELECT * FROM EVE.dbo.T_TpTrip WHERE TpTripID = " . $tripID . "";

	$row=odbc_exec(connectToDB(), $sql);
	while(odbc_fetch_row($row))
	{
		$pris = odbc_result($row, "TpPrivateNotesTx_N");
	}
	
	if($pris == "")
	{
		if($type == "medlem")
			return "0";
		else
			return "100";
	}
	
	$member = substr($pris, 0, strpos($pris, "/"));
	$normal = substr($pris, strpos($pris, "/")+1);
	
	if($type == "medlem")
		return $member;
	else
		return $normal;
}


function getTimeAtDiving2000 ($startDate, $arrivalTime)
{
	return date("H:i",strtotime(date("Y-m-d H:i:s", strtotime($startDate)) . " -" . $arrivalTime . " minutes"));
	
}

function getSizeID ($stockTypeID, $size)
{
	$sql = "SELECT EzSizeID FROM T_EzSize WHERE (EzSizeTx = '" . $size . "') AND (EzStockTypeID = " . $stockTypeID . ") AND (EzUnavailableBl = 0)	";
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "EzSizeID");
	}	
}

function getMedlemsStatus ($eveID)
{
	$conn = connectToDB();

	$sql = "SELECT C4CustID, C4CustTypeID, C4LastUpdatedDate FROM T_C4CustCustType WHERE (C4CustID = " . $eveID . ") AND (C4CustTypeID = 10)";		
	$row=odbc_exec($conn, $sql);
	
	if(odbc_num_rows($row) == 1)
	{
		return "Du er ansat, så du er medlem";	
	}

	
	
	
	$sql = "select TOP 1 CfCardValidToDate_N from EVE.dbo.T_CfCustClub where CfClubID = 1 AND CfCustID = " . $eveID . "";		
	
	$row=odbc_exec($conn, $sql);
	
	$date = "";
	$expireDate = "1";
	while(odbc_fetch_row($row))
	{
		$date = odbc_result($row, "CfCardValidToDate_N");

	}
	
	if (strlen($date) > 8) //Er medlem eller har været
	{
		$expireDate = substr($date, 0, 10);
		
		$dateDiff = dateDiff(date("Y-m-d"), $expireDate);
		
		if ($dateDiff < 30 && $dateDiff > 0)
			return "<div class=\"klubExpire\">JA! Medlemskabet udløber den " . convertDateForPrint($expireDate) . " (Om " . $dateDiff . " dage). <a href=\"http://www.shop-diving2000.dk/group.asp?group=127\">Forlæng dit medlemsskab her</a></div>";
		if($dateDiff > 0)
			return "JA! Medlemskabet udløber den " . convertDateForPrint($expireDate) . " (Om " . $dateDiff . " dage)";
		else
			return "Ikke medlem. <a href=\"http://www.shop-diving2000.dk/product.asp?product=5897\">Køb dit medlemsskab her.</a>";
		
	}
	else
		return "Ikke medlem. <a href=\"http://www.shop-diving2000.dk/product.asp?product=5897\">Køb dit medlemsskab her.</a>";
	
	
}


function getMedlemsStatusBool ($eveID)
{
	$conn = connectToDB();

	$sql = "SELECT C4CustID, C4CustTypeID, C4LastUpdatedDate FROM T_C4CustCustType WHERE (C4CustID = " . $eveID . ") AND (C4CustTypeID = 10)";		
	$row=odbc_exec($conn, $sql);
	
	if(odbc_num_rows($row) == 1)
	{
		return true;
	}
	
	
	$sql = "select TOP 1 CfCardValidToDate_N from EVE.dbo.T_CfCustClub where CfClubID = 1 AND CfCustID = " . $eveID . "";
	
	$row = odbc_exec($conn, $sql);
	
	$date = "";
	$expireDate = "1";
	while(odbc_fetch_row($row))
	{
		$date = odbc_result($row, "CfCardValidToDate_N");
		
	}
	
	if(strlen($date) > 8)
	{
		$expireDate = substr($date, 0, 10);
		
		$dateDiff = dateDiff(date("Y-m-d"), $expireDate);
		
		if($dateDiff > 0)
			return true;
		else
			return false;
		
	}
	else
		return false;
	
	
}

function getEvent ($dato)
{
	
	
	$newDate = "";
	$newDate .= substr($dato, 5, 2);
	$newDate .= "/" . substr($dato, 8, 2);

	$newDate .= "/" . substr($dato, 0, 4);


	$dato2 = str_replace("-", "", $dato);
	$toDay = date("Ymd");
	
	
	if($dato2 >= $toDay)
	{
		$temp = "";
		
		$conn = connectToDB();
		
		
		//Ture
		$dateToMorrow = date("Y-m-d",strtotime(date("Y-m-d", strtotime($dato)) . " +1 day"));
		$sql = "select * from EVE.dbo.T_TpTrip WHERE (TpStartDate_N BETWEEN '" . $dato . "' AND '" . $dateToMorrow . "') AND (TpTripStatusID = 1)";		
		
		$row=odbc_exec($conn, $sql);

		while(odbc_fetch_row($row))
     	{
        	
			$temp .= "<a href=\"info.php?tripID=" . odbc_result($row,"TpTripID") . "\"><font color=\"#FF0000\">" . getDestinationByID(odbc_result($row,"TpDestinationID_N")) . "</font></a><br>";
		}

		
		//Kursus
		$dateToMorrow = date("Y-m-d",strtotime(date("Y-m-d", strtotime($dato)) . " +1 day"));
		$sql = "select * from EVE.dbo.T_CsCourse WHERE (CsStartDate = '" . $dato . "') AND (CsCourseStatusID = 1)";		
		
		$row=odbc_exec($conn, $sql);

		while(odbc_fetch_row($row))
     	{
        	
			$temp .= "<a href=\"infoCourse.php?courseID=" . odbc_result($row,"CsCourseID") . "\"><font color=\"#CC3300\">" . getCourseTypeFromID(odbc_result($row, "CsCourseTypeID")) . "</font></a><br>";
		}
		
		
		
		return $temp;
	}
	else
		return "";
}


function getCourseForMobile ($dato, $returnID = false)
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
				$temp .= "" . convertDateForPrint(substr(odbc_result($row, "CsStartDate"), 0, 10)) . " - " . getCourseTypeFromID(odbc_result($row, "CsCourseTypeID")) . "§";
		}
	}
	
	
	
	return $temp;
	
}



function getTripForMobile ($dato, $returnID = false)
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
		if(odbc_result($row, "TpStartDate_N") != "")
		{
			if($returnID)
				$temp .= "T" . convertDateForPrint(substr(odbc_result($row, "TpStartDate_N"), 0, 10)) . " - " . getDestinationByID(odbc_result($row, "TpDestinationID_N")) . "§";
			else
				$temp .= "" . convertDateForPrint(substr(odbc_result($row, "TpStartDate_N"), 0, 10)) . " - " . getDestinationByID(odbc_result($row, "TpDestinationID_N")) . "§";
		}
	}
	
	return $temp;
}




function getEventForMobile ($dato, $returnID = false)
{
	$conn = connectToDB();
	
	
	//Ture
	$dateToMorrow = date("Y-m-d",strtotime(date("Y-m-d", strtotime($dato)) . " +1 day"));
	$sql = "select * from EVE.dbo.T_TpTrip WHERE (TpStartDate_N BETWEEN '" . $dato . "' AND '" . $dateToMorrow . "') AND (TpTripStatusID = 1)";		
	
	$row=odbc_exec($conn, $sql);

	$temp = "";
	$tripID = 0;
	
	while(odbc_fetch_row($row))
 	{
		if(odbc_result($row, "TpStartDate_N") != "")
		{
			if($returnID)
				return "T" . odbc_result($row, "TpTripID");
			else
				$temp .= "" . convertDateForPrint(substr(odbc_result($row, "TpStartDate_N"), 0, 10)) . " - " . getDestinationByID(odbc_result($row, "TpDestinationID_N")) . "§";
		}
	}

	
	//Kursus
	$sql = "select * from EVE.dbo.T_CsCourse WHERE (CsStartDate = '" . $dato . "') AND (CsCourseStatusID = 1)";		
	
	$row=odbc_exec($conn, $sql);

	while(odbc_fetch_row($row))
 	{
		if(odbc_result($row, "CsStartDate") != "")
		{
			if($returnID)
				return "K" . odbc_result($row, "CSCourseID");
			else	
				$temp .= "" . convertDateForPrint(substr(odbc_result($row, "CsStartDate"), 0, 10)) . " - " . getCourseTypeFromID(odbc_result($row, "CsCourseTypeID")) . "§";
		}
	}
	
	
	
	return $temp;
	
}

function getCourseDate ($courseID)
{
	$sql = "select * from EVE.dbo.T_CsCourse WHERE (CsCourseID = " . $courseID . ")";		
	
	$row=odbc_exec(connectToDB(), $sql);

	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "CsStartDate");
		
	}
}



function getCourseTypeIDFromCourseName ($courseName)
{

	$sql = "SELECT * FROM T_CTCourseType where (CTCourseTypeTx = '" . $courseName . "')";
	
	$row=odbc_exec(connectToDB(), $sql);
	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "CTCourseTypeID");	
	}
}

function getDestinationIDFromName($title)
{
	$sql = "select DtDestinationID, DtDestinationTx from EVE.dbo.T_DtDestination where DtDestinationTx LIKE '%" . $title . "%'";

	$row=odbc_exec(connectToDB(), $sql);
	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "DtDestinationID");	
	}	
}


/**
 * input er dd/mm-yyyy - Title
 */
function printBeskrivelseForMobile ($navn)
{
	$dato = substr($navn, 1, 10);
	$title = substr($navn, 14);
	
	
	
	
	if(substr($navn, 0, 1) == "K")
	{

	
		$uds = "<h2>" . $title . " den " . $dato . "</h2>";
		$uds .= getDescriptionCourse(getCourseTypeIDFromCourseName($title)) . "";
		$uds .= "<br />";

	}
	else
	{
		$date = substr($dato, 6, 4);
		$date .= "-" . substr($dato, 3, 2);
		$date .= "-" . substr($dato, 0, 2);
		
		
		$sql="SELECT * FROM EVE.dbo.T_TpTrip WHERE (TpStartDate_N > '" . $date . "') AND (TpDestinationID_N = " . getDestinationIDFromName($title) . ") ORDER BY TpStartDate_N";

		$row=odbc_exec(connectToDB(), $sql);
		while(odbc_fetch_row($row))
		{
			$pris = getPrice(odbc_result($row, "TpPrivateNotesTx_N"));	
		}		
		
		$uds = "<h2>" . $title . " den " . $dato . "</h2><br />";
		$uds .= "" . getDescription(getDestinationIDFromName($title)) . "<br />";
		$uds .= "<b>Pris: " . $pris . "</b>";
		
	
	
	
	}

	return $uds;
	
}

function getGPSForMobile ($input)
{
	
	$dato = substr($input, 1, 10);
	$title = substr($input, 14);
	
	
	$adresse = "";
	
	if(substr($input, 0, 1) == "T")
	{
		$data = mysql_query("select * from tur_beskrivelse where `turID` = " . getDestinationIDFromName($title) . "") or die(mysql_error());
		while($row = mysql_fetch_array($data))
		{
			$adresse = $row["adresse"] . " " . $row["postnr"] . "";
		}
		
		if($adresse == "")
			return "Asylgade 16 5000";
		else
			return $adresse;
		
	}
	else
	{
		return "Asylgade 16 5000"; //default - Diving 2000	
	}
	
}


function getLinkForMobile ($input)
{
	$dato = substr($input, 1, 10);
	$title = substr($input, 14);
	
	
	
	
	if(substr($input, 0, 1) == "K")
	{


	}
	else
	{
		$date = substr($dato, 6, 4);
		$date .= "-" . substr($dato, 3, 2);
		$date .= "-" . substr($dato, 0, 2);
		
		
		$sql="SELECT * FROM EVE.dbo.T_TpTrip WHERE (TpStartDate_N > '" . $date . "') AND (TpDestinationID_N = " . getDestinationIDFromName($title) . ") ORDER BY TpStartDate_N";

		$row=odbc_exec(connectToDB(), $sql);
		while(odbc_fetch_row($row))
		{
			$id = odbc_result($row, "TpTripID");	
		}		
	
	
	}

	return $id;	
	
}



function getCourseNameFromID ($courseID)
{
	$sql = "Select TOP (1) CsCourseNoTx FROM T_CsCourse WHERE (CsCourseID = " . $courseID . ")";		
	
	$row=odbc_exec(connectToDB(), $sql);
	while(odbc_fetch_row($row))
 	{
    	return odbc_result($row, "CsCourseNoTx");
	}
	
	
	
	
}

function getModuleNameFromID ($moduleID)
{
	$sql = "select MTModuleTypeTx from EVE.dbo.T_MTModuleType WHERE (MTModuleTypeID = " . $moduleID . ")";		
	
	$row=odbc_exec(connectToDB(), $sql);
	while(odbc_fetch_row($row))
 	{
    	return odbc_result($row, "MTModuleTypeTx");
	}
	
	
}

function getModuleLocationFromID ($location)
{
	if($location == "")
		return "";
		
	$sql = "select LcLocationTx from EVE.dbo.T_LcLocation WHERE (LcLocationID = " . $location . ")";		
	
	$row=odbc_exec(connectToDB(), $sql);
	while(odbc_fetch_row($row))
 	{
    	return odbc_result($row, "LcLocationTx");
	}
	
	
}

function getCourseTypeFromCourseID ($courseID, $type = false)
{
	$sql = "select * from EVE.dbo.T_CsCourse WHERE (CsCourseID = " . $courseID . ")";		
	
	$row=odbc_exec(connectToDB(), $sql);
	while(odbc_fetch_row($row))
 	{
		if($type)
			return odbc_result($row, "CsCourseTypeID");
		else
    		return getCourseTypeFromID(odbc_result($row, "CsCourseTypeID"));
	}

	
}

function getCourseTypeFromID ($courseTypeID)
{
	$sql = "select CTCourseTypeTx from EVE.dbo.T_CTCourseType WHERE (CTCourseTypeID = " . $courseTypeID . ")";		
	
	$row=odbc_exec(connectToDB(), $sql);
	while(odbc_fetch_row($row))
 	{
    	return odbc_result($row, "CTCourseTypeTx");
	}
	
}



function getDestinationByID ($destinationID)
{
	if($destinationID == "")
		return "Ukendt";
		
	$conn = connectToDB();
	
	$sql = "select DtDestinationID, DtDestinationTx from EVE.dbo.T_DtDestination WHERE DtDestinationID = " . $destinationID . "";
		
	$row=odbc_exec($conn, $sql);

	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "DtDestinationTx");
		
	}
	
}

function getDestinationFromTripID($tripID, $type = false)
{
	$sql="SELECT * FROM EVE.dbo.T_TpTrip WHERE TpTripID = " . $tripID . "";

	$row=odbc_exec(connectToDB(), $sql);
	while(odbc_fetch_row($row))
	{
		if($type)
			return odbc_result($row, "TpDestinationID_N");
		else
			$sted = getDestinationByID(odbc_result($row, "TpDestinationID_N"));
	}
	
	return $sted;
	
}


function getTripDate ($tripID)
{
	$sql="SELECT * FROM EVE.dbo.T_TpTrip WHERE TpTripID = " . $tripID . "";

	$row=odbc_exec(connectToDB(), $sql);
	while(odbc_fetch_row($row))
	{
	
		$date = odbc_result($row, "TpStartDate_N");
	}
	
	return $date;
}



function getSizeFromID ($sizeID)
{
	$conn = connectToDB();
	
	$sql = "SELECT EzSizeTx FROM T_EzSize WHERE (EzSizeID = " . $sizeID . ")";
	
	$row=odbc_exec($conn, $sql);
	
	
	while(odbc_fetch_row($row))
	{
		return odbc_result($row, "EzSizeTx");
	}
}


function getTripTimeToOutlook ($tripID)
{
	$date = getTripDate($tripID);
	
	$time = substr($date, 11);
	$time = str_replace(":", "", $time);
	
	$date = substr($date, 0, 10);
	$date = str_replace("-", "", $date);
	
	return $date . "T" . substr($time, 0, 4) . "00";	
}


function getTripEndTimeToOutlook ($tripID)
{
	$sql="SELECT * FROM EVE.dbo.T_TpTrip WHERE TpTripID = " . $tripID . "";

	$row=odbc_exec(connectToDB(), $sql);
	while(odbc_fetch_row($row))
	{
	
		$date = odbc_result($row, "TpEndDate_N");
	}
	
	$time = substr($date, 11);
	$time = str_replace(":", "", $time);
	
	$date = substr($date, 0, 10);
	$date = str_replace("-", "", $date);
	
	return $date . "T" . substr($time, 0, 4) . "00";	
}


function getEveIDFromUsername ($username)
{
	$data = mysql_query("select `eveID` from users where `username` = '" . $username . "' LIMIT 1") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		return $row["eveID"];	
	}
}



function getMostActiveOnTrips ($number)
{
	$beskrivelse = "";
	
	$sql="SELECT top " . $number . " COUNT(*) AS antal, CpCustID FROM T_CpCustTrip GROUP BY CpCustID ORDER BY antal DESC";

	$row=odbc_exec(connectToDB(), $sql);
	while(odbc_fetch_row($row))
	{
		$beskrivelse .=  odbc_result($row, "antal") . "&nbsp;&nbsp;&nbsp;&nbsp;" . getNameFromEVE(odbc_result($row, "CpCustID")) . "<br />";
		
	}
	
	return $beskrivelse;
	
}

function getMostActiveOnSite ($antal)
{
	$beskrivelse = "";
	$data = mysql_query("SELECT eveID, count(*) as antal FROM logs where eveID != 0 group by eveID order by antal DESC LIMIT " . $antal . "") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		$beskrivelse .= $row["antal"] . "&nbsp;&nbsp;&nbsp;&nbsp;" . getNameFromEVE($row["eveID"]) . "<br />";
	}
	
	return $beskrivelse;
}

function getMostActiveOnDiveLog ($antal)
{
	$beskrivelse = "";
	  
	$sql="SELECT top " . $antal . " COUNT(*) AS antal, DiCustID FROM T_DiDiveProfile GROUP BY DiCustID ORDER BY antal DESC";

	$row=odbc_exec(connectToDB(), $sql);
	while(odbc_fetch_row($row))
	{
		$beskrivelse .=  odbc_result($row, "antal") . "&nbsp;&nbsp;&nbsp;&nbsp;" . getNameFromEVE(odbc_result($row, "DiCustID")) . "<br />";
		
	}
	
	return $beskrivelse;
		
}

function getLastCreatedUsers ($antal)
{
	
	$beskrivelse = "";
	$data = mysql_query("SELECT eveID FROM users ORDER BY created DESC LIMIT " . $antal . "") or die(mysql_error());
	while($row = mysql_fetch_array($data))
	{
		$beskrivelse .= getNameFromEVE($row["eveID"]) . "<br />";
	}
	
	return $beskrivelse;
	
	
}








function makePassword ($length)
{
    $tegn = 'abcdefghkmnpqrstuvwxyzABCDEFGHKLMNPQRSTWXYZ123456789';
	$kodeord = "";
	
    for ($i = 1 ; $i <= $length ; $i++) 
	{
	    $kodeord .= $tegn[mt_rand(0, strlen($tegn)-1)];
	}	
	
	return $kodeord;
}



function updateUsernameInEVE($username, $eveID)
{
	$conn = connectToDB();
	$sql = "UPDATE [eve].[dbo].[T_CuCust] SET [CuUserNameTx_N] = '" . $username . "' WHERE CuCustID = " . $eveID . ";";
	$row=odbc_exec($conn, $sql);			
	
}





function resetEquipmentProfil ($eveID)
{
	$conn = connectToDB();
	$sql = "DELETE FROM T_PrProfile WHERE (PrCustID = " . $eveID . ")";
	
	$row=odbc_exec($conn, $sql);
	
}


function updateEquipment ($stockID, $size, $eveID) //BCD
{
	if($size == "intet")
		return;
		
	$date = date("Y-m-d H:i:s");
	
	$conn = connectToDB();
	$sql = "INSERT INTO [eve].[dbo].[T_PrProfile] ([PrCustID],[PrStockTypeID],[PrSizeID_N],[PrLastUpdatedDate],[PrCreationDate]) 
	VALUES (" . $eveID . "," . $stockID . "," . $size . ",'" . $date . "','" . $date . "')";
	

	$row=odbc_exec($conn, $sql);		
	
}





function buildDropDown ($formName, $stockTypeID, $stockTypeName, $eveID)
{
	
	$menu = "<select name=\"" . $formName . "\"> 
    <option value=\"intet\">Vælg størrelse</option>";
	
	$size = getEquipmentProfile($eveID, $stockTypeName);
	
	$conn = connectToDB();
	
	$sql = "SELECT EzSizeID, EzStockTypeID, EzSizeTx FROM T_EzSize WHERE (EzStockTypeID = " . $stockTypeID . ") AND (EzUnavailableBl = 0) ORDER BY EzDisplayOrderIn";
	
	$row=odbc_exec($conn, $sql);
	
	
	while(odbc_fetch_row($row))
	{
		if($size == odbc_result($row, "EzSizeID"))
		{
			$checked = "selected=\"selected\"";
		}
		else
		{
			$checked = "";
		}
		
		if($stockTypeID == 1214)
		{
			$temp = odbc_result($row, "EzSizeTx");
			
			
			$first = strpos($temp,"(")+1;
			$last = strpos($temp,")");
			
			
			$prisTemp = substr(odbc_result($row, "EzSizeTx"), $first, $last - $first);
			
			$pris = explode("/", $prisTemp);
			
			if (getMedlemsStatusBool($_SESSION["eveID"]))
				$vare = "" . substr($temp, 0, $first-2) . " - " . $pris[1] . "kr";
			else
				$vare = "" . substr($temp, 0, $first-2) . " - " . $pris[0] . "kr";
				
			$menu .= "<option value=\"" . odbc_result($row, "EzSizeID") . "\" " . $checked . ">" . $vare . "</option>";
			
		}
		else
			$menu .= "<option value=\"" . odbc_result($row, "EzSizeID") . "\" " . $checked . ">" . odbc_result($row, "EzSizeTx") . "</option>";
	}
	
	$menu .= "</select>";
	
	return $menu;
}


function getPriceFromAddOns($stockTypeID, $size, $type)
{
	
	$conn = connectToDB();
	
	$sql = "SELECT EzSizeID, EzStockTypeID, EzSizeTx FROM T_EzSize WHERE (EzStockTypeID = " . $stockTypeID . ") AND (EzSizeID = " . $size . ") ORDER BY EzDisplayOrderIn";
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		
		$temp = odbc_result($row, "EzSizeTx");
		
		$first = strpos($temp, "(") + 1;
		$last = strpos($temp, ")");
		
		
		$prisTemp = substr(odbc_result($row, "EzSizeTx"), $first, $last - $first);
	
		$pris = explode("/", $prisTemp);
		
		if($type == "medlem")
			return $pris[1];
		else
			return $pris[0];
	}			

}


function findEVEID($mail, $mobil, $birthDate)
{
	$conn = connectToDB();
	$birthDate .= " 00:00:00";
	
	$sql = "select top (1) CuCustID from EVE.dbo.T_CuCust WHERE (CuBirthDate_N = '" . $birthDate . "') AND (CuTelMobileTx_N = '" . $mobil . "') AND (CuEMailTx_N = '" . $mail . "')";
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		
		return odbc_result($row, "CuCustID");
	}
}

function findEVEID2($mail, $mobil, $birthDate, $firstName, $sex)
{
	$conn = connectToDB();
	$birthDate .= " 00:00:00";
	
	$sql = "select top (1) CuCustID from EVE.dbo.T_CuCust WHERE (CuBirthDate_N = '" . $birthDate . "') AND (CuTelMobileTx_N = '" . $mobil . "') AND (CuEMailTx_N = '" . $mail . "') AND (CuFirstNameTx = '" . $firstName . "') AND (CuSexID_N = " . $sex . ")";
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		
		return odbc_result($row, "CuCustID");
	}
}



function createUser($username, $password, $mail, $mobil, $birthDay, $fornavn, $efternavn, $sex, $authKey)
{
	$sql = "insert into EVE.dbo.T_CuCust (CuStoreID, CuFirstNameTx, CuLastNameTx, CuBirthDate_N, CuTelMobileTx_N, CuEMailTx_N, CuCreationDate, CuLastUpdatedDate, CuUserNameTx_N, CuSexID_N) VALUES (1, '" . $fornavn . "','" . $efternavn . "', '" . $birthDay . " 00:00:00', " . $mobil . ", '" . $mail . "', '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") . "', '" . $username . "', " . $sex . ")";
	
	odbc_exec(connectToDB(), $sql);
	
	$eveID = findEVEID2($mail, $mobil, $birthDay, $fornavn, $sex);
	
	if($eveID != 0)
	{
		$password = encryption($password);
		
		
		
		updateUsernameInEVE($username, $eveID);	
		
		
		
		$cookie_auth= md5(generateRandomString() . $username);
					
							
				
		$insertSQL = "INSERT into users (`eveID`, `username`, `password`, `created`, `activationKey`, `authKey`, `option`) values (" . $eveID . ", '" . mysql_real_escape_string($username) . "', '" . md5($password) . "', NOW(), '" . $authKey . "', '" . $cookie_auth . "', 'created')";
		
		mysql_query($insertSQL) or die(mysql_error());
		
		return $eveID;
		
	}
	
}

function isEnrolled ($eveID, $tripID)
{
	$conn = connectToDB();
	$sql = "select * from EVE.dbo.T_CpCustTrip WHERE CpCustID = " . $eveID . " AND CpTripID = " . $tripID . "";
	
	
	$row=odbc_exec($conn, $sql);
	
	return odbc_num_rows($row);
	
}



function isUsernameAvailab ($username)
{
	$username = mysql_real_escape_string($username);//Some clean up :)

	$check_for_username = mysql_query("SELECT username FROM users WHERE username='" . $username . "'");
	

	if(mysql_num_rows($check_for_username))
		return false;
	else
		return true; //Brugernavnet findes ikke
	
}

function isUsernameBlocked($username)
{
	$blockUsername = array("diving2000", "diving 2000", "admin", "administrator", "sex", "fuck");
		
	if(in_array(strtolower($username), $blockUsername))
		return true;
	else
		return false;
		return false;
	
}

function isEVEIDAvailab ($eveID)
{
	$checkForEveID = mysql_query("SELECT eveID FROM users WHERE eveID = " . $eveID . "");
	

	if(@mysql_num_rows($checkForEveID))
		return false;
	else
		return true; //EVEID'et findes ikke
	
}



function friePladser ($tripID)
{
	$conn = connectToDB();
	$sql="SELECT * FROM EVE.dbo.T_TpTrip WHERE TpTripID = " . $tripID . "";
	
	$row=odbc_exec($conn, $sql);
	
	while(odbc_fetch_row($row))
	{
		$maksDeltager = odbc_result($row, "TpMaxNoIn");

	}	
	
	return $maksDeltager - antalDeltager($tripID);
	
}


function antalDeltager ($tripID)
{
	$sql = "select * from EVE.dbo.T_CpCustTrip WHERE CpTripID = " . $tripID . "";
	
	$row=odbc_exec(connectToDB(), $sql);
	
	return odbc_num_rows($row);
	
}


function dateDiff($start, $end) 
{
	$start_ts = strtotime($start);
	$end_ts = strtotime($end);
	$diff = $end_ts - $start_ts;
	
	return round($diff / 86400);

}

function convertDateForPrint ($date)
{
	$year = substr($date, 0, 4);
	$month = substr($date, 5, 2);
	$day = substr($date, 8, 2);
	
	return $day . "/" . $month . "-" . $year;
	
}

function convertDateAndTimeForPrint ($date)
{
	$year = substr($date, 0, 4);
	$month = substr($date, 5, 2);
	$day = substr($date, 8, 2);
	$time = substr($date, 11, 5);
	
	return $day . "/" . $month . " kl. " . $time . "";

}

function createLog ($action, $eveID)
{	
	$insertSQL = "INSERT into logs (`eveID`, `ip`, `action`, `date`) values (" . $eveID . ", '" . $_SERVER['REMOTE_ADDR'] . "', " . $action . ", NOW())";
	mysql_query($insertSQL) or die(mysql_error());
}


function encryption ($password)
{
	return "mK&/" . $password . "?#F";
}

function connectToDB ()
{

	$data_source='EVEData';
	$user='EVEUser';
	$password='Dracu1a_99';

	return odbc_connect($data_source,$user,$password);
	
}




function generate_calendar($id, $year, $month, $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0, $pn = array())
{
	if ($month == 13)
	{
		$month = 1;
		$year++;
	}
		
	if ($month == 0)
	{
		$month == 12;
		$year--;
	}
		
	$nextMonth = $month + 1;
	$previousMonth = $month - 1;

	$previousYear = $year;
	$nextYear = $year;
	
	if ($nextMonth == 13)
	{
		$nextMonth = 1;
		$nextYear = $year + 1;
	}
	
	if ($previousMonth == 0)
	{
		$previousMonth = 12;
		$previousYear = $year - 1;
	}

	$first_of_month = gmmktime(0,0,0,$month,1,$year);
	#remember that mktime will automatically correct if invalid dates are entered
	# for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
	# this provides a built in "rounding" feature to generate_calendar()

	$day_names = array(); #generate all the day names according to the current locale
	//for($n=0,$t=(3+$first_day)*86400; $n<7; $n++,$t+=86400) #January 4, 1970 was a Sunday
	//	$day_names[$n] = ucfirst(gmstrftime('%A',$t)); #%A means full textual day name
		
	$day_names[0] = "Mandag";
	$day_names[1] = "Tirsdag";
	$day_names[2] = "Onsdag";
	$day_names[3] = "Torsdag";
	$day_names[4] = "Fredag";
	$day_names[5] = "L&oslash;rdag";
	$day_names[6] = "S&oslash;ndag";

	list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month));
	$weekday = ($weekday + 7 - $first_day-1) % 7; #adjust for $first_day
	
	
	$maaneder = array("Januar","Februar","Marts","April","Maj",
	"Juni","Juli","August","September","Oktober",
	"November","December"); 
	
	
	$title = "<div class=\"textInfo\"><a href=\"?id=" . $id . "&month=" . $previousMonth . "&year=" . $previousYear . "\"style=\"font-family: verdana, arial;font-size: 12px;color: #FFFFFF;\"><<</a>&nbsp;&nbsp;&nbsp;&nbsp;" . htmlentities($maaneder[$month-1]) . "&nbsp;" . $year . "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"?id=" . $id . "&month=" . $nextMonth . "&year=" . $nextYear . "\" style=\"font-family: verdana, arial;font-size: 12px;color: #FFFFFF;\">>></a></div><br />";
	
	//$title   = htmlentities(ucfirst($month_name)).'&nbsp;'.$year;  #note that some locales don't capitalize month and day names
	
	#Begin calendar. Uses a real <caption>. See http://diveintomark.org/archives/2002/07/03
	@list($p, $pl) = each($pn); @list($n, $nl) = each($pn); #previous and next links, if applicable
	if($p) $p = '<span class="text">'.($pl ? '<a href="'.htmlspecialchars($pl).'">'.$p.'</a>' : $p).'</span>&nbsp;';
	if($n) $n = '&nbsp;<span class="text">'.($nl ? '<a href="'.htmlspecialchars($nl).'">'.$n.'</a>' : $n).'</span>';
	$calendar = '<table style="border: 1px solid #000000;background-color: white;background-image: url(images/D2000.png); background-repeat: no-repeat; background-position: bottom right;" cellspacing="0" class="text" width="100%">'."\n".
		'<caption class="text">'.$p.($month_href ? '<a href="'.htmlspecialchars($month_href).'">'.$title.'</a>' : $title).$n."</caption>\n<tr style=\"border: 1px solid #000000;\">";

$day_name_length = 6;
	if($day_name_length){ #if the day names should be shown ($day_name_length > 0)
		#if day_name_length is >3, the full name of the day will be printed
		foreach ($day_names as $d)
			$calendar .= '<th bgcolor="#00309C" style="font-family: verdana, arial;font-size: 12px;color: #FFFFFF; border-bottom: 1px solid #000000;border-left: 1px solid #000000; width: 13%;" abbr="' . $d . '">' . ($day_name_length < 4 ? substr($d, 0, $day_name_length) : $d) . '</th>';
		$calendar .= "</tr>\n<tr>";
	}
	
	if($weekday > 0)
	{
		$i = $weekday;
		
		while($i > 0)
		{
			//Dage fra sidste måned
			$calendar .= '<td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000; width: 13%;" height=\"60\"></td>';
			#initial 'empty' days
			$i--;
		}
	}
	for($day=1,$days_in_month=gmdate('t',$first_of_month); $day<=$days_in_month; $day++,$weekday++){
		if($weekday == 7){
			$weekday   = 0; #start a new week
			$calendar .= "</tr>\n<tr>";
		}
/*		if(isset($days[$day]) and is_array($days[$day])){
			@list($link, $classes, $content) = $days[$day];
			if(is_null($content))  $content  = $day;
			$calendar .= '<td style=\"width: 13%;\" height=\"60\" valign=\"top\" '.($classes ? ' class="'.htmlspecialchars($classes).'"><b>' : '>').
				($link ? '<a href="'.htmlspecialchars($link).'" target="_blank">--'.$content.'</a>' : $content).'</b></td>';
		} */

		
		$date = mktime(0, 0, 0, $month, $day, $year); 
		$week = (int)date('W', $date); 
			
		if(false)
		{
		}
		else if(date("j") == $day && date("m") == $month)
		{

			if (date("N",$date) == 1)
				$calendar .= "<td style=\"border-left: 1px solid #000000;border-bottom: 1px solid #000000;width: 13%;\"  height=\"60\" valign=\"top\"><b>$day</b> - " . $week . "";
			else
				$calendar .= "<td style=\"border-left: 1px solid #000000;border-bottom: 1px solid #000000;width: 13%;\"  height=\"60\" valign=\"top\"><b>$day</b>";
			
			if($day < 10)
				$day = "0" . $day;
			
			if(getEvent($year . "-" . $month . "-" . $day))
				$calendar .= "<br />" . getEvent($year . "-" . $month . "-" . $day) . "";
			$calendar .= "</td>";
		}

		else
		{
			if (date("N",$date) == 1)
				$calendar .= "<td height=\"60\" valign=\"top\" style=\"border-left: 1px solid #000000;border-bottom: 1px solid #000000;\">" . $day . " <font class=\"week\">(" . $week . ")</font>";
			else
				$calendar .= "<td height=\"60\" valign=\"top\" style=\"border-left: 1px solid #000000;border-bottom: 1px solid #000000;\">$day";

			if($day < 10)
				$day = "0" . $day;
			
			if(getEvent($year . "-" . $month . "-" . $day))
				$calendar .= "<br />" . getEvent($year . "-" . $month . "-" . $day) . "";
			$calendar .= "</td>";
			
		}
	}
	if($weekday != 7)
	{
		$i = 7 - $weekday;
		
		while($i > 0)
		{
			//Dage fra næste måned
			$calendar .= '<td style="border-left: 1px solid #000000;border-bottom: 1px solid #000000;" height=\"60\">&nbsp;</td>';
			$i--;
		}
		
		
		
	}

	return $calendar."</tr>\n</table>\n";
}


//////////////////////////////////////// LOGIN FUNKTIONER ///////////////////////////////////////

/**
 * Funktion til at bestemme om en bestemt bruger har admin rettigheder
 * 
 * @param EVE ID
 * 
 * @return 1 Hvis man er Admin ellers 0
 * 
 */
function isAdmin ($eveID)
{
	if($eveID == "" || $eveID == null)
		return 0;
	
	$sql = "SELECT * FROM users WHERE `eveID` = " . $eveID . " AND accountType = 'admin'";
	$result = mysql_query($sql) or die (mysql_error()); 
	
	$num = mysql_num_rows($result);	
	
	return $num;
}


/**
 * Funktion til at bestemme om en bestemt bruger har admin rettigheder
 * 
 * @param EVE ID
 * 
 * @return 1 Hvis man er Admin ellers 0
 * 
 */
function isDM ($eveID)
{
	if($eveID == "" || $eveID == null)
		return 0;
	
	$sql = "SELECT * FROM users WHERE `eveID` = " . $eveID . " AND accountType = 'DM'";
	$result = mysql_query($sql) or die (mysql_error()); 
	
	$num = mysql_num_rows($result);	
	
	return $num;
}




function sec_session_start() 
{
    $session_name = 'sec_session_id'; // Set a custom session name
    $secure = false; // Set to true if using https.
    $httponly = true; // This stops javascript being able to access the session id. 

    ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies. 
    $cookieParams = session_get_cookie_params(); // Gets current cookies params.
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
    session_name($session_name); // Sets the session name to the one set above.
    session_start(); // Start the php session
    session_regenerate_id(true); // regenerated the session, delete the old one.     
}


function login($username, $password, $logInAs) 
{
   
    $sql = "SELECT `eveID` FROM users WHERE `status` = 'aktiv' AND `username` = '" . mysql_real_escape_string($username) . "' AND `password` = '" . $password . "'";
   
	$result = mysql_query($sql) or die (mysql_error()); 
	$num = mysql_num_rows($result);
	
    if($num == 1) 
	{ 
		if($logInAs != 0)
		{
			$data = mysql_query("select * from users where `eveID` = " . $logInAs . "") or die(mysql_error());
			while($row = mysql_fetch_array($data))
			{
				$DBeveID = $row["eveID"];
				$password = $row["password"];
			}	
			
		}
		else
		{
			
			$data = mysql_query("select `eveID`, `username` from users where `username` = '" . mysql_real_escape_string($username) . "' AND `password` = '" . $password . "'") or die(mysql_error());
			while($row = mysql_fetch_array($data))
			{
				$DBeveID = $row["eveID"];
				
			}
		}
		
        $ip_address = $_SERVER['REMOTE_ADDR']; // Get the IP address of the user. 
        $user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.

        $DBeveID = preg_replace("/[^0-9]+/", "", $DBeveID); // XSS protection as we might print this value
        
		$_SESSION['eveID'] = $DBeveID; 
        
        $_SESSION['login_string'] = hash('sha512', $password.$ip_address.$user_browser);
       // Login successful.
        return true;    
    }
	else
	{
        //TODO
		//SKriv log i DB
    
		return false;    
    }
}



function login_check() 
{
   // Check if all session variables are set
	if(isset($_SESSION['eveID'], $_SESSION['login_string'])) 
 	{
   		$eveID = $_SESSION['eveID'];
     	$login_string = $_SESSION['login_string'];
	 
     	$ip_address = $_SERVER['REMOTE_ADDR']; // Get the IP address of the user. 
     	$user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.
 
 
 
    	$result = mysql_query("SELECT * FROM users WHERE `status` = 'aktiv' AND `eveID` = " . $eveID . "") or die (mysql_error()); 
		$num = mysql_num_rows($result);
     
		if($num == 1)
		{
			$data = mysql_query("select `password` from users where `eveID` = " . $eveID . "") or die(mysql_error());
			while($row = mysql_fetch_array($data))
			{
				$DBpassword = $row["password"];
			}
			
			$login_check = hash('sha512', $DBpassword . $ip_address . $user_browser);
			
			if($login_check == $login_string)
			{
				// Logged In!!!!
				return true;
			}
			else
			{
				// Not logged in
				return false;
			}
		}
        else 
		{
            // Not logged in
            return false;
        }
    } 



	// Check that cookie is set
    if(isset($_COOKIE['auth_key']))
    {
        $auth_key = $_COOKIE['auth_key'];

        if($auth_key != "")
        {
            // Select user from database where auth key matches (auth keys are unique)
            $auth_key_query = mysql_query("SELECT eveID, username, password FROM users WHERE authKey = '" . $auth_key . "' LIMIT 1");
            if($auth_key_query === false)
            {
                // If auth key does not belong to a user delete the cookie
                setcookie("auth_key", "", time() - 3600);
				return false;
            }
            else
            {
                while($u = mysql_fetch_array($auth_key_query))
                {
					$ip_address = $_SERVER['REMOTE_ADDR']; // Get the IP address of the user. 
     				$user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.
					
					$_SESSION["eveID"] = $u['eveID'];
					$_SESSION['login_string'] = hash('sha512', $u["password"] . $ip_address . $user_browser);
					$_SESSION['username'] = $u["username"];
				 
                }
				
				return true;
            }
        }
        else
        {
            setcookie("auth_key", "", time() - 3600);
			return false;
        }
    }

}



function generateRandomString($length = 10) 
{
    $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

?>