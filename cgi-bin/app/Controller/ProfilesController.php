<?php
class ProfilesController extends AppController {
	public $name = 'Profiles';

	function view($id = null)	{
		$this->layout = 'redirect';
		$this->set('new_url', "http://www.stanford.edu/group/mappingmilitants/groups/view/$id");
	}
}
?>