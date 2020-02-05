<?php 
require_once("../../includes/constants.php"); 
require_once(REQUIRE_LOCATION . '/global_functions.php'); 

session_start();
session_regenerate_id(true);

/////////// DELETE THIS AFTER FINISH DEBUG ////////////////
//$_SESSION['authenticate'] = ADMIN_CODE;
/////////// DELETE THIS AFTER FINISH DEBUG ////////////////

$message = " ";
if(isset($_POST['submit']))
{
//	if(isset($_POST['g-recaptcha-response']))
//	{
	    /*
		$captcha = $_POST['g-recaptcha-response'];
		
		$secretKey = SECRET_KEY;
		$ip = $_SERVER['REMOTE_ADDR'];
		$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
		$responseKeys = json_decode($response, true);
		
		if(intval($responseKeys["success"]) !== 1) 
		{
			$message = 'Captcha Failed';
		} 
		else 
		{
		*/	
			if (!empty($_SESSION['authenticate']))
			{
				redirect_to('index.php');
			}
			else
			{
				$_SESSION['authenticate'] = '';
				if(isset($_POST['submit']))
				{



					// if submitted check response

					
					$user = $_POST['user'];
					$pass = sha1(SALTY_LOGIN .$_POST['password']. PEPPERY_LOGIN);	
					$pass = numbers_and_letters_only($pass);
					
					if ($user == USER && $pass == ADMIN_HASH)
					{
						$_SESSION['authenticate'] = ADMIN_CODE;	// This is the User Id
						redirect_to('index.php');
					}
					else
					{
						// Authentication Failed
						//echo "YOU SUCK";
						//$error = "You have failed authentication.";
					}
				
				}
			}
		//}
//	}
//	else
//	{
//		$message = 'Captcha Failed';
		
//	}
}


  
?>
<!doctype html>
<html>
	<head>
		<title>Ghost Bandits Attack </title>
		<link href="../css/backend.css" rel="stylesheet" type="text/css"/>
		<script src='https://www.google.com/recaptcha/api.js'></script>
	</head>
	<body>
		
		<div style="color: black; background-color: #fff; text-align: center; position: absolute; top: 50%; left: 0px; width: 100%; height: 1px; overflow: visible; visibility: visible; display: block;">
			<div style="position: absolute; left: 50%; width: 250px; height: 70px; top: -35px; margin-left: -125px; visibility: visible;">
				<table border = "1" cellpadding="10" cellspacing="0" width="100%">
					<tr>
						<td>
							<form id="form1" name="form1" method="post" action="">
								Username:
								<input type="text" name="user" id="textfield" />
								<br />
								<br />
								Password:  
								<input type="password" name="password" id="textfield" />
								<br />
								<br />
								<?PHP
							/*	 echo '<div class="g-recaptcha" data-sitekey="6LdW1wgUAAAAABdSLnKfUTd6MZQX8Eqi_ZQMQOvX"></div>';
								 echo $message ;*/
								?>
								<br />
								<br />
								<input type="submit" name="submit" id="button" value="Submit" />
							</form>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>