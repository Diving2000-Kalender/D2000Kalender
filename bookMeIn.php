<?php
include("function.php");
include ("mysqlConfig.php");
sec_session_start();

if(login_check() == false)
{
	if (strlen($_POST["tripID"]) > 2)
		header('Location: ./book.php?tripID=' . $_POST["tripID"] . '');	
	else
		header('Location: ./kalender.php');	
}

	

if(isset($_POST["tripID"]) && !isEnrolled($_SESSION["eveID"], $_POST["tripID"]) && friePladser($_POST["tripID"]) > 0)
{
	createLog(5, $_SESSION["eveID"]);
	$besked = "";
	//$besked = "Tilmelding fra Diving 2000 hjemmeside - NY tilmeldings system<br />";
	$besked .= getDestinationFromTripID($_POST["tripID"]) . " den " . convertDateForPrint(substr(getTripDate($_POST["tripID"]), 0, 10)) . "<br />";
	$besked .= "<br /><br />";
	$besked .= "<b>Navn:</b> " . getNameFromEVE($_SESSION["eveID"]) . "<br />";
	$besked .= "<b>Telefon:</b> " . getMobileFromEVE($_SESSION["eveID"]) . "<br />";
	$besked .= "<b>Mail:</b> " . getMailFromEVE($_SESSION["eveID"]) . "<br />";
	
	
	
	
	$pris = 0;
	
	
	$meetingPlace = $_POST["sted"];
	
	if($meetingPlace == "turSted")
	{
		$meetingPlace = 82;
		$besked .= "<b>Mødested:</b> Tur sted<br />";
	}
	else
	{
		$meetingPlace = 81;
		$besked .= "<b>Mødested:</b> Diving 2000<br />";
	}
	
	if(substr(getMedlemsStatus($_SESSION["eveID"]), 0, 2) == "Du")
	{
		$klub = 83;
		$pris += 0;
		$besked .= "<b>Odense Dykkerklub:</b> JA (Ansat)<br />";
	}
	
	else if(substr(getMedlemsStatus($_SESSION["eveID"]), 0, 2) == "JA")
	{
		$klub = 83;
		$pris += getPriceFromTripID($_POST["tripID"], "medlem");
		$besked .= "<b>Odense Dykkerklub:</b> JA<br />";
	}
	else
	{
		$klub = 84;
		$pris += getPriceFromTripID($_POST["tripID"], "normal");;
		$besked .= "<b>Odense Dykkerklub:</b> Nej<br />";
	}
	
	$toDay = date("Y-m-d H:i:s");
	
	
	//Tilføjet personen til turen
	$sql = "insert into EVE.dbo.T_CpCustTrip (CpCustID, CpTripID, CpLastUpdatedDate, CpCreationDate) VALUES (" . $_SESSION["eveID"] . "," . $_POST["tripID"] . ", '" . $toDay . "', '" . $toDay . "')";
	odbc_exec(connectToDB(), $sql);
	odbc_close_all();
	
	//Henter der custTripID, som personen har fået da han blev tilmeldt turen
	$sql = "select TOP (1) CpCustTripID from EVE.dbo.T_CpCustTrip WHERE CpCustID = " . $_SESSION["eveID"] . " AND CpTripID = " . $_POST["tripID"] . "";
	$row=odbc_exec(connectToDB(), $sql);
	$custTripID = 0;
	while(odbc_fetch_row($row))
	{
		$custTripID = odbc_result($row, "CpCustTripID");
	}
	
	
	/////////////////////////    Indsætter udstyr data    ///////////////////////////////////
	
	//KLub
	$sql = "insert into EVE.dbo.T_CkCustTripChecklist (CkCustTripID, CkStockTypeID, CkSizeID_N) VALUES (" . $custTripID . ",1213, " . $klub . ")";
	odbc_exec(connectToDB(), $sql);
	
	//Mødested
	$sql = "insert into EVE.dbo.T_CkCustTripChecklist (CkCustTripID, CkStockTypeID, CkSizeID_N) VALUES (" . $custTripID . ",1212, " . $meetingPlace . ")";
	odbc_exec(connectToDB(), $sql);
	
	$besked .= "<br />";
	
	$besked .= "<u><b>Leje af udstyr</b></u><br />";
	
	
	//tank
	if($_POST["tank"] != "intet")
	{
		$besked .= "<b>Tank:</b> " . getSizeFromID($_POST["tank"]) . "<br />";
		
		$pris += getEquipmentPrice("Tank", getMedlemsStatusBool($_SESSION["eveID"])) . "";
			
		$sql = "insert into EVE.dbo.T_CkCustTripChecklist (CkCustTripID, CkStockTypeID, CkSizeID_N) VALUES (" . $custTripID . ",1008, " . $_POST["tank"] . ")";
		odbc_exec(connectToDB(), $sql);
	}

	
	if($_POST["ekstraTank"])
	{
		$sql = "insert into EVE.dbo.T_CkCustTripChecklist (CkCustTripID, CkStockTypeID, CkSizeID_N, CkRequirementTx_N) VALUES (" . $custTripID . ",1008, " . $_POST["tank"] . ", 'x2')";
		odbc_exec(connectToDB(), $sql);
		
		if(getMedlemsStatusBool($_SESSION["eveID"])
			$pris += 90;
		else
			$pris += 100;
			
		$besked .= "Tank til en dags leje<br />";
	}
	
	
	//BCD
	if($_POST["bcd"] != "intet")
	{
		$besked .= "<b>BCD størrelse:</b> " . getSizeFromID($_POST["bcd"] ) . "<br />";
		
		$pris += getEquipmentPrice("BCD", getMedlemsStatusBool($_SESSION["eveID"])) . "";
		
		
		$sql = "insert into EVE.dbo.T_CkCustTripChecklist (CkCustTripID, CkStockTypeID, CkSizeID_N) VALUES (" . $custTripID . ",1037, " . $_POST["bcd"] . ")";
		odbc_exec(connectToDB(), $sql);
	}
	
	//Bly
	if($_POST["bly"] != "intet")
	{
		$besked .= "<b>Bly:</b> " . getSizeFromID($_POST["bly"]) . " kg<br />";
		
		$pris += getEquipmentPrice("Bly", getMedlemsStatusBool($_SESSION["eveID"])) . "";
			
		$sql = "insert into EVE.dbo.T_CkCustTripChecklist (CkCustTripID, CkStockTypeID, CkSizeID_N) VALUES (" . $custTripID . ",1002, " . $_POST["bly"] . ")";
		odbc_exec(connectToDB(), $sql);
	}

	//Finner
	if($_POST["finner"] != "intet")
	{
		$besked .= "<b>Finner størrelse:</b> " . getSizeFromID($_POST["finner"]) . "<br />";	
		
		
		$pris += getEquipmentPrice("Finner", getMedlemsStatusBool($_SESSION["eveID"])) . "";
		
		$sql = "insert into EVE.dbo.T_CkCustTripChecklist (CkCustTripID, CkStockTypeID, CkSizeID_N) VALUES (" . $custTripID . ",1045, " . $_POST["finner"] . ")";
		odbc_exec(connectToDB(), $sql);
	}	

	//Handsker
	if($_POST["handsker"] != "intet")
	{
		$besked .= "<b>Handsker størrelse:</b> " . getSizeFromID($_POST["handsker"]) . "<br />";
		
		$pris += getEquipmentPrice("Handsker", getMedlemsStatusBool($_SESSION["eveID"])) . "";
			
		$sql = "insert into EVE.dbo.T_CkCustTripChecklist (CkCustTripID, CkStockTypeID, CkSizeID_N) VALUES (" . $custTripID . ",1001, " . $_POST["handsker"] . ")";
		odbc_exec(connectToDB(), $sql);
	}	

	//Maske
	if(@$_POST["maske"] == 1)
	{
		$besked .= "<b>Maske:</b> JA<br />";
		
		$pris += getEquipmentPrice("Maske", getMedlemsStatusBool($_SESSION["eveID"])) . "";
			
		$sql = "insert into EVE.dbo.T_CkCustTripChecklist (CkCustTripID, CkStockTypeID, CkSizeID_N) VALUES (" . $custTripID . ",1046, 0)";
		odbc_exec(connectToDB(), $sql);
	}	

		
	//Regulator
	if($_POST["regulator"] == 1)
	{
		$besked .= "<b>Regulator:</b> JA<br />";
		
		$pris += getEquipmentPrice("Regulator", getMedlemsStatusBool($_SESSION["eveID"])) . "";
		
		$sql = "insert into EVE.dbo.T_CkCustTripChecklist (CkCustTripID, CkStockTypeID, CkSizeID_N) VALUES (" . $custTripID . ",1038, 0)";
		odbc_exec(connectToDB(), $sql);
	}

	//boots
	if($_POST["boots"] != "intet")
	{
		$besked .= "<b>Støvler:</b> " . getSizeFromID($_POST["boots"]) . "<br />";
		
		$pris += getEquipmentPrice("Boots", getMedlemsStatusBool($_SESSION["eveID"])) . "";
			
		$sql = "insert into EVE.dbo.T_CkCustTripChecklist (CkCustTripID, CkStockTypeID, CkSizeID_N) VALUES (" . $custTripID . ",1000, " . $_POST["boots"] . ")";
		odbc_exec(connectToDB(), $sql);
	}	
	
	//dragt
	if($_POST["dragt"] != "intet")
	{
		$besked .= "<b>Dragt størrelse:</b> " . getSizeFromID($_POST["dragt"]) . "<br />";
		
		
		$pris += getEquipmentPrice("Dragt", getMedlemsStatusBool($_SESSION["eveID"])) . "";
		
		$sql = "insert into EVE.dbo.T_CkCustTripChecklist (CkCustTripID, CkStockTypeID, CkSizeID_N) VALUES (" . $custTripID . ",1034, " . $_POST["dragt"] . ")";
		odbc_exec(connectToDB(), $sql);
	}		
	
	
	if($_POST["Tilbehør"] != "intet")
	{
		$besked .= "<b>Tilbehør:</b> " . getSizeFromID($_POST["Tilbehør"]) . "<br />";
		
		
		if (substr(getMedlemsStatus($_SESSION["eveID"]), 0, 2) == "JA")
			$pris += getPriceFromAddOns(1214,$_POST["Tilbehør"], "medlem");
		else
			$pris += getPriceFromAddOns(1214,$_POST["Tilbehør"], "normal");
		
		$sql = "insert into EVE.dbo.T_CkCustTripChecklist (CkCustTripID, CkStockTypeID, CkSizeID_N) VALUES (" . $custTripID . ",1214, " . $_POST["Tilbehør"] . ")";
		odbc_exec(connectToDB(), $sql);		
		
	}

	if(substr(getMedlemsStatus($_SESSION["eveID"]), 0, 2) == "Du") //Ansat
	{
		$pris = 0;	
	}

	//Pris
	$sql = "insert into EVE.dbo.T_CkCustTripChecklist (CkCustTripID, CkStockTypeID, CkRequirementTx_N, CkSizeID_N) VALUES (" . $custTripID . ",1012, " . $pris . ", NULL)";
	odbc_exec(connectToDB(), $sql);
		

	sendBookingMail($besked, "info@diving2000.dk");
	
	//Sender backup mail
	sendBookingMail($besked, getMailFromEVE($_SESSION["eveID"]));
	$url = "http://www.itspejderen.dk/diving2000/sendTilmeldingsMail2.php?besked=" . urlencode($besked) . "&mail=" . urlencode(getMailFromEVE($_SESSION["eveID"])) . "";


	$fp = fopen($url, 'r');
	$content = '';
	
	//////////////////////////////
	
	header('Location: ./kalender.php?trip=true');
}
else
{
	if (isEnrolled($_SESSION["eveID"], $_POST["tripID"]))
		header('Location: ./kalender.php?trip=false');
	else if ($_POST["tripID"] == "")
		header('Location: ./kalender.php');
	else
		header('Location: ./book.php?tripID=' . $_POST["tripID"] . '');
	
}
?>
