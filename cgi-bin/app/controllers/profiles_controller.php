<?php
class ProfilesController extends AppController {
	var $name = 'Profiles';
		
	function index()	{
		$this->set('title_for_layout', 'Profiles | Mapping Militant Organizations');
		$this->set('profiles', $this->Profile->find('all', array(
			'conditions'		=>	"id IN (SELECT org_id FROM map_orgs WHERE map_id = 3)",
			'order'				=>	'name'
		)));
	}
	
	function test($id = null)	{
		$this->Profile->id = $id;
		$this->set('profile', $this->Profile->find($id));
	}
	
	function view($id = null)	{
		$this->Profile->id = $id;
		$this->set('profile', $this->Profile->read());
	}
	
	function print_view($id = null)	{
		$this->layout = 'printable';
		$this->Profile->id = $id;
		$this->set('profile', $this->Profile->read());
	}
	
	function search($q = '')	{
		$q = $this->params['url']['q'];
		$this->set('q', $q);
		$params = array('conditions' => array(
			"MATCH(name, html) AGAINST ('$q')"
		));
		$this->set('title_for_layout', 'Search Profiles | Mapping Militant Organizations');
		$this->set('profiles', $this->Profile->find('all', $params));
	}
}
?>