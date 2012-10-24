<?php
include ("function.php");
include ("mysqlConfig.php");

$distID = $_GET["distID"];


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Diving 2000 Routebeskrivelse</title>
	<link rel="stylesheet" href="style.css" />
</head>
<body>
<?php
echo "<img src=\"http://maps.google.com/staticmap?center=" . getGPS($distID) . "&zoom=16&size=230x230&maptype=satellite&key=ABQIAAAAFgSiYTPg0rDl-PfFTKRrHBQXbNJ6Yel82-jv6f1BDN2WQoU4hxQPwuD4nWUO3FplMT4DaY6jDuDHPw&sensor=false&hl=da\" border=\"1\" align=\"left\" vspace=\"10\" hspace=\"10\">";
echo "<br /><b>Mødestedet er:</b><br /> " . getMettingPoint($distID) . "";

?>

</body>
</html>