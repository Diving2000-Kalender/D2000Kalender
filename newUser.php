<?php
include ("function.php");
include ("mysqlConfig.php");

@$fornavn = $_REQUEST["fornavn"];
@$efternavn = $_REQUEST["efternavn"];
@$mail = $_REQUEST["mail"];
@$mobil = $_REQUEST["mobil"];
@$sex = $_REQUEST["sex"];

@$day = $_REQUEST["day"];
@$month = $_REQUEST["month"];
@$year = $_REQUEST["year"];

@$username = $_REQUEST["username"];
@$password = $_REQUEST["password"];
@$password2 = $_REQUEST["password2"];
@$subm = $_REQUEST["subm"];


if($mail && $mobil && $subm && $password && $password2 && $fornavn && $efternavn)
{
	$birthDay = $year . "-" . $month . "-" . $day . "";
	
	
	
	if(checkEmailAddress($mail) && isTelephone($mobil))
	{

		if(isUsernameAvailab($username) && !isUsernameBlocked($username))
		{
			if($password == $password2 && strlen($password) > 5)
			{
				$eveID = findEVEID($mail, $mobil, $birthDay);
				
				
				
				if(isEVEIDAvailab($eveID))
				{
				
					$key = md5("qwe" . date("i-M-s"));
					
					if($eveID != 0)
						//Brugeren blev fundet i EVE
					{
						
						
						$password = encryption($password);
						
						updateUsernameInEVE($username, $eveID);
						$cookie_auth = md5(generateRandomString() . $username);
						
						$insertSQL = "INSERT into users (`eveID`, `username`, `password`, `created`, `activationKey`, `authKey`,`option`) values (" . $eveID . ", '" . mysql_real_escape_string($username) . "', '" . md5($password) . "', NOW(), '" . $key . "', '" . $cookie_auth . "', 'found')";
						
						mysql_query($insertSQL) or die(mysql_error());
						
					}
					else
						//Personen kunne ikke findes i EVE - og opretter derfor personen
					{
						//Gemmer login i MYSQL
						
						$eveID = createUser($username, $password, $mail, $mobil, $birthDay, $fornavn, $efternavn, $sex, $key);
						
					}
					////////////////////// Sender mail   ///////////////////////////////////////
					//$url = "http://www.itspejderen.dk/diving2000/htmlMail.php?mail=" . $mail . "&eveID=" . $eveID . "&auth=" . $key . "";
					
					welcomeMail($mail, $eveID, $key);
					
					//$fp = fopen($url, 'r');
					
					$uds = "<div class=\"godkendt\">Din bruger blev oprettet. Der er sendt en velkomst mail. <a href=\"kalender.php\">Klik her for komme tilbage til kalenderen</a></div>";
					
					
				}
				else
				{
					//EVE ID findes i login DB - brug gensend login oplysninger
					$uds = "<div class=\"fejl\">Du er allerede oprettet. Glemt dine logind oplysninger? <a href=\"gensendLogin.php\">Klik her</a></div>";
				}
			}
			else
			{
				if(!strlen($password) > 5)
					$uds = "<div class=\"fejl\">Adgangskode skal være min. 6 tegn lang<div>";
				else
					$uds = "<div class=\"fejl\">De 2 adgangskoder er ikke ens</div>";
				
			}
		}
		else
			$uds = "<div class=\"fejl\">Brugernavnet findes allerede. Vælg venligst et andet</div>";
	}
	else
		$uds = "<div class=\"fejl\">Mailadressen eller telefonnummeret er ugyldigt</div>";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Opret profil</title>
	<link rel="stylesheet" href="style.css" />
	<link rel="shortcut icon" href="favicon.ico">
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />

	
	
	<SCRIPT type="text/javascript">
function showBlur() {
    document.getElementById('mobil').value = "Example...";
}
function showFocus() {
   document.getElementById('mobil').value = "";
}



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






</SCRIPT>
	
	
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function()//When the dom is ready 
{
$("#username").change(function() 
{ //if theres a change in the username textbox

var username = $("#username").val();//Get the value in the username textbox
if(username.length > 4)//if the lenght greater than 3 characters
{
$("#availability_status").html('<img src="images/loader.gif" align="absmiddle">&nbsp;Vent venligst...');
//Add a loading image in the span id="availability_status"

$.ajax({  //Make the Ajax Request
    type: "POST",  
    url: "ajax_check_username.php",  //file name
    data: "username="+ username,  //data
    success: function(server_response){  
   
   $("#availability_status").ajaxComplete(function(event, request){ 

	if(server_response == '0')//if ajax_check_username.php return value "0"
	{ 
	$("#availability_status").html('<img src="images/available.png" align="absmiddle"> <font color="#FFFFFF"> Ledigt </font>  ');
	//add this image to the span with id "availability_status"
	}  
	else  if(server_response == '1')//if it returns "1"
	{  
	 $("#availability_status").html('<img src="images/not_available.png" align="absmiddle"> <font color="#FFFFFF">Optaget </font>');
	}  
   
   });
   } 
   
  }); 

}
else
{

$("#availability_status").html('<font color="#FFFFFF">Brugernavnet er for kort</font>');
//if in case the username is less than or equal 3 characters only 
}



return false;
});

});
</script>	
	
	
	
</head>
<body>

<?php include ("menu.php"); ?>

<h3>Oprettelse af profil</h3>

Inden du kan gøre brug af vores nye tilmeldingssystem, så er det nødvendigt at oprette dig som bruger. Dette gælder også hvis du allerede 
skulle være oprettet i vores system. <br />
Udfyld venligst alle felterne. <br />
Du vil efter oprettelsen modtage en mail, med et link til at aktiver din konto. <b>HUSK</b> at tjekke din uønsket mail, hvis du ikke modtager 
mailen. 
<br /><br /><br /><br />
<?php
	if(@$uds)
	echo "" . $uds . "<br /><br />";
?>

<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post" name="myForm">


<div class="style_form">
  <label for="fornavn">Fornavn:</label>
  <input type="text" name="fornavn" class="form_element">
  <span class="hint">Indtast dit fornavn og evt. mellemnavn<span class="hint-pointer"> </span></span>
</div>

<div class="style_form">
  <label for="efternavn">Efternavn:</label>
  <input type="text" name="efternavn" class="form_element">
  <span class="hint">Indtast dit efternavn<span class="hint-pointer"> </span></span>
</div>

<div class="style_form">
  <label for="sex">Køn:</label>
  <select name="sex" class="form_element">
  	<option value="1">Kvinde</option>
	<option value="2">Mand</option>
  </select> 
</div>


<div class="style_form">
  <label for="mail">Mail:</label>
  <input type="text" name="mail" class="form_element">
  <span class="hint">Indtast din mail. Vi sender en aktiveringsmail til denne adresse<span class="hint-pointer"> </span></span>
</div>  

<div class="style_form">
  <label for="mobil">Mobil nummer:</label>
  <input type="text" value="Undlad +45" name="mobil" class="form_element" onblur="if(this.value=='') this.value='Undlad +45';" 
onfocus="if(this.value=='Undlad +45') this.value='';">
  <span class="hint">Indtast dit mobil nummer<span class="hint-pointer"> </span></span>
</div>  

<div class="style_form">
  <label for="birthDate">Fødselsdag:</label>
  
            <select name="day">
            <?php
                for ($i=1; $i<=31; $i++)
                {
                    echo "<option value='$i'>$i</option>";
                }
            ?>
            </select>
              <select name="month">
                <option value="1">Januar</option>
                <option value="2">Februar</option>
                <option value="3">Marts</option>
                <option value="4">April</option>
                <option value="5">Maj</option>
                <option value="6">Juni</option>
                <option value="7">Juli</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">Oktober</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>

			
			<select name="year">
            <?php
                for ($i=date("Y"); $i>=1940; $i--)
                {
                    echo "<option value='$i'>$i</option>";
                }
            ?>
            </select>
  
  
</div>  

<div class="style_form">
  <label for="username">Brugernavn:</label>
  <input type="text" name="username" id="username" class="form_element">
  <span class="hint">Vælg et brugernavn, som du vil anvende til at logge ind.<span class="hint-pointer"> </span></span>
  <span id="availability_status"></span>
  
</div>  


<div class="style_form">
  <label for="adgangskode">Adgangskode</label>
  <input type="password" name="password" class="form_element">
  <span class="hint">Indtast den adgangskode, som du ønsker at benytte<span class="hint-pointer"> </span></span>
</div>  

<div class="style_form">
  <label for="adgangskode2">Gentag adgangskode</label>
  <input type="password" name="password2" class="form_element">
  <span class="hint">Gentag din adgangskode<span class="hint-pointer"> </span></span>
</div>  


<br /><br />
 
  <input type="hidden" name="subm" value="1" />
   <input type="submit" value="Opret" class="submitBT" 	 />

<br /><br />
<label for="udfyld" style="width:300px"><i><b>Alle felterne skal udfyldes</b></i></label><br />	
</form>

<?php include("buttonMenu.php"); ?>

</body>
</html>