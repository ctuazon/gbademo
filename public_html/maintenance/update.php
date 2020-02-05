<?PHP

require_once("../../includes/constants.php"); 
require_once("../../articles/ArticleLocation.php"); 
require_once('FileWriter.php'); 
require_once(REQUIRE_LOCATION . '/global_functions.php'); 
require_once(REQUIRE_LOCATION . '/db_commands.php'); 
require_once(REQUIRE_LOCATION . '/pagination.php'); 

session_start();
session_regenerate_id(true);

/*	
	This script is responsible for actually editting the contents of the post
*/

// If the user isn't logged in, kick them out of this page
if (empty($_SESSION['authenticate']) &&  $_SESSION['authenticate'] != ADMIN_CODE)
{
	redirect_to('login.php');	
}


// User Post submission
if (!empty($_POST['SubmitPost']))
{
	// GenerateID
	$ContentId = numbers_and_letters_only($_SESSION['EditPostId']);

	$File = new FileWriter();
	
	// Fetch the "OLD" data so we can delete it's HTML file (If it's a comic)
	$File->FetchFromDatabase ($ContentId);
	
	// Get the previous values
	$OldCategory = $File->StaticCategoty;	//used to subtract the number of the metadata if the post's category changed
	$OldTitle = $File->StaticTitle;			// used to see if we need to update the metadata if the title changed
	
	
	if ($File->StaticCategoty == 'comic')
	{
		//Delete the file
		$File->FileManipulation(false);
		
		
		//redirect_to('p?referer=create&id='. );	
		
	}
		
	
	// $OwnerId
	$OwnerId = ADMIN_CODE;
	
	//date_default_timezone_set("America/Toronto");
	$Date = $File->StaticDate;
	
	$Title = $_POST['PostTitle'];
	$Text =  $_POST['TextId']; //Instead of user text, a file is create containing the text. This is so we don't have to store the images in a database
	$Category = numbers_and_letters_only($_POST['ExistingCategory']);
	$IpAddress = getenv('REMOTE_ADDR');
	$ProxyIp = getenv('HTTP_X_FORWARDED_FOR');
	
	UpdateBlog($ContentId, $Title, $Text, $Category, $Date, $OwnerId, $IpAddress, $ProxyIp);
	
	// Increase the Meta Data Value of the category if the category type was changed
	AlterMetaDataPagination($Category);
	
	// Decrease the Old Category  if the category type was changed
	AlterMetaDataPagination($OldCategory);
	
	// Update the file that keeps track of the User's posts
	// We don't have to take title changes into consideration because the naming scheme doesn't need it
	$Location = ARTICLE_LOCATION. DIRECTORY_SEPARATOR .$File->StaticTitleId .'-'. $File->StaticText.'-'. $Category;
	$FileToWrite = stripslashes($_POST['Content']);	
	WriteToFile($Location, $FileToWrite);
	
	// Incase you're editing the newest comic and changed it's title, this will update the metadata that keeps track of that
	// But only do this if the title changes (Flawed logic because this happens regardless of the article being the newest or not)
	if ($Category == 'comic' && $Title != $OldTitle)
	{
		// Check to see if this is the latest article so we can update the Metadata that tells the site which one is the latest article
		$results = SelectOnlyOneDesc($Category, '1');
		
		// Create META data to tell the site what the lastest comic is
		$FileToCheck = META_LOCATION. DIRECTORY_SEPARATOR .'latestcomic';
		$fp = fopen($FileToCheck, 'w+');
		fwrite($fp, $results[0]['TitleId']."-".ChangeToDash($results[0]['title']));
		fclose($fp);
		
	}
	
	//redirect_to('p?referer=create&id='. );	
	
	

	
		
	if ($Category == 'comic')
	{		
		//Fetch the "NEW" data so we can Create HTML file
		// Now set the STATIC variables again to wipe the old values adn get the new ones
		$File->StaticId = $ContentId; 
		$File->StaticTitle = sanitize($Title);
		$File->StaticText = sanitize($Text); 
		$File->StaticCategoty = sanitize($Category);
		$File->StaticDate = $Date;		
	
		// Create new one in its place
		$File->FileManipulation(true);
		//redirect_to('p?referer=create&id='. );	

		
	}
	

	
	// Then redict to the update page
	redirect_to('update_post.php?referer=update');	
}


// We only need the id. Do not fill in the blanks
$Id = numbers_and_letters_only($_SESSION['EditPostId']);	
$results = SelectSearchById ($Id);

$title = $results[0]['title'];
$category = $results[0]['category_type'];
 
// Read the file currently associated to this post
// The location of the file that contains the text info
$Location = ARTICLE_LOCATION. DIRECTORY_SEPARATOR . $results[0]["TitleId"] .'-'. $results[0]["text"].'-'. $results[0]["category_type"];
$content = CheckFile ($Location);
$Text =	$results[0]["text"];// This is the text code that tells the site which file contains the user's text

function AlterMetaDataPagination($Category)
{
	// Count the values again
	$SQLcmd = "SELECT * FROM content_blog WHERE category_type = '".$Category."' ORDER BY date DESC";
	$results = SelectBlog($SQLcmd);
	$total_values = count($results);
	
	// Update the file that keeps track of the number of total values the blog has
	// Consequently, META data for any other category gets created as well
	$FileToCheck = META_LOCATION. DIRECTORY_SEPARATOR .$Category;
	$fp = fopen($FileToCheck, 'w+');
	fwrite($fp, $total_values);
	fclose($fp);
}
?>

<!doctype html>
<html>
<head>
<title>Daketsu CMS </title>

<!-- Begin tinymce CSS -->
<script src="tinymce/tinymce.min.js"></script>
<script>
tinymce.init
(
	{
		forced_root_block : "",
		width : "100%",
		height : "300",
		selector: 'textarea',  // change this value according to your HTML
		toolbar: 
		[
			'undo redo | styleselect | bold italic | link image',
			'alignleft aligncenter alignright'
		]
	}
);

</script>
<!-- End tinymce CSS -->

<!-- Begin Google CSS -->
<link rel="stylesheet" type="text/css"
          href="https://fonts.googleapis.com/css?family=Pavanam">
    <style>
      body {
        font-family: 'Pavanam', sans-serif;
        font-size: 18px;
      }
    </style>
<link rel="stylesheet" type="text/css"
          href="https://fonts.googleapis.com/css?family=Lalezar">

	
<!-- End Google CSS -->

<link href="css/backend.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<table Class="SiteTable" width="100%" border="0" cellpadding="0" cellspacing="0" >
	<!-- Begin Logo Bar -->
	<tr>
		<td colspan="2" align="center" class="MainLogo">
			<img src ="images/CMS_Layout_Logo.gif" class="Logo"/>
		</td>
	</tr>
		<!-- End Logo Bar -->
	
	<!-- Begin Navigation Bar -->
	<tr>
		<td colspan="2" align="center" class="HighlightBar">
			&nbsp;
		</td>
	</tr>
	<!-- End Navigation Bar -->
	<!-- Begin WhiteSpace Bar -->
	<tr>
		<td colspan="2" align="center">
			&nbsp;
		</td>
	</tr>
	<!-- End WhiteSpace Bar -->
	

	<tr height="800">
		<!-- Begin Left Navigation Side -->
		<td class="LeftContainer"  valign="top" width="15%">
			<?PHP require_once("sitewide/sidenav.php"); ?>
		</td>
		<!-- End Left Navigation Sise -->
		
		<!-- Begin Right Content Sise -->
		<td class="SideContent" valign="top" align="center">
			<div class="PostingHeader">Update Post</div>
			<div class="PostingContent">
				<form action="" method="post">
					<table>
					<tr>
						<td>Post Title:</td>
						<td><input type="text" name="PostTitle" id="textfield" value="<?PHP echo $title ?>"></td>
					<tr>
						<td>Category: </td>
						<td>
							<?PHP
							
							  $string = "";
								$string .= "<select name='ExistingCategory'>";
									$string .= $category == 'blog' ? "<option value='blog' selected='selected'>Blog</option>" : "<option value='blog'>Blog</option>";
									$string .= $category == 'log' ? "<option value='log' selected='selected'>Log</option>" : "<option value='log'>Log</option>";
									$string .= $category == 'comic' ? "<option value='comic' selected='selected'>Comic</option>" : "<option value='comic'>Comic</option>";
								$string .= "</select>";
								echo $string;
							?>
						</td>
					</tr>
					</table>
					<br/>
					<div>
						<textarea name="Content"> 
						<?PHP echo $content; ?>
						</textarea>
						<input type="hidden" value="<?PHP echo $Text;?>" name="TextId"/>
						<br/>
						<input type="submit" value="Submit" name="SubmitPost"/>
					</div>
				</form>
			</div>	
		</td>
		<!-- End Right Content Sise -->
		
	</tr>
	
</table>

</body>
</html>