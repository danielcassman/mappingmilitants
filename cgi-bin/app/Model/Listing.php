<?php
class Listing extends AppModel {
	public $name = 'Listing';
	public $belongsTo = array('Group','Orglist');
}
?>