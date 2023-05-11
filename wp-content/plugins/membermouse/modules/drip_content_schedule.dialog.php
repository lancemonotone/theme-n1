<?php
if($p->dialog=="updateAccessRights")
{
?>
	<table width='98%'>
	<tr>
		<td colspan='2'><?php echo $p->type_name; ?> <?php echo sprintf(_mmt("members get access to %s"), $p->page_name); ?> ... </td>
	</tr>
		<tr>
			<td><input type='radio' id='mm-gar-change' name='mm-gar-change' value='change' checked /> </td>
			<td>
				<?php echo _mmt("on day"); ?> <input type='text' id='mm_gar_day' value='<?php echo $p->day; ?>' style='width: 50px;' />	
				<input type='hidden' id='mm_access_id' value='<?php echo $p->access_id; ?>' />
				<input type='hidden' id='mm_access_type' value='<?php echo $p->access_type; ?>' />
				<input type='hidden' id='mm_post_id' value='<?php echo $p->post_id; ?>' />
			</td>
		</tr>
		<tr>
			<td><input type='radio' id='mm-gar-remove' name='mm-gar-change' value='remove'  /> </td>
			<td>
				<?php echo _mmt("Revoke access"); ?>
			</td>
		</tr>
	</table>
	
	
<div class="mm-dialog-footer-container">
<div class="mm-dialog-button-container">
<a href="javascript:mmjs.updateAccessRights();" class="mm-ui-button blue">Update Access</a>
<a href="javascript:mmjs.closeDialog();" class="mm-ui-button">Cancel</a>
</div>
</div>
	<?php 
}
else if($p->dialog=="showAddAccessRigths")
{
	$selectPostsByDefault = true;
	if(empty($p->posts_select)) {
		$selectPostsByDefault = false;
	}
	?>
	<table width='98%'>
		<tr>
			<td colspan='3'>
			<?php echo sprintf(_mmt("Grant %s access to"),$p->type_name); ?> ... 
			</td>
		</tr>
		<tr><td colspan='3'>&nbsp;</td></tr>
		<?php if(!empty($p->posts_select)) { ?>
		<tr>
			<td><input type='radio' id='mm-gar-page-type-post' name='mm-gar-page-type' value='post' onchange="mmjs.onTypeChange()" <?php echo ($selectPostsByDefault ? "checked":""); ?> /> </td>
			<td><?php echo _mmt("Post"); ?></td>
			<td>
				<select id='mm-gar-post'>
					<?php 
					echo $p->posts_select;
					?>
				</select>
			</td>
		</tr>
		<tr><td colspan='3'>&nbsp;</td></tr>
		<?php } ?>
		<?php if(!empty($p->pages_select)) { ?>
		<tr>
			<td><input type='radio' id='mm-gar-page-type-page' name='mm-gar-page-type' value='page' onchange="mmjs.onTypeChange()" <?php echo (!$selectPostsByDefault ? "checked":""); ?>/> </td>
			<td><?php echo _mmt("Page"); ?></td>
			<td>
				<select id='mm-gar-page'>
					<?php 
					echo $p->pages_select;
					?>
				</select>
			</td>
		</tr>
		<tr><td colspan='3'>&nbsp;</td></tr>
		<?php } ?>
		<tr>
			<td colspan='3'>
				... on day <input type='text' id='mm-gar-day' value='0'  style='width: 50px;' />
				<input type='hidden' id='mm_access_id' value='<?php echo $p->id; ?>' />
				<input type='hidden' id='mm_access_type' value='<?php echo $p->type; ?>' />
			</td>
		</tr>
	</table>
		
<div class="mm-dialog-footer-container">
<div class="mm-dialog-button-container">
<a href="javascript:mmjs.saveAccessRight();" class="mm-ui-button blue"><?php echo _mmt("Grant Access"); ?></a>
<a href="javascript:mmjs.closeDialog();" class="mm-ui-button"><?php echo _mmt("Cancel"); ?></a>
</div>
</div>
<?php } else { ?>
<?php echo _mmt("No dialog found"); ?>.
<?php } ?>
<script type='text/javascript'>
mmjs.onTypeChange();
</script>