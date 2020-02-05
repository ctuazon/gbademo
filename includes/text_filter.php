<?PHP

function ChangeToDash($string)
{
	$string = str_replace('#', '-', $string);
	$string = str_replace(';', '-', $string);		// Ending code
	$string = str_replace('&', '-', $string);
	$string = str_replace('`', '-', $string);
	$string = str_replace('\'','-', $string);		// signle Quotes
	$string = str_replace('"', '-', $string);		// breaking quotes
	$string = str_replace('*', '-', $string);
	$string = str_replace('%', '-', $string);
	$string = str_replace('$', '-', $string);
	$string = str_replace('!', '-', $string);
	$string = str_replace('\\', '-', $string);
	$string = str_replace('/', '-', $string);		// Comments
	$string = str_replace('{', '-', $string);
	$string = str_replace('}', '-', $string);
	$string = str_replace('+', '-', $string);
	$string = str_replace('=', '-', $string);
	$string = str_replace('[', '-', $string);
	$string = str_replace(']', '-', $string);
	$string = str_replace('<', '-', $string);
	$string = str_replace('>', '-', $string);
	$string = str_replace('_', '-', $string);
	$string = str_replace(' ', '-', $string);
	$string = strtolower ($string);
	
	$string = trim($string);
	
	return $string;
}
function sanitize($string)
{
//	$string = str_replace('#', '&#35;', $string);
	$string = str_replace(';', '&#59;', $string);		// Ending code
//	$string = str_replace('&', '&amp;', $string);
	$string = str_replace('`', '&#96;', $string);
	$string = str_replace('\'','&#39;', $string);		// signle Quotes
	//$string = str_replace('"', '&quot;', $string);		// breaking quotes
	$string = str_replace('*', '&#42;', $string);
	$string = str_replace('%', '&#37;', $string);
	$string = str_replace('$', '&#36;', $string);
	$string = str_replace('!', '&#33;', $string);
	$string = str_replace('\\', '&#92;', $string);
	//$string = str_replace('/', '&#47;', $string);		// Comments
	$string = str_replace('{', '&#123;', $string);
	$string = str_replace('}', '&#125;', $string);
	//$string = str_replace('+', '&#43;', $string);
	//$string = str_replace('=', '&#61;', $string);
	//$string = str_replace('[', '&#91;', $string);
	//$string = str_replace(']', '&#93;', $string);
	//$string = str_replace('<', '&lt;', $string);
	//$string = str_replace('>', '&gt;', $string);
	//$string = str_replace('_', '&#95;', $string);
	$string = trim($string);
	
	return $string;
}
function revert ($string)
{
//	$string = str_replace('&amp;', '&', $string);
	$string = str_replace('&#59;', ';', $string);		// Ending code
	$string = str_replace('&#96;', '`', $string);
//	$string = str_replace('&#35;', '#', $string);
	$string = str_replace('&#39;', '\'', $string);		// signle Quotes
	$string = str_replace('&quot;', '"', $string);		// breaking quotes
	$string = str_replace('&#42;', '*', $string);
	$string = str_replace('&#37;', '%', $string);
	$string = str_replace('&#36;', '$', $string);
	$string = str_replace('&#33;', '!', $string);
	$string = str_replace('&#92;', '\\', $string);
	//$string = str_replace('&#47;', '/', $string);		// Comments
	$string = str_replace('&#123;', '{', $string);
	$string = str_replace('&#125;', '}', $string);
	$string = str_replace('&#43;', '+', $string);
	$string = str_replace('&#61;', '=', $string);
	//$string = str_replace('&#91;', '[', $string);
	//$string = str_replace('&#93;', ']', $string);
	$string = str_replace('&lt;', '<', $string);
	$string = str_replace('&gt;', '>', $string);
	//$string = str_replace('&#95;', '_', $string);
	$string = trim($string);
	
	return $string;
}
function numbers_only($string)
{
	$string = preg_replace('/[a-zA-z\`\~\!\@\#\$\%\^\*\(\)\;\,\.\'\/\_\-\"\\\{\}\+\=\[\]\<\>\?\&\:\|\ ]/', '', $string);
	$string = (int)$string; // THIS WAS ADD RECENTLY					
	return $string;											
}
function numbers_and_letters_only ($string)
{
	$string = preg_replace('#[\W\_]#', '', $string);
	return $string;
}


function max_length ($string, $length)
{
	// determine how long the string is suppose to be
	$string = substr($string, 0, $length); 
	// remove any illegal characters
	$string = sanitize($string);
	// do that thing that clean things going into the db
	$string = mysql_real_escape_string($string);
	
	return $string;
}



?>