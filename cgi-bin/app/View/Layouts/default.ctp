<!doctype html>
<html>
<head>
	<title><?php echo $title_for_layout ?></title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="author" content="Daniel Cassman">
	<meta name="google-site-verification" content="twel_cAV1Xkn8O1W3C0Yup5xKW0oQSfl5PONjaPS8E4" />
	<?php 
		echo $this->Html->css('screen', 'stylesheet', array('media' => 'screen, projection')) . "\n\t";
		echo $this->Html->css('style', 'stylesheet', array('media' => 'screen, projection')) . "\n\t";
		echo $this->Html->css('print', 'stylesheet', array('media' => 'print')) . "\n\t<!--[if lt IE 8]>\n\t\t";
		echo $this->Html->css('ie', 'stylesheet', array('media' => 'screen, projection')) . "\n\t<![endif]-->\n";
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
	<div id="header">
		<div>
			<span><?php echo $this->Html->link('Mapping Militant Organizations','/'); ?></span>
			<a href="http://www.stanford.edu" id="institution"><?php echo $this->Html->image('stanford.png', array('alt'=>'Stanford')); ?></a>
		</div>
	</div>
	<div id="wrap" class="container">
		<ul id="nav" class="clearfix">
			<li><?php echo $this->Html->link('Home','/'); ?></li>
			<li><?php echo $this->Html->link('Maps',array('controller'=>'maps','action'=>'index')); ?></li>
			<li><?php echo $this->Html->link('Profiles',array('controller'=>'groups','action'=>'index')); ?></li>
			<li><?php echo $this->Html->link('About',array('controller'=>'pages','action'=>'about')); ?></li>
			<li><?php echo $this->Html->link('Technology',array('controller'=>'pages','action'=>'technology')); ?></li>
			<li><?php echo $this->Html->link('Feedback',array('controller'=>'comments','action'=>'index')); ?></li>
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
				<li><a href="http://cisac.stanford.edu"><?php echo $this->Html->image('cisac2.png', array('alt','CISAC')); ?></a></li>
				<li><a href="http://fsi.stanford.edu"><?php echo $this->Html->image('fsi2.png', array('alt','FSI')); ?></a></li>
			</ul>
		</div>
	</div>
</body>
</html>