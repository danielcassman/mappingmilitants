<?php
	$this->set('title_for_layout', 'Iraq | Mapping Militant Organizations');
	echo $html->css('buttons','stylesheet',array('inline'=>false));
?>
<h1 class="span-24">Map Coming Soon</h1>
<div class="span-15 colborder">
	<p>Sorry for the inconvenience.</p>
</div>
<p class="span-8 last">
	<?php
		echo $html->link('See the map', array('controller'=>'pages', 'action'=>'map'), array('class'=>'button fullwidth')) . "\n\t\t";
		echo $html->link('Read the profiles', array('controller'=>'profiles', 'action'=>'index'), array('class'=>'button fullwidth')) . "\n";
		echo $html->link('Learn about the project', array('controller'=>'pages', 'action'=>'about'), array('class'=>'button fullwidth')) . "\n";
	?>
</p>