<?php 
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
?>
<div style="margin-top: 8px; margin-bottom: 8px; font-size: 11px;">
	<?php echo number_format($p->totalRecords); ?> <?php echo _mmt($p->recordName); ?>
	
	<span style="margin-left: 15px;">
	<a onclick='mmjs.dgPreviousPage("<?php echo $p->crntPage; ?>",<?php echo $p->attributes; ?>)' style="cursor:pointer" title="previous"><?php echo MM_Utils::getIcon('chevron-circle-left', 'light-blue', '1.4em', '1px'); ?></a>
	<?php echo (intval($p->crntPage) + 1); ?>
	<a onclick='mmjs.dgNextPage("<?php echo $p->crntPage; ?>", "<?php echo $p->totalPages; ?>",<?php echo $p->attributes; ?>)' style="cursor:pointer" title="next"><?php echo MM_Utils::getIcon('chevron-circle-right', 'light-blue', '1.4em', '1px'); ?></a>
	<?php echo _mmt('of'); ?>
	<?php echo $p->totalPages; ?>
	<?php echo _mmt('pages'); ?>
	</span>
	
	<span style="margin-left: 15px;">
		<select onchange='mmjs.dgSetResultSize(this,<?php echo $p->attributes; ?>)'>
		<?php echo MM_HtmlUtils::getDataGridResultsCount($p->resultSize); ?>
		</select> 
		<?php echo _mmt('per page'); ?>
	</span>
</div>