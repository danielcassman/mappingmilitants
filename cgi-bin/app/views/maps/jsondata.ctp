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
	$data['Map']['zooms'] = unserialize(stripslashes($data['Map']['zooms']));
	
	/* Format the groups array in the proper order. Iterating through the
	 * groupdata array rather than the profiles assures the correct order
	 * for this map, since the groupdata array is ordered by map position
	 * through the controller.
	 */
	foreach($groupdata as $gd)	{
		$g = findGroupById($gd['Map_org']['org_id'], $groups);
		$g['Profile']['min_zoom'] = $gd['Map_org']['min_zoom'];
		$g['Profile']['max_zoom'] = $gd['Map_org']['max_zoom'];
		$g['Profile']['majorattacks'] = unserialize(stripslashes($g['Profile']['majorattacks']));
		$g['Profile']['leadership'] = unserialize(stripslashes($g['Profile']['leadership']));
		$g['Profile']['name'] = stripslashes($g['Profile']['name']);
		$g['Profile']['description'] = stripslashes($g['Profile']['short']);
		if($g['Profile']['enddate'] == '0000-00-00')
			$g['Profile']['active'] = true;
		else
			$g['Profile']['active'] = false;
		unset($g['Profile']['short']);
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
			if($g['Profile']['id'] == $id) return $g;
		}
	}
?>
