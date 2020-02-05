<?PHP

// DO NOT PUT THESE HERE. LET THE SCIPTS THEMSELVES CALL THESE TWO
//require_once("../includes/db_commands.php"); 
//require_once("../metadata/MetaLocation.php"); 

// Used for default operations. Used for results that weren't serached for. Note: numbered_pages relies on the return output of this function
function pagination_script ($items_per_page = 3, $SQLcmd, $current_page, $these_columns, $these_tables, $where = '', $CateType)
{
	// Count how many entries are stored
	$FileToCheck = META_LOCATION. DIRECTORY_SEPARATOR .$CateType;
	$fp = fopen($FileToCheck, 'r');
	$total_values = fread($fp, filesize($FileToCheck));		// Total entries gets saved here
	fclose($fp);


	/*
		// Use this for debugging only
		// This dips directly into the database to count the number of entries that exists
		//$total_values = count(SelectBlog($SQLcmd));
	*/
	$total_pages = ceil($total_values / $items_per_page);

	if (!empty($current_page))
	{
		$current_page = $current_page;
	}
	else
	{
		$current_page = 1;
	}

	// If the current page is more than or equal the total number of pages, then make this equal the total else, accept current page
	$current_page = ($current_page >= $total_pages)? $total_pages : $current_page;
	
	// If The things in brackets is less than or equal 0, then make it equal zero, else make equal whatever it wants to be	
	$begin_here = ($items_per_page * ($current_page - 1)) <=0? 0 : $items_per_page * ($current_page - 1);
				
	$query ="SELECT {$these_columns} 
	FROM {$these_tables} 
	{$where}
	LIMIT {$items_per_page} OFFSET {$begin_here} ";

	$value['execution'] = SelectBlog($query);		// The return value of query gets put into $value instead
	$value['total_pages'] = $total_pages;			// Used by numbered pages
	$value['current_page'] = $current_page;			// Used by numbered pages
	
	return $value;
}

// prints out the pages the used can use to navigate arount the site
// Command is used to store the current page number in the $_GET. When in doubt, give it any value that matches
function numbered_pages ($total_pages, $current_page, $command)
{
	// If there are too many articles that can't fit in one page, show the pagination
	if ($total_pages >= 2)
	{
		// This is for the back page	
		if ($current_page != 1)
		{
			$back_page = $current_page - 1;
			
			// The original, untouched
			//echo "<a href='?{$command}={$back_page}'>back</a> ";
			
			echo "<a class='PaginationButton' href='?{$command}={$back_page}'>back</a> ";
		}
		
		/*
			First Half
		*/		
		// If the current page is a 2 or below...
		// This is the default that's printed out first
		if ($current_page <= PAGE_PADDING)  // <- this is for outputing the first page numbers
		{
			// The default starting point will be 1 (the first page)
			$first_page = 1;
			// ..start the counter at one and print the pages 1 to X 
			for($i = $first_page; $i <= $first_page + PAGE_PADDING + PAGE_PADDING; $i++) // the array is ready to spit out 5 numbers but will spit out less if there aren't 5
			{
				if ($i < $total_pages) // This prevents the page number from higher than the total_pages
				{

					// The original, untouched
					//echo "<a href='?{$command}={$i}'>{$i}</a> | ";	

					echo "<a class='PaginationButton' href='?{$command}={$i}'>{$i}</a> ";

				
				}
				// When you reach the last number, remove the bar
				else if ($i == $total_pages)
				{
					// The original, untouched
					//echo "<a href='?{$command}={$i}'>{$i}</a> ";	
					
					echo "<a class='PaginationButton' href='?{$command}={$i}'>{$i}</a> ";	
				}	
			}		
		}
		/*
			Second Half
		*/
		// If the page is more than the total amount of pages (minus 2)
		// note: 2 is the padding 
		// example: [padding] [padding] [current_page] [padding] [padding]
		// in this situation, this is what will be printed out:
		// <1> [2] [3] [4] [5] or...
		// [1] <2> [3] [4] [5] 
		// depending on the padding's value
		// That means the users is in half way through the pages
		else if ($current_page >= $total_pages - PAGE_PADDING) // <- this is for outputing the last remaining pages
		{
			// The default starting point will be the total_pages
			$last_pages = $total_pages;
			// Which will be subtracted by the padding * 2
			for($i = $last_pages - PAGE_PADDING - PAGE_PADDING ; $i <= $last_pages; $i++)
			{
				if ($i > 0) // This prevent any zero values from being printed
				{
					// Remove | when you're at the last number
					if ($i == $last_pages )
					{
						// The original, untouched
						//echo "<a href='?{$command}={$i}'>{$i}</a> ";
						
						echo "<a class='PaginationButton' href='?{$command}={$i}'>{$i}</a> ";
					}
					else
					{
						// The original, untouched
						//echo "<a href='?{$command}={$i}'>{$i}</a> | ";
						
						echo "<a class='PaginationButton' href='?{$command}={$i}'>{$i}</a> ";
					}
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
				// The original, untouched
				// echo "<a href='?{$command}={$i}'>{$i}</a> | ";				
				
				echo "<a class='PaginationButton' href='?{$command}={$i}'>{$i}</a>";	
			}				
		}	
	
		// This is for the forward page
		if ($current_page < $total_pages)
		{
			$forward_page = $current_page + 1;
			
			// The original, untouched
			//echo "<a href='?{$command}={$forward_page}'> forward</a>";
			
			echo "<a class='PaginationButton' href='?{$command}={$forward_page}'> forward</a>";
			
		}
	}

		
	

}
?>