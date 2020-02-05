<?PHP 

require_once ('constants.php');
require_once (REQUIRE_LOCATION . '/global_functions.php');


// There isn't much sterilization going on. Apparently, prepared statements are more than enough to protect against SQL injection
// Use this to search for something in the database using its id
function SelectSearchById ($Id)
{
	$count = 0;
	$array = array();
	
	try 
	{
		// First Connect to the database
		$pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=". DB_NAME . ";charset=utf8" , DB_USER, DB_PASS);
	}
	catch (PDOException $e)
	{
		echo "Error Connecting to Source:" . $e->getMessage();
	}
	
	$pdo->exec("set names utf8");		// Added for security
	
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sth = $pdo->prepare("SELECT * from content_blog WHERE content_id = :Content");
	$sth->setFetchMode(PDO::FETCH_ASSOC);
	$sth->execute(array(
				':Content' => $Id
				));

	//$result = $sth->fetch(); // Use fetchAll() if you want all results, or just iterate over the statement, since it implements Iterator

	$result = $sth->fetchAll();
	 
	// Put everything you find in an array
	if(count($result > 0)) 
	{
		foreach($result as $r) 
		{
			
			$array[$count] = array();
			$array[$count]['content_id'] 	= $r['content_id'];
			$array[$count]['title'] 		= $r['title'];
			$array[$count]['text'] 			= $r['text'];
			$array[$count]['category_type']	= $r['category_type'];
			$array[$count]['date'] 			= $r['date'];
			$array[$count]['user_id'] 		= $r['user_id'];
			$array[$count]['ip_address'] 	= $r['ip_address'];
			$array[$count]['TitleId'] 		= $r['TitleId']; 

			$count++;
			
			
		}
	}
	if ($array == null)
	{
		// This is incase the user inserts an invalid page number
		// Just redirect them back to the index page
		// redirect_to("");	
		return null;
	}
	
	return $array;	// Everything in the array gets pushed to this return value
}

function PreparedSearchSelect ($CategoryType, $Title)
{
	$count = 0;
	$array = array();
	
	try 
	{
		// First Connect to the database
		$pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=". DB_NAME . ";charset=utf8" , DB_USER, DB_PASS);
	}
	catch (PDOException $e)
	{
		echo "Error Connecting to Source:" . $e->getMessage();
	}
	
	$pdo->exec("set names utf8");		// Added for security
	
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sth = $pdo->prepare("SELECT * from content_blog WHERE category_type = :Category AND title LIKE :TitleText ORDER BY date DESC LIMIT 5");
	$sth->setFetchMode(PDO::FETCH_ASSOC);
	$sth->execute(array(
				':Category' => $CategoryType, 
				':TitleText' => "%".$Title."%"
				));

	//$result = $sth->fetch(); // Use fetchAll() if you want all results, or just iterate over the statement, since it implements Iterator

	$result = $sth->fetchAll();
	 
	// Put everything you find in an array
	if(count($result > 0)) 
	{
		foreach($result as $r) 
		{
			
			$array[$count] = array();
			$array[$count]['content_id'] 	= $r['content_id'];
			$array[$count]['title'] 		= $r['title'];
			$array[$count]['text'] 			= $r['text'];
			$array[$count]['category_type']	= $r['category_type'];
			$array[$count]['date'] 			= $r['date'];
			$array[$count]['user_id'] 		= $r['user_id'];
			$array[$count]['ip_address'] 	= $r['ip_address'];
			$array[$count]['TitleId'] 		= $r['TitleId'];

			$count++;
			
			
		}
	}
	if ($array == null)
	{
		// This is incase the user inserts an invalid page number
		// Just redirect them back to the index page
		redirect_to("");	
		return null;
	}
	
	return $array;	// Everything in the array gets pushed to this return value
}

function SelectBeforeAndAfter($date, $CategoryType)
{
	$count = 0;
	$array = array();
	
	try 
	{
		// First Connect to the database
		$pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=". DB_NAME . ";charset=utf8" , DB_USER, DB_PASS);
	}
	catch (PDOException $e)
	{
		echo "Error Connecting to Source:" . $e->getMessage();
	}
	
	$pdo->exec("set names utf8");		// Added for security
	
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// It had to be sort by date. It's the only way
	$sth = $pdo->prepare("
	
	SELECT * FROM content_blog 
	WHERE category_type = :Category and date >= (
			SELECT date FROM content_blog 
			WHERE category_type = :Category and date < 
				(SELECT date FROM content_blog 
				WHERE category_type = :Category and  date = :Date)
				ORDER BY date DESC 
				LIMIT 1
			)
	ORDER BY  date ASC
	LIMIT 3
	
	
	");
	
	/*
	
	The original
		SELECT * 
		FROM content_blog 
		WHERE category_type = 'blog' and date >= (
		SELECT date 
		FROM content_blog 
		WHERE category_type = 'blog' and date < (SELECT date FROM content_blog WHERE category_type = 'blog' and date = '2016-04-26 14:14:46')
		ORDER BY  date DESC 
		LIMIT 1
		)
		ORDER BY  date ASC
		LIMIT 3
		
		First is Oldest
		Second is Article
		Thrid is Newest
*/


	$sth->setFetchMode(PDO::FETCH_ASSOC);
	$sth->execute(array(
				':Category' => $CategoryType, 
				':Date' => $date
				));

	//$result = $sth->fetch(); // Use fetchAll() if you want all results, or just iterate over the statement, since it implements Iterator

	$result = $sth->fetchAll();
	 
	// Put everything you find in an array
	if(count($result) > 0) 
	{
		foreach($result as $r) 
		{
			
			$array[$count] = array();
			$array[$count]['content_id'] 	= $r['content_id'];
			$array[$count]['title'] 		= $r['title'];
			$array[$count]['text'] 			= $r['text'];
			$array[$count]['category_type']	= $r['category_type'];
			$array[$count]['date'] 			= $r['date'];
			$array[$count]['user_id'] 		= $r['user_id'];
			$array[$count]['ip_address'] 	= $r['ip_address'];
			$array[$count]['TitleId'] 		= $r['TitleId'];

			$count++;
			
			
		}
	}
	if ($array == null)
	{
		// This is incase the user inserts an invalid page number
		// Just redirect them back to the index page
		redirect_to("");	
		return null;
	}
	
	return $array;	// Everything in the array gets pushed to this return value
}

// This is used to determine what the FIRST 3 ARTICLES ever created ARE for when the user views the very first article
function SelectASCOrder($CategoryType)
{
	$count = 0;
	$array = array();
	
	try 
	{
		// First Connect to the database
		$pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=". DB_NAME . ";charset=utf8" , DB_USER, DB_PASS);
	}
	catch (PDOException $e)
	{
		echo "Error Connecting to Source:" . $e->getMessage();
	}
	
	$pdo->exec("set names utf8");		// Added for security
	
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// It had to be sort by date. It's the only way
	$sth = $pdo->prepare("
	
	SELECT * FROM content_blog 
	WHERE category_type = :Category  ORDER BY date ASC LIMIT 3
			
	
	
	");



	$sth->setFetchMode(PDO::FETCH_ASSOC);
	$sth->execute(array(
				':Category' => $CategoryType 

				));

	//$result = $sth->fetch(); // Use fetchAll() if you want all results, or just iterate over the statement, since it implements Iterator

	$result = $sth->fetchAll();
	 
	// Put everything you find in an array
	if(count($result) > 0) 
	{
		foreach($result as $r) 
		{
			
			$array[$count] = array();
			$array[$count]['content_id'] 	= $r['content_id'];
			$array[$count]['title'] 		= $r['title'];
			$array[$count]['text'] 			= $r['text'];
			$array[$count]['category_type']	= $r['category_type'];
			$array[$count]['date'] 			= $r['date'];
			$array[$count]['user_id'] 		= $r['user_id'];
			$array[$count]['ip_address'] 	= $r['ip_address'];
			$array[$count]['TitleId'] 		= $r['TitleId'];

			$count++;
			
			
		}
	}
	if ($array == null)
	{
		// This is incase the user inserts an invalid page number
		// Just redirect them back to the index page
		redirect_to("");	
		return null;
	}
	
	return $array;	// Everything in the array gets pushed to this return value
}

// This is use primarily by the HTML SEO pages to determine the first article OR To get all the articles start from the OLDEST
// Used by Archive page and comic pagination (SEO Pages)
function SelectOnlyOne($CategoryType, $Limits)
{
	$count = 0;
	$array = array();
	
	try 
	{
		// First Connect to the database
		$pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=". DB_NAME . ";charset=utf8" , DB_USER, DB_PASS);
	}
	catch (PDOException $e)
	{
		echo "Error Connecting to Source:" . $e->getMessage();
	}
	
	$pdo->exec("set names utf8");		// Added for security
	
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );		// This makes it possible to set numbers in LIMIT
	
	// It had to be sort by date. It's the only way
	$sth = $pdo->prepare("
	
	SELECT * FROM content_blog 
	WHERE category_type = :Category ORDER BY date ASC LIMIT :Limit
	
	
	");
	

	$sth->setFetchMode(PDO::FETCH_ASSOC);
	$sth->execute(array(
				':Category' => $CategoryType,
				':Limit' => $Limits
				));

	//$result = $sth->fetch(); // Use fetchAll() if you want all results, or just iterate over the statement, since it implements Iterator

	$result = $sth->fetchAll();
	 
	// Put everything you find in an array
	if(count($result) > 0) 
	{
		foreach($result as $r) 
		{
			
			$array[$count] = array();
			$array[$count]['content_id'] 	= $r['content_id'];
			$array[$count]['title'] 		= $r['title'];
			$array[$count]['text'] 			= $r['text'];
			$array[$count]['category_type']	= $r['category_type'];
			$array[$count]['date'] 			= $r['date'];
			$array[$count]['user_id'] 		= $r['user_id'];
			$array[$count]['ip_address'] 	= $r['ip_address'];
			$array[$count]['TitleId'] 		= $r['TitleId'];

			$count++;
			
			
		}
	}
	if ($array == null)
	{
		// This is incase the user inserts an invalid page number
		// Just redirect them back to the index page
		redirect_to("");	
		return null;
	}
	
	return $array;	// Everything in the array gets pushed to this return value
}
// This one is used to determine the last comic and to link to it
// Used by the update.php script to alter the metadata 
function SelectOnlyOneDesc($CategoryType, $Limits)
{
	$count = 0;
	$array = array();
	
	try 
	{
		// First Connect to the database
		$pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=". DB_NAME . ";charset=utf8" , DB_USER, DB_PASS);
	}
	catch (PDOException $e)
	{
		echo "Error Connecting to Source:" . $e->getMessage();
	}
	
	$pdo->exec("set names utf8");		// Added for security
	
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );		// This makes it possible to set numbers in LIMIT
	
	// It had to be sort by date. It's the only way
	$sth = $pdo->prepare("
	
	SELECT * FROM content_blog 
	WHERE category_type = :Category ORDER BY date DESC LIMIT :Limit
	
	
	");
	

	$sth->setFetchMode(PDO::FETCH_ASSOC);
	$sth->execute(array(
				':Category' => $CategoryType,
				':Limit' => $Limits
				));

	//$result = $sth->fetch(); // Use fetchAll() if you want all results, or just iterate over the statement, since it implements Iterator

	$result = $sth->fetchAll();
	 
	// Put everything you find in an array
	if(count($result > 0)) 
	{
		foreach($result as $r) 
		{
			
			$array[$count] = array();
			$array[$count]['content_id'] 	= $r['content_id'];
			$array[$count]['title'] 		= $r['title'];
			$array[$count]['text'] 			= $r['text'];
			$array[$count]['category_type']	= $r['category_type'];
			$array[$count]['date'] 			= $r['date'];
			$array[$count]['user_id'] 		= $r['user_id'];
			$array[$count]['ip_address'] 	= $r['ip_address'];
			$array[$count]['TitleId'] 		= $r['TitleId'];

			$count++;
			
			
		}
	}
	if ($array == null)
	{
		// This is incase the user inserts an invalid page number
		// Just redirect them back to the index page
		redirect_to("");	
		return null;
	}
	
	return $array;	// Everything in the array gets pushed to this return value
}


// This is used by the pagination script.
// DO NO USE THIS IF YOU PLAN ON IMPLEMENTING USER SEARCHES 
// CREATE A DIFFERENT METHOD THAT CAN ACCEPT PREPARED STATEMENTS. SAMPLES OF THIS ARE THE INSERT AND UPDATE STATEMENTS BELOW
function SelectBlog ($query)
{
	$count = 0;
	$array = '';
	try 
	{
		// First Connect to the database
		$pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=". DB_NAME . ";charset=utf8" , DB_USER, DB_PASS);
	}
	catch (PDOException $e)
	{
		echo "Error Connecting to Source:" . $e->getMessage();
	}
	
	$pdo->exec("set names utf8");		// Added for security
	
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
	$sth = $pdo->query($query);
	$sth->setFetchMode(PDO::FETCH_ASSOC);

	$result = $sth->fetchAll();
	 
	// Put everything you find in an array
	if(count($result) > 0) 
	{
		$r = array();
		$array = array();
		foreach($result as $r) 
		{
	
			$array[$count]['content_id'] 	= $r['content_id'];
			$array[$count]['title'] 		= $r['title'];
			$array[$count]['text'] 			= $r['text'];
			$array[$count]['category_type']	= $r['category_type'];
			$array[$count]['date'] 			= $r['date'];
			$array[$count]['user_id'] 		= $r['user_id'];
			$array[$count]['ip_address'] 	= $r['ip_address'];
			$array[$count]['TitleId'] 	= $r['TitleId'];

			$count++;
			
			
		}
	}
	if ($array == null)
	{
		// This is incase the user inserts an invalid page number
		// Just redirect them back to the index page
		//redirect_to("http://localhost/GhostBanditsAttackV2/root/index.php");
		return null;
	}
	
	return $array;	// Everything in the array gets pushed to this return value
}
// This is used for SELECT, INSERT, UPDATE, DELETE operations
function InsertBlog($ContentId, $Title, $Text, $Date, $Category, $OwnerId, $IpAddress, $ProxyIp, $TitleId)
{	
	// First Connect to the database
	$pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=". DB_NAME . ";charset=utf8" , DB_USER, DB_PASS);
	 
	$pdo->exec("set names utf8");		// Added for security
	
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	
	try
	{
		// Insert into the Blog Database
		$sth = $pdo->prepare("INSERT INTO content_blog (content_id, title, text, date, category_type, user_id, ip_address, proxy_ip, TitleId) 
			VALUES(:Content_Id, :Title, :Text, :Date, :Category, :OwnerId, :IpAddress, :ProxyIp, :titleid)");
		
		// The values
		$sth->execute(array(
		
			"Content_Id" => $ContentId,
			"Title" => $Title,
			"Text" => $Text,
			"Date" => $Date,
			"Category" => $Category,
			"OwnerId" => $OwnerId,
			"IpAddress" => $IpAddress,
			"ProxyIp" => $ProxyIp,
			"titleid" => $TitleId
			));
			
	}
	catch(PDOException $e)
	{
		// Report an error
		echo $e->getMessage();
	}
	
	// Clear everything
	$pdo = null;
	$sth = null;
}

function UpdateBlog ($ContentId, $Title, $Text, $Category, $Date, $OwnerId, $IpAddress, $ProxyIp)
{
	// First Connect to the database
	$pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=". DB_NAME . ";charset=utf8" , DB_USER, DB_PASS);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	try
	{
		$sth = $pdo->prepare("Update content_blog SET title = :Title, 
												text = :Text, 
												date = :Date, 
												category_type = :Category, 
												user_id = :OwnerId, 
												ip_address = :Ip_Address,
												proxy_ip = :ProxyIp
							WHERE content_id = :Content_Id") ;
			
		$sth->execute(array(
			
				"Content_Id" => $ContentId,
				"Title" => $Title,
				"Text" => $Text,
				"Category" => $Category,
				"Date" => $Date,
				"OwnerId" => $OwnerId,
				"Ip_Address" => $IpAddress,
				"ProxyIp" => $ProxyIp
				));
	}
	catch(PDOException $e)
	{
		// Report an error
		echo $e->getMessage();
	}
	
	// Clear Everything
	$pdo = null;
	$sth = null;
}



function PreparedDeleteBlog ($ContentId)
{
	// First Connect to the database
	$pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=". DB_NAME . ";charset=utf8" , DB_USER, DB_PASS);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	try
	{
		$sth = $pdo->prepare("DELETE FROM content_blog
							WHERE content_id = :Content_Id") ;
			
		$sth->execute(array(
			
				"Content_Id" => $ContentId
				
				));
	}
	catch(PDOException $e)
	{
		// Report an error
		echo $e->getMessage();
	}
	
	// Clear Everything
	$pdo = null;
	$sth = null;
}

?>