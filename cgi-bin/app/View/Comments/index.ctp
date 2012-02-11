<?php
	echo $this->Html->css('buttons','stylesheet',array('inline'=>false));
?>
<h1 class="span-24">Feedback</h1>
<div class="span-15 colborder">
	<p class="clearfix"><?php echo $this->Html->link('Leave a comment',array('controller' => 'comments', 'action' => 'create'), array('class' => 'red button')); ?></p>
	<p>We welcome feedback on any aspect of our project, as well as tips and corrections to the information we publish. We request that you leave comments here, but you may also email the project team directly at <b>mappingmilitants [at] lists [dot] stanford [dot] edu</b>.</p>
	<h2>Recent Comments</h2>
	<p><?php echo $this->Paginator->prev('Previous', null, null, array('class' => 'disabled')) . '&emsp;' . $this->Paginator->numbers() . '&emsp;' . $this->Paginator->next('Next', null, null, array('class' => 'disabled')); ?></p>
	<?php foreach($data as $comment): ?>
	<div class="comment">
		<p class="handle"><?php echo $comment['Comment']['handle']; ?> says&hellip;</p>
		<p class="comment_text"><?php echo $comment['Comment']['comment']; ?></p>
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