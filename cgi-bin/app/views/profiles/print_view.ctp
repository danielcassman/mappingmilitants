<!-- File: /app/views/profiles/view.ctp -->
<?php
	$this->set('title_for_layout', stripslashes($profile['Profile']['name']) . ' | Mapping Militant Organizations');
	$this->Html->css('printable', 'stylesheet', array('inline'=>false));
	echo $this->Html->script('http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js', array('inline'=>false));
?>
	<h1>Profile: <?php echo stripslashes($profile['Profile']['name']); ?></h1>
	<?php echo stripslashes($profile['Profile']['html']); ?>
	
	<script type="text/javascript">
		var ref_header;
		$(document).ready(function()	{
			var h2s = $('h2');
			ref_header = h2s[h2s.length - 1];
			$('a.footnote').each(function(i)	{
				var text = $(this).text();
				$(this).text(text.substring(1, text.indexOf(']')));
				$(this).wrap('<sup/>');
				$('a[name="ref' + i + '"]').remove();
			});
			$('#control').append($('<input/>', {
				'id':'show_footnotes',
				'name':'show_footnotes',
				'type':'checkbox',
				'checked':'checked'
			})).append($('<label/>',{
				'text':'Include footnotes',
				'for':'show_footnotes'
			}));
			$('#show_footnotes').change(function()	{
				if($(this).attr('checked'))	{
					$('a.footnote').removeClass('no_print');
					$('ol.reference-list').removeClass('no_print');
					$(ref_header).text('References');
				}	else	{
					$('a.footnote').addClass('no_print');
					$('ol.reference-list').addClass('no_print');
					$(ref_header).text('');
				}
			});
		});
	</script>