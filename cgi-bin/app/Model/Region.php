<?php
class Region extends AppModel {
	public $name = 'Region';
	public $hasMany = 'Subregion';
	public $hasAndBelongsToMany = array('Group');
}
?>