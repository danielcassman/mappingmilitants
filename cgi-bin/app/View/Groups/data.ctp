<!-- File: /app/views/profiles/index.ctp -->
<?php
	$this->Html->script(array('http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js','select2.min'), array('inline'=>false));
	echo $this->Html->css(array('buttons','select2'),'stylesheet',array('inline'=>false));
?>
<h1 class="span-24">Export Data</h1>
<div class="span-15 colborder">
	<form id="dataForm" name="dataForm" action="csv" method="post" enctype="multipart/form-data">
	<h2>Select Groups</h2>
	<select name="groups[]" id="groups" multiple="multiple" style="width:100%;">
		<?php foreach($groups as $group): ?>
		<option value="<?php echo $group['Group']['id']; ?>"><?php echo $group['Group']['name']; ?></option>
		<?php endforeach; ?>
	</select>
	<h2>Select Columns</h2>
	<select name="columns[]" id="columns" multiple="multiple" style="width:100%">
		<option value="start_year">Start Year</option>
		<option value="end_year">End Year</option>
		<option value="active_bool">Still Active</option>
		<option value="years_active">Total Years Active</option>
		<option value="num_leaders">Number of Leaders</option>
		<option value="leader_duration">Average Leader Duration</option>
		<option value="num_countries">Number of Countries</option>
		<option value="num_attacks">Number of Major Attacks</option>
		<option value="attacks_per_year">Major Attaks Per Year</option>
		<option value="num_relationships">Number of relationships with other groups</option>
		<option value="num_splinters">Number of splinter groups</option>
		<option value="num_allies_affiliates">Number of allies and affiliates</option>
		<option value="num_rivals">Number of rivals</option>
	</select>
	<p><input type="submit" value="Go" id="get_data_button"></p>
	</form>
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
<script type="text/javascript">
	$('select','#dataForm').select2();
	$("#dataForm").submit(function()	{
		$.post('csv', {groups:$('#groups').val(), columns:$('#columns').val()}, function(data)	{
			location.href = data;
		})
		return false;
	});
</script>