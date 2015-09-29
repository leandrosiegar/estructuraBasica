<div style="padding:15px;">
	<div>
		<font class=azul15><b><?=$titular;?></b></font><br>
		<!-- FB -->
		<?php 
		if (!isset($linkFacebook))
			$linkFacebook = $_SERVER["REQUEST_URI"];
			
		$linkFacebook = $_SERVER["REQUEST_URI"];
		?>
		
		<div class="fb-like" data-href="<?=$linkFacebook;?>" 
		data-width="300" data-layout="standard" data-action="like" 
		data-show-faces="true" data-share="true"></div>
		
	
		<!-- TW -->
		<a href="https://twitter.com/despotricador" class="twitter-follow-button" data-show-count="false" data-lang="es">@despotricador</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script> 
	</div>
</div>