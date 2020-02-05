<?PHP 

require_once("../includes/constants.php"); 
require_once("../metadata/MetaLocation.php"); 
require_once("../sitewide/MetaLocation.php"); 
require_once("../articles/ArticleLocation.php"); 
require_once(REQUIRE_LOCATION . '/text_filter.php'); 
require_once(REQUIRE_LOCATION . '/global_functions.php'); 
require_once(REQUIRE_LOCATION . '/db_commands.php'); 
require_once(REQUIRE_LOCATION . '/pagination.php'); 


session_start();
session_regenerate_id(true);



//$content = SelectBlog();
$items_per_page = ITEMS_PER_PAGE;
$SQLcmd = "SELECT * FROM content_blog WHERE category_type = 'log' ORDER BY date DESC";
$current_page = !empty($_GET['page']) ? numbers_only(($_GET['page'])) : 1;
$these_columns = "*";
$CateType = "log";
$these_tables = "content_blog";
$where = 'WHERE category_type = "'.$CateType.'" ORDER BY date DESC';
	
// This is needed to make pagination work properly
$content = pagination_script ($items_per_page, $SQLcmd, $current_page, $these_columns, $these_tables, $where, $CateType);



?>
<!doctype html>
<html>
<?PHP include (SITEWIDE_LOCATION . '/SITE_header.php'); // Look here to edit the contents of the navigation bar ?>
<body background="http://<?PHP echo SITE_ROOT; ?>images/bg.gif">

<table Class="SiteTable" width="100%" border="0" cellpadding="0" cellspacing="0" >
	<!-- Begin Logo Bar -->
	<tr>
		<td colspan="2" align="center">
			<img src="http://<?PHP echo SITE_ROOT; ?>images/Logo.png" alt="Welcome to the home of Ghost Bandits Attack!">
		</td>
	</tr>
	<!-- End Logo Bar -->
	
	<!-- Begin Navigation Bar -->
	<tr>
		<td colspan="2" align="center">
			<table width="80%"  border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="http://<?PHP echo SITE_ROOT; ?>images/Navibar_left.gif" align="right" alt=""/></td>
					<td background="http://<?PHP echo SITE_ROOT; ?>images/NaviBar.gif" class="NaviContent" align="center">
					<?PHP include (SITEWIDE_LOCATION . '/SITE_navigation.php'); // Look here to edit the contents of the navigation bar ?>
					<td><img src="http://<?PHP echo SITE_ROOT; ?>images/Navibar_right.gif" align="left" alt=""/></td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- End Navigation Bar -->
	

	<tr height="800">
		<!-- Begin Left Content Side -->
		<td class="LeftContainer"  valign="top">
		
			<!-- Begin Main Content Box -->
			<?PHP
				// Go through each Blog post listed on the database
				// The values were inserted at the top of the page
				// Execution comes from the return variable of pagination_script
				
				if (!empty($content["execution"]))
				{
					foreach($content["execution"] as $output)
					{
						$Location = ARTICLE_LOCATION. DIRECTORY_SEPARATOR . $output["TitleId"] .'-'. $output["text"].'-'. $output["category_type"];
						
						echo '<div class ="MainContent">';
							echo '<div class="Heading">'. $output["title"] . '</div>';
							echo '<div class="Date">'. $output["date"] . '</div>';
							echo '<div class="Text">'. CheckFile ($Location) . '</div>';
						echo "</div>";
						
					}
				}
				
				// This is for the pagination box
				echo '<div class="PaginationBox">';
				numbered_pages ($content["total_pages"], $content["current_page"], "page");
				echo '</div>';
			
			?>	
			<!-- End Main Content Box -->
		</td>
		<!-- End Left Content Sise -->
		
		<!-- Begin Right Content Sise -->
		<td class="SideContent" valign="top">
		
			<?PHP include (SITEWIDE_LOCATION . '/SITE_sidebar.php') // everything related to the side bar?>
			
		</td>
		<!-- End Right Content Sise -->
		
	</tr>
	<tr>
		<td colspan="2" height="200" >
			<table class="Footer" cellpadding="10" cellspacing="10">
				<tr>
					<td class="Copyright"  valign="bottom">Copyright 2016</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

</body>
</html>