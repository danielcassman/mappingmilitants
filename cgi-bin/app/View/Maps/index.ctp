<!-- File: index.ctp -->
<?php
	$this->Html->script(array("http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"), array("inline" => false));
	echo $this->Html->css('buttons','stylesheet',array('inline'=>false));
?>
<div class="span-15 colborder">
	<h1>Maps</h1>
	<p>This page contains descriptions of and links to each of the interactive diagrams we have published.</p>
	<?php foreach($maps as $map): ?>
		<div id="<?php echo $map['Map']['url']; ?>" class="map_box linked" style="background-image:url(<?php echo $this->webroot; ?>img/<?php echo $map['Map']['url']; ?>-sm.jpg)">
			<h2><?php echo $map['Map']['name']; ?></h2>
			<p><?php echo $map['Map']['description']; ?></p>
		</div>
	<?php endforeach; ?>
</div>
<div class="span-8 last">
	<h2>Search</h2>
	<form name="searchform" action="/group/mappingmilitants/cgi-bin/groups/search" method="get">
		<p><input type="text" name="q" id="q" /></p>
	</form>
	<p>
	<?php
		echo $this->Html->link('Home', array('controller'=>'pages', 'action'=>'home'), array('class'=>'button fullwidth')) . "\n\t\t";
		echo $this->Html->link('Read the profiles', array('controller'=>'groups', 'action'=>'index'), array('class'=>'button fullwidth')) . "\n";
		echo $this->Html->link('Learn about the project', array('controller'=>'pages', 'action'=>'about'), array('class'=>'button fullwidth')) . "\n";
	?>
	</p>
</div>
<script type="text/javascript">
	$(function() {
		$("div.map_box.linked").click(function() {
			window.location.href = "<?php echo $this->webroot; ?>maps/view/" + $(this).attr('id');
		});
	});
</script>