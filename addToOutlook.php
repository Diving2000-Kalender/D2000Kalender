<?php

include ("function.php");
include ("mysqlConfig.php");

createLog(14, 0);

$tripID = $_GET["tripID"];
//header('Content-Type: text/html; charset=iso-8859-1');


header("Content-Type: text/Calendar");

header("Content-Disposition: inline; filename=calendar.ics");

echo "
BEGIN:VCALENDAR
PRODID:-//Microsoft Corporation//Outlook 11.0 MIMEDIR//EN
VERSION:1.0
BEGIN:VEVENT
DTSTART:" . getTripTimeToOutlook($tripID) . "
DTEND:" . getTripEndTimeToOutlook($tripID) . "
LOCATION;ENCODING=QUOTED-PRINTABLE:" . replaceChar(getDestinationFromTripID($tripID)) . "
UID:040000008200E00074C5B7101A82E00800000000C095D4C28CF0CA010000000000000000100
 0000064735A18613FFC4EB9F7F420ADFF0BF4
DESCRIPTION;ENCODING=QUOTED-PRINTABLE:Dykkertur med Diving 2000 til " . replaceChar(getDestinationFromTripID($tripID)) . "=0D=0A
SUMMARY;ENCODING=QUOTED-PRINTABLE:Dykkertur med Diving 2000 til " . replaceChar(getDestinationFromTripID($tripID)) . "\n\n
PRIORITY:3
END:VEVENT
END:VCALENDAR
";


?>