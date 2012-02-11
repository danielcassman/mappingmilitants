<!-- File: index.ctp -->
<?php $this->Html->script(array("http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"), array("inline" => false)); ?>
<div class="map_box">
	<h2>Maps</h2>
	<p>The project is developing a series of interactive diagrams that “map” relationships among groups and show how those relationships change over time. The user can change map settings to display different features (e.g., leadership changes), adjust the time scale, and trace individual groups. At present, only the map of Iraq is available for public view, but maps of Italy, Colombia, Somalia, Algeria, Yemen, Israel-Palestine, Turkey, and Pakistan are in progress. The Iraq map shows both groups internal to Iraq and regional or global groups with activities in Iraq (the map can be adjusted to show “domestic” and “external” interactions).</p>
	<p>This page lists all of the maps that are available to the public.</p>
</div>
<div id="map_link_pakistan_un" class="map_box linked">
	<h2>Pakistan</h2>
	<?php echo $this->Html->image("pakistan.jpg"); ?>
	<p>This map includes all organizations operating in Pakistan that have been designated terrorist organizations by the United Nations.</p>
</div>
<div id="map_link_iraq" class="map_box linked">
	<h2>Iraq</h2>
	<?php echo $this->Html->image("iraq.jpg"); ?>
	<p>This map includes all militant organizations operating in Iraq and militant organizations outside Iraq with close ties to those in the country.</p>
</div>
<script type="text/javascript">
	$(function() {
		$("#map_link_pakistan_un").click(function() {
			window.location.href = "maps/pakistan_un";
		});
		$("#map_link_iraq").click(function() {
			window.location.href = "maps/iraq";
		});
	});
</script>