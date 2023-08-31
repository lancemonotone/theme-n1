<?php namespace N1_Durable_Goods;
// $url = urlencode(get_permalink($post_id));
$tw_href = 'https://twitter.com/nplusonemag';
$fb_href = 'https://www.facebook.com/nplusonemag';
$ig_href = 'https://instagram.com/nplusonemag';
?>
<section class="social">
    <div class="floatwrapper">
        <h3 class="social category">
            <span class="module-hed"><?php _e( 'Follow Us' ) ?></span>
        </h3>
        <ul class="social">
            <li class="social twitter">
                <a title="Twitter" href="<?php echo $tw_href ?>" class="social icon twitter"><?php _e( 'Twitter' ) ?></a>
            </li>
            <li class="social instagram">
                <a title="Instagram" href="<?php echo $ig_href ?>" class="social icon instagram"><?php _e( 'Instagram' ) ?></a>
            </li>
            <li class="social facebook">
                <a title="Facebook" href="<?php echo $fb_href ?>" class="social icon facebook"><?php _e( 'Facebook' ) ?></a>
            </li>
        </ul>
    </div>
</section>
