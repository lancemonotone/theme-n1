<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_CaptchaIPNLogView();

if(!empty($_REQUEST["sortby"]))
{
	$dataGrid = new MM_DataGrid($_REQUEST, $_REQUEST["sortby"], "desc", 20);
}
else
{
	$dataGrid = new MM_DataGrid($_REQUEST, "date_added", "desc", 20);
}
$data = $view->getViewData($_REQUEST, $dataGrid);
$dataGrid->setTotalRecords($data);
$dataGrid->recordName = "Entry";

$rows = array();
$headers = array();

foreach($data as $key=>$item)
{	
	// reCAPTCHA Response
	$showResponse = MM_NO_DATA;
	$hasErrors = false;
	
	?>
		<div id="mm-view-info-<?php echo $item->id; ?>" style="display:none;" title="reCAPTCHA Response" style="font-size:11px;">
			<table style="width:100%">
			<?php 
				$rawResponse = json_decode(unserialize($item->raw_response));
				$altRow = false;
				
				if(isset($rawResponse))
				{
    				foreach($rawResponse as $key=>$value)
    				{
    				    if($key == "error-codes")
    				    {
    				        $hasErrors = true;
    				    }
    				    
    					$bkgdColor = "#f9f9f9";
    					
    					if($altRow)
    					{
    						$bkgdColor = "#fff";
    					}
    					
    					echo "<tr style='background-color:{$bkgdColor}'><td style='padding:2px; padding-left:5px; font-size:10px; color:#666'>";
    					echo "<strong>{$key}</strong>";
    					echo "</td><td style='padding:2px; padding-left:5px;'>";
    					echo "<span style='font-family:courier;'>".print_r($value, true)."</span>";
    					echo "</td></tr>";
    					
    					$altRow = !$altRow;
    				}
    				
    				$showResponse = "<a href='javascript:viewInfo({$item->id})'>Show</a>";
				}
			?>
			</table>
		</div>
	<?php

	// Pass/Fail
	$passFail = MM_NO_DATA;
	$name = MM_NO_DATA;
	$score = MM_NO_DATA;
	$actionName = MM_NO_DATA;
	$ipAddress = MM_NO_DATA;
	$checkoutUrl = MM_NO_DATA;
	$email = MM_NO_DATA;
	
	// Name
	$name = "";
	
	if(!empty($item->first_name))
	{
	    $name = $item->first_name." ";
	}
	
	if(!empty($item->last_name))
	{
	    $name .= $item->last_name;
	}
	
	// email link
	$email = $item->email;
	$user = MM_User::findByEmail($email);
	
	if($user->isValid())
	{
	    $email = "<a href='?page=".MM_MODULE_MANAGE_MEMBERS."&module=details_general&user_id=".$user->getId()."'>".$user->getEmail()."</a>";
	}
	
	if(!$hasErrors)
	{
    	if(isset($item->is_accepted))
    	{
    		if(intval($item->is_accepted) == 1)
    		{
    		    $passFail = MM_Utils::getCheckIcon();
    		}
    		else 
    		{
    		    $passFail = MM_Utils::getCrossIcon();
    		}
    	}
    	
        // Score
    	if(!empty($item->score))
    	{
    	    $score = "<span style='font-family:courier;'>{$item->score}</span>";
    	}
    	
    	// Action Name
    	if(!empty($item->action_name))
    	{
    	    $actionName = "<span style='font-family:courier;'>{$item->action_name}</span>";
    	}
    	
    	// IP Address
    	if(!empty($item->ip_address))
    	{
    	    $ipAddress = "<span style='font-family:courier;'><a href='http://www.infosniper.net/index.php?ip_address={$item->ip_address}' target='_blank'>".$item->ip_address."</a></span>";
    	}
	}
	else
	{
	    $passFail = MM_Utils::getIcon('exclamation-triangle', 'red', '1.3em', '1px');
	}
	
	// Checkout URL
	if(!empty($item->checkout_url))
	{
	    $shortUrl = "";
	    $parsedUrl = parse_url($item->checkout_url);
	    
	    if(isset($parsedUrl["path"]))
	    {
	        $shortUrl .= $parsedUrl["path"];
	    }
	    
	    if(isset($parsedUrl["query"]))
	    {
	        $shortUrl .= $parsedUrl["query"];
	    }
	    
	    $checkoutUrl = "<a href='{$item->checkout_url}' target='_blank'>{$shortUrl}</span>";
	}
	
	$row = array();
	$row[] = array('content' => MM_Utils::dateToLocal($item->date_added));
	$row[] = array('content' => $passFail);
	$row[] = array('content' => $score);
	$row[] = array('content' => $name);
	$row[] = array('content' => $email);
	$row[] = array('content' => $checkoutUrl);
	$row[] = array('content' => $ipAddress);
	$row[] = array('content' => $showResponse);
	
	$rows[] = $row;
}

$headers['date_added'] = array('content' => '<a onclick="mmjs.sort(\'date_added\');" href="#">Date</a>', "attr" => "style='width:150px;'");
$headers['is_accepted'] = array('content' => '<a onclick="mmjs.sort(\'is_accepted\');" href="#">Pass/Fail</a>', "attr" => "style='width:50px;'");
$headers['score'] = array('content' => '<a onclick="mmjs.sort(\'score\');" href="#">Score</a>', "attr" => "style='width:50px;'");
$headers['last_name'] = array('content' => '<a onclick="mmjs.sort(\'last_name\');" href="#">Name</a>', "attr" => "style='width:150px;'");
$headers['email'] = array('content' => '<a onclick="mmjs.sort(\'email\');" href="#">Email</a>', "attr" => "style='width:150px;'");
//$headers['action_name'] = array('content' => '<a onclick="mmjs.sort(\'action_name\');" href="#">Action</a>', "attr" => "style='width:150px;'");
$headers['checkout_url'] = array('content' => '<a onclick="mmjs.sort(\'checkout_url\');" href="#">Checkout URL</a>', "attr" => "style='width:150px;'");
$headers['ip_address'] = array('content' => '<a onclick="mmjs.sort(\'ip_address\');" href="#">IP Address</a>', "attr" => "style='width:120px;'");
$headers['raw_response'] = array('content' => 'Response', "attr" => "style='width:80px;'");

$dataGrid->setHeaders($headers);
$dataGrid->setRows($rows);

$dgHtml = $dataGrid->generateHtml();

if($dgHtml == "") 
{
	$dgHtml = "<p><i>No entries found.</i></p>";
}

echo $dgHtml;
?>