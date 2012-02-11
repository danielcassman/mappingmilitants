 <?php
	echo $this->Html->css('buttons','stylesheet',array('inline'=>false));
?>
<h1 class="span-24">Review Feedback</h1>
<div class="span-15 colborder">
	<p>Use this page to review feedback. Comments that have been approved are highlighted in green; comments that have not been approved are highlighed in red.</p>
	<p><?php echo $this->Paginator->prev('Previous', null, null, array('class' => 'disabled')) . '&emsp;' . $this->Paginator->numbers() . '&emsp;' . $this->Paginator->next('Next', null, null, array('class' => 'disabled')); ?></p>
	<?php foreach($data as $comment): ?>
	<div class="comment <?php echo($comment['Comment']['approved'] ? 'approved' : 'not-approved'); ?>">
		<p class="handle"><?php echo $comment['Comment']['handle']; ?> says&hellip;</p>
		<p class="comment_text"><?php echo $this->Html->link($comment['Comment']['comment'], array('controller' => 'comments', 'action' => 'edit', $comment['Comment']['id'])); ?></p>
		<p class="date"><?php echo date('F j, Y \a\t g:i:s a', strtotime($comment['Comment']['date'])); ?></p>
	</div>
	<?php endforeach; ?>
	<p><?php echo $this->Paginator->prev('Previous', null, null, array('class' => 'disabled')) . '&emsp;' . $this->Paginator->numbers() . '&emsp;' . $this->Paginator->next('Next', null, null, array('class' => 'disabled')); ?></p>
</div>
<p class="span-8 last">
	<?php
		echo $this->Html->link('See the map', array('controller'=>'maps', 'action'=>'iraq'), array('class'=>'button fullwidth')) . "\n\t\t";
		echo $this->Html->link('Read the profiles', array('controller'=>'profiles', 'action'=>'index'), array('class'=>'button fullwidth')) . "\n";
		echo $this->Html->link('Learn about the project', array('controller'=>'pages', 'action'=>'about'), array('class'=>'button fullwidth')) . "\n";
	?>
</p>