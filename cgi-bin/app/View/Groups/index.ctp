<!-- File: /app/views/profiles/index.ctp -->
<?php
	$this->Html->script(array('http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js','jquery.tablesorter.min'), array('inline'=>false));
	$this->Html->css('tablesort.css', 'stylesheet', array('inline'=>false));
	echo $this->Html->css('buttons','stylesheet',array('inline'=>false));
	$maps = array(
		"Iraq" => $iraq,
		"Italy" => $italy, 
		"Pakistan (UN Designated)" => $afpak,
		"Somalia" => $somalia
	);
?>
<h1 class="span-24">Profiles</h1>
<div class="span-15 colborder">
	<?php foreach($maps as $name => $var): ?>
		<h2><?php echo $name; ?></h2>
		<table class="tablesorter" border="0">
			<thead>
				<tr>
					<th>Name</th>
					<th>Founded</th>
					<th>Disbanded</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($var as $profile): ?>
				<tr>
					<td><?php echo $this->Html->link(stripslashes($profile['Group']['name']), array('controller' => 'groups', 'action' => 'view', $profile['Group']['id'])); ?></td>
					<td><?php echo substr($profile['Group']['startdate'], 0, 4); ?></td>
					<td><?php echo ($profile['Group']['enddate'] == '0000-00-00' ? 'Active' : substr($profile['Group']['enddate'], 0, 4)); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endforeach; ?>
</div>
<div class="span-8 last">
	<h2>Search</h2>
	<form name="searchform" action="/group/mappingmilitants/cgi-bin/groups/search" method="get">
		<p><input type="text" name="q" id="q" /></p>
	</form>
	<p>
	<?php
		echo $this->Html->link('See the map', array('controller'=>'maps', 'action'=>'iraq'), array('class'=>'button fullwidth')) . "\n\t\t";
		echo $this->Html->link('Read the profiles', array('controller'=>'profiles', 'action'=>'index'), array('class'=>'button fullwidth')) . "\n";
		echo $this->Html->link('Learn about the project', array('controller'=>'pages', 'action'=>'about'), array('class'=>'button fullwidth')) . "\n";
	?>
	</p>
</div>
<script language="Javascript" type="text/javascript">
	$(document).ready(function()	{
		$('table.tablesorter').tablesorter( {sortList: [[0,0]]} );
	});
</script>