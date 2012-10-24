<?php

include("mysqlConfig.php");
include("function.php");

sec_session_start(); // Our custom secure way of starting a php session. 



if(isset($_POST['username']) && isset($_POST['password'])) 
{ 
   $username = $_POST['username'];
   $password = md5(encryption($_POST['password'])); 
   $logInAs = $_POST["loginAs"];
   
   
	if(login($username, $password, $logInAs) == true) 
   	{
		
		if(@$_POST["husk"])
        {
            
            $cookie_auth= md5(generateRandomString() . $username);
            
            $auth_query = mysql_query("UPDATE users SET authKey = '" . $cookie_auth . "' WHERE username = '" . $username . "'");

			
            setcookie("auth_key", $cookie_auth, time() + 60 * 60 * 24 * 7);
        }
		
		createLog(1,getEveIDFromUsername($username));
    	// Login success
	 	header('Location: ./' . $_POST["urlOK"] . '');
    } 
    else 
    {
		$data = mysql_query("select `status` from users where `username` = '" . mysql_real_escape_string($username) . "' AND `password` = '" . $password . "'") or die(mysql_error());
		while($row = mysql_fetch_array($data))
		{
			$status = $row["status"];
		}
		
		
   		header('Location: ./' . $_POST["urlError"] . '_' . $status . '');
    }
} else 
{ 
   // The correct POST variables were not sent to this page.
   echo 'Invalid Request';
}

?>