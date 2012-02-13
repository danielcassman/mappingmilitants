<!doctype html>
<html>
<head>
	<title><?php echo $title_for_layout ?></title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="author" content="Daniel Cassman">
	<?php 
		echo $this->Html->css('map', 'stylesheet', array('media' => 'screen, projection')) . "\n\t";
		echo $this->Html->css('jquery-ui', 'stylesheet', array('media' => 'screen, projection')) . "\n\t";
		echo $this->Html->script(array(
			'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',
			'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js',
			'map.min.js',
			'map-view-functions.js'
		));
		echo $scripts_for_layout

	?>
	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-28503356-1']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	</script>
</head>
<body>
	<div id="header">
		<div id="buttons">
			<a href="http://mappingmilitants.stanford.edu"><input type="button" class="button" value="Home" id="home_button" /></a> <input type="button" class="button" value="Settings" id="settings_button" /> <input id="legend_button" type="button" class="button" value="Legend" /> <input type="button" class="button" value="Stop Tracing" id="stop_trace_button" style="display:none" />
			<div class="slider" id="geo_zoom_slider"></div><span id="geo_zoom_label" class="label">Geo Zoom:</span>
			<div class="slider" id="time_zoom_slider"></div><span id="time_zoom_label" class="label">Timeline Resolution:</span>
		</div>
	</div>
	<!--<div id="wrap" class="container">
		
	</div>-->
	<div id="settings_dialog" class="dialog" style="display:none">
		<div class="box">
			<h2 id="timeline_header">Timeline:</h2>
			<div id="timeline_selector"></div>
		</div>
		<div class="box">
			<h2>Organizations</h2>
			<input type="radio" name="select_organizations" value="active" /><label for="select_organizations">Active</label>
			<input type="radio" name="select_organizations" value="inactive" /><label for="select_organizations">Inactive</label>
			<input type="radio" id="both_radio" name="select_organizations" value="both" checked="checked" /><label for="select_organizations">Both</label>
		</div>
		<div class="box">
			<h2>Organization Events</h2>
			<input data-class="attack" class="toggle_checkbox start_unchecked" type="checkbox" id="attacks_checkbox" name="attacks_checkbox" /><label for="attacks_checkbox">Major attacks</label>
			<input data-class="leader" class="toggle_checkbox start_unchecked" type="checkbox" id="leaders_checkbox" name="leaders_checkbox" /><label for="leaders_checkbox">Leaders</label>
		</div>
		<div class="box">
			<h2>Relationships</h2>
			<p><input data-class="all" class="link_toggle_checkbox" type="checkbox" id="allies_checkbox" name="allies_checkbox" /><label for="allies_checkbox">Ally</label>
			<input data-class="aff" class="link_toggle_checkbox" type="checkbox" id="affiliates_checkbox" name="affiliates_checkbox" /><label for="affiliates_checkbox">Affiliates</label>
			<input data-class="riv" class="link_toggle_checkbox" type="checkbox" id="rivals_checkbox" name="rivals_checkbox" /><label for="rivals_checkbox">Rivals</label></p>
			<p><input data-class="spl" class="link_toggle_checkbox" type="checkbox" id="splits_checkbox" name="splits_checkbox" /><label for="splits_checkbox">Splits</label>
			<input data-class="mer" class="link_toggle_checkbox" type="checkbox" id="mergers_checkbox" name="mergers_checkbox" /><label for="mergers_checkbox">Mergers</label></p>
			<input data-class="umbrella" class="toggle_checkbox" type="checkbox" id="umbrellas_checkbox" name="umbrellas_checkbox" /><label for="umbrellas_checkbox">Umbrellas</label></p>
		</div>
	</div>
	<div id="legend_dialog" class="dialog" style="display:none;">
		<div class="clearfix">
			<h2>Relationships</h2>
			<p><span class="legend-mark-border all"></span>Allies</p>
			<p><span class="legend-mark-border aff"></span>Affiliates</p>
			<p><span class="legend-mark-border mer"></span>Merger</p>
			<p><span class="legend-mark-border riv"></span>Rivals</p>
			<p><span class="legend-mark-border spl"></span>Split</p>
			<p><span class="legend-mark-umbrella"></span>Umbrella</p>
			<p>For more information on how we classify relationships, see the <?php echo $this->Html->link("definitions page", array("controller" => "pages", "action" => "definitions"), array("target" => "_blank")); ?>.</p>
		</div>
		<div class="clearfix">
			<h2>Events</h2>
			<p class="clearfix"><span class="legend-mark-image leader"></span>Leader</p>
			<p class="clearfix"><span class="legend-mark-image attack"></span>Attack</p>
		</div>
	</div>
	<script type="text/javascript">
		$(function() {
		  progressBar();
		  return $.getJSON("/group/mappingmilitants/cgi-bin/maps/jsondata/<?php echo $map_id; ?>", function(data) {
		    $("#progress_bar").progressbar("value", 70);
		    settings.startdate = parseInt(data.Map.startyear, 10);
		    settings.enddate = parseInt(data.Map.endyear, 10);
		    settings.zooms = data.Zoom;
		    setUpControls(settings.zooms);
		    $("#progress_bar").progressbar("value", 80);
		    setUpTimeline(settings.startdate, settings.enddate);
		    $("#progress_bar").progressbar("value", 90);
		    setUpMapArea(data.groups, data.links, data.umbrellas, settings.startdate, settings.enddate);
		    $(".toggle_checkbox.start_unchecked").prop("checked", false).each(function() {
		      return $("." + $(this).attr("data-class"), "#map_container").addClass("settings_inactive").fadeOut(settings.ANIMATION_SPEED);
		    });
		    $("#progress_bar").progressbar("value", 100);
		    $("#progress_dialog").delay(200).dialog("destroy");
		    return zoomGeographic(0);
		  });
		});
	</script>
</body>
</html>
