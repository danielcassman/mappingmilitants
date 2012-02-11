<?php
	/* File: cgi-bin/app/views/maps/jsondata.ctp
	 * -----------------------------------------
	 * This file takes all of the data for a given map, including
	 * map data (title, dates, etc.), all group information, links,
	 * and umbrellas. This page outputs all of the data in JSON
	 * format for passing to the map template.
	 */
	
	$data['groups'] = array();
	unset($data['Map']['groups']);
	//$data['Map']['zooms'] = unserialize(stripslashes($data['Map']['zooms']));
	
	/* Format the groups array in the proper order. Iterating through the
	 * groupdata array rather than the profiles assures the correct order
	 * for this map, since the groupdata array is ordered by map position
	 * through the controller.
	 */
	foreach($groupdata as $gd)	{
		$g = findGroupById($gd['MapGroup']['group_id'], $groups);
		$g['Group']['min_zoom'] = $gd['MapGroup']['min_zoom'];
		$g['Group']['max_zoom'] = $gd['MapGroup']['max_zoom'];
		unset($g['Group']['majorattacks']);
		unset($g['Group']['leadership']);
		$g['Group']['name'] = stripslashes($g['Group']['name']);
		$g['Group']['description'] = trim(stripslashes($g['Group']['description']));
		if($g['Group']['enddate'] == '0000-00-00')
			$g['Group']['active'] = true;
		else
			$g['Group']['active'] = false;
		array_push($data['groups'], $g);
	}
	
	$data['links'] = stripslashesArray($data['links'], 'Link', array('description'));
	
	echo json_encode($data);
	
	function stripslashesArray($array, $identifier = false, $properties = null)	{
		for($i = 0; $i < count($array); $i++)	{
			$item = $array[$i];
			if($identifier)
				$item = $item[$identifier];
			if($properties != null){
				for($j = 0; $j < count($properties); $j++){
					$item[$properties[$j]] = stripslashes($item[$properties[$j]]);
				}
			}
			else	{
				$item = stripslashes($item);
			}
			if($identifier)
				$array[$i][$identifier] = $item;
			else
				$array[$i] = $item;
		}
		return $array;
	}
	
	/* Function: findGroupById
	 * -----------------------
	 * Finds and returns a group in the given array by its unique 
	 * group ID.
	 * 
	 * @param id: the ID of the group to find
	 * @param groups: the groups array, expected to be formatted
	 *   as the default CakPHP Profile object
	 * @return: the Profile object for the desired group
	 */
	function findGroupById($id, $groups)	{
		foreach($groups as $g)	{
			if($g['Group']['id'] == $id) return $g;
		}
	}
?>
