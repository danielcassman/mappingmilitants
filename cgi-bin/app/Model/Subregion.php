<?php
class Subregion extends AppModel {
	public $name = 'Subregion';
	public $belongsTo = 'Region';
	public $hasAndBelongsToMany = array('Group');
}
?>