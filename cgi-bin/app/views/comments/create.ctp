<?php
	date_default_timezone_set('America/Los_Angeles');
	echo $html->css('buttons','stylesheet',array('inline'=>false));
	echo $html->css('validationEngine.jquery','stylesheet',array('inline'=>false));
	$this->Html->script(array('http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js', 'jquery.validationEngine-en', 'jquery.validationEngine'), array('inline'=>false));
?>
<h1 class="span-24">Leave a Comment</h1>
<div class="span-24">
	<p>Leave your comment here. Please note that your comment will not appear on the feedback page until it has been approved by an administrator.</p>
	<?php echo $this->Form->create('Comment', array('type' => 'post', 'action' => 'add')) . "\n\t\t";	
		echo $this->Form->input('email', array('div' => 'clearfix input text', 'class' => 'validate[required,custom[email]]')) . "\n\t\t";
		echo $this->Form->input('handle', array('div' => 'clearfix input text required', 'label' => 'Name', 'class' => 'validate[required,minSize[2],maxSize[255]]', 'minlength' => 2, 'maxlength' => 255)) . "\n\t\t";
		echo $this->Form->input('comment', array('div' => 'clearfix input textarea', 'class' => 'validate[required,minSize[10],maxSize[255]]', 'minlength' => '10')) . "\n\t\t";
		echo '<p class="clearfix"><input type="submit" class="blue button" value="Leave my comment" /><input type="buton" class="button" value="Cancel" id="cancel_button" /></p>' . "\n\t\t";
		echo $this->Form->input('approved', array('type' => 'hidden', 'default' => 0)) . "\n\t\t";
		echo $this->Form->input('date', array('type' => 'hidden', 'default' => date('Y-m-d H:m:s'))) . "\n\t";
		echo $this->Form->end() . "\n";
	?>
	<p id="form_helper">
		<?php echo $this->Html->image('form_helper_arrow.png'); ?>
		<span></span>
	</p>
</div>
<script type="text/javascript">
	var form_help = new Array();
	form_help['CommentEmail'] = 'Your email address. This will never be displayed publicly and will only be used if the researchers need to contact you.';
	form_help['CommentHandle'] = 'Your name, as you wish it to appear publicly with your comment (feel free to use a nickname or screen name if you want to remain anonymous).';
	form_help['CommentComment'] = 'The text of your comment.';
	$(document).ready(function()	{
		$('#form_helper').fadeOut(0);
		$('input, textarea').focus(function(e)	{
			var target = e.target;
			$('#form_helper').stop().children('span').text(form_help[$(target).attr('id')]);
			$('#form_helper').fadeIn('fast').animate({
				'left': ($(target).offset().left + $(target).width() + 46) + 'px',
				'top': parseInt($(target).offset().top - ($('#form_helper').height() / 2), 10) + 'px'
			});
		}).blur(function()	{
			$('#form_helper').fadeOut('fast');
		});
		$("#CommentCreateForm").validationEngine({promptPosition : "topRight", scroll: false});
		$("#CommentCreateForm").validationEngine('init', {promptPosition : "topRight", scroll: false});
		$('#cancel_button').click(function()	{
			window.location = 'index';
		});
	});
</script>