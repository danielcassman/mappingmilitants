	<channel>
		<title>Comments | Mapping Militant Organizations | For Administrative Purposes Only</title>
			<link>http://mappingmilitants.stanford.edu</link>
			<description>The comments feed to alert team members when new comments are submitted. For internal use only. Neither Stanford University, the Mapping Militant Organizations Project, nor any of the individual team members are responsible for the content of this feed.</description>
			<atom10:link xmlns:atom10="http://www.w3.org/2005/Atom" rel="self" type="application/rss+xml" href="http://stanford.edu/group/mappingmilitants/cgi-bin/comments/rss" />
	<?php foreach($comments as $comment): ?>
		<item>
			<title><?php echo $comment['Comment']['handle'];?> says</title>
			<author><?php echo $comment['Comment']['email']; ?> (<?php echo $comment['Comment']['handle']; ?>)</author>
			<description><?php echo $comment['Comment']['comment']; ?></description>
			<link>http://localhost/group/mappingmilitants/cgi-bin/comments/review</link>
			<pubDate><?php echo date('r', strtotime($comment['Comment']['date'])); ?></pubDate>
		</item>
	<?php endforeach; ?>
	</channel>