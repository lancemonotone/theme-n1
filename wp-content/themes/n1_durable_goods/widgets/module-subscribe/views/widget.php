<?php namespace N1_Durable_Goods;
$context_issue = N1_Magazine::get_context_issue();
$current_issue = N1_Magazine::get_current_issue();
?>

<?php if ( Metered_Paywall::is_paywalled() ) { ?>
    <section>
        <span class="module-hed"><?php _e( 'Available Now' ) ?></span>
        <h3 class="issuetitle"><?= $current_issue->post_title ?>: <?= get_field( 'issue_name', $context_issue->ID ) ?></h3>
        <p class="prompt"><?= get_field( 'options_subscribe_prompt', 'options' ) ?></p>
        <a class="button" href="<?= home_url() ?>/subscribe"><?php _e( 'Subscribe' ) ?></a>
        <?php if ( ! is_user_logged_in() ) { ?>
            <div class="signin">
                <p class="action"><?= get_field( 'options_subscribe_action', 'options' ) ?></p>
                <form action="<?= home_url() ?>/wp-login.php?redirect_to=<?= urlencode( site_url( $_SERVER[ 'REQUEST_URI' ] ) ) ?>" method="POST">
                    <fieldset>
                        <label for="username"><?php _e( 'Email' ) ?></label>
                        <input type="text" id="log" name="log" placeholder="email"/>
                    </fieldset>

                    <fieldset>
                        <label for="password"><?php _e( 'Password' ) ?></label>
                        <input type="password" id="pwd" name="pwd" placeholder="password"/>
                    </fieldset>

                    <fieldset>
                        <input class="button" type="submit" value="<?php _e( 'Sign In' ) ?>"/>
                        <br/>
                        <a href="<?= home_url() ?>/forgot-password/">Forgot Password</a>
                    </fieldset>
                </form>
            </div>
        <?php } ?>
    </section>
<?php } ?>
