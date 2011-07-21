<?php
	echo $html->css('buttons','stylesheet',array('inline'=>false));
?>
<h1 class="span-24"><?php echo ($valid ? 'Comment added!' : 'Invalid comment'); ?></h1>
<div class="span-15 colborder">
	<p><?php
		if($valid)	{
			echo "Thank you for your feedback! Your comment has been submitted to our research team, and it will be posted to the feedback page once an administrator approves it. If we have any questions regarding your comment, we will contact you using the email address you supplied.";
		}	else	{
			echo "Your comment was invalid. Please ensure you supplied a valid email address, your name, and comment of at least ten characters. " . $this->Html->link('Return to the comment form and try again.', array('controller' => 'comments', 'action' => 'create'));
		}
	?></p>
	<p><?php echo $this->Html->link('Return to the feedback page', array('controller' => 'comments', 'action' => 'index')); ?></p>
</div>
<p class="span-8 last">
	<?php
		echo $html->link('See the map', array('controller'=>'pages', 'action'=>'map'), array('class'=>'button fullwidth')) . "\n\t\t";
		echo $html->link('Read the profiles', array('controller'=>'profiles', 'action'=>'index'), array('class'=>'button fullwidth')) . "\n";
		echo $html->link('Learn about the project', array('controller'=>'pages', 'action'=>'about'), array('class'=>'button fullwidth')) . "\n";
	?>
</p>