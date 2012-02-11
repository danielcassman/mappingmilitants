<?php
class UsersController extends AppController {

	public $name = 'Users';
	public $components = array('Auth'); // Not necessary if declared in your app controller

	function beforeFilter(){
		parent::beforeFilter();
	}
	
	/**
	 *  The AuthComponent provides the needed functionality
	 *  for login, so you can leave this function blank.
	 */
	function login() {
	}

	function logout() {
		$this->redirect($this->Auth->logout());
	}
}
?>