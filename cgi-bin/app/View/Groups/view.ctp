<?php echo $this->Html->script(array('http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'), array('inline' => false)); ?>
<div class="span-15 colborder">
<?php
	if(isset($_GET['highlight']))	$highlight = stripslashes($_GET['highlight']);
	else $highlight = '';

	if(strlen($highlight) > 0)	{
		echo "<p id=\"highlight-summary\">These terms from your <a href=\"../search?q=$highlight\">search</a> are highlighted:";
		$h = explode(' ', $highlight);
		foreach($h as $i => $term)
			echo " <span class=\"highlight" . (($i + 1) % 6) . "\">$term</span>";
		echo ". <a href=\"#\" onclick=\"clearHighlight(); return false;\">Clear highlighting</a>.</p>";
	}
	
	function highlight($text, $highlight)	{
		if(strlen($highlight) < 1)	return $text;
		$highlight = explode(' ', $highlight);
		foreach($highlight as $i => $h)	{
			$text = preg_replace('!('. $h .')!i','<span class="highlight highlight' . (($i + 1) % 6) . '">$1</span>',$text);
		}
		return $text;
	}

	echo highlight($this->data['Group']['html'], $highlight) . "<br />";
	echo '<h2>References</h2>';
	echo $this->data['Group']['footnotes'];
?>
</div>
<div class="span-8 last">
	<p><?php echo $this->Html->link('Print this page', array('controller' => 'groups', 'action' => 'print_view', $this->data['Group']['id']), array('target' => '_blank')); ?></p>
	<h2>Contents</h2>
	<?php echo $this->data['Group']['toc']; ?>
	<h2>Search</h2>
	<form name="searchform" action="/group/mappingmilitants/cgi-bin/groups/search" method="get">
		<p><input type="text" name="q" id="q" /></p>
	</form>
</div>
<script type="text/javascript" language="javascript">
	function clearHighlight()	{
		$("span.highlight").attr("class","");
		$('#highlight-summary').remove();
	}
</script>