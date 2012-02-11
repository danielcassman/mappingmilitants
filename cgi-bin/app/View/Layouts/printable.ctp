<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo $title_for_layout?></title>
	<meta charset="utf-8" />
	<?php
		echo $scripts_for_layout 
	?>
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-28503356-1']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
</head>
<body>
	<div id="wrap">
		<div id="control">
			<input type="button" value="Print" onclick="window.print()" /> 
			<input type="button" value="Close" onclick="window.close()" />
		</div>
		<div id="header"><p>Mapping Militant Organizations</p></div>
		<?php echo $content_for_layout ?>
	</div>
</div>
</body>
</html>
