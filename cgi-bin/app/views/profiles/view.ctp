<!-- File: /app/views/profiles/view.ctp -->
<div id="content" class="span-15 colborder">
<?php
	$this->set('title_for_layout', stripslashes($profile['Profile']['name'] . ' | Mapping Terrorist Organizations'));
	echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js', array('inline'=>false));
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
			$text = preg_replace('!('. $h .')!i','<span class="highlight' . (($i + 1) % 6) . '">$1</span>',$text);
		}
		return $text;
	}
	
	echo '<h1 class="span-24">' . stripslashes($profile['Profile']['name']) . '</h1>';
	echo highlight(stripslashes($profile['Profile']['html']), $highlight);
?>
</div>

<div class="span-8 last">
	<p><?php echo $html->link('Printable Version', array('controller' => 'profiles', 'action' => 'print_view', $profile['Profile']['id']), array('target' =>'_blank')); ?></p>
	<h2>Search</h2>
	<form name="searchform" action="../search" method="get">
		<p><input type="text" name="q" id="q" style="border:1px solid black; border-radius:3px; -moz-border-radius:3px; -webkit-border-radius:3px; padding:5px;" size="40" /></p>
	</form>
	<h2>Contents</h2> 
	<ul id="toc_list">
	</ul>
</div>

<script type="text/javascript" language="javascript">
	function clearHighlight()	{
		for(var i = 1; i <= 6; i++)	{
			$('span.highlight' + i, '#content').removeClass('highlight' + i);
		}
		$('#highlight-summary').remove();
	}
	
	$(document).ready(function()	{
		var cols = [];
		$('h2','#content').each(function(i)	{
			cols.push($(this).text());
			$(this).prepend($('<a/>', {name:'toc' + i}));
		});
		for(var i = 0; i < cols.length; i++)	{
			$('#toc_list').append($('<li/>', {
				html: "<a href='#toc" + i + "'>" + cols[i] + "</a>"
			}));
		}
	});
</script>