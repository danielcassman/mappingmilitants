<?php
class Group extends AppModel {
	public $name = 'Group';
	public $hasMany = array(
		'Attack',
		'Leader',
		'Listing',
		'NameChange',
		'Size'
	);
	public $hasAndBelongsToMany = array('Region','Subregion','Ideology');
}
?>