<?php

////////////////////////////
// SETTINGS PAGE
////////////////////////////

function fca_pc_plugin_menu() {

	add_menu_page(
		esc_attr__( 'Pixel Cat', 'facebook-conversion-pixel' ),
		esc_attr__( 'Pixel Cat', 'facebook-conversion-pixel' ),
		'manage_options',
		'fca_pc_settings_page',
		'fca_pc_settings_page',
		FCA_PC_PLUGINS_URL . '/assets/icon.png',
		119
	);

}
add_action( 'admin_menu', 'fca_pc_plugin_menu' );

//ENQUEUE ANY SCRIPTS OR CSS FOR OUR ADMIN PAGE EDITOR
function fca_pc_admin_enqueue() {

	wp_enqueue_style( 'dashicons' );
	wp_enqueue_script( 'jquery' );

	wp_enqueue_script( 'fca_pc_select2', FCA_PC_PLUGINS_URL . '/includes/select2/select2.min.js', array(), FCA_PC_PLUGIN_VER, true );
	wp_enqueue_style( 'fca_pc_select2', FCA_PC_PLUGINS_URL . '/includes/select2/select2.min.css', array(), FCA_PC_PLUGIN_VER );

	wp_enqueue_style( 'fca_pc_tooltipster_stylesheet', FCA_PC_PLUGINS_URL . '/includes/tooltipster/tooltipster.bundle.min.css', array(), FCA_PC_PLUGIN_VER );
	wp_enqueue_style( 'fca_pc_tooltipster_borderless_css', FCA_PC_PLUGINS_URL . '/includes/tooltipster/tooltipster-borderless.min.css', array(), FCA_PC_PLUGIN_VER );
	wp_enqueue_script( 'fca_pc_tooltipster_js',FCA_PC_PLUGINS_URL . '/includes/tooltipster/tooltipster.bundle.min.js', array( 'jquery' ), FCA_PC_PLUGIN_VER, true );

	$admin_dependencies = array( 'jquery', 'fca_pc_select2', 'fca_pc_tooltipster_js' );

	if ( FCA_PC_DEBUG ) {
		wp_enqueue_script( 'fca_pc_admin_js', FCA_PC_PLUGINS_URL . '/includes/editor/admin.js', $admin_dependencies, FCA_PC_PLUGIN_VER, true );
		wp_enqueue_style( 'fca_pc_admin_stylesheet', FCA_PC_PLUGINS_URL . '/includes/editor/admin.css', array(), FCA_PC_PLUGIN_VER );
	} else {
		wp_enqueue_script( 'fca_pc_admin_js', FCA_PC_PLUGINS_URL . '/includes/editor/admin.min.js', $admin_dependencies, FCA_PC_PLUGIN_VER, true );
		wp_enqueue_style( 'fca_pc_admin_stylesheet', FCA_PC_PLUGINS_URL . '/includes/editor/admin.min.css', array(), FCA_PC_PLUGIN_VER );
	}
	$admin_data = array (
		'ajaxurl' => admin_url ( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'fca_pc_admin_nonce' ),
		'pixelTemplate' => fca_pc_pixel_row_html(),
		'eventTemplate' => fca_pc_event_row_html(),
		'premium' => function_exists ( 'fca_pc_editor_premium_data' ),
		'edd_active' => is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ),
		'woo_active' => is_plugin_active( 'woocommerce/woocommerce.php' ),
		'debug' => FCA_PC_DEBUG,
	);
	wp_localize_script( 'fca_pc_admin_js', 'fcaPcAdminData', $admin_data );
	
	if ( function_exists( 'fca_pc_editor_premium_data' ) ) {
		fca_pc_editor_premium_data();
	}

}

function fca_pc_settings_page() {

	$options = get_option( 'fca_pc', array() );

	if ( isSet( $_POST['fca_pc_save'] ) ) {
		$nonce = sanitize_text_field( $_POST['fca_pc']['nonce'] );

		if( wp_verify_nonce( $nonce, 'fca_pc_admin_nonce' ) === false ){
			wp_die( 'Unauthorized, please log in and try again.' );
		}
		
		$options = fca_pc_settings_save();
	}

	$form_class = FCA_PC_PLUGIN_PACKAGE === 'Lite' ? 'fca-pc-free' : 'fca-pc-premium';

	$options['events'] = empty ( $options['events'] ) ? array() : $options['events'];

	fca_pc_admin_enqueue();

	$html = "<div id='fca-pc-overlay' style='display:none'></div>";

	$html .= "<form novalidate style='display: none' action='' method='post' id='fca_pc_main_form' class='$form_class'>";

		$html .= wp_nonce_field( 'fca_pc_admin_nonce', 'fca_pc[nonce]' );

		$html .= '<h1>' .  esc_attr__( 'Pixel Cat', 'facebook-conversion-pixel' ) . '</h1>';

		$html .= '<p>' . esc_attr__( 'Help: ', 'facebook-conversion-pixel' );
			$html .= '<a href="https://fatcatapps.com/facebook-pixel/#Option_2_Install_a_Facebook_Pixel_WordPress_plugin_recommended" target="_blank">' . esc_attr__( 'Setup Instructions', 'facebook-conversion-pixel' ) . '</a> | ';
			$html .= '<a href="https://fatcatapps.com/knowledge-base/testing-facebook-pixel/" target="_blank">' . esc_attr__( 'How To Check If Your Pixel Is Working', 'facebook-conversion-pixel' ) . '</a> | ';
			$html .= '<a href="https://fatcatapps.com/facebook-pixel/" target="_blank">' . esc_attr__( 'FB Pixel: The Definitive Guide', 'facebook-conversion-pixel' ) . '</a> | ';
			$html .= '<a href="https://wordpress.org/support/plugin/facebook-conversion-pixel" target="_blank">' . esc_attr__( 'Support Forum', 'facebook-conversion-pixel' ) . '</a>';
		$html .= '</p>';

		$html .= "<h1 class='nav-tab-wrapper fca-pc-nav $form_class'>";
			$html .= '<a href="#" data-target="#fca-pc-main-table, #fca-pc-active-pixels-table, #fca-pc-events-table" class="nav-tab">' . esc_attr__( 'Main', 'facebook-conversion-pixel' ) . '</a>';
			$html .= '<a href="#" data-target="#fca_pc_settings_table" class="nav-tab">' . esc_attr__( 'Settings', 'facebook-conversion-pixel' ) . '</a>';
			$html .= '<a href="#" data-target="#fca-pc-e-commerce" class="nav-tab">' . esc_attr__( 'E-commerce', 'facebook-conversion-pixel' ) . '</a>';
			$html .= '<a href="#" data-target="#fca_pc_integrations_table" class="nav-tab">' . esc_attr__( 'More Integrations', 'facebook-conversion-pixel' ) . '</a>';

		$html .= '</h1>';

		$html .= fca_pc_active_pixels_table( $options );

		$html .= fca_pc_event_panel( $options );

		$html .= fca_pc_add_settings_table( $options );

		$html .= fca_pc_add_e_commerce_integrations( $options );

		$html .= fca_pc_add_more_integrations( $options );

		$html .= '<button id="fca_pc_save" type="submit" style="margin-top: 20px;" name="fca_pc_save" class="button button-primary">' . esc_attr__( 'Save', 'facebook-conversion-pixel' ) . '</button>';

		$html .= fca_pc_add_pixel_form();

		$html .= fca_pc_add_event_form();

	$html .= '</form>';

	if ( FCA_PC_PLUGIN_PACKAGE === 'Lite' ) {
		$html .= fca_pc_marketing_metabox();
	}

	echo $html;
}

function fca_pc_add_event_form() {

	$events = array(
		'ViewContent' => 'ViewContent',
		'Lead' => 'Lead',
		'AddToCart' => 'AddToCart',
		'AddToWishlist' => 'AddToWishlist',
		'InitiateCheckout' => 'InitiateCheckout',
		'AddPaymentInfo' => 'AddPaymentInfo',
		'Purchase' => 'Purchase',
		'CompleteRegistration' => 'CompleteRegistration'
	);

	$triggers = array(
		'all' => esc_attr__( 'All Pages', 'facebook-conversion-pixel' ),
		'front' => esc_attr__( 'Front Page', 'facebook-conversion-pixel' ),
		'blog' => esc_attr__( 'Blog Page', 'facebook-conversion-pixel' )
	);

	$custom_post_type_triggers = apply_filters( 'fca_pc_custom_post_support', array() );

	if ( is_array( $custom_post_type_triggers ) && count( $custom_post_type_triggers ) > 0 ) {
		forEach ( $custom_post_type_triggers as $cpt_slug ) {
			$cpt_obj = get_post_type_object( $cpt_slug );

			if ( $cpt_obj ) {
				$cpt_name = $cpt_obj->labels->singular_name;

				forEach ( get_posts( array( 'posts_per_page' => -1, 'post_type' => $cpt_slug ) ) as $p ) {
					$triggers[$p->ID] = $cpt_name . ' ' . $p->ID . ' - ' . $p->post_title;
				}
			}
		}
	}

	forEach ( get_posts( array( 'posts_per_page' => -1, 'post_type' => 'product' ) ) as $product ) {
		$triggers[$product->ID] = 'Product ' . $product->ID . ' - ' . $product->post_title;
	}

	forEach ( get_posts( array( 'posts_per_page' => -1, 'post_type' => 'download' ) ) as $download ) {
		$triggers[$download->ID] = 'Download ' . $download->ID . ' - ' . $download->post_title;
	}

	forEach ( get_pages( array( 'posts_per_page' => -1 ) ) as $page ) {
		$triggers[$page->ID] = 'Page ' . $page->ID . ' - ' . $page->post_title;
	}
	forEach ( get_posts( array( 'posts_per_page' => -1 ) ) as $post ) {
		$triggers[$post->ID] = 'Post ' . $post->ID . ' - ' . $post->post_title;
	}

	forEach ( get_categories() as $cat ) {
		$triggers['cat' . $cat->cat_ID] = 'Category ' . $cat->cat_ID . ' - ' . $cat->category_nicename;
	}

	forEach ( get_tags() as $tag ) {
		$triggers['tag' . $tag->term_id] = 'Tag ' . $tag->term_id  . ' - ' . $tag->name;
	}

	//REMOVE BLOG PAGE FROM OPTIONS - USE BLOG SETTING INSTEAD
	$blog_id = get_option( 'page_for_posts' );
	if ( $blog_id !== 0 ) {
		unset ( $triggers[$blog_id] );
	}

	$modes = array (
		'post' => 'Page Visit',
		'css' => 'Click on Element',
		'hover' => 'Hover over Element',
		'url' => 'URL Click',
	);

	ob_start(); ?>
	<div id='fca-pc-event-modal' style='display: none;'>
		<h3><?php esc_attr_e( 'Edit Event', 'facebook-conversion-pixel' ) ?></h3>
		<table class="fca_pc_modal_table">
			<tr>
				<span style="padding-left: 3px;" class='fca_pc_hint'><?php esc_attr_e("Note: Looking to add WooCommerce events? Add them all ", 'facebook-conversion-pixel') ?></span> <a href="#" class='fca_pc_hint' id="fca_pc_woo_toggle_link"> <?php esc_attr_e("with a single click!", 'facebook-conversion-pixel') ?></a>
			</tr>
			<tr>
				<th><?php esc_attr_e( 'Trigger', 'facebook-conversion-pixel' ) ?></th>
				<td><?php echo fca_pc_select( 'modal_post_trigger_input', array(), $modes, "id='fca-pc-modal-trigger-type-input'" ); ?></td>
			</tr>
			<tr id='fca-pc-css-input-tr'>
				<th><?php esc_attr_e( 'CSS Target', 'facebook-conversion-pixel' ); echo fca_pc_tooltip( esc_attr__( 'Enter CSS classes or IDs that will trigger the event on click.<br>Add more than one class or ID separted by commas.  E.g. "#my-header, .checkout-button"', 'facebook-conversion-pixel' ) ) ?></th>
				<td>
					<input id='fca-pc-modal-css-trigger-input' type='text' placeholder='e.g. #checkout-button' class='fca-pc-input-text fca-pc-css-trigger' style='width: 100%'>
				</td>
			</tr>
			<tr id='fca-pc-url-input-tr'>
				<th><?php esc_attr_e( 'URL Click', 'facebook-conversion-pixel' ); echo fca_pc_tooltip( esc_attr__( 'Enter the URL you wish to trigger the event on click.', 'facebook-conversion-pixel' ) ) ?></th>
				<td>
					<input id='fca-pc-modal-url-trigger-input' type='url' placeholder='https://fatcatapps.com' class='fca-pc-input-text fca-pc-url-trigger' style='width: 100%'>
				</td>
			</tr>
			<tr id='fca-pc-post-input-tr'>
				<th><?php esc_attr_e( 'Pages', 'facebook-conversion-pixel' ); echo fca_pc_tooltip( esc_attr__( 'Choose where on your site to trigger this event. You can choose any posts, pages, or categories.', 'facebook-conversion-pixel' ) ) ?></th>
				<td>
					<?php echo fca_pc_select_multiple( 'modal_post_trigger_input', array(), $triggers, "id='fca-pc-modal-post-trigger-input'" ); ?>
				</td>
			</tr>
			<tr>
				<th><?php esc_attr_e( 'Event', 'facebook-conversion-pixel' ); ?></th>
				<td>
					<select id='fca-pc-modal-event-input' class='fca_pc_select' style='width: 100%' >
						<optgroup label='<?php esc_attr_e( 'Standard Events', 'facebook-conversion-pixel' ) ?>'>
						<?php
						forEach ( $events as $key => $value ) {
							echo "<option value='" . esc_attr( $key ) . "'>$value</option>";
						}?>
						</optgroup>
						<option value='custom' id='custom-event-option' class='fca-bold'><?php esc_attr_e( 'Custom Event', 'facebook-conversion-pixel' ) ?></option>
					</select>
				</td>
			</tr>
			<tr id='fca_pc_param_event_name'>
				<th><?php esc_attr_e( 'Event Name', 'facebook-conversion-pixel' ); echo fca_pc_tooltip( esc_attr__( 'Choose the name of the Custom Event.  Max 50 characters', 'facebook-conversion-pixel' ) ) ?></th>
				<td><?php echo fca_pc_input ( 'event_name', '', '', 'text' ) ?></td>
			</tr>
			<tr>
				<th><?php esc_attr_e( 'Time delay', 'facebook-conversion-pixel' ); echo fca_pc_tooltip( esc_attr__( 'You can add a time-delay to exclude bouncing visitors (Pro only).', 'facebook-conversion-pixel' ) ) ?></th>
				<td><input id='fca-pc-modal-delay-input' type='number' min='0' max='3600' step='1' value='0'><?php esc_attr_e( 'seconds', 'facebook-conversion-pixel' ) ?></td>
			</tr>
			<tr>
				<th><?php esc_attr_e( 'Scroll %', 'facebook-conversion-pixel' ); echo fca_pc_tooltip( esc_attr__( 'You can add a scroll percent trigger to exclude bouncing visitors (Pro only).', 'facebook-conversion-pixel' ) ) ?></th>
				<td><input id='fca-pc-modal-scroll-input' type='number' min='0' max='100' step='5' value='0'><?php esc_attr_e( '%', 'facebook-conversion-pixel' ) ?></td>
			</tr>
			<tr>
				<th style='vertical-align: top'><?php esc_attr_e( 'Parameters', 'facebook-conversion-pixel' )?></th>
				<td><?php echo '<span id="fca-pc-show-param" class="fca-pc-param-toggle">' . esc_attr__( '(show)', 'facebook-conversion-pixel' ) . '</span><span style="display: none;" id="fca-pc-hide-param" class="fca-pc-param-toggle">' . esc_attr__( '(hide)', 'facebook-conversion-pixel' ) . '</span>' ?></td>
			</tr>
			<tr>
				<td id='fca-pc-param-help' class='fca-pc-param-row' colspan=2 style='font-style: italic;'><?php esc_attr_e( 'Add custom parameters here.  You can use any of the following automatic parameters:', 'facebook-conversion-pixel' )?><br>
					{post_title}, {post_id}, {post_type}, {post_category}
				</td>
			</tr>
			<tr>
				<?php echo fca_pc_event_parameters() ?>
			</tr>
		</table>

		<button type='button' id='fca-pc-event-save' class='button button-primary' style='margin-right: 8px'><?php esc_attr_e( 'Add', 'facebook-conversion-pixel' ) ?></button>
		<button type='button' id='fca-pc-event-cancel' class='button button-secondary'><?php esc_attr_e( 'Cancel', 'facebook-conversion-pixel' ) ?></button>

	</div>

	<?php
	return ob_get_clean();
}

//SPIT OUT THE DIFFERENT PARAMETER OPTIONS FOR EACH EVENT
function fca_pc_event_parameters () {
	ob_start(); ?>
		<tr class='fca-pc-param-row' id='fca_pc_param_value'>
			<th>value:<span class='fca-required-param-tooltip'><?php echo fca_pc_tooltip( esc_attr__( 'The purchase price. This field is required.', 'facebook-conversion-pixel' ) ) ?></span></th>
			<td><?php echo fca_pc_input( 'value', '', '', 'text' ) ?></td>
		</tr>
		<tr class='fca-pc-param-row' id='fca_pc_param_currency'>
			<th>currency:<span class='fca-required-param-tooltip'><?php echo fca_pc_tooltip( esc_attr__( 'E.g. USD, EUR or JPY. This field is required.', 'facebook-conversion-pixel' ) ) ?></span></th>
			<td><?php echo fca_pc_input( 'currency', '', '', 'text' ) ?></td>
		</tr>
		<tr class='fca-pc-param-row' id='fca_pc_param_content_name'>
			<th>content_name:</th>
			<td><?php echo fca_pc_input( 'content_name', '', '', 'text' ) ?></td>
		</tr>
		<tr class='fca-pc-param-row' id='fca_pc_param_content_type'>
			<th>content_type:</th>
			<td><?php echo fca_pc_select( 'content_type', '', array( 'product' => 'product', 'product_group' => 'product_group' ), "class='fca-pc-input-select fca-pc-content_type'" ) ?></td>
		</tr>
		<tr class='fca-pc-param-row' id='fca_pc_param_content_ids'>
			<th>content_ids:</th>
			<td><?php echo fca_pc_input( 'content_ids', '', '', 'text' ) ?></td>
		</tr>
		<tr class='fca-pc-param-row' id='fca_pc_param_content_category'>
			<th>content_category:</th>
			<td><?php echo fca_pc_input( 'content_category', '', '', 'text' ) ?></td>
		</tr>
		<tr class='fca-pc-param-row' id='fca_pc_param_search_string'>
			<th>search_string:</th>
			<td><?php echo fca_pc_input( 'search_string', '', '', 'text' ) ?></td>
		</tr>
		<tr class='fca-pc-param-row' id='fca_pc_param_num_items'>
			<th>num_items:</th>
			<td><?php echo fca_pc_input( 'num_items', '', '', 'text' ) ?></td>
		</tr>
		<tr class='fca-pc-param-row' id='fca_pc_param_status'>
			<th>status:</th>
			<td><?php echo fca_pc_input( 'status', '', '', 'text' ) ?></td>
		</tr>
		<tr class='fca-pc-param-row' id='fca_pc_param_custom'>
			<td colspan='3' style='position: relative; left: -3px;'>
				<?php
				echo fca_pc_custom_param_table();
				if ( FCA_PC_PLUGIN_PACKAGE === 'Lite' ) {
					echo '<span style="font-weight: bold; position: relative; top: 5px; left: 5px;">' . esc_attr__( '(Pro Only)', 'facebook-conversion-pixel' ) . '</span>';
				}
				?>
			</td>
		</tr>
	<?php
	return ob_get_clean();
}

function fca_pc_custom_param_table() {

	ob_start(); ?>

	<table id='fca_pc_custom_param_table' style='width:100%;'>
	</table>
	<button type='button' id='fca-pc-add-custom-param' class='button button-secondary' ><span class='dashicons dashicons-plus' style='vertical-align: middle;' ></span><?php esc_attr_e( 'Add Custom Parameter', 'facebook-conversion-pixel' ) ?></button>

	<?php
	return ob_get_clean();
}

function fca_pc_custom_param_row() {

	ob_start(); ?>

	<tr class='fca_deletable_item'>
		<td style='width: 120px;'><input type='text' style='width:100%; height: 35px;' placeholder='<?php esc_attr_e( 'Parameter', 'facebook-conversion-pixel' ) ?>' class='fca-pc-input-parameter-name'></td>
		<td><input type='text' style='width:100%; height: 35px;' placeholder='<?php esc_attr_e( 'Value', 'facebook-conversion-pixel' ) ?>' class='fca-pc-input-parameter-value'></td>
		<td style='width: 66px; text-align: right; height: 35px;'><?php echo fca_pc_delete_icons() ?></td>
	</tr>

	<?php
	return ob_get_clean();
}

function fca_pc_event_tooltips(){

	$viewcontent_hover_text =  htmlentities ( esc_attr__("We'll automatically send the following event parameters to Facebook:<br>content_name: Post/Page title (eg. \"My first blogpost\")<br>content_type: Post type (eg. \"Post\", \"Page\", \"Product\")<br>content_ids: The WordPress post id (eg. \"47\")", 'facebook-conversion-pixel' ), ENT_QUOTES );
	$lead_hover_text = htmlentities ( esc_attr__("We'll automatically send the following event parameters to Facebook:<br>content_name: Post/Page title (eg. \"My first blogpost\")<br>content_category: The post's category, if any (eg. \"News\")", 'facebook-conversion-pixel' ), ENT_QUOTES );

	$html = "<p class='fca_pc_hint' id='fca_pc_tooltip_viewcontent'>";
		$html .= sprintf( esc_attr__("Send the %1sViewContent%2s standard event to Facebook.<br>(%3sWhich Parameters will be sent?%4s)", 'facebook-conversion-pixel' ), '<strong>', '</strong>', "<span class='fca_pc_event_tooltip' title='$viewcontent_hover_text'>", '</span>' );
	$html .= '</p>';

	$html .= "<p class='fca_pc_hint' id='fca_pc_tooltip_lead' style='display: none'>";
		$html .= sprintf( esc_attr__("Send the %1sLead%2s standard event to Facebook.<br>(%1sWhich Parameters will be sent?%2s)", 'facebook-conversion-pixel' ), '<strong>', '</strong>', "<span class='fca_pc_event_tooltip' title='$lead_hover_text'>", '</span>' );
	$html .= '</p>';
	return $html;
}

function fca_pc_add_pixel_form() {

	$types = array(
		'Facebook Pixel' => 'Facebook Pixel',
		'Conversions API' => 'Conversions API'
	);

	ob_start(); ?>
	<div id='fca-pc-pixel-modal' style='display: none;'>
		<h3><?php esc_attr_e( 'Pixel settings', 'facebook-conversion-pixel' ) ?></h3>
		<table class="fca_pc_pixel_modal_table">
			<tr>
				<th><?php esc_attr_e( 'Type of pixel', 'facebook-conversion-pixel' ); ?></th>
				<td>
					<select id='fca-pc-modal-type-select' class='fca_pc_select' style='width: 100%' >
						<optgroup label='<?php esc_attr_e( 'Type of pixel', 'facebook-conversion-pixel' ) ?>'>
						<?php
						forEach ( $types as $key => $value ) {
							echo "<option value='" . esc_attr( $key ) . "'>$value</option>";
						}?>
						</optgroup>
					</select>
				</td>
			</tr>
			<tr id='fca-pc-pixel-input-tr'>
				<th style="top: 0;"><?php esc_attr_e( 'Pixel ID', 'facebook-conversion-pixel' ); echo fca_pc_tooltip( esc_attr__( 'Enter your Facebook Pixel ID here', 'facebook-conversion-pixel' ) ) ?>
					<br><a class="fca_pc_hint" href="https://fatcatapps.com/knowledge-base/facebook-pixel-id/" target="_blank"> <?php echo esc_attr__( 'What is my Pixel ID?', 'facebook-conversion-pixel' ) ?></a>
				</th>
				<td id="fca-pc-helptext" title="<?php echo esc_attr__('Your Facebook Pixel ID should only contain numbers', 'facebook-conversion-pixel' ) ?>">
					<input id='fca-pc-modal-pixel-input' type='text' placeholder='e.g. 1234567890' class='fca-pc-input-text' style='width: 100%'>
				</td>
			</tr>
			<tr id='fca-pc-capi-input-tr'>
				<th style="top: 0;"><?php esc_attr_e( 'Conversions API Token', 'facebook-conversion-pixel' ); echo fca_pc_tooltip( esc_attr__( 'Enter your Conversions API Token here', 'facebook-conversion-pixel' ) ) ?>
					<br><a class="fca_pc_hint" href="https://developers.facebook.com/docs/marketing-api/conversions-api/get-started#access-token" target="_blank"> <?php echo esc_attr__( 'Where do i get this?', 'facebook-conversion-pixel' ) ?></a>
				</th>
				<td>
					<input id='fca-pc-modal-capi-input' type='text' placeholder='e.g. EAAMHTc1Wx2UBADK0r...' class='fca-pc-input-text' style='width: 100%'>
				</td>
			</tr>
			<tr id='fca-pc-test-input-tr'>
				<th><?php esc_attr_e( 'Test Code', 'facebook-conversion-pixel' ); echo fca_pc_tooltip( esc_attr__( 'Only for testing Conversions API connectivity, remove this when going live.', 'facebook-conversion-pixel' ) ) ?></th>
				<td>
					<input id='fca-pc-modal-test-input' type='text' placeholder='optional - e.g. TEST12345' class='fca-pc-input-text' style='width: 100%'>
				</td>
			</tr>
		</table>
		<span id="fca_pc_capi_info" class="fca_pc_hint"><?php esc_attr_e( 'Important: Even with the Conversions API active, events will also be sent through the Conversions Pixel. In case the Pixel gets blocked by an ad blocker the Conversions API will kick in and make sure the event is still logged and sent to Facebook' , 'facebook-conversion-pixel' ); ?><br></span> 
		<br>
		<button type='button' id='fca-pc-pixel-save' class='button button-primary' style='margin-right: 8px'><?php esc_attr_e( 'Add', 'facebook-conversion-pixel' ) ?></button>
		<button type='button' id='fca-pc-pixel-cancel' class='button button-secondary'><?php esc_attr_e( 'Cancel', 'facebook-conversion-pixel' ) ?></button>

	</div>

	<?php
	return ob_get_clean();
}


function fca_pc_active_pixels_table( $options ){

	$pixels = empty( $options['pixels'] ) ? array() : $options['pixels'];

	ob_start(); ?>

		<div id="fca-pc-active-pixels-table">
			<div id="fca-pc-active-pixels-content">
				<h3> <?php echo esc_attr__( 'Pixels', 'facebook-conversion-pixel' ) ?></h3>
				<table id="fca-pc-pixels" class="widefat">
					<tr id="fca-pc-pixel-table-heading">
						<th style="display:none;"></th>
						<th style="width: 67px;"><?php echo esc_attr__( 'Status', 'facebook-conversion-pixel' ) ?></th>
						<th style="width: 30%;"><?php echo esc_attr__( 'Pixel Type', 'facebook-conversion-pixel' ) ?></th>
						<th style="width: 30%;"><?php echo esc_attr__( 'Pixel ID', 'facebook-conversion-pixel' ) ?></th>
						<th style="width: calc( 30% - 150px );"><?php echo esc_attr__( 'Conversions API token', 'facebook-conversion-pixel' ) ?></th>
						<th style="text-align: right; width: 67px;"></th>
					</tr><?php
					if( $pixels ){
						forEach ( $pixels as $pixel ) {
							echo fca_pc_pixel_row_html( $pixel );
						}
					} ?>
				</table>
				<button type="button" id="fca_pc_new_pixel_id" class="button button-secondary" title=" <?php echo esc_attr__( 'Add another Pixel', 'facebook-conversion-pixel' ) ?> ">
					<span class="dashicons dashicons-plus" style="vertical-align: middle;"></span>Add Pixel
				</button> </br></br>

			</div>
		</div>

	<?php
	return ob_get_clean();

}

function fca_pc_pixel_row_html( $pixel = '' ) {

	ob_start(); ?>

	<tr id='{{ID}}' class='fca_pc_pixel_row fca_deletable_item'>
		<td class='fca-pc-json-td' style='display:none;'><input type='hidden' class='fca-pc-input-hidden fca-pc-pixel-json' name='fca_pc[pixel_json][]' value='<?php echo esc_attr( stripslashes_deep( $pixel ) ) ?>' /></td>
		<td class='fca-pc-controls-td'>
			<span class='dashicons dashicons-controls-pause fca_controls_icon fca_controls_icon_pixel_play' title='<?php esc_attr_e( 'Paused - Click to Activate', 'facebook-conversion-pixel' ) ?>' style='display:none;' ></span>
			<span class='dashicons dashicons-controls-play fca_controls_icon fca_controls_icon_pixel_pause' title='<?php esc_attr_e( 'Active - Click to Pause', 'facebook-conversion-pixel' ) ?>' ></span>
		</td>
		<td class='fca-pc-type-td'>{{TYPE}}</td>
		<td class='fca-pc-pixel-td'>{{PIXEL}}</td>
		<td class='fca-pc-capi-td'>{{CAPI}}</td>
		<td class='fca-pc-delete-td'><?php echo fca_pc_delete_icons() ?></td>
	</tr>

	<?php
	return ob_get_clean();
}

function fca_pc_event_panel( $options ) {

	$events = empty( $options['events'] ) ? array() : $options['events'];

	$html = '<div id="fca-pc-events-table">';
		$html .= "<h3>" . esc_attr__( 'Events', 'facebook-conversion-pixel' ) . "</h3>";
		$html .= "<h4>" . esc_attr__( 'Events based on user behavior', 'facebook-conversion-pixel' ) . "</h4>";
		$html .= '<table id="fca-pc-events" class="widefat">';
			$html .= '<tr id="fca-pc-event-table-heading">';
				//HIDDEN COLUMN FOR JSON
				$html .= '<th style="display:none;"></th>';
				$html .= '<th style="width: 67px;">' . esc_attr__( 'Status', 'facebook-conversion-pixel' ) . '</th>';
				$html .= '<th style="width: 30%;">' . esc_attr__( 'Event', 'facebook-conversion-pixel' ) . '</th>';
				$html .= '<th style="width: calc( 70% - 150px );">' . esc_attr__( 'Trigger', 'facebook-conversion-pixel' ) . '</th>';
				$html .= '<th style="text-align: right; width: 67px;"></th>';
			$html .= '</tr>';
			forEach ( $events as $event ) {
				$html .= fca_pc_event_row_html( $event );
			}
		$html .= '</table>';
		$html .= '<button type="button" id="fca_pc_new_event" class="button button-secondary"><span class="dashicons dashicons-plus" style="vertical-align: middle;"></span>' . esc_attr__( 'Add New', 'facebook-conversion-pixel' ) . '</button><br>';
	$html .= '</div>';

	return $html;
}

//EVENT TABLE ROW TEMPLATE
function fca_pc_event_row_html( $event = '' ) {
	ob_start(); ?>
	<tr id='{{ID}}' class='fca_pc_event_row fca_deletable_item'>
		<td class='fca-pc-json-td' style='display:none;'><input type='hidden' class='fca-pc-input-hidden fca-pc-json' name='fca_pc[event_json][]' value='<?php echo esc_attr( stripslashes_deep( $event ) ) ?>' /></td>
		<td class='fca-pc-controls-td'>
			<span class='dashicons dashicons-controls-pause fca_controls_icon fca_controls_icon_play' title='<?php esc_attr_e( 'Paused - Click to Activate', 'facebook-conversion-pixel' ) ?>' style='display:none;' ></span>
			<span class='dashicons dashicons-controls-play fca_controls_icon fca_controls_icon_pause' title='<?php esc_attr_e( 'Active - Click to Pause', 'facebook-conversion-pixel' ) ?>' ></span>
		</td>
		<td class='fca-pc-event-td'>{{EVENT}}</td>
		<td class='fca-pc-trigger-td'>{{TRIGGER}}</td>
		<td class='fca-pc-delete-td'><?php echo fca_pc_delete_icons() ?></td>
	</tr>
	<?php
	return ob_get_clean();
}

function fca_pc_settings_save() {
	$data = array();

	echo '<div id="fca-pc-notice-save" class="notice notice-success is-dismissible">';
		echo '<p><strong>' . esc_attr__( "Settings saved.", 'facebook-conversion-pixel' ) . '</strong></p>';
	echo '</div>';

	$data['pixels'] = empty( $_POST['fca_pc']['pixel_json'] ) ? array() : array_map( 'fca_pc_sanitize_text_array', $_POST['fca_pc']['pixel_json'] );
	$data['events'] = empty( $_POST['fca_pc']['event_json'] ) ? array() : array_map( 'fca_pc_sanitize_text_array', $_POST['fca_pc']['event_json'] );

	$data['exclude'] = empty( $_POST['fca_pc']['exclude'] ) ? array() : array_map( 'fca_pc_sanitize_text_array', $_POST['fca_pc']['exclude'] );

	$data['search_integration'] = empty( $_POST['fca_pc']['search_integration'] ) ? '' : 'on';
	$data['quizcat_integration'] = empty( $_POST['fca_pc']['quizcat_integration'] ) ? '' : 'on';
	$data['optincat_integration'] = empty( $_POST['fca_pc']['optincat_integration'] ) ? '' : 'on';
	$data['landingpagecat_integration'] = empty( $_POST['fca_pc']['landingpagecat_integration'] ) ? '' : 'on';
	$data['ept_integration'] = empty( $_POST['fca_pc']['ept_integration'] ) ? '' : 'on';

	$data['woo_integration'] = empty( $_POST['fca_pc']['woo_integration'] ) ? '' : 'on';
	$data['woo_feed'] = empty( $_POST['fca_pc']['woo_feed'] ) ? '' : 'on';
	$data['woo_variations'] = empty( $_POST['fca_pc']['woo_variations'] ) ? '' : 'on';
	$data['woo_excluded_categories'] = empty( $_POST['fca_pc']['woo_excluded_categories'] ) ? '' : sanitize_text_field( $_POST['fca_pc']['woo_excluded_categories'] );
	$data['woo_product_id'] = empty( $_POST['fca_pc']['woo_product_id'] ) ? '' : sanitize_text_field( $_POST['fca_pc']['woo_product_id'] );
	$data['woo_desc_mode'] = empty( $_POST['fca_pc']['woo_desc_mode'] ) ? '' : sanitize_text_field( $_POST['fca_pc']['woo_desc_mode'] );
	$data['google_product_category'] = empty( $_POST['fca_pc']['google_product_category'] ) ? '' : intval( $_POST['fca_pc']['google_product_category'] );

	$data['edd_integration'] = empty( $_POST['fca_pc']['edd_integration'] ) ? '' : 'on';
	$data['edd_feed'] = empty( $_POST['fca_pc']['edd_feed'] ) ? '' : 'on';
	$data['edd_excluded_categories'] = empty( $_POST['fca_pc']['edd_excluded_categories'] ) ? '' : sanitize_text_field( $_POST['fca_pc']['edd_excluded_categories'] );
	$data['edd_desc_mode'] = empty( $_POST['fca_pc']['edd_desc_mode'] ) ? '' : sanitize_text_field( $_POST['fca_pc']['edd_desc_mode'] );

	if ( function_exists( 'fca_pc_premium_save' ) ) {
		$data = fca_pc_premium_save( $data );
	}

	update_option( 'fca_pc', $data );

	return $data;

}

function fca_pc_add_settings_table( $options ) {

	$amp_on = empty( $options['amp_integration'] ) ? '' : 'on';

	$user_parameters_on = empty( $options['user_parameters'] ) ? '' : 'on';
	$utm_support_on = empty( $options['utm_support'] ) ? '' : 'on';

	$conversions_api = empty ( $options['conversions_api'] ) ? false : true;
	$advanced_matching = empty ( $options['advanced_matching'] ) ? false : true;
	$exclude = empty ( $options['exclude'] ) ? array() : $options['exclude'];
	
	$role_options = array();
	forEach ( get_editable_roles() as $role ) {
		$role_name = esc_attr( $role['name'] );
		$role_options[ $role_name ] = $role_name;
	}
	$pro_tooltip = FCA_PC_PLUGIN_PACKAGE !== 'Lite' ? '' : 'class="fca_pc_pro_tooltip" title="' . esc_attr__("This option is available only with Pixel Cat Pro. Click the blue button on the right-hand side to learn more.", 'facebook-conversion-pixel' ) . '"';
					
	ob_start(); ?>

	<table id="fca_pc_settings_table" class='fca_pc_setting_table fca_pc_integrations_table'>
		<tr>
			<th><?php esc_attr_e( 'Exclude Users', 'facebook-conversion-pixel' ) ?></th>
			<td><?php echo fca_pc_select_multiple( 'exclude', $exclude, $role_options ) ?>
			<span class='fca_pc_hint'><?php esc_attr_e("Logged in users selected above will not trigger your pixel.", 'facebook-conversion-pixel' ) ?></span></td>
		</tr>

		<tr <?php echo esc_attr( $pro_tooltip ) ?>>
			<th><?php esc_attr_e( 'Advanced Matching', 'facebook-conversion-pixel' ) ?></th>
			<td><?php echo fca_pc_input( 'advanced_matching', '', $advanced_matching, 'checkbox' ) ?>
			<span class='fca_pc_hint'><?php esc_attr_e("Enable Advanced Matching for all events. According to Facebook, advertisers using advanced matching can expect a 10% increase in attributed conversions and 20% increase in reach.", 'facebook-conversion-pixel' ) ?></span></td>
		</tr>
		<tr <?php echo esc_attr( $pro_tooltip ) ?>>
			<th><?php esc_attr_e( 'Additional user information', 'facebook-conversion-pixel' ) ?></th>
			<td><?php echo fca_pc_input( 'user_parameters', '', $user_parameters_on, 'checkbox' ) ?>
			<span class='fca_pc_hint'><?php esc_attr_e("Send HTTP referrer, user language, post categories and tags as event parameters, so you can create better custom audiences.", 'facebook-conversion-pixel' ) ?></span></td>
		</tr>
		<tr <?php echo esc_attr( $pro_tooltip ) ?>>
			<th><?php esc_attr_e( 'UTM Tag support', 'facebook-conversion-pixel' ) ?></th>
			<td><?php echo fca_pc_input( 'utm_support', '', $utm_support_on, 'checkbox' ) ?>
			<span class='fca_pc_hint'><?php esc_attr_e("Send Google UTM source, medium, campaign, term, and content as event parameters, so you can target your visitors based on Google Analytics data.", 'facebook-conversion-pixel' ) ?></span></td>
		</tr>

		<tr <?php echo esc_attr( $pro_tooltip ) ?>>
			<th><?php esc_attr_e( 'AMP support', 'facebook-conversion-pixel' ) ?></th>
			<td><?php echo fca_pc_input( 'amp_integration', '', $amp_on, 'checkbox' ) ?>
			<span class='fca_pc_hint'><?php esc_attr_e("Make sure your pixel fires on AMP pages.", 'facebook-conversion-pixel' ) ?></span></td>
		</tr>

	</table>

	<?php
	return ob_get_clean();
}


function fca_pc_add_more_integrations( $options ) {

	$qc_active = ( is_plugin_active( 'quiz-cat/quizcat.php' ) OR
				 is_plugin_active( 'quiz-cat-premium/quizcat.php' ) OR
				 is_plugin_active( 'quiz-cat-wp/quizcat.php' ) );

	$lpc_active = ( is_plugin_active( 'landing-page-cat/landing-page-cat.php' ) OR
				  is_plugin_active( 'landing-page-cat-premium/landing-page-cat.php' ) OR
				  is_plugin_active( 'landing-page-cat-wp/landing-page-cat.php' ) );

	$eoi_active = class_exists( 'DhEasyOptIns' );

	$ept_active = ( is_plugin_active( 'easy-pricing-tables-premium/easy-pricing-tables-premium.php' ) OR
					is_plugin_active( 'easy-pricing-tables/pricing-table-plugin.php' ) );

	$search_integration_on = empty( $options['search_integration'] ) ? '' : 'on';
	$quizcat_integration_on = empty( $options['quizcat_integration'] ) ? '' : 'on';
	$optincat_integration_on = empty( $options['optincat_integration'] ) ? '' : 'on';
	$landingpagecat_integration_on = empty( $options['landingpagecat_integration'] ) ? '' : 'on';
	$ept_integration_on = empty( $options['ept_integration'] ) ? '' : 'on';
	$video_events_on = empty( $options['video_events'] ) ? '' : 'on';

	ob_start(); ?>
	<div id="fca_pc_integrations_table" style="display: none">
		<h3><?php esc_attr_e( 'WordPress Integrations', 'facebook-conversion-pixel' ) ?></h3>
		<table class='fca_pc_setting_table fca_pc_integrations_table'>
			<tr>
				<th><?php esc_attr_e( 'WordPress Search Event', 'facebook-conversion-pixel' ) ?></th>
				<td><?php echo fca_pc_input( 'search_integration', '', $search_integration_on, 'checkbox' ) ?>
				<span class='fca_pc_hint'><?php esc_attr_e("Trigger the Search event when a search is performed using WordPress' built-in search feature.", 'facebook-conversion-pixel' ) ?></span></td>
			</tr>
			<tr>
			<?php if ( $ept_active ) { ?>
				<th>
					<?php esc_attr_e( 'Easy Pricing Tables', 'facebook-conversion-pixel' ) ?>
					<p class='installed-text installed'><span class="dashicons dashicons-yes"></span><?php esc_attr_e( 'Installed', 'facebook-conversion-pixel' ) ?></p>
				</th>
				<td>
					<?php echo fca_pc_input( 'ept_integration', '', $ept_integration_on, 'checkbox' ) ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Send InitiateCheckout event to Facebook.", 'facebook-conversion-pixel' ) ?>
					<a target='_blank' href='https://fatcatapps.com/knowledge-base/pixel-cat-integrations/'><?php esc_attr_e( 'Learn More...', 'facebook-conversion-pixel' ) ?></a></span>
				</td>
			<?php } else { ?>
				<th class='fca-pc-integration-disabled'>
					<?php esc_attr_e( 'Easy Pricing Tables', 'facebook-conversion-pixel' ) ?>
					<p class='installed-text'><span class="dashicons dashicons-no"></span><?php esc_attr_e( 'Not Installed', 'facebook-conversion-pixel' ) ?></p>
				</th>
				<td>
					<?php echo fca_pc_input( 'ept_integration', '', false, 'checkbox', 'disabled' ) ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Create beautiful pricing comparison tables that increase your sales.", 'facebook-conversion-pixel' ) ?>
					<a target='_blank' href='https://fatcatapps.com/easypricingtables/'><?php esc_attr_e( 'Learn more here', 'facebook-conversion-pixel' ) ?></a>.</span>
				</td>
			<?php } ?>
			</tr>
			<tr>
			<?php if ( $eoi_active ) { ?>
				<th>
					<?php esc_attr_e( 'Optin Cat', 'facebook-conversion-pixel' ) ?>
					<p class='installed-text installed'><span class="dashicons dashicons-yes"></span><?php esc_attr_e( 'Installed', 'facebook-conversion-pixel' ) ?></p>
				</th>
				<td>
					<?php echo fca_pc_input( 'optincat_integration', '', $optincat_integration_on, 'checkbox' ) ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Send Lead event to Facebook.", 'facebook-conversion-pixel' ) ?>
					<a target='_blank' href='https://fatcatapps.com/knowledge-base/pixel-cat-integrations/'><?php esc_attr_e( 'Learn More...', 'facebook-conversion-pixel' ) ?></a></span>
				</td>
			<?php } else { ?>
				<th class='fca-pc-integration-disabled'>
					<?php esc_attr_e( 'Optin Cat', 'facebook-conversion-pixel' ) ?>
					<p class='installed-text'><span class="dashicons dashicons-no"></span><?php esc_attr_e( 'Not Installed', 'facebook-conversion-pixel' ) ?></p>
				</th>
				<td>
					<?php echo fca_pc_input( 'optincat_integration', '', false, 'checkbox', 'disabled' ) ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Convert more blog readers into email subscribers using Optin Cat.", 'facebook-conversion-pixel' ) ?>
					<a target='_blank' href='https://fatcatapps.com/optincat/'><?php esc_attr_e( 'Learn more here', 'facebook-conversion-pixel' ) ?></a>.</span>
				</td>
			<?php } ?>
			</tr>
			<tr>
			<?php if ( $qc_active ) { ?>
				<th>
					<?php esc_attr_e( 'Quiz Cat', 'facebook-conversion-pixel' ) ?>
					<p class='installed-text installed'><span class="dashicons dashicons-yes"></span><?php esc_attr_e( 'Installed', 'facebook-conversion-pixel' ) ?></p>
				</th>
				<td>
					<?php echo fca_pc_input( 'quizcat_integration', '', $quizcat_integration_on, 'checkbox' ) ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Send Lead event, plus custom events related to Quiz engagement to Facebook.", 'facebook-conversion-pixel' ) ?>
					<a target='_blank' href='https://fatcatapps.com/knowledge-base/pixel-cat-integrations/'><?php esc_attr_e( 'Learn More...', 'facebook-conversion-pixel' ) ?></a></span>
				</td>
			<?php } else { ?>
				<th class='fca-pc-integration-disabled'>
					<?php esc_attr_e( 'Quiz Cat', 'facebook-conversion-pixel' ) ?>
					<p class='installed-text'><span class="dashicons dashicons-no"></span><?php esc_attr_e( 'Not Installed', 'facebook-conversion-pixel' ) ?></p>
				</th>
				<td>
					<?php echo fca_pc_input( 'quizcat_integration', '', false, 'checkbox', 'disabled' ) ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Quiz Cat lets you create beautiful quizzes that boost social shares and grow your email list.", 'facebook-conversion-pixel' ) ?>
					<a target='_blank' href='https://fatcatapps.com/quizcat/'><?php esc_attr_e( 'Learn more here', 'facebook-conversion-pixel' ) ?></a>.</span>
				</td>
			<?php } ?>
			</tr>
			<tr>
			<?php if ( $lpc_active ) { ?>
				<th>
					<?php esc_attr_e( 'Landing Page Cat', 'facebook-conversion-pixel' ) ?>
					<p class='installed-text installed'><span class="dashicons dashicons-yes"></span><?php esc_attr_e( 'Installed', 'facebook-conversion-pixel' ) ?></p>
				</th>
				<td>
					<?php echo fca_pc_input( 'landingpagecat_integration', '', $landingpagecat_integration_on, 'checkbox' ) ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Send Lead event to Facebook.", 'facebook-conversion-pixel' ) ?>
					<a target='_blank' href='https://fatcatapps.com/knowledge-base/pixel-cat-integrations/'><?php esc_attr_e( 'Learn More...', 'facebook-conversion-pixel' ) ?></a></span>
				</td>
			<?php } else { ?>
				<th class='fca-pc-integration-disabled'>
					<?php esc_attr_e( 'Landing Page Cat', 'facebook-conversion-pixel' ) ?>
					<p class='installed-text'><span class="dashicons dashicons-no"></span><?php esc_attr_e( 'Not Installed', 'facebook-conversion-pixel' ) ?></p>
				</th>
				<td>
					<?php echo fca_pc_input( 'landingpagecat_integration', '', false, 'checkbox', 'disabled' ) ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Landing Page Cat lets you publish simple, beautiful landing pages in 2 minutes.", 'facebook-conversion-pixel' ) ?>
					<a target='_blank' href='https://fatcatapps.com/landingpagecat/'><?php esc_attr_e( 'Learn more here', 'facebook-conversion-pixel' ) ?></a>.</span>
				</td>
			<?php } ?>
			</tr>
			<?php if ( FCA_PC_PLUGIN_PACKAGE === 'Lite' ) { 
				$tooltip = esc_attr__("This option is available only with Pixel Cat Pro. Click the blue button on the right-hand side to learn more.", 'facebook-conversion-pixel' );
				?>
				<tr class='fca-pc-integration-disabled fca_pc_pro_tooltip' title="<?php echo $tooltip ?>" >
					<th >
						<?php esc_attr_e( 'Video Events', 'facebook-conversion-pixel' ) ?>
					</th>
					<td>
						<?php echo fca_pc_input( 'video_events', '', false, 'checkbox', 'disabled' ) ?>
						<span class='fca_pc_hint'><?php esc_attr_e("Enable Video Events feature.", 'facebook-conversion-pixel' ) ?></span>
					</td>
				</tr>
			<?php } else { ?>
				<tr>
					<th>
						<?php esc_attr_e( 'Video Events', 'facebook-conversion-pixel' ) ?>
					</th>
					<td>
						<?php echo fca_pc_input( 'video_events', '', $video_events_on, 'checkbox' ) ?>
						<span class='fca_pc_hint'><?php esc_attr_e("Enable Video Events feature.", 'facebook-conversion-pixel' ) ?></span>
					</td>
				</tr>
			<?php } ?>
			
		</table>
	</div>
	<?php
	return ob_get_clean();
}

function fca_pc_add_e_commerce_integrations( $options ) {
	ob_start(); ?>
	<div id="fca-pc-e-commerce">
		<?php
		echo fca_pc_add_woo_integrations( $options );
		echo fca_pc_add_edd_integrations( $options );
		?>
	</div>
	<?php
	return ob_get_clean();
}

function fca_pc_add_woo_integrations( $options ) {

	$version_ok = false;
	$woo_is_active = is_plugin_active( 'woocommerce/woocommerce.php' );

	if ( $woo_is_active ) {
		global $woocommerce;
		if ( version_compare( $woocommerce->version, '3.0.0', ">=" ) ) {
			$version_ok = true;
		}
	}
	$woo_active = $woo_is_active && $version_ok;

	$woo_integration_on = empty( $options['woo_integration'] ) ? '' : 'on';
	$woo_extra_params = empty( $options['woo_extra_params'] ) ? '' : 'on';
	$woo_delay = empty( $options['woo_delay'] ) ? 0 : intVal($options['woo_delay']);
	$woo_feed_on = empty( $options['woo_feed'] ) ? '' : 'on';
	$woo_include_variations = isset( $options['woo_variations'] ) ? $options['woo_variations'] : 'on';

	$woo_id_mode = empty( $options['woo_product_id'] ) ? 'post_id' : $options['woo_product_id'];
	$id_options = array(
		'post_id' => 'WordPress Post ID (recommended)',
		'sku' => 'WooCommerce SKU',
	);

	$woo_desc_mode = empty( $options['woo_desc_mode'] ) ? 'description' : $options['woo_desc_mode'];
	$description_options = array(
		'description' => 'Product Content',
		'short' => 'Product Short Description',
	);

	$excluded_categories = empty( $options['woo_excluded_categories'] ) ? array() : $options['woo_excluded_categories'];
	$categories = fca_pc_woo_product_cat_and_tags();

	$google_product_category = empty( $options['google_product_category'] ) ? '' : $options['google_product_category'];
	$pro_tooltip = FCA_PC_PLUGIN_PACKAGE !== 'Lite' ? '' : 'class="fca_pc_pro_tooltip" title="' . esc_attr__("This option is available only with Pixel Cat Pro. Click the blue button on the right-hand side to learn more.", 'facebook-conversion-pixel' ) . '"';

	ob_start(); ?>
	<div id='fca-pc-woo-table'>
		<?php if ( !$woo_is_active ) { ?>
			<h3>
				<?php esc_attr_e( 'WooCommerce', 'facebook-conversion-pixel' ) ?>
				<span class="installed-text"><span alt="f158" class="dashicons dashicons-no-alt"></span><?php esc_attr_e( 'Not Installed', 'facebook-conversion-pixel' ) ?></span>
			</h3>
			<p><?php esc_attr_e( 'Plugin not detected. To use this integration, please install Woocommerce v.3.0 or greater.', 'facebook-conversion-pixel' ) ?></p>
		<?php } else {
			?>
			<h3>
				<?php esc_attr_e( 'WooCommerce Integration', 'facebook-conversion-pixel' ) ?>
				<span class="installed-text installed"><div alt="f147" class="dashicons dashicons-yes"></div><?php esc_attr_e( 'Installed', 'facebook-conversion-pixel' ) ?></span>
			</h3>

			<table class='fca_pc_integrations_table'>
				<tr>
					<th><?php esc_attr_e('Track Shopping Events', 'facebook-conversion-pixel') ?></th>
						<td><?php echo fca_pc_input( 'woo_integration', '', $woo_integration_on, 'checkbox' ) ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Automatically send the following events to Facebook: Add&nbsp;To&nbsp;Cart, Add&nbsp;Payment&nbsp;Info, Purchase, View&nbsp;Content, Search, and Add&nbsp;to&nbsp;Wishlist.", 'facebook-conversion-pixel') ?></span></td>
				</tr>
				<tr <?php echo $pro_tooltip; ?>>
					<th><?php esc_attr_e( 'Delay ViewContent Event', 'facebook-conversion-pixel' ) ?></th>
						<td><?php echo fca_pc_input( 'woo_delay', '', $woo_delay, 'number', "min='0' max='100' step='1'" ) ?>seconds<br>
					<span class='fca_pc_hint'><?php esc_attr_e("Exclude bouncing visitors by delaying the ViewContent event on product pages.", 'facebook-conversion-pixel' ) ?></span></td>
				</tr>
				<tr <?php echo $pro_tooltip; ?>>
					<th><?php esc_attr_e( 'Send Extra Info with Purchase Event', 'facebook-conversion-pixel' ) ?></th>
						<td><?php echo fca_pc_input( 'woo_extra_params', '', $woo_extra_params, 'checkbox' ) ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Sends LTV (lifetime value), coupon codes (if used) and shipping info as parameters of your purchase event, so you can build better, more targeted custom audiences.", 'facebook-conversion-pixel' ) ?></span></td>
				</tr>
				<tr>
					<th><?php esc_attr_e( 'Product Feed', 'facebook-conversion-pixel' ) ?></th>
						<td><?php echo fca_pc_input( 'woo_feed', '', $woo_feed_on, 'checkbox' ) ?>
					<span class='fca_pc_hint'><?php esc_attr_e("A Product Feed is required to use Facebook Dynamic Product Ads.", 'facebook-conversion-pixel' ) ?></span></td>
				</tr>

				<tr class='fca-pc-woo-feed-settings'>
					<th><?php esc_attr_e( 'Include Variations', 'facebook-conversion-pixel' ) ?></th>
						<td><?php echo fca_pc_input( 'woo_variations', '', $woo_include_variations, 'checkbox' ) ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Having a lot of product variations can cause load issues with your feed, disable to exclude variations from the feed.", 'facebook-conversion-pixel' ) ?></span></td>
				</tr>

				<tr class='fca-pc-woo-feed-settings'>
					<th><?php esc_attr_e( 'Feed URL', 'facebook-conversion-pixel' ) ?></th>
						<td><input style='height: 35px;' type='text' onclick='this.select()' readonly value='<?php echo get_site_url() . '?feed=pixelcat' ?>' >
					<span class='fca_pc_hint'><?php esc_attr_e("You'll need above URL when setting up your Facebook Product Catalog.", 'facebook-conversion-pixel' ) ?></span></td>
				</tr>
				<tr class='fca-pc-woo-feed-settings'>
					<th><?php esc_attr_e( 'Exclude Categories/Tags', 'facebook-conversion-pixel' ) ?></th>
						<td><?php echo fca_pc_select_multiple( 'woo_excluded_categories', $excluded_categories, $categories ); ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Selected product categories and tags will not be included in your feed.", 'facebook-conversion-pixel' ) ?></span></td>
				</tr>
				<tr class='fca-pc-woo-feed-settings'>
					<th><?php esc_attr_e( 'Advanced Feed Settings', 'facebook-conversion-pixel' ) ?></th>
						<td><?php echo '<span id="fca-pc-show-feed-settings" class="fca-pc-feed-toggle">' . esc_attr__( '(show)', 'facebook-conversion-pixel' ) . '</span><span style="display: none;" id="fca-pc-hide-feed-settings" class="fca-pc-feed-toggle">' . esc_attr__( '(hide)', 'facebook-conversion-pixel' ) . '</span>' ?></td>
				</tr>
				<tr class='fca-pc-woo-feed-settings fca-pc-woo-advanced-feed-settings' style='display:none;'>
					<th><?php esc_attr_e( 'Product Identifier', 'facebook-conversion-pixel' ) ?></th>
						<td> <?php echo fca_pc_select( 'woo_product_id', $woo_id_mode, $id_options ); ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Set how to identify your product using the Facebook Pixel (content_id) and the feed (g:id). Please make sure to have SKUs set on your products if you choose WooCommerce SKU.", 'facebook-conversion-pixel' ) ?></span></td>
				</tr>
				<tr class='fca-pc-woo-feed-settings fca-pc-woo-advanced-feed-settings' style='display:none;'>
					<th><?php esc_attr_e( 'Description Field', 'facebook-conversion-pixel' ) ?></th>
						<td><?php echo fca_pc_select( 'woo_desc_mode', $woo_desc_mode, $description_options ); ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Set the field to use as your product description for the Facebook product catalog", 'facebook-conversion-pixel' ) ?></span></td>
				</tr>
				<tr class='fca-pc-woo-feed-settings fca-pc-woo-advanced-feed-settings' style='display:none;'>
					<th><?php esc_attr_e( 'Google Product Category', 'facebook-conversion-pixel' ) ?></th>
						<td><?php echo fca_pc_input( 'google_product_category', 'e.g. 2271', $google_product_category, 'number' ) ?>
					<span class='fca_pc_google_hint'><?php echo esc_attr__("Enter your numeric Google Product Category ID here.  If your category is \"Apparel & Accessories > Clothing > Dresses\", enter 2271.  ", 'facebook-conversion-pixel' ) . '<a href="http://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.xls" target="_blank">' . esc_attr__("Click here", 'facebook-conversion-pixel' ) . '</a> ' . esc_attr__("for a current spreadsheet of all Categories & IDs.", 'facebook-conversion-pixel' ) ?></span></td>
				</tr>
			</table>
			<?php
		} ?>
	</div>
<?php return ob_get_clean();

}

function fca_pc_add_edd_integrations( $options ) {

	$edd_active = is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' );
	$edd_integration_on = empty( $options['edd_integration'] ) ? '' : 'on';
	$edd_extra_params = empty( $options['edd_extra_params'] ) ? '' : 'on';
	$edd_delay = empty( $options['edd_delay'] ) ? 0 : intVal($options['edd_delay']);
	$edd_feed_on = empty( $options['edd_feed'] ) ? '' : 'on';

	$edd_desc_mode = empty( $options['edd_desc_mode'] ) ? 'content' : $options['edd_desc_mode'];
	$description_options = array(
		'content' => 'Product Description',
		'excerpt' => 'Excerpt',
	);

	$excluded_categories = empty( $options['edd_excluded_categories'] ) ? array() : $options['edd_excluded_categories'];
	$categories = fca_pc_edd_product_cat_and_tags();
	$pro_tooltip = FCA_PC_PLUGIN_PACKAGE !== 'Lite' ? '' : 'class="fca_pc_pro_tooltip" title="' . esc_attr__("This option is available only with Pixel Cat Pro. Click the blue button on the right-hand side to learn more.", 'facebook-conversion-pixel' ) . '"';

	ob_start();	?>

	<div id='fca-pc-edd-table'>

		<?php if ( !$edd_active ) {
			?>
			<h3>
				<?php esc_attr_e( 'Easy Digital Downloads Integration', 'facebook-conversion-pixel' ) ?>
				<span class="installed-text"><span alt="f158" class="dashicons dashicons-no-alt"></span><?php esc_attr_e( 'Not Installed', 'facebook-conversion-pixel' ) ?></span>
			</h3>
			<p><?php esc_attr_e( 'Plugin not detected. To use this integration, please install Easy Digital Downloads v2.8 or greater.', 'facebook-conversion-pixel' ) ?></p>
			<?php
		} else {
			?>
			<h3>
				<?php esc_attr_e( 'Easy Digital Downloads Integration', 'facebook-conversion-pixel' ) ?>
				<span class="installed-text installed"><div alt="f147" class="dashicons dashicons-yes"></div><?php esc_attr_e( 'Installed', 'facebook-conversion-pixel' ) ?></span>
			</h3>
			<table class='fca_pc_integrations_table'>
				<tr>
					<th><?php esc_attr_e( 'Track EDD Events', 'facebook-conversion-pixel' ) ?></th>
						<td><?php echo fca_pc_input( 'edd_integration', '', $edd_integration_on, 'checkbox' ) ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Automatically send the following Easy Digital Downloads events to Facebook: Add To Cart, Add&nbsp;Payment&nbsp;Info, Purchase, View&nbsp;Content, Search, and Add&nbsp;to&nbsp;Wishlist.", 'facebook-conversion-pixel' ) ?></span></td>
				</tr>

				<tr <?php echo $pro_tooltip ?>>
					<th><?php esc_attr_e( 'Delay ViewContent Event', 'facebook-conversion-pixel' ) ?></th>
						<td><?php echo fca_pc_input( 'edd_delay', '', $edd_delay, 'number', "min='0' max='100' step='1'" ) ?>seconds<br>
					<span class='fca_pc_hint'><?php esc_attr_e("Exclude bouncing visitors by delaying the ViewContent event on download pages.", 'facebook-conversion-pixel' ) ?></span></td>
				</tr>
				<tr <?php echo $pro_tooltip ?>>
					<th><?php esc_attr_e( 'Send Extra Info with Purchase Event', 'facebook-conversion-pixel' ) ?></th>
						<td><?php echo fca_pc_input( 'edd_extra_params', '', $edd_extra_params, 'checkbox' ) ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Sends LTV (lifetime value), coupon codes (if used) and shipping info as parameters of your purchase event, so you can build better, more targeted custom audiences.", 'facebook-conversion-pixel' ) ?></span></td>
				</tr>

				<tr>
					<th><?php esc_attr_e( 'Product Feed', 'facebook-conversion-pixel' ) ?></th>
						<td><?php echo fca_pc_input( 'edd_feed', '', $edd_feed_on, 'checkbox' ) ?>
					<span class='fca_pc_hint'><?php esc_attr_e("A Product Feed is required to use Facebook Dynamic Product Ads.", 'facebook-conversion-pixel' ) ?></span></td>
				</tr>
				<tr class='fca-pc-edd-feed-settings'>
					<th><?php esc_attr_e( 'Feed URL', 'facebook-conversion-pixel' ) ?></th>
						<td><input style='height: 35px;' type='text' onclick='this.select()' readonly value='<?php echo get_site_url() . '?feed=edd-pixelcat' ?>' >
					<span class='fca_pc_hint'><?php esc_attr_e("You'll need above URL when setting up your Facebook Product Catalog.", 'facebook-conversion-pixel' ) ?></span></td>
				</tr>
				<tr class='fca-pc-edd-feed-settings'>
					<th><?php esc_attr_e( 'Exclude Categories/Tags', 'facebook-conversion-pixel' ) ?></th>
						<td><?php echo fca_pc_select_multiple( 'edd_excluded_categories', $excluded_categories, $categories ); ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Selected product categories and tags will not be included in your feed.", 'facebook-conversion-pixel' ) ?></span></td>
				</tr>
				<tr class='fca-pc-edd-feed-settings' style='display:none;'>
					<th><?php esc_attr_e( 'Description Field', 'facebook-conversion-pixel' ) ?></th>
						<td><?php echo fca_pc_select( 'edd_desc_mode', $edd_desc_mode, $description_options ); ?>
					<span class='fca_pc_hint'><?php esc_attr_e("Set the field to use as your product description for the Facebook product catalog", 'facebook-conversion-pixel' ) ?></span></td>
				</tr>

			</table>
			<?php
		} ?>
	</div>
<?php return ob_get_clean();

}

function fca_pc_marketing_metabox() {
	ob_start(); ?>
	<div id='fca-pc-marketing-metabox' style='display: none;'>
		<h3><?php esc_attr_e( 'Get Pixel Cat Premium', 'facebook-conversion-pixel' ); ?></h3>

		<ul>
			<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Dynamic Events, so you can build <strong>powerful custom audiences</strong>', 'facebook-conversion-pixel' ); ?></li>
			<li><div class="dashicons dashicons-yes"></div> <?php esc_attr_e( 'Advanced Matching for improved reach', 'facebook-conversion-pixel' ); ?></li>
			<li><div class="dashicons dashicons-yes"></div> <?php esc_attr_e( 'Powerful Custom Events & Parameters', 'facebook-conversion-pixel' ); ?></li>
			<li><div class="dashicons dashicons-yes"></div> <?php esc_attr_e( 'Multiple Pixels', 'facebook-conversion-pixel' ); ?></li>
			<li><div class="dashicons dashicons-yes"></div> <?php esc_attr_e( 'Google AMP Integration', 'facebook-conversion-pixel' ); ?></li>
			<li><div class="dashicons dashicons-yes"></div> <?php esc_attr_e( '1-Click WooCommerce Integration', 'facebook-conversion-pixel' ); ?></li>
			<li><div class="dashicons dashicons-yes"></div> <?php esc_attr_e( '1-Click Easy Digital Downloads Integration', 'facebook-conversion-pixel' ); ?></li>
			<li><div class="dashicons dashicons-yes"></div> <?php esc_attr_e( 'Priority Email Support', 'facebook-conversion-pixel' ); ?></li>
		</ul>
		<div style='text-align: center'>
			<a href="https://fatcatapps.com/pixelcat/premium" target="_blank" class="button button-primary button-large"><?php esc_attr_e( 'Run Better Ads >>', 'facebook-conversion-pixel' ); ?></a>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
