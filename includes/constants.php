<?php

// Database Constants for Posts
define('DB_SERVER', 'localhost');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_NAME', '');


define('DEFAULT_TIME_ZONE', 'America/New_York');					// Globalfunctions uses this
define('DEFAULT_TIME', @date("Y-m-d H:i:sa"));						// Globalfunctions uses this
define('TIME_ZONE_PREFIX', ' EST');									// Globalfunctions uses this

define('ID_LENGTH', '47');											// Globalfunctions uses this

define('PAGE_PADDING', '3');										// Used by the pagination script

define('ITEMS_PER_PAGE', '6');										// 6 because free hosting won't show the pagination if it's more than 6

// Captcha Variables
define('SECRET_KEY', '');

// Salt / Pepper  for login CMS only
define ('SALTY_LOGIN' , '');
define ('PEPPERY_LOGIN' , '');
define ('USER', 'C. C.');

// Site location
define ('SITE_ROOT' , 'czyrus.com/');

//define ('REQUIRE_LOCATION', __DIR__ );
define ('REQUIRE_LOCATION', dirname(__FILE__) ); // Anything below php 5.3

define ('ADMIN_CODE', '');		// The user ID of the one allow to edit the website
define ('ADMIN_HASH', '');										// The SHA1 hash that's checked upon login

?>