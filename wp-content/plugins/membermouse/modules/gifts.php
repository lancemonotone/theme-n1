<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

global $wpdb;
global $current_user;

$view = new MM_GiftView();
$dataGrid = new MM_DataGrid($_REQUEST, "c.date_added", "desc", 10);
$data = $view->getViewData($dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = _mmt("gift");

$rows = array();

foreach($data as $key => $item)
{
    $purchasedByUser = new MM_User();
    $giftLink = MM_NO_DATA;
    
    if(!empty($item->order_item_id))
    {
        $sql = "SELECT order_id from ".MM_TABLE_ORDER_ITEMS." where id={$item->order_item_id}";
        $result = $wpdb->get_row($sql);
        if($result)
        {
            $order = new MM_Order();
            $purchasedByUser = new MM_User($order->getUserIdByOrderId($result->order_id));
            
            $giftCouponCode = MM_Coupon::getGiftCouponCode($item->order_item_id);
            
            if(!empty($giftCouponCode))
            {
                $orderItem = new MM_OrderItem($item->order_item_id);
                
                if($orderItem->isValid())
                {
                    $giftLink = MM_CorePageEngine::getRedeemGiftPageStaticLink($orderItem->getItemId());
                    $giftLink = MM_Utils::appendUrlParam($giftLink, "coupon", $giftCouponCode);
                }
            }
        }
    }
    
    $purchaseByUserDesc = MM_NO_DATA;
    if($purchasedByUser->isValid())
    {
        $purchaseByUserDesc = "<a href='".MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_MEMBER_DETAILS_GENERAL)."&user_id={$purchasedByUser->getId()}'>".MM_Utils::abbrevString($purchasedByUser->getEmail(), 30)."</a>";
    }
    
    $productGiftedName = MM_NO_DATA;
    if(!empty($item->name))
    {
        $productGiftedName = "<a href='".MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS, MM_MODULE_PRODUCTS)."&autoload=".$item->product_id."' target='_blank'>".$item->name."</a>";
    }
    $redeemDate = MM_NO_DATA;
    $redeemUserDesc = MM_NO_DATA;
    $statusIcon = MM_Utils::getIcon('clock-o', 'blue', '1.4em', '2px', _mmt("Gift pending redemption"));
    
    if(!empty($item->usage_user_id))
    {
        $redeemUser = new MM_User($item->usage_user_id);
        if($redeemUser->isValid())
        {
            $redeemUserDesc = "<a href='".MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS, MM_MODULE_MEMBER_DETAILS_GENERAL)."&user_id={$redeemUser->getId()}'>".MM_Utils::abbrevString($redeemUser->getEmail(), 30)."</a>";
            $redeemDate = MM_Utils::dateToLocal($item->usage_date);
            $statusIcon = MM_Utils::getIcon('check', 'green', '1.4em', '2px', _mmt("Gift redeemed"));
            $giftLink = MM_NO_DATA;
        }
        else 
        {
            // the user who redeemed the coupon is no longer in the system so skip
            continue;
        }
    }
    
    
    $rows[] = array
    (
        array( 'content' => $statusIcon),
        array( 'content'  => MM_Utils::dateToLocal($item->date_gifted)),
        array( 'content' => $purchaseByUserDesc),
        array( 'content' => $productGiftedName),
        array( 'content' => $redeemUserDesc),
        array( 'content' => $redeemDate),
        array( 'content' => "<span style='font-family:courier; font-size:11px;'>".$giftLink."</span>")
    );
}

$headers = array
(
    'status'         => array('content' => "", "attr" => "style='width:20px;'"),
    'c.date_added'   => array('content' => '<a onclick="mmjs.sort(\'c.date_added\');" href="#">'._mmt("Date Purchased").'</a>', "attr" => "style='width:150px;'"),
    ''	             => array('content' => _mmt("Purchased By")),
    'p.name'	     => array('content' => _mmt("Product Gifted")),
    'cu.user_id'	 => array('content' => _mmt("Redeemed By")),
    'cu.date_added'	 => array('content' => '<a onclick="mmjs.sort(\'cu.date_added\');" href="#">'._mmt("Date Redeemed").'</a>', "attr" => "style='width:150px;'"),
    'gift_link'	 => array('content' => _mmt("Gift Link"))
);

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

$giftStatus = MM_OptionUtils::getOption(MM_GiftView::$SEARCH_OPTION_GIFT_STATUS."-".$current_user->ID);

$giftStatusList = [];
$giftStatusList["all"] = _mmt("Show All Gifts");
$giftStatusList["redeemed"] = _mmt("Show Redeemed Gifts Only");
$giftStatusList["pending"] = _mmt("Show Pending Gifts Only");

$giftStatuses = MM_HtmlUtils::generateSelectionsList($giftStatusList, $giftStatus);

if($dgHtml == "") {
	$dgHtml = "<p><i>"._mmt("No gifts").".</i></p>";
}
?>
<div class="mm-wrap">
	<div class="mm-button-container">
		<span style="font-size:11px;">
			<input type='hidden' id='mm-admin-id' value='<?php echo $current_user->ID; ?>' />
			<select id='mm_gift_status' name='mm_gift_status' onchange="mmjs.storeSearchOptions();"><?php echo $giftStatuses; ?></select>
		</span>
	</div>

	<div class="clear"></div>
	
	<div style="width:98%">
	<?php echo $dgHtml; ?>
	</div>
</div>