<?PHP 

require_once("../../includes/constants.php"); 
require_once(REQUIRE_LOCATION . '/global_functions.php'); 
require_once(REQUIRE_LOCATION . '/db_commands.php'); 

// 1 Find the session
session_start();

// 2. Unset all session variables
$_SESSION = array();
session_unset();

// 3. Destroy the cookie
if(isset($_COOKIE[session_name()]))
{
	setcookie(session_name(), '', time()-40000, '/');
}

// 4. destroy the session
session_destroy();

redirect_to('http://'.SITE_ROOT. 'maintenance/login.php');

?>