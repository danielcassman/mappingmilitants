<?php
class Ideology extends AppModel {
	public $name = 'Ideology';
	public $hasAndBelongsToMany = array('Group');
}
?>