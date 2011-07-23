<?php
	$this->set('title_for_layout', 'Definitions | Mapping Militant Organizations');
	echo $html->css('buttons','stylesheet',array('inline'=>false));
?>
<h1 class="span-24">Definitions</h1>
<div class="span-15 colborder">
	<p>Each of the relationships represented on our <?php echo $html->link("map", array("controller" => "maps", "action" => "iraq")); ?> has been thoroughly researched and classified according to the definitions on this page.</p>
	<h2>Affiliates</h2>
	<p>When a group pledges fealty to and relies on support (materiel, financial, ideological, etc) and/or guidance from another, usually more senior group, it is defined as an affiliate of that more senior group.</p>
	<p><i>Example</i>: Usama Bin Ladin&rsquo;s al-Qaida organization has many affiliate groups that have sworn allegiance to al-Qaida.  Most of these affiliate groups exist in areas distinct from al-Qaida&rsquo;s base of operations in the Afghanistan/Pakistan border areas. Algeria-based Al-Qaida in the Islamic Maghreb (AQIM), formerly the Salafist Group for Preaching and Combat (GSPC), pledged fealty to al-Qaida and is thus considered an al-Qaida affiliate; the same is true for al-Qaida in Iraq (AQI).</p>
	
	<h2>Allies</h2>
	<p>When groups are known to share similar ideology and/or goals and are known to communicate and sometimes coordinate operations, they are identified as allies.  Ally relationship may contain elements of competition amongst groups/group members, but in general, the relationship is defined as one of cooperation.</p>
	<p><i>Example</i>: In Southeast Asia, the relationship between two Sunni jihadist groups, the Abu Sayyaf Group (ASG) and Jemaah Islamiyah (JI), is characterized as an ally relationship.  Philippines-based ASG elements have provided shelter to fugitive JI members and ASG elements have reportedly supported JI operations in the past.  JI is also allied with al-Qaida, from which JI received both logistical and operational support; JI has retained its independence from al-Qaida, thus it is not characterized as an al-Qaida affiliate.</p>
	
	<h2>Merger</h2>
	<p>Group mergers occur when two or more groups agree to consolidate resources and operate jointly under the same banner towards the same cause, thus forming a new group. Group mergers require one or all groups shed their original identity and commit to the new group&rsquo;s articulated vision.</p>
	<p><i>Example</i>: Ayman al-Zawahiri&rsquo;s Egyptian Islamic Jihad (EIJ) merges with Usama Bin Ladin&rsquo;s al-Qaida in early 2001; the EIJ ceases to exist contemporaneously and al-Zawahiri becomes al-Qaida&rsquo;s second in command.</p>
	
	<h2>Rivals</h2>
	<p>When groups engage in sustained competition, often vying for resources, prestige, and/or support, they are understood as rivals.  Rival groups can engage in violence against each other, though sometimes the rivalry is less explicit.  Shared ideology does not preclude groups from being rivals.  Some rivalries may contain elements of cooperation, but in general, the relationship is defined as on of competitiveness.</p>
	<p><i>Example</i>: In the Palestinian Territories, the Islamic Resistance Movement (HAMAS) and Palestinian Islamic Jihad (PIJ) are considered rivals despite their shared credentials as Sunni jihadist groups committed to violence against Israel.   While there have been instances of cooperation between HAMAS and PIJ operatives, in general, the two groups work independently and compete for support among the Palestinian population and external supporters.  Often, HAMAS and PIJ will articulate their differences in opinion/strategy/etc. publicly.</p>
	
	<h2>Splits</h2>
	<p>When part of one group establishes itself as an independent entity (almost always with a new name), it <b>splits</b> from the parent group. A group may also splinter into several smaller groups. It is important to note that splits are not always the result of dissension; sometimes a split is a tactical decision. For example, splitting into militant and political arms might grant the political arm more legitimacy while still allowing it to carry out militant activities.</p>
	
	<h2>Umbrella Organizations</h2>
	<p>When separate but like-minded groups formally organize under a single banner to show unity of purpose and strategic and/or tactical cooperation, they do so under umbrella organizations.  Groups that are part of an umbrella organization usually retain their original identities and names and can still function independently.</p>
	<p><i>Example</i>: In 2006, the Islamic Army of Iraq, Mujahideen Army, and elements of Ansar al-Sunna form the Jihad and Reform Front&mdash;a militant Sunni Iraqi umbrella organization that rejected both the U.S.-led Coalition and al-Qaida in Iraq.</p>
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