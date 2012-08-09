<?php
	$this->set('title_for_layout', 'Mapping Militant Organizations');
	echo $this->Html->css('buttons','stylesheet',array('inline'=>false));
	$this->Html->script(array("http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"), array("inline" => false));
?>
<div class="span-15 colborder">
	<h1>Welcome</h1>
	<p>This research project traces the evolution of militant organizations and the interactions that develop between them over time. Findings are presented in interactive &ldquo;maps,&rdquo; which provide both (1) visual representations of how relationships among militant organizations change over time and (2) links to in-depth profiles of individual groups.</p>
	<div id="iraq" class="map_box linked" style="background-image:url(<?php echo $this->webroot; ?>img/iraq-sm.jpg);">
		<h2>Iraq</h2>
		<p>This map depicts militant organizations operating in Iraq and militant organizations outside Iraq with close ties to those in the country. The map focuses on the time period following the U.S.-led invasion of Iraq in 2003, though it traces groups from 2001 onwards.</p>
	</div>
	<div id="italy" class="map_box linked" style="background-image:url(<?php echo $this->webroot; ?>img/italy-sm.jpg);">
		<h2>Italy</h2>
		<p>This map diagrams militant organizations that operated in Italy during the 1950s through the 1980s.</p>
	</div>
	<div id="somalia" class="map_box linked" style="background-image:url(<?php echo $this->webroot; ?>img/somalia-sm.jpg);">
		<h2>Somalia</h2>
		<p>This map shows militant organizations that operated in Somalia beginning in the 1980s through the present day.</p>
	</div>
	<div id="pakistan_un" class="map_box linked" style="background-image:url(<?php echo $this->webroot; ?>img/pakistan_un-sm.jpg); margin-top:0;">
		<h2>Pakistan</h2>
		<p>The map of Pakistani militants includes those organizations designated by the United Nations Security Council Committee charged with maintaining the &ldquo;Al-Qaida Sanctions List of Entities and other groups and undertakings associated with Al-Qaida,&rdquo; pursuant to resolutions 1267 (1999) and 1989 (2011). The list was last updated June 6, 2012.</p>
	</div>
</div>
<div class="span-8 last">
	<h2>Search</h2>
	<form name="searchform" action="/group/mappingmilitants/cgi-bin/groups/search" method="get">
		<p><input type="text" name="q" id="q" /></p>
	</form>
	<p>
	<?php
		echo $this->Html->link('See the maps', array('controller'=>'maps', 'action'=>'index'), array('class'=>'button fullwidth')) . "\n\t\t";
		echo $this->Html->link('Read the profiles', array('controller'=>'groups', 'action'=>'index'), array('class'=>'button fullwidth')) . "\n";
		echo $this->Html->link('Learn about the project', array('controller'=>'pages', 'action'=>'about'), array('class'=>'button fullwidth')) . "\n";
	?>
	</p>
	<h2>The Maps</h2>
	<p>The project is developing a series of interactive diagrams that &ldquo;map&rdquo; relationships among groups and show how those relationships change over time.  The user can change map settings to display different features (e.g., leadership changes), adjust the time scale, and trace individual groups.  At present, maps of conflicts in Iraq, Italy, and Pakistan are available for public view, but work is in progress on Germany, Colombia, Somalia, Algeria, Israel-Palestine, Turkey, and the Philippines. The Iraq map shows both groups internal to Iraq and regional or global groups with activities in Iraq (the map can be adjusted to show &ldquo;domestic&rdquo; and &ldquo;external&rdquo; interactions).</p>
	<h2>The Profiles</h2>
	<p>The maps contain links to group profiles. The profiles include information on group size, formation, ideology, activity, leaders, ties to other groups, resources, community relationships, and more.  Each profile follows a standard format and provides citations to sources for all references.  In some cases information is minimal and not all fields can be filled.</p>
	<h2>Feedback</h2>
	<p>This project is a work in progress, and we welcome substantive feedback on our research and profiles as well as suggestions for the improvement of our web application. To submit a suggestion or correction, please visit the <?php echo $this->Html->link('Feedback Page', array('controller'=>'comments', 'action'=>'index')); ?>. You may also email the project team directly at <b>mappingmilitants [at] lists [dot] stanford [dot] edu</b>.</p>
</div>
<script type="text/javascript">
	$(function() {
		$("div.map_box.linked").click(function() {
			window.location.href = "<?php echo $this->webroot; ?>maps/view/" + $(this).attr('id');
		});
	});
</script>