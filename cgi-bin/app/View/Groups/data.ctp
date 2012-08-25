<!-- File: /app/views/profiles/index.ctp -->
<?php
	$this->Html->script(array('http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js','select2.min'), array('inline'=>false));
	echo $this->Html->css(array('buttons','select2'),'stylesheet',array('inline'=>false));
?>
<style type="text/css">
	div.scroll-wrapper h2	{
		padding-top:		15px;
	}
	ul.checkbox-list	{
		margin:				0;
		padding:			0;
		margin-bottom:		1em;
	}
	ul.checkbox-list li	{
		list-style-type:	none;
		display:			block;
		float:				left;
		width:				45%;
		padding:			5px 0;
	}
	
	ul.checkbox-list li input[type=checkbox]	{
		float:				left;
		margin-right:		5px;
		margin-top:			5px;
	}
	
	ul.checkbox-list li label	{
		width:				auto;
	}
</style>
<h1 class="span-24">Export Data</h1>
<div class="span-15 colborder">
	<form id="dataForm" name="dataForm" action="csv" method="post" enctype="multipart/form-data">
	<div class="scroll-wrapper">
		<h2 id="groups-header">Select Groups Individually</h2>
		<p>First, you need to select which groups to include in your data export. You can pick individual groups using the box below. You can select as many groups as you like by selecting them directly from the list, or you can begin typing a group's name, and the list will filter the groups by name to the letters you type.</p>
		<p><select name="groups[]" id="groups" multiple="multiple" style="width:100%;">
			<?php foreach($groups as $group): ?>
			<option value="<?php echo $group['Group']['id']; ?>"><?php echo $group['Group']['name']; ?></option>
			<?php endforeach; ?>
		</select></p>
		<h2>Select Groups by Map</h2>
		<p>Alternatively, you can select all the groups from a map by choosing as many maps as you like from below:</p>
		<p><select name="maps[]" id="maps" multiple="multiple" style="width:100%;">
			<?php foreach($maps as $map): ?>
			<option value="<?php echo $map['Map']['id']; ?>"><?php echo $map['Map']['name']; ?></option>
			<?php endforeach; ?>
		</select></p>
		<p><a href="#variables-header" class="scroll-button button">Next: Select Variables</a></p>
	</div>
	<div class="scroll-wrapper">
		<h2 id="variables-header">Select Variables</h2>
		<p>Next, you need to choose which variables to include in your dataset.</p>
		<p class="clearfix"><input type="checkbox" style="float:left;margin-right:5px; margin-top:5px;" name="select_all" id="select_all" /><label for="select_all">Select all</label></p>
		<ul id="variables-list" class="checkbox-list clearfix">
			<li><input type="checkbox" name="start_year" id="start_year" /> <label for="start_year">Start year</label></li>
			<li><input type="checkbox" name="end_year" id="end_year" /> <label for="end_year">End year</label></li>
			<li><input type="checkbox" name="active" id="active" /> <label for="active">Still active</label></li>
			<li><input type="checkbox" name="years_active" id="years_active" /> <label for="years_active">Total years active</label></li>
			<li><input type="checkbox" name="num_leaders" id="num_leaders" /> <label for="num_leaders">Number of leaders</label></li>
			<li><input type="checkbox" name="avg_leader_duration" id="avg_leader_duration" /> <label for="avg_leader_duration">Average leader duration</label></li>
			<li><input type="checkbox" name="num_countries" id="num_countries" /> <label for="num_countries">Number of countries</label></li>
			<li><input type="checkbox" name="num_attacks" id="num_attacks" /> <label for="num_attacks">Number of major attacks</label></li>
			<li><input type="checkbox" name="attacks_per_year" id="attacks_per_year" /> <label for="attacks_per_year">Major attacks per year</label></li>
			<li><input type="checkbox" name="num_relationships" id="num_relationships" /> <label for="num_relationships">Number of relationships</label></li>
			<li><input type="checkbox" name="num_splinters" id="num_splinters" /> <label for="num_splinters">Number of splinter groups</label></li>
			<li><input type="checkbox" name="num_allies_affiliates" id="num_allies_affiliates" /> <label for="num_allies_affiliates">Number of allies/affiliates</label></li>
			<li><input type="checkbox" name="num_rivals" id="num_rivals" /> <label for="num_rivals">Number of rivals</label></li>
		</ul>
		<p><a href="#groups-header" class="scroll-button button">Back: Select Groups</a><a href="#output-header" class="scroll-button button">Next: Choose Format</a></p>
	</div>
	<div class="scroll-wrapper">
		<h2 id="output-header">Choose Output Format</h2>
		<p><select name="format" id="format">
			<option value="csv">.csv file (Excel, Stata, R, etc.)</option>
			<option value="html">HTML table</option>
		</select></p>
		<p><a href="#variables-header" class="button scroll-button">Back: Select Variables</a><a href="#" id="get_data_button" class="blue button">Get my data</a></p>
	</div>
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
	$(function()	{
		$('html,body').scrollTop(0);
		$('div.scroll-wrapper').css('min-height',$(window).innerHeight());
		$('select','#dataForm').select2();
		$('.scroll-button').click(function()	{
			var target = $(this).attr('href');
			console.log(target);
			$('html,body').animate({
				'scrollTop': $(target).offset().top
			}, 'slow');
			return false;
		});
		$("#select_all").click(function()	{
			if($(this).is(":checked"))	{
				$("input","#variables-list").each(function()	{
					$(this).attr('checked','checked');
				});
			}	else	{
				$("input","#variables-list").each(function()	{
					$(this).removeAttr('checked');
				});
			}
		});
		$("input", "#variables-list").click(function()	{
			if(!$(this).is(":checked"))	{
				$("#select_all").removeAttr('checked');
			}
		});
	});
	
	$("#get_data_button").click(function()	{
		var post_data = {
			'groups': ($('#groups').val() == null ? [] : $('#groups').val()),
			'maps': ($('#maps').val() == null ? [] : $('#maps').val()),
			'vars': [],
			'format': $('#format').val()
		}
		var checkboxes = $('input', '#variables-list');
		for(var i = 0; i < checkboxes.length; i++)	{
			if($(checkboxes[i]).is(':checked'))	{
				post_data.vars.push($(checkboxes[i]).attr('name'));;
			}
		}
		$.post('csv', post_data, function(data)	{
			if($('#format').val() == 'csv')	{
				window.location.href = data;
			}	else if($('#format').val() == 'html')	{
				$('html,body').scrollTop(0);
				console.log(data);
				var elements = $('body').children().detach();
				/*$('body').prepend($('<div/>', {
					'css':	{
						'position':	'absolute',
						'top': 0,
						'left': 0,
						'width':'100%',
						'height':'100%',
						'background-color':'#fff',
						'text-align':'center'
					}
				}));*/
				$('body').append($('<table/>', {
					'html': data,
					'css': {
						'margin':'0 auto 20px',
						'width':'80%',
						'background-color':'#fff'
					}
				}));
				$('body').prepend($('<p/>', {
					'class': 'clearfix',
					'html': $('<a/>', {
						'href': '#',
						'class': 'button',
						'text': 'Close',
						'click': function()	{
							$('body').empty().append(elements);
						}
					}),
					'css':	{
						'width': '80%',
						'margin': '20px auto 10px'
					}
				}));
			}
		});
		return false;
	});
</script>