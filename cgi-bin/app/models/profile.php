<?php
class Profile extends AppModel {
	var $name = 'Profile';
	var $hasMany = array('Leader');
	var $hasAndBelongsToMany = array('Attack');
}
?>