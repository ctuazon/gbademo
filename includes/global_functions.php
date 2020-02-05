<?PHP
require_once("constants.php"); 
require_once(REQUIRE_LOCATION . '/text_filter.php'); 
require_once(REQUIRE_LOCATION . '/db_commands.php'); 

// This reads the file
function CheckFile ($FileToCheck)
{
	$Text = "";
	if(file_exists($FileToCheck) && filesize($FileToCheck) >=1)
	{
		$fp = fopen($FileToCheck, 'r');
		$Text = fread($fp, filesize($FileToCheck));		// Total entries gets saved here
		fclose($fp);
	}
	return $Text;
}

function DeleteFile ($FileLocation) 
{
	// Path relative to where the php file is or absolute server path
	// First Delete the index file because you can't delete the folder if has something inside it
	if (file_exists($FileLocation))
	{
		unlink ($FileLocation);
	}
		
}
function WriteToFile($Location, $FileToWrite)
{
	// Consequently, META data for any other category gets created as well
	$FileToCheck = $Location;
	$fp = fopen($FileToCheck, 'w+');
	fwrite($fp, $FileToWrite);
	fclose($fp);
}

// A simple redirection script
function redirect_to($location = NULL)
{
	if ($location != NULL)
	{
	    echo "<script>";
	    echo "window.location.replace('{$location}');";
	    echo "</script>";
		//header("Location: {$location}");
		exit;
	}
}
function confirm_valid_num($value)// this makes sure that the input is a number
{
	if (empty($value) || intval($value) == 0 || !is_numeric($value))
	{
		$passed = false;
	}
	else
	{
		$passed = true;
	}

	return $passed;
}
// this generated a random string of letters
function random_char_generator($length)
{
	$string = "0 1 2 3 4 5 6 7 8 9 a b c d e f g h i j k l m n o p q r s t u v w x y z A B C D E F G H I J K L M N O P Q R S T U V W X Y Z";
	
	$string = explode(" ", $string);

	$string_length = count($string);

	$number_of_characters = $length;
	$new_string = "";
	for ($i = 0 ; $i < $number_of_characters ; $i++)
	{
		$new_string .= $string[mt_rand(0, $string_length - 1)];
	}
	return $new_string;
}
// Unsets every global variables
function global_unset()
{
	unset ($_GET, $_SESSION, $GLOBALS, $_SERVER, $_FILES, $user_id);
}
// this is used for sanitizing images before they're uploaded to my server
function conversion_type ($image_to_resize, $file_mime_type, $put_in_here, $image_name)
{
			
	// First Security Check: make sure it's a real image
	if (getimagesize($image_to_resize))
	{
		// this block gets the size of the picture
		$picsize = @getimagesize($image_to_resize);
		$source_x = $picsize[0];
		$source_y  = $picsize[1];
		
		// if the source size us less than the default size, then stick with the source's size
		if($source_x <= ADJUSTED_SIZE && $source_y <= ADJUSTED_SIZE )
		{
			$new_width = $source_x;
			$new_height = $source_y;
		}
		else // adjust the images
		{
			// this part calculates the size of the thumbnail
			$new_width = ADJUSTED_SIZE;
			$new_height = floor( $source_y * ( $new_width / $source_x ) );
			if ($new_height > ADJUSTED_SIZE)
			{
				$new_width = floor( $source_x * ( ADJUSTED_SIZE / $source_y ) );
				$new_height = ADJUSTED_SIZE;
			}
		}
		// prepare 'canvas'
		$sanitized_thumbnail = imagecreatetruecolor($new_width, $new_height);	
			
		// This value is for calculating the transparancy value of the PNG/GIF image
		$color = imagecolorallocate($sanitized_thumbnail, 255, 255, 255);	//white	
			
		// If the image is a JPG...
		if ($file_mime_type == 'jpg')
		{	
			// create the thumbnail after recreating the image above
			$source_id = imagecreatefromjpeg($image_to_resize);
			$target_pic = imagecopyresampled($sanitized_thumbnail,$source_id, 0,0,0,0, $new_width,$new_height,$source_x,$source_y);
			imagejpeg ($sanitized_thumbnail, $put_in_here, JPEG_GIF_QUALITY);
				
		}
		// If the image is a GIF...
		else if ($file_mime_type == 'gif')
		{
			//imagecolortransparent($sanitized_picture, $color); Check box will be applied to form
				
			// create the thumbnail after recreating the image above
			$source_id = imagecreatefromgif($image_to_resize);
			$target_pic = imagecopyresampled($sanitized_thumbnail,$source_id, 0,0,0,0, $new_width,$new_height,$source_x,$source_y);
			imagegif ($sanitized_thumbnail, $put_in_here, JPEG_GIF_QUALITY);
		}
		// If the image is a PNG...
		else if ($file_mime_type == 'png')
		{
			//imagecolortransparent($sanitized_picture, $color);
				
			// create the thumbnail after recreating the image above
			$source_id = imagecreatefrompng($image_to_resize);
			$target_pic = imagecopyresampled($sanitized_thumbnail,$source_id, 0,0,0,0, $new_width,$new_height,$source_x,$source_y);
			imagepng ($sanitized_thumbnail, $put_in_here, PNG_QUALITY);
		}
			
		// Data need for recording data in database
		date_default_timezone_set  (DEFAULT_TIME_ZONE);
		$date_created		= DEFAULT_TIME. TIME_ZONE_PREFIX;		

		db_query("INSERT INTO image_temp (file_name, date_created)
VALUES ('{$image_name}', '{$date_created}')");

		unset($_FILES, $date_created, $thumb_file_name, $picsize, $source_x, $source_y, $source_id, $image_to_resize, $target_id, $target_pic , $new_width, $new_height);
	}
}

// paginates searches when a user is searching for something in particular from the db. Note: dynamic_numbered_pages relies of this. DO NOT USE ORIDINARY NUMBERED PAGES FUNCTION 
function dynamic_pagination_script($look_for, $current_page, $sql_command)
{
	$items_per_page = PICTURES_PER_PAGE;

	$begin_here = $items_per_page * ($current_page - 1);
	
	$seperated_tags = explode(",", $look_for); // tags entered by the user
	$number_of_tags = count($seperated_tags); // i counts how many tags there are total
	$i = 0;
	$query = '';
	
	// used the tags the user entered to produce a result from the DB
	while($i <= $number_of_tags || $i <= MAX_LENGTH_SEARCH_TAGS)
	{
		$seperated_tags[$i] = trim($seperated_tags[$i]);
			
		$query .= $sql_command . "'%{$seperated_tags[$i]}%'";
		
		$i++;
		if ($i != $number_of_tags)
		{
			$query .= " UNION ";
		}
		else
		{
			break;
		}
			
	}
	$number_rows = db_query($query);
	$total_values = mysql_num_rows($number_rows); // total items found
	
	$query .= " ORDER BY date_created DESC LIMIT {$items_per_page} OFFSET {$begin_here} ";
	$value['execution'] = db_query($query); // can be used for mysql_fetch_assoc
	
	$total_pages = ceil($total_values / $items_per_page);
	
	$value['total_pages'] = $total_pages; // numbered pages needs this
	// Current page is need to test against total num of pages to prevent it from printing pages over its limit
	$value['current_page'] = $current_page; // numbered pages needs this
	
	return $value;
}

// The difference here is that this maintains the tags the user serached for throughout the pages
// regualar numbered_pages can't do that
function dynamic_numbered_pages($total_pages, $current_page, $look_for)
{
	if ($total_pages >= 2)
	{
		// This is for the back page	
		if ($current_page != 1)
		{
			$back_page = $current_page - 1;
			echo "<a href='?page={$back_page}&search=".urlencode($look_for). "'>Back</a> ";

		}
					
		// If the current page is a 2 or below...
		// This is the default that's printed out first
		if ($current_page <= PAGE_PADDING)  // <- this is for outputing the first page numbers
		{
			// The default starting point will be 1 (the first page)
			$first_page = 1;
			// ..start the counter at one and print the pages 1 to X 
			for($i = $first_page; $i <= $first_page + PAGE_PADDING + PAGE_PADDING; $i++)
			{
				if ($i <= $total_pages) // This prevents the page number from higher than the total_pages
				{
					echo "<a href='?page={$i}&search=".urlencode($look_for). "'>{$i}</a> | ";				
				}					
			}		
		}
		// If the page is more than the total amount of pages (minus 2)
		// note: 2 is the padding 
		// example: [padding] [padding] [current_page] [padding] [padding]
		// in this situation, this is what will be printed out:
		// <1> [2] [3] [4] [5] or...
		// [1] <2> [3] [4] [5] 
		// depending on the padding's value
		else if ($current_page >= $total_pages - PAGE_PADDING) // <- this is for outputing the last remaining pages
		{
			// The default starting point will be the total_pages
			$last_pages = $total_pages;
			// Which will be subtracted by the padding * 2
			for($i = $last_pages - PAGE_PADDING - PAGE_PADDING ; $i <= $last_pages; $i++)
			{
				if ($i > 0) // This prevent any zero values from being printed
				{
					echo "<a href='?page={$i}&search=".urlencode($look_for). "'>{$i}</a> | ";
				}	
			}
	
		}
		// This is the default action
		// It prints out the 5 numbers based on what page the person is on
		// ie. if they're on page 6 then this will happen:
		// [4] [5] <6> [7] [8]
		// and so on..
		else
		{	
			for($i = $current_page -PAGE_PADDING ; $i <= $current_page + PAGE_PADDING ; $i++)
			{
				echo "<a href='?page={$i}&search=".urlencode($look_for). "'>{$i}</a> | ";				
			}
		}	
	
		// This is for the forward page
		if ($current_page < $total_pages)
		{
			$forward_page = $current_page + 1;
			echo " <a href='?page={$forward_page}&search=".urlencode($look_for). "'> forward</a>";
		}
	}
}
function rss_feed($user_id, $file_name)
{
	$file_location = '../rss/'.$file_name;
	$id_command = 'blog.php?view=';
	$fh = fopen($file_location, 'w');
	if($fh)
	{	
		$stringData = '<?xml version="1.0" encoding="ISO-8859-1" ?>
		<rss version="2.0">
		<channel>
		  <title>'. SITE_NAME .'</title>
		  <link>'.ROOT.'</link>
		  <description>'.SITE_DESCRIPTION.'</description>';
		
		$execute = db_query("SELECT * FROM post WHERE user_id='{$user_id}' ORDER BY date_created DESC LIMIT 10");
		while($results = mysql_fetch_assoc($execute))
		{  
			$post_title = prepare_for_edit($results['post_title']);
			$post_content = prepare_for_edit($results['post_content']);
			
			// Ampersand (&) ruin the title's and post content formating. Must replace
			$post_title = str_replace('&', 'and', $post_title);
			$post_content =str_replace('&', 'and' ,$post_content);
		
			$stringData .= 
				'<item>
				<title>'.$post_title.'</title>
				<link>'. ROOT . $id_command .$results['id'] .'</link>
				<description><![CDATA['.nl2br($post_content).']]></description>
				</item>';
		}
	  
		$stringData .= '</channel></rss>';
	
		fwrite($fh, $stringData);
		fclose($fh);
	}
}
function validate_user($user_id, $status)
{
	if ((!empty($user_id) && $status != 'Banned') || ($status == 'Guest_Only' && ALLOW_GUEST))
	{
		return true;
	}
	else
	{
		return false;
	}
}
function anti_spam_timer ($user_id, $member_status, $ip_address)
{
	// 1. We need to connect to the user database
	db_connect('user');
	
	// if the user has been authenticated.....
	if ($user_id != 'Guest_Only')
	{
		// check if a value for the corresponding user id exists
		$execute = db_query("SELECT * FROM user_anti_spam 
		WHERE user_id = '{$user_id}'
		AND ip_address = '{$ip_address}'");
	}
	else // if the user hasn't been authenticated
	{
		// identify the guest use based on his ip and check if it exists
		$execute = db_query("SELECT * FROM user_anti_spam 
		WHERE user_id = 'Guest_Only'
		AND ip_address = '{$ip_address}'");
	}
	
	$exists = mysql_num_rows ($execute);
	$current_time = time();	// the current time now
	if ($exists > 0) // if $exists is anything but zero, then something does exists
	{
		// put what was found above in here
		$results = mysql_fetch_assoc($execute);
		
		$recorded_time = $results['time']; // the last time the user made a post
		
		$amount_of_time_passed = $current_time - $recorded_time; // // then see how much time has passed since
		
		// if the amount of time passed has surpassed the wait time		
		if ($amount_of_time_passed > WAIT_TIME)
		{
			if ($_SESSION['user_id'] != 'Guest_Only')
			{
				$execute = db_query("DELETE FROM user_anti_spam 
				WHERE user_id = '{$user_id}'
				AND ip_address = '{$ip_address}'");
			}
			else // the user is an authenticated person
			{
				$execute = db_query("DELETE FROM user_anti_spam 
				WHERE user_id = 'Guest_Only'
				AND ip_address = '{$ip_address}'");
			}
			
			// after deleting the expired fields, enter new time entries for the person
			db_query("INSERT INTO user_anti_spam (user_id, ip_address, time)
			VALUES ('{$user_id}', '{$ip_address}', '{$current_time}')");
			
			return true;
		}
		else
		{
			return false;
		}
	}
	else // nothing was found in the database, so a new entry will be created
	{
		db_query("INSERT INTO user_anti_spam (user_id, ip_address, time)
				VALUES ('{$user_id}', '{$ip_address}', '{$current_time}')");
		return true;

	}
}
function MetaDetails ()
{
	// record some statndard security stuff
	$ip_address			= sanitize($_SERVER['REMOTE_ADDR']);
	date_default_timezone_set  (DEFAULT_TIME_ZONE);
	$date_created		= DEFAULT_TIME. TIME_ZONE_PREFIX;
			
	// generates a new id
	$generated_id = random_char_generator(ID_LENGTH);

			
	
}
function html_head($title='')
{
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>'.$title.'</title></head><body>';
	echo global_navigation (!empty($_SESSION['user_id']));
}
function html_foot()
{
	echo '</body></html>';
}
function textfield(string $name, string $size)
{
	$element = "<input type='text' name='{$name}' id='{$name}' size='{$size}'/>";
	return $element;
}
function delete_all_pictures($filename, $user_id)
{
	db_query("Delete 
			uploaded_media.*, 
			image_flags.*, 
			image_tags.*,
			image_votes.*
			 
			FROM 
			uploaded_media, 
			image_flags, 
			image_tags, 
			image_votes
			
			WHERE uploaded_media.file_name = image_flags.file_name
			AND uploaded_media.file_name = image_tags.file_name
			AND uploaded_media.file_name = image_votes.file_name
			AND uploaded_media.file_name = '{$filename}' and uploaded_media.user_id='{$user_id}'");

			db_query("DELETE 
			comment.*, 
			comment_votes.* 
			
			FROM 
			comment, 
			comment_votes
			
			where 
			comment.id = comment_votes.comment_id
			and post_id_origin = '{$filename}' 
			and post_id_origin = '{$filename}' 
			and user_id='{$user_id}'" );
				
}
function create_image($generated_name, $passNum)
{
	$passNum = '[' . $passNum . ']';
	$fp = fopen("../media/temp_folder/{$generated_name}{$passNum}.jpg", 'wb');	// This is a new temp file, resized
	fwrite($fp, $GLOBALS["HTTP_RAW_POST_DATA"]);
	fclose($fp);
	
	$GLOBALS["HTTP_RAW_POST_DATA"] = '';
	$GLOBALS["HTTP_RAW_POST_DATA"] = null;
	return getimagesize("../media/temp_folder/{$generated_name}{$passNum}.jpg");	// Check if it's a real picture
}


?>