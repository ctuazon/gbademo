<?PHP
/*
echo 	'<!-- Begin Get Game Box -->
			<article class ="subcontent">
				<h1>The Game</h1>
				<div class="Text">
					<a href="https://play.google.com/store/apps/details?id=com.czyrustuazon.ghostbanditsattack" target="_blank">
						<img src="images/GoogleBadge.png" alt="Get The Addicting Game, Ghost Bandits Attack, today! Available for free on Google Play!"/>
					</a>
				</div>
			</article>
			<!-- End Get Game Box -->
			
			<!-- Begin Comic Box -->
			<article class ="subcontent">
				<h1 class="Heading">Latest Comic</h1>
					<div class="Text">
						<a href="http://ghostbanditsattack.com/comic">
							<img class="Comic" src="images/ComicPreview.jpg"  alt=""/>
						</a>
					</div>
			</article>
			<!-- End Comic Box -->
			
			<!-- Begin Gameplay Video Box -->
			<article class ="subcontent">
				<h1 class="Heading">Game Play</h1>
				<div class="Text"><iframe width="380" height="214" src="https://www.youtube.com/embed/ZMefFq1NZtE" frameborder="0" allowfullscreen></iframe></div>
			</article>
			<!-- End Gameplay Video Box -->
			
			<!-- Begin Social Box -->
			<article class ="subcontent">
				<h1 class="Heading">Social</h1>
				<div class="Social">
					<a href="https://www.facebook.com/GhostBanditsAttack/" target="_blank"><img src="images/Facebook.jpg"  alt="Check out our Facebook pages for more things related to the Ghost Bandits"/></a>
					<a href="https://twitter.com/ghostbandits" target="_blank"><img src="images/Twitter.jpg" alt="We also have a Twitter feed! Follow us on Twitter to get up to the minute updates"/>
					<a href="https://www.youtube.com/channel/UC2JJqAw5lsUY61jUTlWhb2Q" target="_blank"><img src="images/Youtube.jpg"  alt="And A Youtube Page as well, where you get to see the game in action!"/></a>
				</div>
			</article>
			<!-- End Social Box -->';
*/

// Fetch the image
$results = SelectASCOrder("COMIC_THUMBNAIL");
// Fetch the current thumbnail image if it exists
if (($results != null || !empty($results)) && !empty($results[0]['text']))
{
	
	$content =		
			
			'<!-- Begin Comic Box -->
			<article class ="subcontent">
				<h1 class="Heading">Latest Comic</h1>
					<div class="Text">
						<a href="http://ghostbanditsattack.com/comic">
							'.$results[0]['text'].'
						</a>
					</div>
			</article>
			<!-- End Comic Box -->';

	
}
else	// Don't show the box that contains the comic 
{
	$content="";
}
		
echo 	'<!-- Begin Get Game Box -->
			<article class ="subcontent">
				<h1 class="Heading">The Game</h1>
				<div class="Text">
					<a href="https://play.google.com/store/apps/details?id=com.czyrustuazon.ghostbanditsattack" target="_blank">
						<img src="http://'. SITE_ROOT .'images/GoogleBadge.png" alt="Get The Addicting Game, Ghost Bandits Attack, today! Available for free on Google Play!"/>
					</a>
				</div>
			</article>
			<!-- End Get Game Box -->
			
			'.$content.'
			
			<!-- Begin Gameplay Video Box -->
			<article class ="subcontent">
				<h1 class="Heading">Game Play</h1>
				<div class="Text"><iframe width="100%" height="100%" src="https://www.youtube.com/embed/ZMefFq1NZtE" frameborder="0" allowfullscreen></iframe></div>
			</article>
			<!-- End Gameplay Video Box -->
			
			<!-- Begin Social Box -->
			<article class ="subcontent">
				<h1 class="Heading">Social</h1>
				<div class="Social">
					<a href="https://www.facebook.com/GhostBanditsAttack/" target="_blank"><img class="sociallogo" src="http://'. SITE_ROOT .'images/Facebook.jpg"  alt="Check out our Facebook pages for more things related to the Ghost Bandits"/></a>
					<a href="https://twitter.com/ghostbandits" target="_blank"><img class="sociallogo" src="http://'. SITE_ROOT .'images/Twitter.jpg" alt="We also have a Twitter feed! Follow us on Twitter to get up to the minute updates"/>
					<a href="https://www.youtube.com/channel/UC2JJqAw5lsUY61jUTlWhb2Q" target="_blank"><img src="http://'. SITE_ROOT .'images/Youtube.jpg"  class="sociallogo" alt="And A Youtube Page as well, where you get to see the game in action!"/></a>
				</div>
			</article>
			<!-- End Social Box -->';
?>