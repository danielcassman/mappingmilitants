<?php
	$this->set('title_for_layout', 'Mapping Militant Organizations');
	echo $html->css('buttons','stylesheet',array('inline'=>false));
?>
<h1 class="span-24">Home</h1>
<div class="span-15 colborder">
	<p>This research project traces the evolution of militant organizations and the interactions that develop between them over time. Findings are presented in interactive &ldquo;maps,&rdquo; which provide both (1) visual representations of how relationships among militant organizations change over time and (2) links to in-depth profiles of individual groups. The project provides uniquely accessible and clear genealogical information about violent extremist organizations that, combined with the detailed group profiles, is an invaluable resource to students, scholars, journalists, policy analysts, and other interested in violent oppositional organizations. This website contains a map of groups in Iraq but other maps are in progress and will be posted as soon as they are completed.</p>
	<h2>The Maps</h2>
	<p>The project is developing a series of interactive diagrams that &ldquo;map&rdquo; relationships among groups and show how those relationships change over time.  The user can change map settings to display different features (e.g., leadership changes), adjust the time scale, and trace individual groups.  At present, only the map of Iraq is available for public view, but maps of Italy, Colombia, Somalia, Algeria, Yemen, Israel-Palestine, Turkey, and Pakistan are in progress.  The Iraq map shows both groups internal to Iraq and regional or global groups with activities in Iraq (the map can be adjusted to show &ldquo;domestic&rdquo; and &ldquo;external&rdquo; interactions).</p>
	<h2>The Profiles</h2>
	<p>The maps contain links to group profiles. The profiles include information on group size, formation, ideology, activity, leaders, ties to other groups, resources, community relationships, and more.  Each profile follows a standard format and provides citations to sources for all references.  In some cases information is minimal and not all fields can be filled.</p>
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