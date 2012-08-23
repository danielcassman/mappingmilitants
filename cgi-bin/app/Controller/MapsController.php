<?php
class MapsController extends AppController {
	public $name = 'Maps';
	public $uses = array('Map','Group','MapGroup','Link','Umbrella','UmbrellaGroup');

	function index()	{
		$this->set("title_for_layout", "Maps | Mapping Militant Organizations");
		$this->set('maps', $this->Map->find('all', array(
			'conditions'	=>		array('published'	=> true),
			'order'			=>		'name',
			'recursive'		=>		0
		)));
	}
	
	function view($id)	{
		$map = $this->Map->find('first', array(
			'conditions'		=>	array('url' => $id)
		));
		if($map['Map']['published'] != 1)	{
			die("Error: this map is not published.");
		}
		$this->set('title_for_layout', $map['Map']['name'] . ' | Mapping Militant Organizations');
		$this->set('map_id', $map['Map']['id']);
		$this->layout = 'map';
	}
	
	function iraq()	{
		$this->redirect(array(
			'controller'=>'maps',
			'action' => 'view',
			'iraq'
		));
	}

	function pakistan_un()	{
		$this->redirect(array(
			'controller'=>'maps',
			'action' => 'view',
			'pakistan_un'
		));
	}

	function italy()	{
		$this->redirect(array(
			'controller'=>'maps',
			'action' => 'view',
			'italy'
		));
	}
	
	function somalia()	{
		$this->redirect(array(
			'controller'=>'maps',
			'action' => 'view',
			'somalia'
		));
	}

	/* Function: jsondata
	 * ------------------
	 * The critical map function that collects all the map data from the database
	 * and parses it to JSON for the map javascript engine.
	 */
	function jsondata($id = null)	{
		$this->layout = 'ajax';
		if(isset($this->params['url']['edit']))
			$edit = $this->params['url']['edit'];
		else
			$edit = 0;
		$data = array();
		$this->Map->id = $id;
		$map = $this->Map->read();
		$data['Map'] = $map['Map'];
		$data['Zoom'] = $map['Zoom'];
		$this->set('edit', $edit);
		$groups = $this->Group->find('all', array(
			'fields'			=>	array('id','name','shortname','startdate','enddate','description','dummy'),
			'conditions'		=>	array("id IN (SELECT group_id FROM map_groups WHERE map_id = $id)")
		));
		$this->set('groups', $groups);
		$this->set('excgroups', $this->Group->find('all', array(
			'fields'			=>	array('id','name','shortname'),
			'conditions'		=>	array("id NOT IN (SELECT group_id FROM map_groups WHERE map_id = $id)")
		)));
		$this->set('groupdata', $this->MapGroup->find('all', array(
			'fields'			=>	array('group_id','min_zoom','max_zoom','position'),
			'conditions'		=>	array('map_id' => $id),
			'order'				=>	'MapGroup.position'
		)));
		$data['links'] = $this->Link->find('all', array(
			'conditions'		=>	array(
				"group1 IN (SELECT group_id FROM map_groups WHERE map_id = $id)",
				"group2 IN (SELECT group_id FROM map_groups WHERE map_id = $id)"
			)
		));

		$all_umbrellas = $this->Umbrella->find('all');
		$umbrellas = array();
		foreach($all_umbrellas as $umbrella)	{
			$orgs = $this->UmbrellaGroup->find('all', array(
				'conditions'		=>	array(
					'umbrella_id' => $umbrella['Umbrella']['id'],
					"group_id IN (SELECT group_id FROM map_groups WHERE map_id = $id)"
				)
			));
			if(count($orgs) > 1)	{
				$umb_array = array(
					'name'	=> $umbrella['Umbrella']['name'],
					'shortname'	=> $umbrella['Umbrella']['shortname'],
					'description'	=> $umbrella['Umbrella']['description'],
					'startdate'	=> $umbrella['Umbrella']['startdate'],
					'enddate'	=> $umbrella['Umbrella']['enddate'],
					'groups' => array()
				);
				foreach($orgs as $org)	{
					array_push($umb_array['groups'], $org['UmbrellaGroup']['group_id']);
				}
				array_push($umbrellas, $umb_array);
			}
		}
		$data['umbrellas'] = $umbrellas;
		$this->set('data', $data);
	}
}
?>