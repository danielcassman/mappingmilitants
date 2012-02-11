<?php
class UmbrellasController extends AppController {
	public $name = 'Umbrellas';
	public $uses = array('Umbrella','Profile','Umbrella_org');
	
	function index()	{
		$this->set('umbrellas', $this->Umbrella->find('all'));
		$this->set('title_for_layout', 'Umbrellas | Mapping Terrorist Organizations');
	}
}
?>