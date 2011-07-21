<!doctype html>
<html>
<head>
	<title><?php echo $title_for_layout ?></title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="author" content="Daniel Cassman">
	<?php 
		echo $html->css('screen', 'stylesheet', array('media' => 'screen, projection')) . "\n\t";
		echo $html->css('style', 'stylesheet', array('media' => 'screen, projection')) . "\n\t";
		echo $html->css('print', 'stylesheet', array('media' => 'print')) . "\n\t<!--[if lt IE 8]>\n\t\t";
		echo $html->css('ie', 'stylesheet', array('media' => 'screen, projection')) . "\n\t<![endif]-->\n";
		echo $scripts_for_layout
	?>
</head>
<body>
	<div id="header">
		<div>
			<span><?php echo $html->link('Mapping Militant Organizations','/'); ?></span>
			<a href="http://www.stanford.edu" id="institution"><?php echo $html->image('stanford.png', array('alt'=>'Stanford')); ?></a>
		</div>
	</div>
	<div id="wrap" class="container">
		<ul id="nav" class="clearfix">
			<li><?php echo $html->link('Home','/'); ?></li>
			<li><?php echo $html->link('Map',array('controller'=>'maps','action'=>'iraq')); ?></li>
			<li><?php echo $html->link('Profiles',array('controller'=>'profiles','action'=>'index')); ?></li>
			<li><?php echo $html->link('About',array('controller'=>'pages','action'=>'about')); ?></li>
			<li><?php echo $html->link('Technology',array('controller'=>'pages','action'=>'technology')); ?></li>
			<li><?php echo $html->link('Feedback',array('controller'=>'comments','action'=>'index')); ?></li>
		</ul>
		<?php echo $content_for_layout ?>
		<hr class="hidden">
		<div id="footer">
			<p id="copyright" class="span-7 append-1">
				Copyright 2010-<?php echo date('Y'); ?>
			</p>
			<p class="span-8 append-1 align-center">
				<?php echo $this->Html->link('Administration', array('controller' => 'comments', 'action' => 'review')); ?>
			</p>
			<ul id="affiliations" class="span-7 last">
				<li><a href="http://cisac.stanford.edu"><?php echo $html->image('cisac2.png', array('alt','CISAC')); ?></a></li>
				<li><a href="http://fsi.stanford.edu"><?php echo $html->image('fsi2.png', array('alt','FSI')); ?></a></li>
			</ul>
		</div>
	</div>
</body>
</html>