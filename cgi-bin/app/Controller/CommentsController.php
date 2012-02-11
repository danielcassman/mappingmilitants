<?php
class CommentsController extends AppController {
	public $name = 'Comments';
	public $components = array('Auth');
	
	function beforeFilter() {
		$this->Auth->allow('index','create','add','rss');
	}

		
	function index()	{
		$this->paginate = array(
			'order' => 'Comment.date DESC',
			'conditions' => array('Comment.approved' => 1),
			'limit' => 25
		);
		$data = $this->paginate('Comment');
		$this->set(compact('data'));
		$this->set('title_for_layout', 'Feedback | Mapping Militant Organizations');
	}
	
	function rss()	{
		$this->layout = 'rss';
		$this->set('comments', $this->Comment->find('all', array(
			'order' => 'Comment.date DESC',
			'limit' => 25
		)));
	}
	
	function create()	{
		$this->set('title_for_layout', 'Leave a Comment | Mapping Militant Organizations');
	}
	
	function add()	{
		$valid = false;
		if(strpos($this->data['Comment']['email'], '@') > 0 && strrpos($this->data['Comment']['email'], '.') > strpos($this->data['Comment']['email'], '@') + 1)	{
			if(strlen($this->data['Comment']['handle']) >= 2 && strlen($this->data['Comment']['handle']) <= 255)	{
				if(strlen($this->data['Comment']['comment']) >= 2 && strlen($this->data['Comment']['comment']) <= 255)	{
					$valid = true;
				}
			}
		}
		if($valid)	{
			$this->Comment->create();
			$this->Comment->save($this->data);
		}
		$this->set('valid', $valid);
		$this->set('title_for_layout', 'Leave a Comment | Mapping Militant Organizations');
	}
	
	function review()	{
		$this->paginate = array(
			'order' => array('Comment.approved', 'Comment.date DESC'),
			'limit' => 25
		);
		$data = $this->paginate('Comment');
		$this->set(compact('data'));
		$this->set('title_for_layout', 'Review Feedback | Mapping Militant Organizations');
	}
	
	function edit($id = null)	{
		$this->Comment->id = $id;
		$this->data = $this->Comment->read();
		$this->set('comment', $this->Comment->read());
	}
	
	function update()	{
		$this->Comment->save($this->data);
		$this->redirect(array('controller' => 'comments', 'action' => 'review'));
	}
	
	function delete($id = null)	{
		$this->Comment->id = $id;
		$this->Comment->delete($id);
		$this->redirect(array('controller' => 'comments', 'action' => 'review'));
	}
}
?>