<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo $title_for_layout?></title>
	<meta charset="utf-8" />
	<?php
		echo $scripts_for_layout 
	?>
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
