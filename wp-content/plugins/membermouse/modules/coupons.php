<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

global $current_user;

$couponsSupported = MM_PaymentServiceFactory::couponsSupported();

$view = new MM_CouponView();
$dataGrid = new MM_DataGrid($_REQUEST, "id", "desc", 10);
$data = $view->getViewData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "coupon";

$rows = array();

$showArchivedCoupons = (MM_OptionUtils::getOption(MM_CouponView::$SEARCH_OPTION_SHOW_ARCHIVED."-".$current_user->ID) == "1") ? true : false;
foreach($data as $key => $item)
{
    $coupon = new MM_Coupon($item->id);
    
	$availableDates = "";
	$endDate = $coupon->getEndDate(true);
	
	if(!empty($endDate))
	{    	
		$availableDates = $coupon->getStartDate(true)." - ".$endDate;
	}
	else
	{
		$availableDates = "After ". $coupon->getStartDate(true);
	}
	
	switch($coupon->getQuantity())
	{
		case "-1":
		case "":
			$quantityDescription = number_format($item->quantity_used)." used";
            break;
			
		default:
			$quantityDescription = number_format($item->quantity_used)." of ".number_format($coupon->getQuantity())." used ";
			break;
	}
	
	if(!empty($quantityDescription))
	{
	    $quantityDescription = "<a href='?page=".MM_MODULE_LOGS."&module=".MM_MODULE_COUPON_LOG."&coupon_code=".$coupon->getCouponCode()."'>".$quantityDescription."</a>";
	}
	
	$description = "";
	
	switch($coupon->getCouponType())
	{
		case MM_Coupon::$TYPE_PERCENTAGE:
			$description = "<span style='font-family:courier;'>".$coupon->getCouponValue()."%</span> off";
			break;
			
		case MM_Coupon::$TYPE_DOLLAR:
			$description = "<span style='font-family:courier;'>".$coupon->getCouponValue(true)."</span> off";
			break;
			
		case MM_Coupon::$TYPE_FREE:
			$description = "<span style='font-family:courier;'>FREE</span>";
			break;
	}
	
	if($coupon->getCouponType() != MM_Coupon::$TYPE_FREE)
	{
		if($coupon->getRecurringBillingSetting() == "all")
		{
			$description .= " all charges";
		}
		else
		{
			$description .= " the first charge";
		}
	}
	
	$editActionUrl = 'onclick="mmjs.edit(\'mm-coupons-dialog\', \''.$coupon->getId().'\', 620, 615)"';
	$deleteActionUrl = 'onclick="mmjs.remove(\''.$coupon->getId().'\')"';
	$archiveActionUrl = 'onclick="mmjs.archive(\''.$coupon->getId().'\')"';
	$unarchiveActionUrl = 'onclick="mmjs.unarchive(\''.$coupon->getId().'\')"';
	$actions = MM_Utils::getEditIcon("Edit Coupon", '', $editActionUrl);
	$archiveActions = "";
	$archiveStatus = "";
	if(!MM_Coupon::isBeingUsed($coupon->getId()))
    {
		$actions .= MM_Utils::getDeleteIcon("Delete Coupon", 'margin-left:5px;', $deleteActionUrl);
    }
    else 
    {
    	$actions .= MM_Utils::getDeleteIcon("This coupon is currently being used and cannot be deleted.", 'margin-left:5px;', '', true); 
    }
    
	if(!$coupon->isArchived())
	{
		$archiveMsg = "Archive this coupon to hide it and disable it from being used.";
		$archiveActions .= MM_Utils::getArchiveIcon($archiveMsg, 'margin-left:5px;', $archiveActionUrl);
	}
	else if($coupon->isArchived())
	{
		$archiveStatus = "&nbsp;".MM_Utils::getIcon('archive', 'grey', '1em', '2px', 'Archived');
		$archiveMsg = "Unarchive this coupon to enable it to be used";
		$archiveActions .= MM_Utils::getArchiveIcon($archiveMsg, 'margin-left:5px;', $unarchiveActionUrl, true);
	}
	if($showArchivedCoupons)
	{
		$rows[] = array
		(
				array( 'content'  => $coupon->getId()),
				array( 'content' => "<span title='ID [".$coupon->getId()."]'>".$coupon->getCouponName()."</span>"),
				array( 'content' => "<span style='font-family:courier;'>".strtoupper($coupon->getCouponCode())."</span>"),
				array( 'content' => $description),
				array( 'content' => $quantityDescription),
				array( 'content' => $availableDates),
				array( 'content' => (empty($item->product_restrictions) ? MM_NO_DATA : $item->product_restrictions)), 
				array( 'content' => $archiveStatus),
				array( 'content' => $actions. $archiveActions)
		);
	} 
	else
	{ 
		$rows[] = array
		(
				array( 'content'  => $coupon->getId()),
				array( 'content' => "<span title='ID [".$coupon->getId()."]'>".$coupon->getCouponName()."</span>"),
				array( 'content' => "<span style='font-family:courier;'>".strtoupper($coupon->getCouponCode())."</span>"),
				array( 'content' => $description),
				array( 'content' => $quantityDescription),
				array( 'content' => $availableDates),
				array( 'content' => (empty($item->product_restrictions) ? MM_NO_DATA : $item->product_restrictions)),  
				array( 'content' => $actions. $archiveActions)
		);
	}
}

$headers = array
(
	'id'                    => array('content' => '<a onclick="mmjs.sort(\'id\');" href="#">'._mmt("ID").'</a>'),
	'name'					=> array('content' => '<a onclick="mmjs.sort(\'c.coupon_name\');" href="#">'._mmt("Name").'</a>'),
	'coupon_code'			=> array('content' => '<a onclick="mmjs.sort(\'c.coupon_code\');" href="#">'._mmt("Coupon Code").'</a>'),
	'description'			=> array('content' => _mmt('Description')),
	'quantity_used'			=> array('content' => '<a onclick="mmjs.sort(\'quantity_used\');" href="#"># '._mmt("Used").'</a>'),
	'start_date_end_date'	=> array('content' => '<a onclick="mmjs.sort(\'c.start_date\');" href="#">'._mmt("Valid Dates").'</a>'),
	'product_restrictions'	=> array('content' => _mmt('Product Restrictions')), 
	'actions'				=> array('content' => _mmt('Actions'), "attr" => "style='width:70px;'")
);
if($showArchivedCoupons)
{
	$headers = array
	(
		'id'                    => array('content' => '<a onclick="mmjs.sort(\'id\');" href="#">'._mmt("ID").'</a>'),
		'name'					=> array('content' => '<a onclick="mmjs.sort(\'c.coupon_name\');" href="#">'._mmt("Name").'</a>'),
		'coupon_code'			=> array('content' => '<a onclick="mmjs.sort(\'c.coupon_code\');" href="#">'._mmt("Coupon Code").'</a>'),
		'description'			=> array('content' => _mmt('Description')),
		'quantity_used'			=> array('content' => '<a onclick="mmjs.sort(\'quantity_used\');" href="#"># '._mmt("Used").'</a>'),
		'start_date_end_date'	=> array('content' => '<a onclick="mmjs.sort(\'c.start_date\');" href="#">'._mmt("Valid Dates").'</a>'),
		'product_restrictions'	=> array('content' => _mmt('Product Restrictions')),
		'status'				=> array('content' => _mmt('Archived'), "attr" => ""),
		'actions'				=> array('content' => _mmt('Actions'), "attr" => "style='width:70px;'")
	);
} 

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") {
	$dgHtml = "<p><i>"._mmt("No coupons").".</i></p>";
}
?>
<div class="mm-wrap">
	<?php if(MM_Response::isError($couponsSupported)) { ?>
	<div class="error">
		<p><?php echo $couponsSupported->message; ?></p>
	</div>
	<?php } ?>
	
	<div class="mm-button-container">
		<a onclick="mmjs.create('mm-coupons-dialog', 620, 645)" class="mm-ui-button green"><?php echo MM_Utils::getIcon('plus-circle', '', '1.2em', '1px'); ?> <?php echo _mmt("Create Coupon"); ?></a>
		
		<a href="<?php echo MM_ModuleUtils::getUrl(MM_MODULE_LOGS, MM_MODULE_COUPON_LOG); ?>" class='mm-ui-button'>
        	<i class="fa fa-ticket"></i> <?php echo _mmt("View Coupon Log"); ?>
        </a>
		
		<?php 
			$showExpiredCoupons = (MM_OptionUtils::getOption(MM_CouponView::$SEARCH_OPTION_SHOW_EXPIRED."-".$current_user->ID) == "1") ? true : false;
		?>
		
		<span style="font-size:11px;">
			<input type='hidden' id='mm-admin-id' value='<?php echo $current_user->ID; ?>' />
			<input type='checkbox' id='mm-show-archived-coupons' <?php echo ($showArchivedCoupons ? "checked":""); ?> style="margin-left:10px;" onclick="mmjs.storeCouponSearchOptions();" /> <?php echo _mmt("Show archived coupons"); ?>
			<input type='checkbox' id='mm-show-expired-coupons' <?php echo ($showExpiredCoupons ? "checked":""); ?> style="margin-left:10px;" onclick="mmjs.storeCouponSearchOptions();" /> <?php echo _mmt("Show expired coupons"); ?>
		</span>
	</div>

	<div class="clear"></div>
	
	<div style="width:98%">
	<?php echo $dgHtml; ?>
	</div>
</div>