<?php
	date_default_timezone_set('America/Los_Angeles');
	echo $html->css('buttons','stylesheet',array('inline'=>false));
	$this->Html->script(array('http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js'), array('inline'=>false));
?>
<h1 class="span-24">Edit a Comment</h1>
<div class="span-24">
	<p>Use this form to edit, approve, or delete a comment.</p>
	<?php echo $this->Form->create('Comment', array('type' => 'post', 'action' => 'update')) . "\n\t\t";	
		echo $this->Form->input('email', array('div' => 'clearfix input text', 'disabled' => 'disabled')) . "\n\t\t";
		echo $this->Form->input('handle', array('div' => 'clearfix input text required', 'label' => 'Name', 'disabled' => 'disabled', 'minlength' => 2, 'maxlength' => 255)) . "\n\t\t";
		echo $this->Form->input('comment', array('div' => 'clearfix input textarea', 'minlength' => '10')) . "\n\t\t";
		echo $this->Form->input('approved') . "\n\t\t";
		echo '<p class="clearfix"><input type="submit" class="blue button" value="Update Comment" /> <input type="button" class="red button" value="Delete Comment" id="delete_button" /> <input type="buton" class="button" value="Cancel" id="cancel_button" /></p>' . "\n\t\t";
		echo $this->Form->input('date', array('type' => 'hidden')) . "\n\t";
		echo $this->Form->end() . "\n";
	?>
</div>
<script type="text/javascript">
	$(document).ready(function()	{
		$('#delete_button').click(function()	{
			if(confirm('Are your sure you want to delete this comment?'))	{
				window.location = '../delete/<?php echo $comment['Comment']['id']; ?>';
			}
		});
		
		$('#cancel_button').click(function()	{
			window.location = '../review';
		});
	});
</script>