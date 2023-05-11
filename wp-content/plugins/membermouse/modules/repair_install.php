<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

global $wp_filesystem,$wpdb;

set_time_limit(300); //in seconds - 5 mins

$cacheDir = MM_Utils::getCacheDir();
if(!is_dir($cacheDir))
{
	//the cache directory (com/membermouse/cache) MUST exist where expected and be a directory
	echo _mmt("Error locating the cache directory. Please create the com/membermouse/cache directory inside the MemberMouse plugin directory and rerun the repair");
}

if (false === ($creds = request_filesystem_credentials($_SERVER["REQUEST_URI"], '', false, $cacheDir, null) ) ) {
	return; // stop processing here
}

if ( ! WP_Filesystem($creds) ) 
{
	//credentials were no good, ask the user for them again
	$error = true;
	if (is_object($wp_filesystem) && $wp_filesystem->errors->get_error_code())
	{
		$error = $wp_filesystem->errors;
	}
	request_filesystem_credentials($_SERVER["REQUEST_URI"], '', $error, $cacheDir, null);
	return true;
}

if (isset($wp_filesystem))
{
	$wpfsCacheDir = MM_PLUGIN_ABSPATH."/com/membermouse/cache";
	if (!$wp_filesystem->exists($wpfsCacheDir)) 
	{
		$wp_filesystem->mkdir($wpfsCacheDir,0777);
	}
	
	$wp_filesystem->chmod($wpfsCacheDir,0777);
	$wp_filesystem->chmod($wpfsCacheDir,0777,true);
}

function restoreOrderItemAccessPatch()
{
	if (class_exists("MM_OrderItem"))
	{
		global $wpdb;
			
		$productAccessLevels = array();
		$accessSQL = "SELECT membership_id as access_type_id, product_id,'membership' AS access_type FROM ".MM_TABLE_MEMBERSHIP_LEVEL_PRODUCTS." ".
				"UNION ALL SELECT bundle_id as access_type_id, product_id, 'bundle' AS access_type FROM ".MM_TABLE_BUNDLE_PRODUCTS;
		$results = $wpdb->get_results($accessSQL);
		foreach ($results as $res)
		{
			$productAccessLevels[$res->product_id] = $res;
		}
			
		$orphanSQL = "SELECT o.user_id, oi.id, oi.item_id FROM ".MM_TABLE_ORDER_ITEMS." oi LEFT JOIN ".MM_TABLE_ORDERS." o ON (oi.order_id = o.id) ".
				"LEFT JOIN ".MM_TABLE_ORDER_ITEM_ACCESS." oia ON (oi.id = oia.order_item_id) ".
				"WHERE (oi.is_recurring = 1) ".
				"AND (oi.status IN (".MM_OrderItem::$STATUS_RECURRING.",".MM_OrderItem::$STATUS_RECURRING_COMPLETE.",".MM_OrderItem::$STATUS_RECURRING_REBILL_FAILED.")) ".
				"AND (oia.access_type_id IS NULL)";
		$results = $wpdb->get_results($orphanSQL);
		foreach ($results as $res)
		{
			if (isset($productAccessLevels[$res->item_id]))
			{
				$access = $productAccessLevels[$res->item_id];
				$wpdb->replace(MM_TABLE_ORDER_ITEM_ACCESS,array("order_item_id"=>$res->id, "user_id"=>$res->user_id, "access_type"=>$access->access_type, "access_type_id"=>$access->access_type_id));
			}
		}
	}
}

function subscriptionPatchColumnExists($table, $column)
{
	global $wpdb;

	$sql = "SELECT COUNT(*) as total FROM information_schema.columns WHERE table_name = '{$table}' AND column_name = '{$column}'";
	$row = $wpdb->get_row($sql);
	return ($row->total>0);
}

function temporaryPendingOverdueSubscriptionPatch()
{	
	if (class_exists("MM_OrderItem") && subscriptionPatchColumnExists(MM_TABLE_AUTHNET_ARB_SUBSCRIPTIONS, "id"))
	{
		global $wpdb;
		
		$pymtServicesTable = MM_TABLE_PAYMENT_SERVICES;
		$authNetToken = MM_PaymentService::$AUTHORIZENET_SERVICE_TOKEN;
		$authNetPaymentId = $wpdb->get_var("SELECT id from {$pymtServicesTable} where token='{$authNetToken}'");
 
		if(!is_null($authNetPaymentId))
		{
			$ordersTable = MM_TABLE_ORDERS;
			$orderItemsTable = MM_TABLE_ORDER_ITEMS;
			$transactionsTable = MM_TABLE_TRANSACTION_LOG;
			$transTypePayment = MM_TransactionLog::$TRANSACTION_TYPE_PAYMENT;
			$transTypeRecurringPayment = MM_TransactionLog::$TRANSACTION_TYPE_RECURRING_PAYMENT;
			$authNetSubscriptionsTable = MM_TABLE_AUTHNET_ARB_SUBSCRIPTIONS;
			$orderItemStatusRecurring = MM_OrderItem::$STATUS_RECURRING;
			$orderItemTypeProduct = MM_OrderItem::$ORDER_ITEM_TYPE_PRODUCT;
				
			$sql = "SELECT oi.id as order_item_id, oi.order_id, oi.rebill_period, oi.rebill_frequency, o.user_id FROM {$orderItemsTable} oi INNER JOIN {$ordersTable} o ON (oi.order_id = o.id) LEFT JOIN {$authNetSubscriptionsTable} arbs ON ".
					"(oi.id = arbs.order_item_id) WHERE o.payment_id='{$authNetPaymentId}' AND oi.status='{$orderItemStatusRecurring}' AND ".
					"oi.item_type='{$orderItemTypeProduct}' AND oi.is_recurring=1 AND (arbs.x_subscription_id IS NULL)";
			$result = $wpdb->get_results($sql);

			foreach($result as $row)
			{
				// check if pending overdue item has already been inserted for this order item
				$pendingOverdueSubscriptions = MM_TABLE_AUTHNET_PENDING_OVERDUE_SUBSCRIPTIONS;
				$pendingOverdueSubscriptionId = $wpdb->get_var("SELECT id from {$pendingOverdueSubscriptions} where order_item_id='{$row->order_item_id}'");

				if (is_null($pendingOverdueSubscriptionId))
				{
					// lookup the last transaction associated with the order item
					$sql = "SELECT transaction_date FROM {$transactionsTable} WHERE order_item_id = {$row->order_item_id} AND ";
					$sql .= "transaction_type IN ({$transTypePayment}, {$transTypeRecurringPayment}) ";
					$sql .= "ORDER BY transaction_date DESC LIMIT 1;";
						
					$lastTransaction = $wpdb->get_row($sql);
					if($lastTransaction)
					{
						// set status of order item to pending overdue
						$orderItem = new MM_OrderItem($row->order_item_id);
							
						if($orderItem->isValid())
						{
							$orderItem->setStatus(5); // set to pending overdue status, not using constant as this script is being run in the installer
							// and on first run the constant may not be defined
							$orderItem->commitData();
								
							// calculate overdue date based on last transaction date and length of subscription cycle
							$overdueDate = date('Y-m-d', strtotime($lastTransaction->transaction_date.' +'.$row->rebill_period.' '.$row->rebill_frequency));
								
							$insertData = array( "order_item_id" => $row->order_item_id, "overdue_date" => $overdueDate); 
							$wpdb->insert(MM_TABLE_AUTHNET_PENDING_OVERDUE_SUBSCRIPTIONS,$insertData);
						}
					}
				}
			}
		}
	} 
}


function populateOriginAffiliateIds()
{
	global $wpdb;

	$crntValue = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_ORIGIN_AFFILIATE_MIGRATED);
	if($crntValue == false || $crntValue == "")
	{
		$excludedStatuses = implode(",",array(MM_Status::$ERROR,MM_Status::$PENDING_ACTIVATION));
		$migrationSQL = "UPDATE ".MM_TABLE_USER_DATA." mu INNER JOIN (SELECT mu1.wp_user_id, mu1.became_active, o.affiliate_id, o.sub_affiliate_id ".
				"FROM ".MM_TABLE_USER_DATA." mu1 INNER JOIN  (SELECT user_id, MIN(id) AS min_order_id FROM ".MM_TABLE_ORDERS." GROUP BY user_id) AS FLATTEN ".
				"ON (mu1.wp_user_id = FLATTEN.user_id) INNER JOIN ".MM_TABLE_ORDERS." o ON (FLATTEN.min_order_id = o.id) WHERE (mu1.status NOT IN ({$excludedStatuses}))".
				"AND (((o.affiliate_id IS NOT NULL) AND (o.affiliate_id != '')) OR ((o.sub_affiliate_id IS NOT NULL) AND (o.sub_affiliate_id != '')))) AS Q ".
				"ON (mu.wp_user_id = Q.wp_user_id) set mu.origin_affiliate_id=Q.affiliate_id, mu.origin_subaffiliate_id=Q.sub_affiliate_id";
		if ($wpdb->query($migrationSQL) !== false)
		{
			MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_ORIGIN_AFFILIATE_MIGRATED, "1");
		}
	}
}


?>
<div class="mm-wrap">
    <p class="mm-header-text"><?php echo _mmt(""); ?>Repair MemberMouse</p>
	<div class="mm-button-container">
	</div>
	
	<div class="clear"></div>
	<div id='mm-repair-content' style='width: 700px; font-size:14px; line-height:26px;'>
		<?php if (!MM_Utils::cacheIsWriteable()) { ?>
		<?php echo _mmt("1. Unable to automatically repair the cache permissions. Please manually change the permissions on the following directory and all files contained within to 777 using an FTP/SSH client. "); ?><br/>
		<?php echo _mmt("Cache directory location"); ?>: <strong><?php echo MM_Utils::getCacheDir(); ?></strong><br/>
		<?php } else { ?>
		<?php echo _mmt("1. Cache directory permissions modified successfully"); ?><br/>
		<?php } ?>
		
		<?php echo _mmt("2. Refreshing cache"); ?>... <br/>
		<em><?php echo _mmt("Please wait"); ?>...</em><br/>
		
		<?php 
		@ob_end_flush();
		@ob_flush();
		@flush();
		@ob_start();
		// refresh cache
		MM_MemberMouseService::authorize();
		?>
		
		<?php echo _mmt("Cache refreshed successfully"); ?><br/>
		<?php echo _mmt("3. Repairing database indexes"); ?>...<br/>
		
		<br/>
		<em><?php echo _mmt("Please wait"); ?></em><br/>
		<?php 
			$tableIndexesToPurge = array("mm_membership_levels" => "name",
										 "mm_commission_profiles" => "name",
										 "mm_orders" => "order_number",
										 "mm_transaction_key" => "transaction_key",
										 "mm_payment_services" => "token",
										 "mm_shipping_methods" => "token",
										 "mm_social_login_providers" => "token");
			
			foreach ($tableIndexesToPurge as $table=>$indexRoot)
			{
				$enumerationQuery= "SHOW INDEXES FROM {$table} WHERE Non_unique=0 AND Key_name LIKE '{$indexRoot}%'";
				$enumerationResults = $wpdb->get_results($enumerationQuery);
				foreach ($enumerationResults as $enumeratedResult)
				{
					$wpdb->query("DROP INDEX {$enumeratedResult->Key_name} ON {$table}");
				}
			}
		?>
		
		<br/>
		<?php echo _mmt("4. Detecting and restoring damaged access links"); ?>...<br/>
		<br/>
		<em><?php echo _mmt("Please wait"); ?></em><br/>
		<?php restoreOrderItemAccessPatch(); ?>
		
		<br/>
		<?php echo _mmt("5. Detecting and restoring missing pending-overdue subscription links"); ?>...<br/>
		<br/>
		<em><?php echo _mmt("Please wait"); ?></em><br/>
		<?php temporaryPendingOverdueSubscriptionPatch(); ?><br/>
		
		<br/>
		<?php echo _mmt("6. Performing maintenance on reporting data"); ?>...<br/>
		<br/>
		<em><?php echo _mmt("Please wait"); ?></em><br/>
		<?php populateOriginAffiliateIds(); ?><br/><br/>
		<?php echo _mmt("DONE"); ?>
	</div>
</div>
