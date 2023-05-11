<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
global $wpdb;
global $current_user;

$startDate = MM_Utils::dateToUTC(MM_Utils::getCurrentTime("Y-m-d 00:00",false), "Y-m-d 00:00:00","00:00:00"); 

/* 
 * start calculations
 */

$activeStatus = MM_Status::$ACTIVE;
$pendingCancellationStatus = MM_Status::$PENDING_CANCELLATION;
$canceledStatus = MM_Status::$CANCELED;

$statistics = MM_MemberMouseService::generateStatistics();

$newMembersPaid = 0;
$newMembersFree = 0;

$sql = "SELECT count(1) as members, memberships.is_free FROM ".MM_TABLE_USER_DATA." u, ".MM_TABLE_MEMBERSHIP_LEVELS." memberships ";
$sql .= " WHERE u.membership_level_id=memberships.id AND u.became_active >= '{$startDate}' AND (u.status='{$activeStatus}' OR u.status='{$pendingCancellationStatus}')";
$sql .= " GROUP BY memberships.is_free";
$memberResults = $wpdb->get_results($sql);

if (($memberResults !=null) && is_array($memberResults) && (count($memberResults)>0))
{
	foreach($memberResults as $k=>$memberCount)
	{
		if ($memberCount->is_free == 1)
		{
			$newMembersFree = $memberCount->members;
		}
		else 
		{
			$newMembersPaid = $memberCount->members;
		}
	}
}

$newMbrCancelPaid = 0;
$newMbrCancelFree = 0;

$sql = "SELECT count(1) as members, memberships.is_free FROM ".MM_TABLE_USER_DATA." u, ".MM_TABLE_MEMBERSHIP_LEVELS." memberships ";
$sql .= " WHERE ((u.membership_level_id=memberships.id) and ((u.status='{$canceledStatus}') OR (u.status='".MM_Status::$PAUSED."'))) ";
$sql .= " GROUP BY memberships.is_free";
$cancelResults = $wpdb->get_results($sql);

if (($cancelResults !=null) && is_array($cancelResults) && (count($cancelResults)>0))
{
	foreach($cancelResults as $k=>$memberCount)
	{
		if ($memberCount->is_free == 1)
		{
			$newMbrCancelFree = $memberCount->members;
		}
		else 
		{
			$newMbrCancelPaid = $memberCount->members;
		}
	}
}

$newBundlesPaid = 0;
$newBundlesFree = 0;

$baseSql = "SELECT count(1) as total_bundles FROM ".MM_TABLE_APPLIED_BUNDLES." appBundles, {$wpdb->users} users, ";
$baseSql .= MM_TABLE_BUNDLES." bundles WHERE appBundles.access_type='".MM_AppliedBundle::$ACCESS_TYPE_USER."' AND appBundles.access_type_id = users.id ";
$baseSql .= "AND (appBundles.status='".MM_Status::$ACTIVE."' OR appBundles.status='".MM_Status::$PENDING_CANCELLATION."') AND appBundles.bundle_id = bundles.id AND appBundles.apply_date >= '{$startDate}' ";

// PAID BUNDLES
$result = $wpdb->get_row($baseSql." AND bundles.is_free = '0';");

if($result)
{
	$newBundlesPaid = $result->total_bundles;
}
else
{
	$newBundlesPaid = 0;
}

// FREE BUNDLES
$result = $wpdb->get_row($baseSql." AND bundles.is_free = '1';");

if($result)
{
	$newBundlesFree = $result->total_bundles;
}
else
{
	$newBundlesFree = 0;
}                                                

$newBundleCancelPaid = 0;
$newBundleCancelFree = 0;

$baseSql = "SELECT count(1) as total_bundles FROM ".MM_TABLE_APPLIED_BUNDLES." appBundles, {$wpdb->users} users, ";
$baseSql .= MM_TABLE_BUNDLES." bundles WHERE appBundles.access_type='".MM_AppliedBundle::$ACCESS_TYPE_USER."' AND appBundles.access_type_id = users.id ";
$baseSql .= "AND (appBundles.status='".MM_Status::$CANCELED."' OR appBundles.status='".MM_Status::$PAUSED."') ";
$baseSql .= "AND appBundles.bundle_id = bundles.id ";

// PAID BUNDLES
$result = $wpdb->get_row($baseSql." AND bundles.is_free = '0';");

if($result)
{
	$newBundleCancelPaid = $result->total_bundles;
}
else
{
	$newBundleCancelPaid = 0;
}

// FREE BUNDLES
$result = $wpdb->get_row($baseSql." AND bundles.is_free = '1';");

if($result)
{
	$newBundleCancelFree = $result->total_bundles;
}
else
{
	$newBundleCancelFree = 0;
}                                                                                                                                                                                                                                               

/*
 * end calculations
 */

//reformat start date for display
$startDate = MM_Utils::dateToLocal($startDate);
?>

<div class="mm-view-container">
	<div class="mm-dashboard-header">       
        <div class="logo"><a href="http://membermouse.com/" target="_blank"><img src="https://membermouse.com/assets/plugin_images/membermouse-logo.png" alt="MemberMouse" style="width:340px; padding-top:12px;"></a></div> 
		        <ul class="mm-quick-nav">
	   				<li><a href="<?php echo MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_MEMBERS); ?>"><i class="fa fa-users"></i> <?php echo _mmt("Manage Members"); ?></a></li>
	   				<li><a href="<?php echo MM_ModuleUtils::getUrl(MM_MODULE_MANAGE_TRANSACTIONS); ?>"><i class="fa fa-money"></i> <?php echo _mmt("Browse Transactions"); ?></a></li>
	   				<li><a href="<?php echo MM_ModuleUtils::getUrl(MM_MODULE_REPORTING); ?>"><i class="fa fa-bar-chart-o"></i> <?php echo _mmt("Reporting Suite"); ?></a></li>
	   				<li><a href="<?php echo MM_ModuleUtils::getUrl(MM_MODULE_PRODUCT_SETTINGS); ?>"><i class="fa fa-shopping-cart"></i> <?php echo _mmt("Product Settings"); ?></a></li>
	   			</ul>       
		    </div>
		    
   			<div class="mm-metric-wrapper">
   				<div class="mm-metric-container mm-metric-container-25 blue">
   					<h3 class="metric-header">
   						<i class="fa fa-users"></i> <?php echo _mmt("Membership Snapshot"); ?>
   					</h3>
   					<div class="metric-row">
	   					<?php echo _mmt("Free Members Today"); ?>
	   					<span class="row-value"><?php echo number_format($newMembersFree); ?></span>
	   				</div>
	   				<div class="metric-row">
	   					<?php echo _mmt("Total Free Members"); ?>
	   					<span class="row-value"><?php echo number_format($statistics[MM_MemberMouseService::$USAGE_FREE_MEMBERS]); ?></span>
	   				</div>
	   				<div class="metric-row">
	   					<?php echo _mmt("Paid Members Today"); ?>
	   					<span class="row-value"><?php echo number_format($newMembersPaid); ?></span>
	   				</div>
	   				<div class="metric-row">
	   					<?php echo _mmt("Total Paid Members"); ?>
	   					<span class="row-value"><?php echo number_format($statistics[MM_MemberMouseService::$USAGE_PAID_MEMBERS]); ?></span>
	   				</div>
   				</div>
   				<!--/.mm-metric-container-->
   				
   				<div class="mm-metric-container mm-metric-container-25 blue">
   					<h3 class="metric-header">
   						<i class="fa fa-cubes"></i> <?php echo _mmt("Bundle Snapshot"); ?>
   					</h3>
   					<div class="metric-row">
	   					<?php echo _mmt("Free Bundles Today"); ?>
	   					<span class="row-value"><?php echo number_format($newBundlesFree); ?></span>
	   				</div>
	   				<div class="metric-row">
	   					<?php echo _mmt("Total Free Bundles"); ?>
	   					<span class="row-value"><?php echo number_format($statistics[MM_MemberMouseService::$USAGE_FREE_BUNDLES]); ?></span>
	   				</div>
	   				<div class="metric-row">
	   					<?php echo _mmt("Paid Bundles Today"); ?>
	   					<span class="row-value"><?php echo number_format($newBundlesPaid); ?></span>
	   				</div>
	   				<div class="metric-row">
	   					<?php echo _mmt("Total Paid Bundles"); ?>
	   					<span class="row-value"><?php echo number_format($statistics[MM_MemberMouseService::$USAGE_PAID_BUNDLES]); ?></span>
	   				</div>
   				</div>
   				<!--/.mm-metric-container-->
   				
   				<div class="mm-metric-container mm-metric-container-25 blue">
   					<h3 class="metric-header">
   						<i class="fa fa-shopping-cart"></i> <?php echo _mmt("Product Snapshot"); ?>
   					</h3>
   					<div class="metric-row">
	   					<?php echo _mmt("Products"); ?>
	   					<span class="row-value"><?php echo number_format($statistics[MM_MemberMouseService::$CONFIG_PRODUCTS]); ?></span>
	   				</div>
	   				<div class="metric-row">
	   					<?php echo _mmt("Membership Levels"); ?>
	   					<span class="row-value"><?php echo (number_format($statistics[MM_MemberMouseService::$CONFIG_MEMBERSHIPS_FREE]) + number_format($statistics[MM_MemberMouseService::$CONFIG_MEMBERSHIPS_PAID])) ; ?></span>
	   				</div>
	   				<div class="metric-row">
	   					<?php echo _mmt("Bundles"); ?>
	   					<span class="row-value"><?php echo (number_format($statistics[MM_MemberMouseService::$CONFIG_BUNDLES_FREE]) + number_format($statistics[MM_MemberMouseService::$CONFIG_BUNDLES_PAID])) ; ?></span>
	   				</div>
	   				<div class="metric-row">
	   					&nbsp;
	   				</div>
   				</div>
   				<!--/.mm-metric-container-->
   				
   				<div class="mm-metric-container mm-metric-container-25 blue">
   					<h3 class="metric-header">
   						<i class="fa fa-times"></i> <?php echo _mmt("Retention Snapshot"); ?>
   					</h3>
   					<div class="metric-row">
	   					<?php echo _mmt("Canceled Free Memberships"); ?>
	   					<span class="row-value"><?php echo number_format($newMbrCancelFree); ?></span>
	   				</div>
	   				<div class="metric-row">
	   					<?php echo _mmt("Canceled Paid Memberships"); ?>
	   					<span class="row-value"><?php echo number_format($newMbrCancelPaid); ?></span>
	   				</div>
	   				<div class="metric-row">
	   					<?php echo _mmt("Canceled Free Bundles"); ?>
	   					<span class="row-value"><?php echo number_format($newBundleCancelFree); ?></span>
	   				</div>
	   				<div class="metric-row">
	   					<?php echo _mmt("Canceled Paid Bundles"); ?>
	   					<span class="row-value"><?php echo number_format($newBundleCancelPaid); ?></span>
	   				</div>
   				</div>
   				<!--/.mm-metric-container-->
   				
   			</div>
   			<!--/.mm-metric-wrapper-->
		    
		    <?php 
			    echo "<input type='hidden' id='mm-admin-id' value='{$current_user->ID}' />";
			    $showTrainingVideos = true;
			    
			    // determine if this user's preference is to have the advanced search open
			    $showOptionName = MM_OptionUtils::$OPTION_KEY_SHOW_TRAINING_VIDEOS."-".$current_user->ID;
			    $showOptionValue = MM_OptionUtils::getOption($showOptionName);
			    
			    if($showOptionValue === "0")
			    {
			    	$showTrainingVideos = false;
			    }
		    ?>
   			
   			<div>
				<div class="mm-video-header" style="cursor:pointer;" onclick="mmjs.toggleTrainingVideos();">
			 		<h3 style="color:#fff">
			 			<i class="fa fa-youtube-play" style="left:6px; top:1px;"></i> <?php echo _mmt("Training Videos"); ?>
			 			<a id="hide-training-videos-btn" <?php echo ($showTrainingVideos) ? "style=\"display:none;\"" : ""; ?>><i class="fa fa-chevron-circle-down" style="color:#fff"></i></a>
			 			<a id="show-training-videos-btn" <?php echo ($showTrainingVideos) ? "" : "style=\"display:none;\""; ?>><i class="fa fa-chevron-circle-up" style="color:#fff"></i></a>
			 		</h3>
			 	</div>
			 	<div id="mm-training-videos" <?php echo ($showTrainingVideos) ? "" : "style=\"display:none;\""; ?> class="mm-video-area">
			 		<?php echo MM_TrainingVideosView::displayVideos(); ?>
			 	</div>
			</div>
   			
   			<div class="mm-content-wrapper" style="margin-top:20px">
   				<div class="mm-content-container" style="position:relative;">
   					<h3><i class="fa fa-support" style="top:0px;"></i> <?php echo _mmt("Support Center"); ?></h3>
   					<div style="position:absolute; top:-5px; right:0px;">
   						<a href="<?php echo MM_ModuleUtils::getUrl(MM_MODULE_GENERAL_SETTINGS, MM_MODULE_SAFE_MODE); ?>" class="mm-ui-button white"><i class="fa fa-life-saver"></i> Safe Mode</a>
   					</div>
   					<ul class="spaced-list">
   						<li>
   							<a href="https://support.membermouse.com/" target="_blank"><?php echo _mmt("Knowledge Base"); ?></a> |
   							<a href=" https://membermouse.com/dashboard/guides/" target="_blank"><?php echo _mmt("Getting Started Guides"); ?></a> |
   							<a href="https://membermouse.com/collection/best-practices-guide/" target="_blank"><?php echo _mmt("Best Practices"); ?></a>
   						</li>
   						<?php if(MM_SupportUtils::hasEmailSupport()) { ?>
   						<li><a href="https://membermouse.com/contact-support" target="_blank"><i class="fa fa-envelope"></i> <?php echo _mmt("Contact Support"); ?></a></li>
   						<?php } ?>
   						<?php if(MM_SupportUtils::hasPhoneSupport() && false) { ?>
		   				<li><i class="fa fa-phone"></i> (510) 564-7189</li>
		   				<?php } ?>
   					</ul>
	   				
   					<h3><i class="fa fa-money" style="top:1px;"></i> <?php echo _mmt("Profit Alerts"); ?></h3>
   					<ul class="tips">
   					<?php
   						if(MM_MemberMouseService::hasPermission(MM_MemberMouseService::$FEATURE_REPORTING_SUITE) != MM_MemberMouseService::$ACTIVE)
   						{
   					?>
   						<li><i class="fa fa-check-square"></i> <?php echo _mmt("Learn more about the"); ?> <a href="<?php echo MM_ModuleUtils::getUrl(MM_MODULE_GET_REPORTING); ?>"><?php echo _mmt("Advanced Reporting Suite"); ?></a></li>
   					<?php } ?>
   						<li><i class="fa fa-check-square"></i> <?php echo _mmt("Earn 25% recurring commission with our"); ?> <a href="http://membermouse.com/affiliate-program/" target="_blank"><?php echo _mmt("Affiliate Program"); ?></a></li>
   					</ul>
   					<h3><i class="fa fa-comments"></i> <?php echo _mmt("News"); ?></h3>
   					<div class="mm-news-feed">
   						<?php 
	   			function changeCachePeriod($secs)
	   			{
	   				return 7200;
	   			}
	   			if(function_exists('fetch_feed')) 
	   			{  
        			include_once(ABSPATH . WPINC . '/feed.php'); // the file to rss feed generator  
        			add_filter('wp_feed_cache_transient_lifetime' , 'changeCachePeriod');
        			$feed = fetch_feed('https://membermouse.com/?cat=117&feed=rss2'); // specify the rss feed for category 117 (mm dashboard)
        			remove_filter('wp_feed_cache_transient_lifetime' , 'changeCachePeriod');
        			
        			if (!is_wp_error($feed))
        			{
        				$limit = $feed->get_item_quantity(7); // specify number of items  
        				$items = $feed->get_items(0, $limit); // create an array of items  
        			}
        			else
        			{
        				$limit = 0;
        			}
    			}  
    			
    			if ($limit == 0) 
    			{
    				echo "<p class=\"news-body\">No news at the moment.</p>";  
    			}
    			else 
    			{
    				foreach ($items as $item) 
    				{ 
    			?>  
    					<h4 class="news-header"><a href="<?php echo esc_url($item->get_permalink()); ?>" target="_blank" alt="<?php echo esc_html($item->get_title()); ?>"><?php echo esc_html($item->get_title()); ?></a></h1>  
					    <p class="news-date"><?php echo $item->get_date('j F Y @ g:i a'); ?></p>  
					    <p class="news-body">
					    	<?php 
					    	  $thePostIndex = strpos($item->get_description(), "The post");
					    	  $thePostIndex = ($thePostIndex !== false) ? $thePostIndex : 180;
					    	  echo strip_tags(substr($item->get_description(), 0, $thePostIndex)); 
					    	 ?>
					    </p>
    			<?php 
    				}
    			} 
    			?>  
	   				</div>
	   				<!--/.mm-news-feed-->
   				</div>
   				<!--/.mm-content-container-->
   				
   				<div class="mm-content-container w50">
   					
   					<h3><i class="fa fa-microphone" style="top:1px;"></i><?php echo _mmt("Subscription Entrepreneur Podcast"); ?></h3>
   					<div class="mm-academy-feed">
   						<?php 
	   			if(function_exists('fetch_feed')) 
	   			{  
        			include_once(ABSPATH . WPINC . '/feed.php'); // the file to rss feed generator  
        			add_filter('wp_feed_cache_transient_lifetime' , 'changeCachePeriod');
        			$feed = fetch_feed('https://membermouse.com/feed/?post_type=podcast'); // specify the rss feed  
        			remove_filter('wp_feed_cache_transient_lifetime' , 'changeCachePeriod');
        			
        			if (!is_wp_error($feed))
        			{
        				$limit = $feed->get_item_quantity(7); // specify number of items  
        				$items = $feed->get_items(0, $limit); // create an array of items  
        			}
        			else
        			{
        				$limit = 0;
        			}
    			}  
    			
    			if ($limit == 0) 
    			{
    				echo "<p class=\"news-body\">No Subscription Entrepreneur episodes at the moment.</p>";  
    			}
    			else 
    			{
    				foreach ($items as $item) 
    				{ 
    			?>  
    					<h4 class="news-header"><a href="<?php echo esc_url($item->get_permalink()); ?>" target="_blank" alt="<?php echo esc_html($item->get_title()); ?>"><?php echo esc_html($item->get_title()); ?></a></h4>  
					    <p class="news-date"><?php echo $item->get_date('j F Y @ g:i a'); ?></p>  
					    <p class="news-body">
					    	<?php 
					    	  $thePostIndex = strpos($item->get_description(), "The post");
					    	  $thePostIndex = ($thePostIndex !== false) ? $thePostIndex : 180;
					    	  echo strip_tags(substr($item->get_description(), 0, $thePostIndex)); 
					    	 ?>
					    </p>
    			<?php 
    				}
    			} 
    			?>  
	   				</div>
	   				<!--/.mm-academy-feed-->
   				</div>
   				<!--/.mm-content-container-->
   			</div>
   			<!--/.mm-content-wrapper-->
   	
   	<?php 
   		$minorVersion = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_MINOR_VERSION);
   		if(empty($minorVersion)) { $minorVersion = MM_MemberMouseService::$DEFAULT_MINOR_VERSION; }
   	?>
   	<div class="mm-version"><?php echo _mmt("MemberMouse Version"); ?> <span><?php echo MemberMouse::getPluginVersion()."-".$minorVersion; ?></span></div>
   		
</div>
<!--/.mm-view-container-->
