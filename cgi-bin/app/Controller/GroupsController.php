<?php
class GroupsController extends AppController {
	public $name = 'Groups';
		
	function index()	{
		$this->set('title_for_layout', 'Groups | Mapping Militant Organizations');
		$this->set('profiles', $this->Group->find('all', array(
			'conditions'		=>	array("id IN (SELECT group_id FROM map_groups WHERE map_id = 3)", "dummy=0"),
			'order'				=>	'name'
		)));
	}
	
	function test($id = null)	{
		$this->Group->id = $id;
		$this->set('profile', $this->Group->find($id));
	}
	
	function view($id = null)	{
		$this->Group->id = $id;
		$this->data = $this->Group->read();
		$this->set('title_for_layout', $this->data['Group']['name'] . ' | Mapping Militant Organizations');
	}
	
	function print_view($id = null)	{
		$this->layout = 'printable';
		$this->Group->id = $id;
		$this->set('profile', $this->Group->read());
	}
	
	function search($q = '')	{
		$q = $this->params['url']['q'];
		$this->set('q', $q);
		$params = array('conditions' => array(
			"MATCH(name, html) AGAINST ('$q')",
			"id IN (SELECT group_id FROM map_groups WHERE map_id = 3)"
		));
		$this->set('title_for_layout', 'Search Profiles | Mapping Militant Organizations');
		$this->set('profiles', $this->Group->find('all', $params));
	}
}
?>