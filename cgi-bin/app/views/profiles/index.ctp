<!-- File: /app/views/profiles/index.ctp -->
<?php
	$this->Html->script(array('http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js','jquery.tablesorter.min'), array('inline'=>false));
	$this->Html->css('tablesort.css', 'stylesheet', array('inline'=>false));
	echo $this->Html->css('buttons','stylesheet',array('inline'=>false));
?>
<h1 class="span-24">Profiles</h1>
<div class="span-15 colborder">
	<table class="tablesorter" border="0" id="profile_table">
	<thead>
		<tr>
			<th>Name</th>
			<th>Founded</th>
			<th>Disbanded</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($profiles as $profile): ?>
		<tr>
			<td><?php echo $html->link(stripslashes($profile['Profile']['name']), array('controller' => 'profiles', 'action' => 'view', $profile['Profile']['id'])); ?></td>
			<td><?php echo substr($profile['Profile']['startdate'], 0, 4); ?></td>
			<td><?php echo ($profile['Profile']['enddate'] == '0000-00-00' ? 'Active' : substr($profile['Profile']['enddate'], 0, 4)); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
</div>
<div class="span-8 last">
	<h2>Search</h2>
	<form name="searchform" action="/group/mappingmilitants/cgi-bin/profiles/search" method="get">
		<p><input type="text" name="q" id="q" style="border:1px solid black; border-radius:3px; -moz-border-radius:3px; -webkit-border-radius:3px; padding:5px;" size="40" /></p>
	</form>
	<p>
	<?php
		echo $html->link('See the map', array('controller'=>'maps', 'action'=>'iraq'), array('class'=>'button fullwidth')) . "\n\t\t";
		echo $html->link('Read the profiles', array('controller'=>'profiles', 'action'=>'index'), array('class'=>'button fullwidth')) . "\n";
		echo $html->link('Learn about the project', array('controller'=>'pages', 'action'=>'about'), array('class'=>'button fullwidth')) . "\n";
	?>
	</p>
</div>
<script language="Javascript" type="text/javascript">
	$(document).ready(function()	{
		$('#profile_table').tablesorter( {sortList: [[0,0]]} );
	});
</script>