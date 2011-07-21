<!doctype html>
<html>
<head>
	<title><?php echo $title_for_layout ?></title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="author" content="Daniel Cassman">
	<?php 
		echo $html->css('map', 'stylesheet', array('media' => 'screen, projection')) . "\n\t";
		echo $html->css('jquery-ui', 'stylesheet', array('media' => 'screen, projection')) . "\n\t";
		echo $this->Html->script(array(
			'http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js',
			'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js',
			'map'
		));
		echo $scripts_for_layout
	?>
</head>
<body>
	<div id="header">
		<div id="buttons">
			<input type="button" class="button" value="Settings" id="settings_button" /> <input type="button" class="button" value="Legend" />
			<div class="slider" id="geo_zoom_slider"></div><span id="geo_zoom_label" class="label">Geo Zoom:</span>
			<div class="slider" id="time_zoom_slider"></div><span id="time_zoom_label" class="label">Timeline Resolution:</span>
		</div>
	</div>
	<div id="wrap" class="container">
		
	</div>
	<div id="settings_dialog" class="dialog">
		<h2 id="timeline_header">Timeline:</h2>
		<div id="timeline_selector"></div>
		<h2>Organizations</h2>
			<label for="select_organizations">Active</label> <input type="radio" name="select_organizations" value="active" />
			<label for="select_organizations">Inactive</label> <input type="radio" name="select_organizations" value="inactive" />
			<label for="select_organizations">Both</label> <input type="radio" name="select_organizations" value="both" checked="checked" />
		<h2>Organization Events</h2>
			<label for="attacks_checkbox">Major attacks</label> <input data-class="attack" class="toggle_checkbox start_unchecked" type="checkbox" id="attacks_checkbox" />
			<label for="leaders_checkbox">Leaders</label> <input data-class="leader" class="toggle_checkbox start_unchecked" type="checkbox" id="leaders_checkbox" />
		<h2>Relationships</h2>
			<label for="allies_checkbox">Ally</label> <input data-class="all" class="link_toggle_checkbox" type="checkbox" id="attacks_checkbox" />
			<label for="leaders_checkbox">Affiliates</label> <input data-class="aff" class="link_toggle_checkbox" type="checkbox" id="affiliates_checkbox" />
			<label for="leaders_checkbox">Rivals</label> <input data-class="riv" class="link_toggle_checkbox" type="checkbox" id="rivals_checkbox" />
			<label for="leaders_checkbox">Splits</label> <input data-class="spl" class="link_toggle_checkbox" type="checkbox" id="splits_checkbox" />
			<label for="leaders_checkbox">Mergers</label> <input data-class="mer" class="link_toggle_checkbox" type="checkbox" id="mergers_checkbox" />
	</div>
</body>
</html>