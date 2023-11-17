<!-- utility links -->
<?php
$n1_mag = N1_Magazine::Instance();
// Get context issue.  This is the issue currently active on the front.
$issue = $n1_mag->context_issue;

// only show edition info if we know we're in an edition
if ( $n1_mag->is_issue_known() ){?>
   <a class="context-issue" href="/<?php echo $issue->post_name ?>/"><?php echo $issue->post_title ?></a>
<?php } else { ?>
   <a class="latest-issue" href="<?php echo $n1_mag->get_current_issue_url()?>">Latest Issue</a>
<?php } ?>

<ul class="mag-utility-links">
	<li><a href="/past-issues/">Past Issues</a></li>
</ul>

<!-- search -->
<div class="search-container">
	 <form role="search" method="get" id="searchform" action="/" >
	 	   <input type="text" value="Search Magazine" name="s" id="s" />
	 	   <input type="submit" id="searchsubmit" value="GO" />
	</form>
</div>
