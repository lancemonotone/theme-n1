<?php namespace N1_Durable_Goods;

function get_signin(): string {
    $home_url     = home_url();
    $redirect_url = urlencode( site_url( $_SERVER[ 'REQUEST_URI' ] ) );
    $signin_text  = _( 'Sign In' );

    return <<<EOD
<div class="signin-container">
    <form action="{$home_url}/wp-login.php?redirect_to={$redirect_url}" method="POST">
        <label for="log">Email Address</label>
        <input type="text" id="log"  name="log"/>
        
        <label for="pwd">Password</label>
        <input type="password" id="pwd"  name="pwd"/>
        
        <div class="links">
            <input type="submit" class="button" value="{$signin_text}"/>
            <a href="{$home_url}/forgot-password/">Forgot Password</a>
        </div>
    </form>
</div>
EOD;
}

function get_status(): string {
    $home_url     = home_url();
    $user_name    = wp_get_current_user()->display_name;
    $user_status  = __( mm_member_data( [ "name" => "statusName" ] ) );
    $status_class = $user_status === 'Active' ? 'status-active' : 'status-inactive';
    $status_text  = _( 'Subscription Status' );
    $renew_text   = _( 'Renew Subscription' );
    $account_text = _( 'Manage Account' );
    $gift_text    = _( 'Give a Gift Subscription' );
    $logout_text  = _( 'Sign Out' );
    $logout_link  = wp_logout_url( home_url() );

    $user_svg = <<<EOD
<svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none">
    <path fill-rule="evenodd" clip-rule="evenodd" d="M6 8C6 4.68629 8.68629 2 12 2C15.3137 2 18 4.68629 18 8C18 11.3137 15.3137 14 12 14C8.68629 14 6 11.3137 6 8Z"/>
    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.43094 16.9025C7.05587 16.2213 9.2233 16 12 16C14.771 16 16.9351 16.2204 18.5586 16.8981C20.3012 17.6255 21.3708 18.8613 21.941 20.6587C22.1528 21.3267 21.6518 22 20.9592 22H3.03459C2.34482 22 1.84679 21.3297 2.0569 20.6654C2.62537 18.8681 3.69119 17.6318 5.43094 16.9025Z"/>
</svg>

EOD;

    return <<<EOD
<div class="status-container {$status_class}">
    <div class="name-status">
        <span class="user-svg">{$user_svg}</span>
        <div class="name">{$user_name}</div>
        <div class="status">{$status_text}: <strong>{$user_status}</strong></div>
    </div>
    <div class="links">
        <a class="link-renew" href="{$home_url}/renew/">{$renew_text}</a>
        <a class="link-my-account" href="{$home_url}/your-account/">{$account_text}</a>
        <a class="link-gift-subscription" href="https://shop.nplusonemag.com/products/gift-subscription">{$gift_text}</a>
    </div>
    <a class="button" href="{$logout_link}">{$logout_text}</a>
</div>
EOD;
}

$actions_text  = is_user_logged_in() ? _( 'My Account' ) : _( 'Sign In' );
$actions_class = is_user_logged_in() ? 'logged-in' : '';
?>
<div id="nav-actions" class="accordion">
    <input id="main-nav-toggle" type="checkbox" class="toggle visually-hidden" role="button" aria-expanded="false"/>
    <label class="toggle-label <?php echo $actions_class ?>" for="main-nav-toggle">
        <?php echo $actions_text ?>
        <span class="drop-icon"></span>
    </label>
    <section class="toggle-section">
        <?php echo is_user_logged_in() ? get_status() : get_signin() ?>
    </section>
</div>
