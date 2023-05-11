<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */

// check if Limit Login Attempts plugin is active
$plugins = get_option('active_plugins');
$required_plugin = "limit-login-attempts/limit-login-attempts.php";
$pluginActive = false;

if(in_array($required_plugin, $plugins))
{
	$pluginActive = true;
}
?>
<div style="width: 600px; margin-top: 8px;" class="mm-divider"></div> 
<div class="mm-wrap">
    <p class="mm-header-text"><?php echo _mmt("Limit Login Attempts"); ?></p>
   
	<div style="margin-top:10px;">
		<p><?php echo sprintf(_mmt("MemberMouse integrates with a popular plugin for %slimiting login attempts%s written by %sJohan Eenfeldt%s."),'<a href="http://wordpress.org/plugins/limit-login-attempts/" target="_blank">','</a>','<a href="http://devel.kostdoktorn.se/" target="_blank">','</a>'); ?></p>
		
		<?php if($pluginActive) { ?>
		<p>
			<?php echo MM_Utils::getCheckIcon(); ?> <strong><?php echo _mmt("Plugin activated"); ?></strong>
		</p>
		<?php } else { ?>
		<p>
			<a href="<?php echo esc_url( network_admin_url('plugin-install.php?tab=plugin-information&plugin=limit-login-attempts&TB_iframe=true&width=650&height=600' ) ); ?>" class="mm-ui-button green thickbox"><?php echo _mmt("Install Plugin"); ?></a>
			<a href="<?php echo esc_url( network_admin_url('plugin-install.php?tab=plugin-information&plugin=limit-login-attempts&TB_iframe=true&width=650&height=600' ) ); ?>" class="mm-ui-button thickbox"><?php echo _mmt("Learn more"); ?></a> 
		</p>
		<?php } ?>
	</div>
</div>