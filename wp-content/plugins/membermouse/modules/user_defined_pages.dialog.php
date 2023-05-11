<?php
$id = 0; 

if(isset($p->id))
{
	$id = $p->id;
}

$udPage = new MM_UserDefinedPage($id);
?>
<div id="mm-pages-container">
	<table cellspacing="10">
		<tr>
			<td width="140">Name*</td>
			<td>
				<input type='hidden' id='id' value='<?php echo $udPage->getId(); ?>' />
				<input id="mm_page_name" type="text" value='<?php echo $udPage->getName(); ?>' style='width:300px;' />
			</td>
		</tr>
		<tr>
			<td width="140">Page URL*</td>
			<td>
				<input id="mm_page_url" type="text" value='<?php echo $udPage->getUrl(); ?>' style='width:400px;' />
			</td>
		</tr>
		</table>
	</div>
	
<div class="mm-dialog-footer-container">
<div class="mm-dialog-button-container">
<a href="javascript:mmjs.createPage();" class="mm-ui-button blue">Save User-Defined Page</a>
<a href="javascript:mmjs.closeDialog();" class="mm-ui-button">Cancel</a>
</div>
</div>