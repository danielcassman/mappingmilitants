<?php
class GroupsController extends AppController {
	public $name = 'Groups';
	public $uses = array('Group','Link');
		
	function index()	{
		$this->set('title_for_layout', 'Profiles | Mapping Militant Organizations');
		$this->set('iraq', $this->Group->find('all', array(
			'conditions'		=>	array("id IN (SELECT group_id FROM map_groups WHERE map_id = 3)", "dummy=0"),
			'order'				=>	'name'
		)));
		$this->set('afpak', $this->Group->find('all', array(
			'conditions'		=>	array("id IN (SELECT group_id FROM map_groups WHERE map_id = 13)", "dummy=0"),
			'order'				=>	'name'
		)));
		$this->set('italy', $this->Group->find('all', array(
			'conditions'		=>	array("id IN (SELECT group_id FROM map_groups WHERE map_id = 15)", "dummy=0"),
			'order'				=>	'name'
		)));
		$this->set('somalia', $this->Group->find('all', array(
			'conditions'		=>	array("id IN (SELECT group_id FROM map_groups WHERE map_id = 11)", "dummy=0"),
			'order'				=>	'name'
		)));
	}
	
	function data()	{
		$this->set('groups', $this->Group->find('all', array(
			'conditions'	=>	array('id IN (SELECT group_id FROM map_groups WHERE map_id IN (SELECT id FROM maps WHERE maps.published = 1))'),
			'order'			=>	'name'
		)));
		$this->set('title_for_layout', 'Export Data');
	}
	
	function csv()	{
		$this->layout = 'ajax';
		$group_ids = $_POST['groups'];
		$groups = array();
		$columns = $_POST['columns'];
		$csv = array('id,name,' . implode(',', $columns));
		foreach($group_ids as $id)	{
			$group = $this->Group->findById($id);
			$links = $this->Link->find('all', array(
				'conditions'	=>	"group1=$id OR group2=$id"
			));
			$group['Link'] = $links;
			//$groups[$id] = $group;
			array_push($csv, $group['Group']['id'] . ',"' . $group['Group']['name'] . '"');
			array_push($groups, $group);
		}
		foreach($columns as $col)	{
			$group_count = 1;
			foreach($groups as $group)	{
				if($col == 'active_bool')	{
					$csv[$group_count] .= ',' . ($group['Group']['enddate'] == '0000-00-00' ? 1 : 0);
				}
				if($col == 'start_year')	{
					$csv[$group_count] .= ',' . substr($group['Group']['startdate'], 0, 4);
				}
				if($col == 'start_year')	{
				$csv[$group_count] .= ',' . ($group['Group']['enddate'] == '0000-00-00' ? date('Y') : substr($group['Group']['enddate'], 0, 4));
				}
				if($col == 'years_active')	{
					$years_active = ($group['Group']['enddate'] == '0000-00-00' ? date('Y') : substr($group['Group']['enddate'], 0, 4))
						- substr($group['Group']['startdate'], 0, 4);
					$csv[$group_count] .= ',' . $years_active;
				}
				if($col == 'num_leaders')	{
					$csv[$group_count] .= ',' . count($group['Leader']);
				}
				if($col == 'leader_duration')	{
					$sum = 0;
					$count = 0;
					foreach($group['Leader'] as $leader)	{
						if($leader['startdate'] == '0000-00-00' || $leader['enddate'] == '0000-00-00')
							continue;
						$sum += (substr($leader['enddate'], 0, 4) - substr($leader['startdate'], 0, 4));
						$count ++;
					}
					$average = '""';
					if($sum != 0 && $count != 0)	{
						$average = $sum / $count;
					}
					$csv[$group_count] .= ',' . $average;
				}
				if($col == 'num_countries')	{
					$csv[$group_count] .= ',' . count($group['Region']);
				}
				if($col == 'num_attacks')	{
					$csv[$group_count] .= ',' . count($group['Attack']);
				}
				if($col == 'attacks_per_year')	{
					$total = count($group['Attack']);
					$years_active = ($group['Group']['enddate'] == '0000-00-00' ? date('Y') : substr($group['Group']['enddate'], 0, 4))
						- substr($group['Group']['startdate'], 0, 4);
					$average = $total / $years_active;
					$csv[$group_count] .= ',' . $average;
				}
				if($col == 'num_relationships')	{
					$csv[$group_count] .= ',' . count($group['Link']);
				}
				if($col == 'num_splinters')	{
					$count = 0;
					foreach($group['Link'] as $link)	{
						if($link['Link']['type'] == 'spl' && $link['Link']['group1'] == $group['Group']['id'])
							$count ++;
					}
					$csv[$group_count] .= ',' . $count;
				}
				if($col == 'num_allies_affiliates')	{
					$count = 0;
					foreach($group['Link'] as $link)	{
						if($link['Link']['type'] == 'all' || $link['Link']['type'] == 'aff')
							$count ++;
					}
					$csv[$group_count] .= ',' . $count;
				}
				if($col == 'num_rivals')	{
					$count = 0;
					foreach($group['Link'] as $link)	{
						if($link['Link']['type'] == 'riv')
							$count ++;
					}
					$csv[$group_count] .= ',' . $count;
				}
				$group_count ++;
			}
		}
		$filename = date('YmdHis') . rand(10000,99999) . '.csv';
		$myFile = '../webroot/files/csv/' . $filename;
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, implode("\n", $csv));
		fclose($fh);
		$this->set('path', $this->webroot . 'files/csv/' . $filename);
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