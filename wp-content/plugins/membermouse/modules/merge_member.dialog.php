<style>
	.ui-widget {
		font-family: sans-serif;
	}

	.mm-small-text {
		font-size: .8em;
		font-weight: 400;
	}

	.mm-edit-section {
		padding: 15px;
		margin: 0 0 15px;
		border: 1px solid #ccc;
		border-radius: 3px;
	}

	.mm-edit-section .section-content {
		padding: 15px 0 0;
	}

	.mm-edit-section .section-content.content-hidden {
		display: none;
	}

	.mm-edit-title {
		font-size: 1.1em;
		margin: 0 0 10px;
	}

	.mm-edit-title a {
		color: #066cd2 !important;
	}

	.mm-edit-title a:hover {
		color: #000 !important;
	}

	.mm-edit-list {
		list-style: square;
		margin: 0 0 20px 20px;
	}

	/* tables */
	.mm-data-table {
		display: table;
		width: 100%;
		font-size: .9em;
		border-collapse: collapse;
		margin: 0 0 20px;
	}

	.mm-data-table th,
	.mm-data-table td {
		padding: 8px;
		border: 1px solid #ddd;
		text-align: left;
		vertical-align: top;
	}

	.mm-data-table tbody th,
	.mm-data-table tbody td:first-child {
		width: 150px;
	}

	.mm-edit-table {
		display: table;
		width: 100%;
		border-collapse: collapse;
	}

	.mm-edit-table th,
	.mm-edit-table td {
		padding: 10px;
		text-align: left;
		vertical-align: top;
	}

	.mm-edit-table thead {
		background: #f5f5f5;
	}

	.mm-edit-table tbody th,
	.mm-edit-table tbody td:first-child {
		width: 250px;
	}

	.mm-edit-table .table-label {
		font-size: 1em;
		margin: 0 0 5px;
	}

	.mm-edit-table .table-desc {
		font-size: .85em;
		margin: 0 0 10px;
	}

	/* accordion */
	.mm-edit-accordion {
		display: block;
		font-size: 1.2em;
		font-weight: 700;
		color: #066cd2 !important;
		text-decoration: none;
	}

	.mm-edit-accordion:hover {
		color: #000 !important;
	}

	/* inputs */
	.mm-input-container {
		margin: 0 0 15px;
	}

	.mm-edit-input {
		width: 100%;
		padding: 10px;
	}

	/* input option */
	.mm-edit-input-option {
		display: block;
		position: relative;
		padding-left: 25px;
		margin: 0 0 15px;
	}

	.mm-edit-input-option input {
		position: absolute;
		top: 0;
		left: 0;
		margin: 0;
	}

	.mm-edit-input-option .option-label {
		display: block;
		font-weight: 300;
		margin: 0 0 5px;
	}

	.mm-edit-input-option .option-desc {
		display: block;
		font-size: .85em
	}

	.mm-ui-button .grey {
		pointer-events: none;
	}

	.accordian-edit-link {
		cursor: pointer;
		margin-left:10px;
	}
	
	.mm-row-changed {
	   background-color:#f2f8ff;
	   border: 2px solid #c1ddfd;
	}
	
	.mm-warning-box {
	   background-color:#f6b91b26;
	   border: 2px solid #f6b91b;
	   border-radius: 5px;
	   padding: 10px;
       margin: 20px 0px 20px 0px;
	}
	
	ul {
       list-style-type: disc;
       padding-inline-start: 40px;
	}
</style>
<script type='text/javascript'>
    /**
     * Ajax textbox / dropdown search feature 
     */
	jQuery(function() {
		jQuery("#secondary_user_id_field").autocomplete({
			source: function(request, response) {
				mmjs.findMembersToMerge(response, request.term);
			},
			minLength: 2,
			select: function(event, ui) {
				event.preventDefault();
				jQuery("#secondary_user_id").val(ui.item.value);
				jQuery("#secondary_user_id_field").val(ui.item.label);
			},
			focus: function(event, ui) {
				event.preventDefault();
				jQuery("#secondary_user_id").val(ui.item.value);
				jQuery("#secondary_user_id_field").val(ui.item.label);
			}
		});
	});
	
    /**
     * Open close / accordian feature initiation 
     */
	jQuery(document).ready(function() {
		// Accordion
		jQuery('a[data-toggle="accordion"]').on('click', function(e) {
			e.preventDefault();

			var accordionTarget = jQuery(this).attr("href");

			hideAllSections();
			var compareUserId = jQuery("#secondary_user_id_field").val();
			if (compareUserId == undefined || compareUserId == null || compareUserId == "") {
				alert("<?php echo _mmt("Please choose a member to merge first"); ?>");
				return;
			}
 
			jQuery(accordionTarget).slideToggle(250);
		});
	});

	// Action switching
	// used with HTML select dropdowns
	jQuery('.js-action-switch').on('change', function() {
		var actionTarget = jQuery(this).find(':selected').data("action");
		var actionFamilyClass = jQuery(this).find(':selected').data("family-class");
		jQuery('.' + actionFamilyClass).hide();
		jQuery("#" + actionTarget).toggle(); 
	});

	// Content Toggling
	jQuery('.js-content-toggle').on('click', function() {
		var contentTarget = jQuery(this).data("content");
		jQuery("#" + contentTarget).toggle();
	});

	// More content
	jQuery('.js-more-content').on('click', function(e) {
		e.preventDefault();

		var contentTarget = jQuery(this).closest('.js-content-limit');
		contentTarget.addClass("content-expand");
		jQuery(this).hide();
	});

	// Option Toggling
	jQuery('.js-option-toggle').on('change', function() {
		var toggleTarget = jQuery(this).data("option");
		jQuery("#" + toggleTarget).toggle();
	});

	// Overlays
	jQuery('a[data-toggle="overlay"]').on('click', function(e) {
		e.preventDefault();

		var overlayTarget = jQuery(this).attr("href");
		jQuery(overlayTarget).toggleClass("visible");
	});

	// Status Toggling
	jQuery('.js-status-toggle').on('click', function() {
		jQuery(this).toggleClass("status-active");
	});

	// Switch Toggling
	jQuery('.js-switch-toggle').on('change', function() {
		var toggleTarget = jQuery(this).data("switch");
		var toggleFamilyClass = jQuery(this).data("family-class");
		jQuery('.' + toggleFamilyClass).hide();
		jQuery("#" + toggleTarget).toggle();
	});

	// Tabs
	jQuery('.js-tabs li a').on('click', function(e) {
		e.preventDefault();

		// toggle tab containers
		var tabTarget = jQuery(this).attr("href");
		var tabFamily = jQuery(this).data("tab");
		jQuery('.' + tabFamily).removeClass("active");
		jQuery(tabTarget).addClass("active");

		// highlight active tab
		jQuery(this).closest('.js-tabs').find('li').removeClass("active");
		jQuery(this).parent('li').addClass("active");
	});

	// Tabs
	jQuery('a[data-toggle="tab"]').on('click', function(e) {
		e.preventDefault();

		var tabTarget = jQuery(this).attr("href");

		// active class highlighting
		var tabFamily = jQuery(this).closest('.mmuix-tabs');
		var tabSiblings = tabFamily.find('li a');

		tabSiblings.removeClass("active");
		jQuery(this).addClass("active");

		// tab content
		var tabContentFamily = jQuery(tabTarget).closest('.mmuix-tab-content');
		var tabContentSiblings = tabContentFamily.find('.mmuix-tab-pane');

		// tab interactions
		tabContentSiblings.hide();
		jQuery(tabTarget).fadeIn(250);
	});
	
	// DIALOG CONTROLS & SECTION NAVIGATION
	function cancelMerge()
	{  
		mmjs.closeDialog(); 
	}
	
	function startOver() {
		jQuery("#start_over").hide();
		jQuery("#compare_btn").show();
		jQuery("#compare_cancel").show();
		jQuery("#config").hide();
		
		jQuery("#secondary_user_id_field").val("");
		jQuery("#secondary_user_id_field").removeAttr("readonly");
		jQuery("#secondary_user_id_field").focus();
		
		hideAllSections();
		
		jQuery("#general_div").hide();
		jQuery("#custom_div").hide();
		jQuery("#access_div").hide(); 
	}

	function hideAllSections() {
		jQuery("#general-options").attr("style", "display:none");
		jQuery("#custom-fields").attr("style", "display:none"); 
		jQuery("#access-options").attr("style", "display:none");
		jQuery("#preview").hide();
	}
	
	function showSection(name) {
		hideAllSections();
		
		jQuery("#compare_btn").hide();
		jQuery("#compare_cancel").hide();
		jQuery("#start_over").show();
		jQuery("#config").show();
		
		switch(name)
		{
			case "general":
				jQuery("#general_div").show();
        		jQuery("#general-options").attr("style", "display:block");
        		jQuery("#general-options").focus();
        		break;
        		
        	case "custom_fields":
        		jQuery("#custom_div").show();
        		jQuery("#custom-fields").attr("style", "display:block");
        		jQuery("#custom-fields").focus();
        		break;
        		
        	case "access_rights":
        		jQuery("#access_div").show();
        		jQuery("#access-options").attr("style", "display:block");
        		jQuery("#access-options").focus();
        		break;
        }
	}
</script>
<!--
START CUSTOM MERGE MEMBER UI/UX
-->

<div id="mm-edit-merge-dialog">
	<div id="mm-main-container">
		<input type='hidden' id='user_id' value='<?php echo $p->user_id ?>' />
		<input type='hidden' id='secondary_user_id' value='' />
		
		<!-- START custom HTML -->
		<h2><?php echo _mmt("Step 1: Select Member to Merge"); ?></h2>
		<p><?php echo _mmt("Select the member to merge with this account."); ?></p>
		<div class="mm-input-container">
			<input type="text" class="mm-edit-input" id="secondary_user_id_field" name="secondary_user_id_field" placeholder="Enter member name or email address" />
		</div>
		<!--/.mm-input-container-->

		<input type="submit" disabled="disabled" class="mm-ui-button grey" value="<?php echo _mmt("Continue");?>" onclick="mmjs.getMergeUserData()" id="compare_btn" />
		<input type="button" id="compare_cancel" class="mm-ui-button" value="<?php echo _mmt("Cancel");?>" onclick="cancelMerge()" />
		<a style="cursor:pointer; display:none;" class="mm-ui-button" id="start_over" onclick="startOver()"><?php echo _mmt("Start Over");?></a>

		<div id="config" style="display:none">
		<hr />
		<h2><?php echo _mmt("Step 2: Merge Options"); ?></h2>
		<p><?php echo _mmt("Below you'll find all the data, access rights, subscriptions, etc. presented in two columns with the current account on left
		and the secondary account on the right. Select the radio button or checkbox next to any item you'd like to transfer from the secondary
		account to the current account."); ?></p>
		
		<div class="mm-edit-section" id="general_div" style="display:none">
		<a href="#general-options" data-toggle="accordion" class="mm-edit-accordion"> <?php echo _mmt("General"); ?></a>
			<div class="section-content content-hidden" id="general-options" style="display: none;">
				<table class="mm-edit-table">
					<thead>
						<tr>
							<th><?php echo _mmt("Account Information"); ?></th>
							<th><?php echo _mmt("Current Account"); ?><br /><span class="mm-small-text" class="current_user_class" id="first_user_id"></span></th>
							<th><?php echo _mmt("Secondary Account"); ?><br /><span class="mm-small-text" class="second_user_class" id="second_user_id"></span></th>
						</tr>
					</thead>
					<tbody>
						<tr id="fname_row">
							<th><?php echo _mmt("First Name"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-first_name" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="fname1"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-first_name" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="fname2"></span>
								</label>
							</td>
						</tr>
						<tr id="lname_row">
							<th><?php echo _mmt("Last Name"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-last_name" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="lname1"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-last_name" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="lname2"></span>
								</label>
							</td>
						</tr>
						<tr id="email_row">
							<th><?php echo _mmt("Email"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-email" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="email1"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-email" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="email2"></span>
								</label>
							</td>
						</tr>
						<tr id="username_row">
							<th><?php echo _mmt("Username"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-username" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="username1"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-username" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="username2"></span>
								</label>
							</td>
						</tr> 
						<tr id="password_row">
							<th><?php echo _mmt("Password"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-password" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' disabled />
									<span class="option-label" id="password1">**********</span>
								</label>
								<span style="font-size:11px; margin-left:20px"><?php echo MM_Utils::getInfoIcon(); ?> <em>Passwords cannot be transferred</em></span>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-password" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" disabled />
									<span class="option-label" id="password2">**********</span>
								</label>
							</td>
						</tr>
						<tr id="phone_row">
							<th><?php echo _mmt("Phone"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-phone" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="phone1"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-phone" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="phone2"></span>
								</label>
							</td>
						</tr>
						<thead id="billing_rows">
							<tr>
								<th colspan='3'><?php echo _mmt("Billing Address"); ?></th>
							</tr>
						</thead>
						<tr id="billing_address1_row">
							<th><?php echo _mmt("Address 1"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-billing-address" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="billing_address1"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-billing-address"  value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="billing_address2"></span>
								</label>
							</td>
						</tr>
						<tr id="billing_address2_row">
							<th><?php echo _mmt("Address 2"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-billing-address2" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="billing_address21"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-billing-address2" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="billing_address22"></span>
								</label>
							</td>
						</tr>
						<tr id="billing_city_row">
							<th><?php echo _mmt("City"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-billing-city" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="billing_city1"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-billing-city" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="billing_city2"></span>
								</label>
							</td>
						</tr>
						<tr id="billing_state_row">
							<th><?php echo _mmt("State"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-billing-state" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="billing_state1"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-billing-state" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="billing_state2"></span>
								</label>
							</td>
						</tr>
						<tr id="billing_zip_row">
							<th><?php echo _mmt("Postal Code"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-billing-zip" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="billing_zip1"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-billing-zip" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="billing_zip2"></span>
								</label>
							</td>
						</tr>
						<tr id="billing_country_row">
							<th><?php echo _mmt("Country"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-billing-country" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="billing_country1"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-billing-country" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="billing_country2"></span>
								</label>
							</td>
						</tr>
						<thead id="shipping_rows">
							<tr>
								<th colspan='3'><?php echo _mmt("Shipping Address"); ?></th>
							</tr>
						</thead>
						<tr id="address1_row">
							<th><?php echo _mmt("Address 1"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-address"  value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="address1"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-address" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="address2"></span>
								</label>
							</td>
						</tr>
						<tr id="address2_row">
							<th><?php echo _mmt("Address 2"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-address2" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="address21"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-address2" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="address22"></span>
								</label>
							</td>
						</tr>
						<tr id="city_row">
							<th><?php echo _mmt("City"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-city" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="city1"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-city" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="city2"></span>
								</label>
							</td>
						</tr>
						<tr id="state_row">
							<th><?php echo _mmt("State"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-state" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="state1"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-state" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="state2"></span>
								</label>
							</td>
						</tr>
						<tr id="zip_row">
							<th><?php echo _mmt("Postal Code"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-zip" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="zip1"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-zip" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="zip2"></span>
								</label>
							</td>
						</tr>
						<tr id="country_row">
							<th><?php echo _mmt("Country"); ?></th>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-country" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="country1"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="general-option-country" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="country2"></span>
								</label>
							</td>
						</tr>
					</tbody>
				</table>
				<input type="submit" class="mm-ui-button blue" value="<?php echo _mmt("Next"); ?>" onclick="showSection('custom_fields')" />
				<input type="button" class="mm-ui-button" value="<?php echo _mmt("Back"); ?>" onclick="startOver()" />
				<input type="button" class="mm-ui-button" value="<?php echo _mmt("Cancel"); ?>" onclick="cancelMerge()" />
			</div>
			<!--/.section-content-->
		</div>
		<!--/.mm-edit-section--> 

		<div class="mm-edit-section" id="custom_div" style="display:none">
		<a href="#custom-fields" data-toggle="accordion" class="mm-edit-accordion"><?php echo _mmt("Custom Fields"); ?></a>
			<div class="section-content content-hidden" id="custom-fields" style="display: none;">
				<table class="mm-edit-table">
					<thead>
						<tr>
							<th></th>
							<th><?php echo _mmt("Current Account"); ?><br /><span class="mm-small-text" class="current_user_class" id="first_user_id"></span></th>
							<th><?php echo _mmt("Secondary Account"); ?><br /><span class="mm-small-text" class="second_user_class" id="second_user_id"></span></th>
						</tr>
					</thead>
					<tbody id="custom_fields">

					</tbody>
				</table>
				<input type="submit" class="mm-ui-button blue" value="<?php echo _mmt("Next"); ?>" onclick="showSection('access_rights')" />
				<input type="button" class="mm-ui-button" value="<?php echo _mmt("Back"); ?>" onclick="showSection('general')" />
				<input type="button" class="mm-ui-button" value="<?php echo _mmt("Cancel"); ?>" onclick="cancelMerge()" />
			</div>
			<!--/.section-content-->
		</div>
		<!--/.mm-edit-section--> 

		<div class="mm-edit-section" id="access_div" style="display:none">
		<a href="#access-options" data-toggle="accordion" class="mm-edit-accordion"> <?php echo _mmt("Access Rights, Subscriptions &amp; Payment Info"); ?></a>
			<div class="section-content content-hidden" id="access-options" style="display: none;">
				<table class="mm-edit-table">
					<thead>
						<tr>
							<th></th>
							<th><?php echo _mmt("Current Account"); ?><br /><span class="mm-small-text" class="current_user_class" id="first_user_id"></span></th>
							<th><?php echo _mmt("Secondary Account"); ?><br /><span class="mm-small-text" class="second_user_class" id="second_user_id"></span></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<h6 class="table-label"><?php echo _mmt("Membership Level"); ?></h6>
								<p class="table-desc"><?php echo _mmt("Select the membership level you want applied to the current account. Any active subscriptions on the membership not selected will be cancelled."); ?></p>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="option-membership" value="<?php echo MM_MergeAccountUtils::$CHOICE_PRIMARY; ?>" checked='checked' />
									<span class="option-label" id="membership_level1"></span>
									<span class="option-desc" id="membership_description1"></span>
								</label>
							</td>
							<td>
								<label class="mm-edit-input-option">
									<input type="radio" name="option-membership" value="<?php echo MM_MergeAccountUtils::$CHOICE_SECONDARY; ?>" />
									<span class="option-label" id="membership_level2"></span>
									<span class="option-desc" id="membership_description2"></span>
								</label>
							</td>
						</tr>
						<tr id="bundles">
							<td>
								<h6 class="table-label"><?php echo _mmt("Bundles"); ?></h6>
								<p class="table-desc"><?php echo _mmt("Select the bundles that you would like to transfer to the current account. Any active subscriptions on bundles that aren't transferred will be cancelled."); ?></p>
							</td>
						</tr>
						<tr id="products">
							<td>
								<h6 class="table-label"><?php echo _mmt("Products"); ?></h6>
								<p class="table-desc"><?php echo _mmt("These products are not associated with a membership level or bundle. Select the products that you would like to transfer to the current account. Any active subscriptions on products that aren't transferred will be cancelled."); ?></p>
							</td>
						</tr>
						<tr id="card_on_file">
							<td>
								<h6 class="table-label"><?php echo _mmt("Credit Card on File"); ?></h6>
								<p class="table-desc"><?php echo _mmt("Select the credit card that should be billed on all future payments. The credit card not chosen will be deleted."); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
				<input type="submit" class="mm-ui-button blue" value="<?php echo _mmt("Preview Merged Account"); ?>" onclick="mmjs.generatePreview()" />
				<input type="button" class="mm-ui-button" value="<?php echo _mmt("Back"); ?>" onclick="showSection('custom_fields')" />
				<input type="button" class="mm-ui-button" value="<?php echo _mmt("Cancel"); ?>" onclick="cancelMerge()" />
			</div>
			<!--/.section-content-->
		</div>
		<!--/.mm-edit-section-->
		</div>

		<div id="preview" style="display:none">
			<hr />

			<h2><?php echo _mmt("Step 3: Preview &amp; Confirm Merge");?></h2>
			<p><?php echo _mmt("Below is a preview of the changes that will be made to the current account if the merge is confirmed."); ?></p>
			<p>
			<?php 
			     echo MM_Utils::getInfoIcon()."&nbsp;"; 
			     echo sprintf(_mmt("Rows %shighlighted in blue%s indicate changes to the current account."),'<span class="mm-row-changed" style="padding:5px;"><strong>', '</strong></span>'); 
			?> 
			</p>
			
			<h5 class="mm-edit-title"><?php echo _mmt("General");?> <a class="accordian-edit-link" onclick="showSection('general')"><?php echo _mmt("edit"); ?> <i class="fa fa-pencil"></i></a></h5>
			<table class="mm-data-table">
				<tbody id="preview_general">
				</tbody>
			</table>

			<h5 class="mm-edit-title"><?php echo _mmt("Custom Fields");?> <a class="accordian-edit-link" onclick="showSection('custom_fields')"><?php echo _mmt("edit"); ?> <i class="fa fa-pencil"></i></a></h5>
			<table class="mm-data-table">
				<tbody id="preview_custom_fields">
				</tbody>
			</table>

			<h5 class="mm-edit-title"><?php echo _mmt("Access Rights, Subscriptions &amp; Payment Info");?> <a class="accordian-edit-link" onclick="showSection('access_rights')"><?php echo _mmt("edit"); ?> <i class="fa fa-pencil"></i></a></h5>
			<table class="mm-data-table">
				<tbody id="preview_access_rights">
				</tbody>
			</table>
			<div class="mm-warning-box">
			<?php 
			
			// get a list of offsite services
			$offsiteServices = array();
			
			$services = MM_PaymentServiceFactory::getAvailablePaymentServices();
		
			foreach ($services as $service)
			{
			    if ($service instanceof MM_PaymentService)
			    {
			        if($service->supportsFeature(MM_PaymentServiceFeatures::OFFSITE_SERVICE) && ($service->getToken() !== MM_PaymentService::$TEST_SERVICE_TOKEN))
			        {
			            array_push($offsiteServices, $service->getName());
			        }
			    }
			}
			
			$hasOffsiteService = (count($offsiteServices) > 0);
			?>
			<?php echo MM_Utils::getIcon('exclamation-triangle', 'yellow', '1.3em', '1px'); ?> <?php echo _mmt("If you proceed with merging these accounts"); ?>:
			<ul>
			<li><?php echo _mmt("The secondary account will be deleted."); ?></li>
			<li><?php echo _mmt("Any subscriptions not associated with the choices listed above will be cancelled."); ?></li>
			<?php if($hasOffsiteService) { ?>
			<li>
			<?php 
			$pymtServiceStr = implode("/", $offsiteServices);
			echo sprintf(_mmt("Cancellation requests will be sent to %s for subscriptions managed by them. We cannot guarantee these subscriptions will be cancelled. Following the merge, log into %s to confirm."), $pymtServiceStr, $pymtServiceStr); 
			?></li>
			<?php } ?>
			</ul>
			</div>
			<input type="button" class="mm-ui-button blue" value="<?php echo _mmt("Merge Accounts");?> " onclick="mmjs.executeMerge();" />
			<input type="button" class="mm-ui-button" value="<?php echo _mmt("Back");?>" onclick="showSection('access_rights')" />
			<input type="button" class="mm-ui-button" value="<?php echo _mmt("Cancel");?>" onclick="cancelMerge()" />
			<!-- END custom HTML -->
		</div>
	</div>
</div>
<div class="ui-resizable-handle ui-resizable-n" style="z-index: 90;"></div>
<div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div>
<div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;"></div>
<div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div>
<div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;"></div>
<div class="ui-resizable-handle ui-resizable-sw" style="z-index: 90;"></div>
<div class="ui-resizable-handle ui-resizable-ne" style="z-index: 90;"></div>
<div class="ui-resizable-handle ui-resizable-nw" style="z-index: 90;"></div>

<!--
END CUSTOM MERGE MEMBER UI/UX
-->