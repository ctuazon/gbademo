<?PHP
require_once("../../includes/constants.php"); 
require_once("../../metadata/MetaLocation.php");
require_once("../../articles/ArticleLocation.php"); 
require_once(REQUIRE_LOCATION . '/global_functions.php'); 
require_once(REQUIRE_LOCATION . '/db_commands.php'); 
require_once(REQUIRE_LOCATION . '/pagination.php'); 

session_start();
session_regenerate_id(true);

$message = "&nbsp;";
/*	
	This script displays posts that can be editted
	You can also search for posts based on its title
*/

// If the user isn't logged in, kick them out of this page
if (empty($_SESSION['authenticate']) &&  $_SESSION['authenticate'] != ADMIN_CODE)
{
	redirect_to('login.php');	
}

if (!empty($_GET['id']))
{
	$_SESSION['EditPostId'] = numbers_and_letters_only($_GET['id']);
	
	redirect_to('update.php');	
}

// User Post submission
if (!empty($_POST['SubmitPost']))
{
	// GenerateID
	$ContentId = random_char_generator(30);

	// $OwnerId
	$OwnerId = ADMIN_CODE;
	
	date_default_timezone_set("America/Toronto");
	$Date = date("y-m-d G:i:s");
	
	$Title = $_POST['PostTitle'];
	$Text =  stripslashes($_POST['Content']);		// Strip Slash because TinyMCE adds slashes. We're using Prepared statements, so we don't have to do this
	$Category = $_POST['ExistingCategory'];
	$IpAddress = getenv('REMOTE_ADDR');
	$ProxyIp = getenv('HTTP_X_FORWARDED_FOR');
	
	InsertBlog($ContentId, $Title, $Text, $Date, $Category, $OwnerId, $IpAddress, $ProxyIp);
	
}
if (empty($_POST['SearchPost']))
{
	$items_per_page = ITEMS_PER_PAGE;
	$SQL_cmd = "SELECT * FROM content_blog WHERE category_type = 'blog' ORDER BY date DESC";
	$current_page = !empty($_GET['page']) ? numbers_only(($_GET['page'])) : 1;
	$these_columns = "*";
	$these_tables = "content_blog";
	$CateType = 'blog';
	$where = 'WHERE category_type = "'.$CateType.'" ORDER BY date DESC';
		
	// This is needed to make pagination work properly
	$content = pagination_script ($items_per_page, $SQL_cmd, $current_page, $these_columns, $these_tables, $where, $CateType);
}
else
{
	// A custom search was preformed
	$CategoryType =  numbers_and_letters_only ($_POST['ExistingCategory']);
	$Title = $_POST['SearchText'];
	$Id = '';
	
	$content = PreparedSearchSelect ($CategoryType, $Title, $Id);
	
}	

// this is so that I know what happened after I clicked the submit button
if (!empty($_GET['referer']))
{
	if ($_GET['referer'] == "delete")
	{
		$message = "<b><small>Delete Successful</small></b>";
	}
	else if($_GET['referer'] == "update")
	{
		$message = "<b><small>Revision Successful</small></b>";
	}
	else if($_GET['referer'] == "create")
	{
		$message = "<b><small>Creation Successful</small></b>";
	}
	else
	{
		$message = "&nbsp;";
	}
}
?>

<!doctype html>
<html>
<head>
<title> </title>

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
		
			<form action="update_post.php" method="post">
				<table>
					<tr>
						<td colspan=2 align="center"> 
							<?PHP echo $message;?>
						</td>
					</tr>
					<tr>
						<td>Search</td>
						<td>
							<input type="text" name="SearchText" id="textfield" />
							<?PHP
								require_once("sitewide/ListOfCategory.php");
							?>
							<input type="submit" value="Submit" name="SearchPost"/>
						</td>
					</tr>
				</table>
				<br/>
			</form>
		
			<!-- Begin Main Content Box -->
			<?PHP
				// Go through each Blog post listed on the database
				// The values were inserted at the top of the page
				// Execution comes from the return variable of pagination_script
	
	
				if (empty($_POST['SearchPost']))
				{
					
					if (!empty($content["execution"]))
					{
						foreach($content["execution"] as $output)
						{
							// The location of the file that contains the text info
							$Location = ARTICLE_LOCATION. DIRECTORY_SEPARATOR . $output["TitleId"] .'-'. $output["text"].'-'. $output["category_type"];
									
							echo '<div class ="MainContent">';
								echo '<div class="Heading">'. $output["title"] . '</div>';
								echo '<div class="Date">'. $output["date"] . '</div>';
								echo '<div class="Edit"><a href="update_post.php?id='. $output["content_id"] . '">Edit</a> | <a onclick="return confirm(\'Are you sure you want to delete this item?\');" href="delete_post.php?delete='. $output["content_id"] . '">Delete</a> </div>';
								echo '<div class="Text">'. CheckFile ($Location) . '</div>';
								echo "</div>";
								
						}
						// This is for the pagination box
						echo '<div class="PaginationBox">';
						numbered_pages ($content["total_pages"], $content["current_page"], "page");
						echo '</div>';
					}
				}
				else
				{	
					
					// This is used instead for custom searches
					for ($i = 0 ; $i < count($content) ; $i++)
					{
						$Location = ARTICLE_LOCATION. DIRECTORY_SEPARATOR . $content[$i]["TitleId"] .'-'. $content[$i]["text"].'-'. $content[$i]["category_type"];
						
						echo '<div class ="MainContent">';
							echo '<div class="Heading">'. $content[$i]["title"] . '</div>';
							echo '<div class="Date">'. $content[$i]["date"] . '</div>';
							echo '<div class="Edit"><a href="update_post.php?id='. $content[$i]["content_id"] . '">Edit</a> | <a onclick="return confirm(\'Are you sure you want to delete this item?\');" href="delete_post.php?delete='. $content[$i]["content_id"] . '">Delete</a> </div>';
							echo '<div class="Text">'. CheckFile ($Location) . '</div>';
						echo "</div>";
							
					}
										
				}

			
			?>	
			<!-- End Main Content Box -->
			
			
		<!-- End Right Content Sise -->
		
	</tr>
	
</table>

</body>
</html>

<?PHP

	
?>