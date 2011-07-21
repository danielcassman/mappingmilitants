<?php
class MapsController extends AppController {
	var $name = 'Maps';
	var $uses = array('Map','Profile','Map_org','Link','Umbrella','Umbrella_org');
	
	/*function index()	{
		$this->set('maps', $this->Map->find('all'));
		$this->set('title_for_layout', 'Maps | Mapping Militant Organizations');
	}
	
	function view($id = null)	{
		$this->Map->id = $id;
		$this->set('map', $this->Map->read());
		$this->layout = 'map';
	}*/
	
	function iraq()	{
		//$this->Map->id = 3;
		//$this->set('map', $this->Map->read());
		$this->set('title_for_layout', 'Iraq | Mapping Militant Organizations');
		$this->layout = 'map';
	}

	function jsondata($id = null)	{
		$this->layout = 'ajax';
		if(isset($this->params['url']['edit']))
			$edit = $this->params['url']['edit'];
		else
			$edit = 0;
		$data = array();
		$this->Map->id = $id;
		//$this->set('map', $this->Map->read());
		$map = $this->Map->read();
		$data['Map'] = $map['Map'];
		$this->set('edit', $edit);
		$groups = $this->Profile->find('all', array(
			'fields'			=>	array('id','wikiid','name','shortname','startdate','enddate','leadership','majorattacks','short'),
			'conditions'		=>	array("id IN (SELECT org_id FROM map_orgs WHERE map_id = $id)")
		));
		$this->set('groups', $groups);
		$this->set('excgroups', $this->Profile->find('all', array(
			'fields'			=>	array('id','name','shortname'),
			'conditions'		=>	array("id NOT IN (SELECT org_id FROM map_orgs WHERE map_id = $id)")
		)));
		$this->set('groupdata', $this->Map_org->find('all', array(
			'fields'			=>	array('org_id','min_zoom','max_zoom','position'),
			'conditions'		=>	array('map_id' => $id),
			'order'				=>	'Map_org.position'
		)));
		$data['links'] = $this->Link->find('all', array(
			'conditions'		=>	array(
				"group1 IN (SELECT org_id FROM map_orgs WHERE map_id = $id)",
				"group2 IN (SELECT org_id FROM map_orgs WHERE map_id = $id)"
			)
		));
		$all_umbrellas = $this->Umbrella->find('all');
		$umbrellas = array();
		foreach($all_umbrellas as $umbrella)	{
			$orgs = $this->Umbrella_org->find('all', array(
				'conditions'		=>	array(
					'umbrella_id' => $umbrella['Umbrella']['id'],
					"org_id IN (SELECT org_id FROM map_orgs WHERE map_id = $id)"
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
					array_push($umb_array['groups'], $org['umbrella_org']['org_id']);
				}
				array_push($umbrellas, $umb_array);
			}
		}
		$data['umbrellas'] = $umbrellas;
		$this->set('data', $data);
	}
}
?>