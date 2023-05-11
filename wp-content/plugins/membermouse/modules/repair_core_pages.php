<?php
/**
 *
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

$view = new MM_RepairCorePagesView();
$missingTypes = $view->findTypesNotDefined();
$missingPages = $view->findMissingCorePages();

$isMissingTypes = !is_array($missingTypes) || (count($missingTypes) > 0);
$hasMissingPages = !is_array($missingPages->typesMissingPages) || (count($missingPages->typesMissingPages) > 0);
$hasTrashedPages = !is_array($missingPages->pagesInTrash) || (count($missingPages->pagesInTrash) > 0);

if ($isMissingTypes)
{
    $missingTypesMsg = _mmt("The following Core Page definitons are missing from the database:")."\n";
    
    $missingTypesMsg .= "<ul class='mm_repair_cp_list'>\n";
    
    foreach($missingTypes as $pageTypeId)
    {
        $missingTypesMsg .= "<li><i>".MM_CorePageType::getCorePageName($pageTypeId)."</i></li>\n";
    }
    
    $missingTypesMsg .= "</ul>\n";
}

if ($hasMissingPages)
{
    $missingPagesMsg = _mmt("The following Core Pages are missing or unassigned: ")."\n";
    
    $missingPagesMsg .= "<ul class='mm_repair_cp_list'>\n";
    
    foreach($missingPages->typesMissingPages as $pageTypeId)
    {
        $missingPagesMsg .= "<li><i>".MM_CorePageType::getCorePageName($pageTypeId)."</i></li>\n";
    }
    
    $missingPagesMsg .= "</ul>\n";
}


if ($hasTrashedPages)
{
    $trashedPagesMsg = _mmt("The following Core Pages are in the trash, which will cause MemberMouse to function incorrectly: ");
    $trashedPagesMsg .= "<ul class='mm_repair_cp_list'>\n";
    
    foreach($missingPages->pagesInTrash as $pageTypeId)
    {
        $trashedPagesMsg .= "<li><i>".MM_CorePageType::getCorePageName($pageTypeId)."</i></li>\n";
    }
    
    $trashedPagesMsg .= "</ul>\n";
}

?>
<div class="mm-wrap">
    <p class="mm-header-text"><?php echo _mmt("Repair Core Pages");?></p>
	
	<?php if (!$isMissingTypes && !$hasMissingPages && !$hasTrashedPages) { ?>
		<div style="width:750px; margin-top: 20px;" class="mm-info-box blue">
			<?php echo _mmt("No problems have been detected"); ?>
		</div>
	<?php } else { ?>
		<div class="mm-info-box yellow" style="width:750px; margin-top:20px;">
			<h3><?php echo _mmt("The following problems have been detected"); ?></h3>
			<?php 
			     if($isMissingTypes)
			     {
			         echo $missingTypesMsg."<br/>\n";
			     }
			     
			     if($hasMissingPages)
			     {
			         echo $missingPagesMsg."<br/>\n";
			     }
			     
			     if($hasTrashedPages)
			     {
			         echo $trashedPagesMsg."<br/>\n";
			     }
			?>
		</div>
	<a onclick="mmjs.repairCorePageProblems();" class="mm-ui-button green"><?php echo _mmt("Repair Core Page Problems"); ?></a>
	<?php } ?>
</div>
