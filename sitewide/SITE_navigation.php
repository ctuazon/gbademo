<?PHP 



	// These are the links that will appear on every page
	//echo '<a href="http://ghostbanditsattack.com">HOME</a> &nbsp;  &nbsp;  &nbsp;  &nbsp;| &nbsp;  &nbsp;  &nbsp;  &nbsp;';
	//echo '<a href="http://ghostbanditsattack.com/comics">COMICS</a>  &nbsp;  &nbsp;  &nbsp;  &nbsp;| &nbsp;  &nbsp;  &nbsp;  &nbsp;';
	//echo '<a href="http://ghostbanditsattack.com/log">Version History</a> &nbsp;  &nbsp;  &nbsp;  &nbsp;| &nbsp;  &nbsp;  &nbsp;  &nbsp;';
	//echo '<a href="http://ghostbanditsattack.com/about">ABOUT</a></td>';
	
	
	// Get whatever the lastest comic's name is
	$FileToCheck = META_LOCATION. DIRECTORY_SEPARATOR .'latestcomic';			
	$fp = fopen($FileToCheck, 'r');
	$filename = fread($fp, filesize($FileToCheck));		// Total entries gets saved here
	fclose($fp);

	// If it's not long enough, that means, there arent any comics available
	if (strlen($filename) <=5)
	{
		$comics = "";
	}
	else
	{
		$comics = '<a href="http://' .SITE_ROOT .'comic/' .$filename.'">COMIC</a> ';	
	}
	
	echo '<div class="navlinks">' . '<a href="http://' .SITE_ROOT .'">HOME</a></div>';
	echo '<div class="navlinks">' . '<a href="http://' .SITE_ROOT .'/log.php">VERSION LOG</a></div>';
	echo '<div class="navlinks">' . $comics . '</div>';
	
	
?>