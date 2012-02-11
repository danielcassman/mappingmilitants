<?php
class Map extends AppModel {
	var $name = 'Map';
	public $hasMany = array('Zoom');
}
?>