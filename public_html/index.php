<?PHP 
session_start();
session_regenerate_id(true);

require_once("../includes/constants.php"); 
require_once("../metadata/MetaLocation.php"); 
require_once("../sitewide/MetaLocation.php"); 
require_once("../articles/ArticleLocation.php"); 
require_once(REQUIRE_LOCATION . '/text_filter.php'); 
require_once(REQUIRE_LOCATION . '/global_functions.php'); 
require_once(REQUIRE_LOCATION . '/db_commands.php'); 
require_once(REQUIRE_LOCATION . '/pagination.php'); 

//$content = SelectBlog();
$items_per_page = ITEMS_PER_PAGE;
$SQL_cmd = "SELECT * FROM content_blog WHERE category_type = 'blog' ORDER BY date DESC";
$current_page = !empty($_GET['page']) ? numbers_only(($_GET['page'])) : 1;
$these_columns = "*";
$these_tables = "content_blog";
$CateType = 'blog';
$where = 'WHERE category_type = "'.$CateType.'" ORDER BY date DESC';
	
// This is needed to make pagination work properly
$content = pagination_script ($items_per_page, $SQL_cmd, $current_page, $these_columns, $these_tables, $where, $CateType);



?>
<!doctype html>

<html>
<?PHP include (SITEWIDE_LOCATION . '/SITE_header.php'); // Look here to edit the contents of the navigation bar ?>
<body background="http://<?PHP echo SITE_ROOT; ?>images/bg.gif">
	
	<header>
		<div>
			<img class="logo"  src="http://<?PHP echo SITE_ROOT; ?>images/Logo.png" alt="Welcome to the home of Ghost Bandits Attack!">
		</div>	
		<nav>
			<?PHP include (SITEWIDE_LOCATION . '/SITE_navigation.php'); // Look here to edit the contents of the navigation bar ?>	
		</nav>
		
	</header>
		
	<main>
		<section>
		<?PHP
			// Go through each Blog post listed on the database
			// The values were inserted at the top of the page
			// Execution comes from the return variable of pagination_script
			if (!empty($content["execution"]))
			{
				foreach($content["execution"] as $output)
				{
					$Location = ARTICLE_LOCATION. DIRECTORY_SEPARATOR . $output["TitleId"] .'-'. $output["text"].'-'. $output["category_type"];
						
					echo '<article class ="maincontent">';
						echo '<h1>'. $output["title"] . '</h1>';
						echo '<h2>'. $output["date"] . '</h2>';
						echo '<div class="Text">'. CheckFile ($Location) . '</div>';
					echo "</article>";
					
					}
			}
			// This is for the pagination box
			echo '<div class="PaginationBox">';
			numbered_pages ($content["total_pages"], $content["current_page"], "page");
			echo '</div>';
				
		?>	

	
		</section>
		<section class="rightsection">
			<?PHP include (SITEWIDE_LOCATION . '/SITE_sidebar.php') // everything related to the side bar?>
		</section>
	</main>	
		
	<!-- End Right Content Sise -->
	<footer>
		Copyright 2016
	</footer>

</body>
</html>