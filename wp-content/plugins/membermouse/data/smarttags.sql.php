<?php
$sql[] = "INSERT IGNORE INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(1, 0, 'Content', 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(2, 0, 'Core Pages', 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(3, 0, 'Custom Fields', 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(4, 0, 'Employees', 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(5, 0, 'Errors', 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(6, 0, 'Forms', 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(7, 0, 'Members', 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(8, 0, 'Orders', 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(9, 0, 'Decisions', 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(10, 0, 'Products', 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttag_groups` (`id`, `parent_id`, `name`, `visible`) VALUES(11, 0, 'Affiliates', 0);";

$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(100, 1, 'MM_Content_Data', 1, 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(101, 1, 'MM_Content_Link', 1, 1);";

$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(200, 2, 'MM_CorePage_Link', 1, 1);";

$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(300, 3, 'MM_CustomField_Data', 1, 1);";

$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(400, 4, 'MM_Employee_Data', 1, 1);";

$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(500, 5, 'MM_Error_Message', 1, 1);";

$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(600, 6, 'MM_Form', 1, 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(601, 6, 'MM_Form_Section', 1, 0);";
$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(602, 6, 'MM_Form_Field', 1, 0);";
$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(603, 6, 'MM_Form_Data', 1, 0);";
$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(604, 6, 'MM_Form_Message', 1, 0);";
$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(605, 6, 'MM_Form_Button', 1, 0);";
$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(606, 6, 'MM_Form_Subsection', 1, 0);";

$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(700, 7, 'MM_Member_Data', 1, 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(701, 7, 'MM_Member_Link', 1, 1);";

$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(800, 8, 'MM_Order_Data', 1, 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(801, 8, 'MM_Purchase_Link', 1, 1);";

$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(900, 9, 'MM_Access_Decision', 1, 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(901, 9, 'MM_Affiliate_Decision', 1, 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(902, 9, 'MM_Custom_Decision', 1, 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(903, 9, 'MM_Member_Decision', 1, 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(904, 9, 'MM_Order_Decision', 1, 1);";
$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(905, 9, 'MM_Order_Subdecision', 1, 1);";

$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(1000, 10, 'MM_Product_Data', 1, 1);";

$sql[] = "INSERT IGNORE INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`, `autoload`) VALUES(1100, 11, 'MM_Affiliate_Data', 1, 1);";
?>