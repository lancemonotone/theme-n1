<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

/*
 * This script is utilized to export files from our file system (within membermouse). 
 * It is our mechanism to allow customers to download csv files etc. 
 */

require_once(dirname(__FILE__)."/../bootstrap/barebones.php");

function redirectToErrorPage()
{
	$url = MM_CorePageEngine::getUrl(MM_CorePageType::$ERROR, MM_Error::$ACCESS_DENIED);
	wp_redirect($url);
	exit;
}

//-------------------- Attempt to extend time limit and extend memory limit in case there is a lot of data ------------------------------//

set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('memory_limit','128M');

//-------------------- Authenticate the user ------------------------------//

global $current_user;
$userHooks = new MM_UserHooks();
if ($userHooks->checkEmployeeAccess() === false)
{
	redirectToErrorPage();
}   

//-------------------- Setup the filename and file type (header generation also)  ------------------------------//

//TODO: set charset to utf-8
$fileType = "application/csv";
$fileName = "export.csv";

if(isset($_REQUEST["name"]))
{
	header("Content-type: ".$fileType);
	header("Content-Disposition: filename=".$fileName);
	header("Pragma: no-cache");
	
	if($_REQUEST["name"] == "import_members_template")
	{
		$filePath=MM_PLUGIN_ABSPATH."/templates/mm_import_template.csv";
		header("Content-type: ".$fileType);
		header("Content-Disposition: filename=".$fileName);
		header("Pragma: no-cache");
		header("Expires: 0");
		echo file_get_contents($filePath); 
	} 
	else
	{
		redirectToErrorPage();
	}
}
else
{
	redirectToErrorPage();
}
exit;
