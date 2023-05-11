<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
	$crntPage = MM_ModuleUtils::getPage();
	$module = MM_ModuleUtils::getModule();
	
	if($user->getFullName() != "") 
	{
		$displayName = $user->getFullName();
	}
	else 
	{
		$displayName = $user->getEmail();
	}
?> 
	<div class="mm-sub-header">
   		<h3>
   			<?php 
   				echo MM_Status::getImage($user->getStatus());
   				
   				if($user->isComplimentary()) 
   				{
   					echo MM_Utils::getIcon('ticket', 'purple', '1.2em', '1px', "Membership is complimentary", "margin-left:4px;");
   				} 
   			?>
   			Member Details for <?php echo $displayName; ?>
   			
   			<?php 
   				echo "<span style='margin-left: 5px; background-color:#fff; padding: 2px 5px 6px; border-radius: 3px; font-size: .9em; box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);'>";   				
   				echo "<span style='color:#9c3;'>".$user->getDaysAsMember()." </span>";
   				echo "<span style='color:#888'><abbr title=\"Days as Member\">Days</abbr></span> ";
   				echo "</span>";
   			
   				$totalPayments = $user->getTotalPayments();
   				$totalRefunds = $user->getTotalRefunds();
   				$totalLCV = $totalPayments - $totalRefunds;
   				
   				$lcvDescription = "";
   				
   				echo "<span style='margin-left: 5px; background-color:#fff; padding: 2px 5px 6px; border-radius: 3px; font-size: .9em; box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);'>";
   				echo "<span style='color:#888'><abbr title=\"Lifetime Customer Value\">LCV</abbr></span> ";
   				if($totalPayments > 0 && $totalRefunds > 0)
   				{
   					$lcvDescription .= _mmf($totalPayments)." Paid";
   					
   					if($totalRefunds > 0)
   					{
   						$lcvDescription .= " - ";
   						$lcvDescription .= _mmf($totalRefunds)." Refunded";
   					}
   					
   					$lcvDescription .= " = "._mmf($totalLCV)." LCV";
   				}
   				
   				echo "<span style='color:#9c3;'><abbr title=\"{$lcvDescription}\">"._mmf($totalLCV)."</abbr></span>";
   				echo "</span>";
   			?>
   		</h3>
   		<p>
   			<?php 
   			echo MM_Utils::getIcon('user', 'blue', '1.2em', '1px', "Membership Level", "margin-right:8px;");
   				
   			if($user->isImported()) 
   			{
   				echo MM_Utils::getIcon('sign-in', 'blue', '1.2em', '1px', "Membership Level (Member Imported)", "margin-right:8px;");
   			} 
   				
   			echo $user->getMembershipName(); 
   				
			$appliedBundles = $user->getAppliedBundleNames();
			
			if(!empty($appliedBundles)) 
			{
				echo MM_Utils::getIcon('cubes', 'yellow', '1.2em', '1px', "Bundles", "margin-left:15px;");
				echo $appliedBundles;
		 	} 
		 	?>
   		</p>
   	</div>
