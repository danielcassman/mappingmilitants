<?php
class GroupsController extends AppController {
	public $name = 'Groups';
	public $uses = array('Group','Link', 'Map');
		
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
		$this->set('maps', $this->Map->find('all', array(
			'conditions'	=>	array('published' => 1),
			'fields'		=>	array('id','name')
		)));
		$this->set('title_for_layout', 'Export Data');
	}
	
	function csv()	{
		$this->layout = 'ajax';
		//$this->set('format', $_POST['format']);
		if(isset($_POST['groups']) && $_POST['groups'] != null)	{
			$group_ids = $_POST['groups'];
		}	else	{
			$group_ids = array();
		}
		if(isset($_POST['maps']))	{
			$maps = $_POST['maps'];
		}	else	{
			$maps = array();
		}
		$all_groups = array();
		foreach($maps as $id)	{
			$map_groups = $this->Group->find('all', array(
				'conditions'		=>	array("id IN (SELECT group_id FROM map_groups WHERE map_id = $id)")
			));
			foreach($map_groups as $group)	{
				if(!isset($all_groups[$group['Group']['id']]))	{
					$links = $this->Link->find('all', array(
						'conditions'	=>	'group1=' . $group['Group']['id'] . ' OR group2=' . $group['Group']['id']
					));
					$group['Link'] = $links;
					$all_groups[$group['Group']['id']] = $group;
				}
			}
		}
		foreach($group_ids as $id)	{
			if(!isset($all_groups[$id]))	{
				$group = $this->Group->findById($id);
				$links = $this->Link->find('all', array(
					'conditions'	=>	"group1=$id OR group2=$id"
				));
				$group['Link'] = $links;
				$all_groups[$id] = $group;
			}
		}
		$groups = array();
		foreach($all_groups as $group)	{
			array_push($groups, $group);
		}
		//$this->set('groups', $groups);
		if(isset($_POST['vars']))	{
			$vars = $_POST['vars'];
		}	else	{
			$vars = array();
		}
		
		$csv = array(array('id','name'));
		for($i = 0; $i < count($groups); $i++)	{
			array_push($csv, array($groups[$i]['Group']['id'], $groups[$i]['Group']['name']));
		}
		foreach($vars as $var)	{
			array_push($csv[0], $var);
			if($var == 'start_year')	{
				for($i = 0; $i < count($groups); $i++)	{
					array_push($csv[$i + 1], substr($groups[$i]['Group']['startdate'], 0, 4));
				}
			}
			if($var == 'end_year')	{
				for($i = 0; $i < count($groups); $i++)	{
					array_push($csv[$i + 1], ($groups[$i]['Group']['enddate'] == '0000-00-00' ? date('Y') : substr($groups[$i]['Group']['enddate'], 0, 4)));
				}
			}
			if($var == 'active')	{
				for($i = 0; $i < count($groups); $i++)	{
					array_push($csv[$i + 1], ($groups[$i]['Group']['enddate'] == '0000-00-00' ? 1 : 0));
				}
			}
			if($var == 'years_active')	{
				for($i = 0; $i < count($groups); $i++)	{
					$years_active = ($groups[$i]['Group']['enddate'] == '0000-00-00' ? date('Y') : substr($groups[$i]['Group']['enddate'], 0, 4))
						- substr($groups[$i]['Group']['startdate'], 0, 4);
					array_push($csv[$i + 1], $years_active);
				}
			}
			if($var == 'num_leaders')	{
				for($i = 0; $i < count($groups); $i++)	{
					array_push($csv[$i + 1], count($groups[$i]['Leader']));
				}
			}
			if($var == 'avg_leader_duration')	{
				for($i = 0; $i < count($groups); $i++)	{
					$sum = 0;
					$count = 0;
					foreach($groups[$i]['Leader'] as $leader)	{
						if($leader['startdate'] == '0000-00-00' || $leader['enddate'] == '0000-00-00')
							continue;
						$sum += (($leader['enddate'] == '0001-00-00' ? date('Y') : substr($leader['enddate'], 0, 4)) - substr($leader['startdate'], 0, 4));
						$count ++;
					}
					$average = '""';
					if($sum != 0 && $count != 0)	{
						$average = round($sum / $count, 4);
					}
					array_push($csv[$i + 1], $average);
				}
			}
			if($var == 'num_countries')	{
				for($i = 0; $i < count($groups); $i++)	{
					array_push($csv[$i + 1], count($groups[$i]['Region']));
				}
			}
			if($var == 'num_attacks')	{
				for($i = 0; $i < count($groups); $i++)	{
					array_push($csv[$i + 1], count($groups[$i]['Attack']));
				}
			}
			if($var == 'attacks_per_year')	{
				for($i = 0; $i < count($groups); $i++)	{
					$total = count($groups[$i]['Attack']);
					$years_active = ($groups[$i]['Group']['enddate'] == '0000-00-00' ? date('Y') : substr($groups[$i]['Group']['enddate'], 0, 4))
						- substr($groups[$i]['Group']['startdate'], 0, 4);
					$average = '""';
					if($years_active > 0)
						$average = round($total / $years_active, 4);
					array_push($csv[$i + 1], $average);
				}
			}
			if($var == 'num_relationships')	{
				for($i = 0; $i < count($groups); $i++)	{
					array_push($csv[$i + 1], count($groups[$i]['Link']));
				}
			}
			if($var == 'num_splinters')	{
				for($i = 0; $i < count($groups); $i++)	{
					$count = 0;
					foreach($groups[$i]['Link'] as $link)	{
						if($link['Link']['type'] == 'spl' && $link['Link']['group1'] == $groups[$i]['Group']['id'])
							$count ++;
					}
					array_push($csv[$i + 1], $count);
				}
			}
			if($var == 'num_allies_affiliates')	{
				for($i = 0; $i < count($groups); $i++)	{
					$count = 0;
					foreach($groups[$i]['Link'] as $link)	{
						if($link['Link']['type'] == 'all' || $link['Link']['type'] == 'aff')
							$count ++;
					}
					array_push($csv[$i + 1], $count);
				}
			}
			if($var == 'num_rivals')	{
				for($i = 0; $i < count($groups); $i++)	{
					$count = 0;
					foreach($groups[$i]['Link'] as $link)	{
						if($link['Link']['type'] == 'riv')
							$count ++;
					}
					array_push($csv[$i + 1], $count);
				}
			}
		}
		
		if($_POST['format'] == 'csv')	{
			for($i = 0; $i < count($csv); $i++)	{
				$csv[$i] = implode(',', $csv[$i]);
			}
			$filename = date('YmdHis') . rand(10000,99999) . '.csv';
			$myFile = '../webroot/files/csv/' . $filename;
			$fh = fopen($myFile, 'w') or die("can't open file");
			fwrite($fh, implode("\n", $csv));
			fclose($fh);
			$this->set('output', $this->webroot . 'files/csv/' . $filename);
		}	else if($_POST['format'] == 'html')	{
			$csv[0] = '<thead><tr><th>' . implode('</th><th>', $csv[0]) . '</th></tr></thead><tbody>';
			for($i = 1; $i < count($csv); $i++)	{
				$csv[$i] = implode('</td><td>', $csv[$i]);
				$csv[$i] = '<tr><td>' . $csv[$i] . '</td></tr>';
			}
			$this->set('output', implode($csv) . '</tbody>');
		}
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