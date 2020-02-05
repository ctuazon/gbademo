<?PHP

require_once("../../includes/constants.php"); 
require_once("../../metadata/MetaLocation.php"); 
require_once("../../articles/ArticleLocation.php");
require_once('FileWriter.php'); 
require_once(REQUIRE_LOCATION . '/global_functions.php'); 
require_once(REQUIRE_LOCATION . '/db_commands.php'); 

session_start();
session_regenerate_id(true);

// If the user isn't logged in, kick them out of this page
if (empty($_SESSION['authenticate']) &&  $_SESSION['authenticate'] != ADMIN_CODE)
{
	redirect_to('login.php');	

}

// User Post submission
if (!empty($_POST['SubmitPost']))
{
	// Generate Identifier for Text Content instead of storing the User's text
	$TextId = random_char_generator(5);
	$PostContent = stripslashes($_POST['Content']);		// Strip Slash because TinyMCE adds slashes. We're using Prepared statements, so we don't have to do this
	
	// GenerateID
	$ContentId = random_char_generator(30);

	// $OwnerId
	$OwnerId = ADMIN_CODE;
	
	date_default_timezone_set("America/Toronto");
	$Date = date("Y-m-d G:i:s");
	
	$Title = $_POST['PostTitle'];
	$TextCode =  $TextId;
	$Category = numbers_and_letters_only($_POST['ExistingCategory']);
	$IpAddress = getenv('REMOTE_ADDR');
	$ProxyIp = getenv('HTTP_X_FORWARDED_FOR');
	$TitleId = rand(10000, 99999);
	
	InsertBlog($ContentId, $Title, $TextCode, $Date, $Category, $OwnerId, $IpAddress, $ProxyIp, $TitleId);
	
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


	/*
		// Use this for debugging only
		// This dips directly into the database to count the number of entries that exists
		//$total_values = count(SelectBlog($SQLcmd));
	*/
	
	// Change this incase you ever decide to implement comments
	
	// Write the File that contains the text the USER entered
	WriteToFile(ARTICLE_LOCATION. DIRECTORY_SEPARATOR . $TitleId .'-'. $TextId .'-'. $Category, $PostContent);
	
	if ($Category == 'comic')
	{
		// Start writing the SEO HTML file
		$File = new FileWriter();
		
		$File->StaticId = $ContentId; 
		$File->StaticTitle = sanitize($Title);
		$File->StaticText = $TextCode; 
		$File->StaticCategoty = sanitize($Category);
		$File->StaticDate = $Date;		
		$File->StaticTitleId = $TitleId;
		
		$File->FileManipulation(true);

		// Write The SEO META file that tells which one is the latest comic
		WriteToFile(META_LOCATION. DIRECTORY_SEPARATOR .'latestcomic', $results[0]['TitleId']."-".ChangeToDash($results[0]['title']));
		
		
		//redirect_to('p?referer=create&id='. );	
		
	}
	

	
	redirect_to('update_post.php?referer=create');	
	
}


?>

<!doctype html>
<html>
<head>
<title>null </title>

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
		automatic_uploads: true,
		images_upload_base_path: '/images',
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
			<div class="PostingHeader">Create Post</div>
			<div class="PostingContent">
				<form action="" method="post">
					<table>
					<tr>
						<td>Post Title:</td>
						<td><input type="text" name="PostTitle" id="textfield" /></td>
					<tr>
						<td>Category: </td>
						<td>
							<?PHP
								require_once("sitewide/ListOfCategory.php");
							?>
						</td>
					</tr>
					</table>
					<br/>
					<div>
						<textarea name="Content"> 

						</textarea>
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