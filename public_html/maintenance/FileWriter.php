<?php 
require_once("../../includes/constants.php"); 
require_once("../../metadata/MetaLocation.php"); 
require_once("../../articles/ArticleLocation.php");
require_once(REQUIRE_LOCATION . '/text_filter.php'); 
require_once(REQUIRE_LOCATION . '/global_functions.php'); 
require_once(REQUIRE_LOCATION . '/db_commands.php'); 
require_once(REQUIRE_LOCATION . '/pagination.php'); 


class FileWriter 
{
	private $id;
	
	public $StaticId; 
	public $StaticTitle;
	public $StaticText; 
	public $StaticCategoty;
	public $StaticDate;		
	public $StaticTitleId;

	private $PathToDir;
	private	$FileToCheck ;
	
	// CALL THIS during Updates To the database
	function FetchFromDatabase ($new_id) 
	{
		$this->id = $new_id;
		
		// Unecessary but sanitize anyway
		$Id = numbers_and_letters_only($this->id);
		$results = SelectSearchById ($Id);

		// This prevents the user from entering something in the id field and getting something back
		if (!empty($results) && $results != null)
		{

			// It found something
			$this->StaticId 		= $results[0]['content_id'];
			$this->StaticTitle 	= sanitize($results[0]['title']); 	
			$this->StaticText 	= sanitize($results[0]['text']); 		
			$this->StaticCategoty = $results[0]['category_type'];
			$this->StaticDate		= $results[0]['date']; 	
			$this->StaticTitleId	= $results[0]['TitleId']; 	


		}
	}

	/*
		DO NOT CALL THIS UNLESS STATIC variables are set somehow
		EITHER SET STATIC MANUALLY OR USE FETCHFROMDABASE FIRST
	*/
	
	function FileManipulation($create)	
	{
		$filename = "index.php";	// Do not change this

		$fileDir = $this->StaticTitle;				// The post's title
		$fileDir = ChangeToDash($fileDir);		// Inseert the dashes and Makes the whole thing lower case
		$fileDir = '..'.DIRECTORY_SEPARATOR . $this->StaticCategoty. DIRECTORY_SEPARATOR .$this->StaticTitleId."-".$fileDir;		// Expected Output = 4565-Title-Of-Article
		//$fileDir = "content/".$this->StaticId."/".$fileDir;

		// Create an SEO friendly directory and always namethe file index.php
		$this->PathToDir = $fileDir . DIRECTORY_SEPARATOR;
		$this->FileToCheck = $fileDir . DIRECTORY_SEPARATOR . $filename ;
		
		if ($create)
		{
			// If true, create a new file
			$this->CreateFile();
		}
		else
		{
			// If false, delete existing file
			$this->DeleteFile();
		}
	}
	
	
	private function CreateFile ()
	{
		mkdir($this->PathToDir, 0777, true);
		chmod($this->PathToDir, 0777);
				
		// Write to that file
		$fp = fopen($this->FileToCheck, 'w+');
		fwrite($fp, $this->HTMLCode($this->StaticTitle, $this->StaticDate, $this->StaticText)); 
		fclose($fp);
	}
	
	private function DeleteFile () 
	{
		// Path relative to where the php file is or absolute server path
		// First Delete the index file because you can't delete the folder if has something inside it
		if (file_exists($this->FileToCheck))
		{
			unlink ($this->FileToCheck);
		}
		
		// Then delete the directory
		if (is_dir($this->PathToDir) || file_exists($this->PathToDir))
		{
			rmdir($this->PathToDir);
		}
		//rmdir (realpath(__DIR__). DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR . $StaticCategory. DIRECTORY_SEPARATOR .$StaticTitleId."-".$StaticTitle);
		
	}
	
	
	private function HTMLCode($StaticTitle, $StaticDate, $StaticText)
	{

		
		
		$string = '
<?PHP
require_once("../../../includes/constants.php"); 
require_once("../../../metadata/MetaLocation.php"); 
require_once("../../../sitewide/MetaLocation.php"); 
require_once("../../../articles/ArticleLocation.php");
require_once(REQUIRE_LOCATION . \'/text_filter.php\'); 
require_once(REQUIRE_LOCATION . \'/global_functions.php\'); 
require_once(REQUIRE_LOCATION . \'/db_commands.php\'); 
require_once(REQUIRE_LOCATION . \'/pagination.php\'); 

$NavPages = \'\';
		
$output = array();
// This is for the before and after pagination
$results = SelectBeforeAndAfter(\''.$StaticDate.'\', \''.$this->StaticCategoty.'\');
		
/*	
	First is Oldest
	Second is Article
	Thrid is Newest
*/

for ($i = 0 ; $i < count($results) ; $i++)
{
	$output[$i][\'title\']		= $results[$i][\'title\'];
	$output[$i][\'TitleId\'] 		= $results[$i][\'TitleId\'];	
	$output[$i][\'category_type\']= $results[$i][\'category_type\'];
}
	
// Find out what the very first article was
$FirstComic = SelectOnlyOne(\''.$this->StaticCategoty.'\',\'1\', \'ASC\');

if (count($results) == 3)
{
	$NavPages = \'<a class="PaginationButton" href="http://\'.SITE_ROOT . $output[0][\'category_type\'].\'/\'.$output[0][\'TitleId\'].\'-\'.ChangeToDash($output[0][\'title\']).\'/\'. \'">Back</a> 
				<a class="PaginationButton" href="http://\'.SITE_ROOT . $FirstComic[0][\'category_type\'].\'/\'.$FirstComic[0][\'TitleId\'].\'-\'.ChangeToDash($FirstComic[0][\'title\']).\'/\'. \'">First Comic</a> 
				<a class="PaginationButton" href="http://\'.SITE_ROOT . \'comic-archive.php">Archives</a> 
				<a class="PaginationButton" href="http://\'.SITE_ROOT . $output[2][\'category_type\'].\'/\'.$output[2][\'TitleId\'].\'-\'.ChangeToDash($output[2][\'title\']).\'/\'. \'">Forward</a> \' ;
}
else if (count($results) == 2)
{
	$NavPages = \'<a class="PaginationButton" href="http://\'.SITE_ROOT . $output[0][\'category_type\'].\'/\'.$output[0][\'TitleId\'].\'-\'.ChangeToDash($output[0][\'title\']).\'/\'. \'">Back</a> 
				<a class="PaginationButton" href="http://\'.SITE_ROOT . $FirstComic[0][\'category_type\'].\'/\'.$FirstComic[0][\'TitleId\'].\'-\'.ChangeToDash($FirstComic[0][\'title\']).\'/\'. \'">First Comic</a> 
				<a class="PaginationButton" href="http://\'.SITE_ROOT . \'comic-archive.php">Archives</a> 
				\';
}
else // probably the first article
{
	$results = SelectASCOrder(\''.$this->StaticCategoty.'\');

	if (count($results) >= 2)
	{
		for ($i = 0 ; $i < count($results) ; $i++)
		{
			$output[$i][\'title\']		= $results[$i][\'title\'];
			$output[$i][\'TitleId\'] 		= $results[$i][\'TitleId\'];	
			$output[$i][\'category_type\']= $results[$i][\'category_type\'];
		}
		$NavPages = \'<a class="PaginationButton" href="http://\'.SITE_ROOT . \'comic-archive.php">Archives</a> 
		<a class="PaginationButton" href="http://\'.SITE_ROOT . $output[1][\'category_type\'].\'/\'.$output[1][\'TitleId\'].\'-\'.ChangeToDash($output[1][\'title\']).\'/\'. \'">Forward</a> 
		
				\';
	}
	else // incase its the only article in the database
	{
		$NavPages =\'\';
	}
	
	
}

	
?>

<!doctype html>
<html>
<?PHP include (SITEWIDE_LOCATION . \'/SITE_header.php\'); // Look here to edit the contents of the navigation bar ?>
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
					<?PHP include (SITEWIDE_LOCATION . \'/SITE_navigation.php\'); // Look here to edit the contents of the navigation bar ?>
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
				
				$Location = ARTICLE_LOCATION . DIRECTORY_SEPARATOR . "' .$this->StaticTitleId.'" . "-" . "'.$this->StaticText.'" . "-" . "'.$this->StaticCategoty.'";
				

				echo \'<div class ="MainContent">\';
					echo \'<div class="Heading">'.$StaticTitle .'</div>\';
					echo \'<div class="Date">'.$StaticDate .'</div>\';
					echo \'<div class="Text">\'.CheckFile ($Location).\'</div>\';
				echo "</div>";
					
				echo \'<div class="PaginationBox">\';
				echo $NavPages;
				echo \'</div>\';
					
			?>	
			<!-- End Main Content Box -->
		</td>
		<!-- End Left Content Sise -->
		
		<!-- Begin Right Content Sise -->
		<td class="SideContent" valign="top">
		
			<?PHP include (SITEWIDE_LOCATION . \'/SITE_sidebar.php\') // everything related to the side bar?>
			
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
</html>';
		
		return $string;
	}

}
?>