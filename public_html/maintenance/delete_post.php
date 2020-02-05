<?PHP

require_once("../../includes/constants.php"); 
require_once("../../metadata/MetaLocation.php");
require_once("../../articles/ArticleLocation.php");
require_once('FileWriter.php');  
require_once(REQUIRE_LOCATION . '/global_functions.php'); 
require_once(REQUIRE_LOCATION . '/db_commands.php'); 
require_once(REQUIRE_LOCATION . '/pagination.php'); 

session_start();
session_regenerate_id(true);

// If the user isn't logged in, kick them out of this page
if (empty($_SESSION['authenticate']) &&  $_SESSION['authenticate'] != ADMIN_CODE)
{
	redirect_to('login.php');	
}

if (!empty($_GET['delete']))
{
	$id = numbers_and_letters_only ($_GET['delete']);

	// This is for the HTML Cached SEO Directories
	// It deletes them
	$File = new FileWriter();
	$File->FetchFromDatabase ($id);
	
	if ($File->StaticCategoty == 'comic')
	{
		$File->FileManipulation(false);
		
		//redirect_to('p?referer=create&id='. );	
		
	}
		
	PreparedDeleteBlog ($id);	// Delete entry
	
	// Count the values again
	$SQLcmd = "SELECT * FROM content_blog WHERE category_type = '". numbers_and_letters_only($File->StaticCategoty) ."' ORDER BY date DESC";
	$total_values = count(SelectBlog($SQLcmd));
		
	// Update the number of blog postsed
	$FileToCheck = META_LOCATION. DIRECTORY_SEPARATOR .$File->StaticCategoty;
	if (!empty($File->StaticCategoty))
	{
		$fp = fopen($FileToCheck, 'w+');
		fwrite($fp, $total_values);
		fclose($fp);
	}
		
	// If you delete the lastest comic, update the metadata that keeps track of the latest comic
	if ($File->StaticCategoty == 'comic')
	{
		// Check to see if this is the latest article so we can update the Metadata that tells the site which one is the latest article
		$results = SelectOnlyOneDesc(numbers_and_letters_only($File->StaticCategoty), '1');
		
		// Create META data to tell the site what the lastest comic is
		$FileToCheck = META_LOCATION. DIRECTORY_SEPARATOR .'latestcomic';
		$fp = fopen($FileToCheck, 'w+');
		fwrite($fp, $results[0]['TitleId']."-".ChangeToDash($results[0]['title']));
		fclose($fp);
		
	}
	
	// Delete The Text file that contains the TEXT content as well
	$FileLocation = ARTICLE_LOCATION. DIRECTORY_SEPARATOR . $File->StaticTitleId .'-'. $File->StaticText .'-'. $File->StaticCategoty;
	DeleteFile ($FileLocation) ;
	
	redirect_to('update_post.php?referer=delete');	
	
}

// This is the post that's going to get deleted

