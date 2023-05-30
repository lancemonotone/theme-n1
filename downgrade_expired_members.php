<?php
// script downloaded from: https://support.membermouse.com/support/solutions/articles/9000055539-automatically-downgrade-to-a-free-membership-when-an-account-expires-or-is-canceled
/*
require_once("wp-load.php");
require_once("wp-content/plugins/membermouse/includes/mm-constants.php");
require_once("wp-content/plugins/membermouse/includes/init.php");
*/
// require_once("../../../../wp-load.php");
// require_once("../includes/mm-constants.php");
// require_once("../includes/init.php");

// ================= START CUSTOMIZATION ====================================

// If you need help finding your API URL, key or secret, read this article:
// http://support.membermouse.com/support/solutions/articles/9000020340-api-credentials-overview

// Your API URL
//$apiUrl = "https://www.nplusonemag.com/wp-content/plugins/membermouse/api/request.php";

// Your API key
$apiKey = "uTG496m";

// Your API secret
$apiSecret = "KJOELKV";

// If you need help finding the membership level ID, read this article:
// http://support.membermouse.com/support/solutions/articles/9000020396-finding-ids-for-membership-levels-products-and-bundles

// The ID of the free membership level to switch the member to
$freeMembershipLevelId = 1;

// ================= END CUSTOMIZATION ======================================
// ==========================================================================


if(!isset($_GET["member_id"]) || empty($_GET["member_id"]))
{
	exit;
}

$memberId = $_GET["member_id"];

$inputParams = "apikey={$apiKey}&apisecret={$apiSecret}&";
$inputParams .= "member_id={$memberId}&";
$inputParams .= "status=1&";
$inputParams .= "membership_level_id={$freeMembershipLevelId}&";

$apiCallUrl = "{$apiUrl}?q=/updateMember";
$ch = curl_init($apiCallUrl);

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $inputParams);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
curl_close($ch);

echo "<pre>".print_r($result, true)."</pre>";
?>
