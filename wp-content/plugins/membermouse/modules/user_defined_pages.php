<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_UserDefinedPageView();
$dataGrid = new MM_DataGrid($_REQUEST, "id", "desc", 10);
$data = $view->getViewData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "page";

$rows = array();

foreach($data as $key => $item)
{
    $coupon = new MM_UserDefinedPage($item->id);
	
	$editActionUrl = 'onclick="mmjs.edit(\'mm-page-dialog\', \''.$item->id.'\', 580, 300)"';
	$deleteActionUrl = 'onclick="mmjs.remove(\''.$item->id.'\')"';
	$actions = MM_Utils::getEditIcon("Edit User-Defined Page", '', $editActionUrl);
	$actions .= MM_Utils::getDeleteIcon("Delete User-Defined Page", 'margin-left:5px;', $deleteActionUrl);
    
    $rows[] = array
    (
    		array( 'content' => "<span title='ID [".$coupon->getId()."]'>".$item->name."</span>"),
    		array( 'content' => "<span style='font-family:courier;'>".$item->url."</span>"),
    		array( 'content' => $actions)
    );
}

$headers = array
(	    
   	'name'		=> array('content' => 'Name'),
   	'url'		=> array('content' => 'Page URL'),
   	'actions'	=> array('content' => 'Actions')
);

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>No user-defined pages</i></p>";
}
?>
<div class="mm-wrap">	
	<div style="width:750px;" class="mm-info-box blue">
		<p>Sometimes you may have customer data specific to your business that you'd like to display alongside 
		member information in MemberMouse. User-defined pages give you the opportunity to load this proprietary customer data right 
		from the member details area. This can help streamline your internal processes by allowing your team to access everything 
		about a customer in one place. Read this article for more information on <a href="http://support.membermouse.com/support/solutions/articles/9000020503-user-defined-member-details-pages" target="_blank">creating user-defined pages</a>.</p>
		
		<p>
			MemberMouse will pass data associated with the current member to your user-defined page script. Download the sample script below to
			see what data is available and how to access it:<br/>
			<a href="https://www.dropbox.com/s/545y4rwof6n5ww3/sample_user_defined_page.php?dl=0" target="_blank" class="mm-ui-button orange" style="margin-top:5px;"><?php echo MM_Utils::getIcon('file-code-o', '', '1.2em', '1px'); ?> Download Sample Script</a>
		</p>
	</div>
	
	<div class="mm-button-container">
		<a onclick="mmjs.create('mm-page-dialog', 580, 320)" class="mm-ui-button green"><?php echo MM_Utils::getIcon('plus-circle', '', '1.2em', '1px'); ?> Create User-Defined Page</a>
	</div>

	<div class="clear"></div>
	
	<div style="width:70%">
	<?php echo $dgHtml; ?>
	</div>
</div>