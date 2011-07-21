<?php
class Attack extends AppModel {
	var $name = 'Attack';
	var $hasAndBelongsToMany = array('Profile');
}
?>