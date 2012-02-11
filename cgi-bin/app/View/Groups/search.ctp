<!-- File: /app/views/profiles/index.ctp -->
<?php
	$this->Html->css('buttons.css', 'stylesheet', array('inline'=>false));
	$this->Html->script(array('http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js'), array('inline'=>false));
?>
<h1 class="span-24">Search for &ldquo;<?php echo $q; ?>&rdquo;</h1>
<div class="span-15 colborder">
	<form name="searchform" action="search" method="get">
		<p>Search Profiles: <input type="text" name="q" id="q" value="<?php echo $q ?>" style="border:1px solid black; border-radius:3px; -moz-border-radius:3px; -webkit-border-radius:3px; padding:3px;" size="40" /></p>
	</form>
		<?php echo("<p>Found <strong>$q</strong> in " . count($profiles) . " profiles.</p>"); ?>
		<ol class="spaced">
		<?php foreach($profiles as $profile): ?>
			<li>
				<?php echo $this->Html->link(stripslashes($profile['Group']['name']), array('controller' => 'groups', 'action' => 'view', $profile['Group']['id'], '?' => array('highlight' => $q))); ?>
			</li>
		<?php endforeach; ?>
		</ol>
</div>
<p class="span-8 last">
	<?php
		echo $this->Html->link('See the map', array('controller'=>'maps', 'action'=>'iraq'), array('class'=>'button fullwidth')) . "\n\t\t";
		echo $this->Html->link('Read the profiles', array('controller'=>'profiles', 'action'=>'index'), array('class'=>'button fullwidth')) . "\n";
		echo $this->Html->link('Learn about the project', array('controller'=>'pages', 'action'=>'about'), array('class'=>'button fullwidth')) . "\n";
	?>
</p>
<script language="Javascript" type="text/javascript">
	$(document).ready(function()	{
		$('#q').focus(function()	{
			$(this).val('');
		});
	});
</script>