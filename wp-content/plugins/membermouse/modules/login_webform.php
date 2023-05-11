<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
?>

<div class="mm-wrap">
<div style='padding-left: 10px;margin-top:10px;'>
    
<div style='width:650px'>
<p><?php echo _mmt("Below is a login webform that can be included anywhere which allows your members to login from any location."); ?> 
<br/><?php echo _mmt("Just copy and paste the HTML code below"); ?> :</p>
</div>
	
<textarea id='mm-form-html' rows='18' cols='90' readonly style='font-family: Courier New; font-size:11px;' onclick="jQuery('#mm-form-html').focus(); jQuery('#mm-form-html').select();">
<form action="<?php echo site_url(); ?>/wp-login.php" method="post"> 
<table>
  <tr>
    <td><?php echo _mmt("Username"); ?></td>
    <td><input type="text" id="log" name="log" /></td>
  </tr>
  <tr>
    <td><?php echo _mmt("Password"); ?></td>
    <td><input type="password" id="pwd" name="pwd" /></td>
  </tr>
  <tr>
    <td></td>
    <td>
      <input type="submit" name="submit" value="<?php echo _mmt("Login"); ?>" />
	  <input name="rememberme" type="checkbox" checked="checked" value="forever" />
	  <?php echo _mmt("Remember me"); ?>
    </td>
  </tr>
  <tr>
    <td></td>
    <td>
      <a href="<?php echo MM_CorePageEngine::getUrl(MM_CorePageType::$FORGOT_PASSWORD); ?>"><?php echo _mmt("Forgot Password"); ?></a>
    </td>
  </tr>
</table>
</form></textarea>
</div>
</div>