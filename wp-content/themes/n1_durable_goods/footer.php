<?php namespace N1_Durable_Goods;

$issue_title = N1_Magazine::get_current_issue()->post_title;
$issue_name  = get_field( 'issue_name', N1_Magazine::get_current_issue()->ID );
$footer_copy = get_field( 'options_footer_copy', 'options' );
?>
<footer>
    <div class="footer-wrapper">
        <section class="footer-about">
            <a class="footer-logo" href="<?php echo home_url() ?>" aria-label="Visit n+1 homepage">
                n+1
            </a>
            <?php echo str_replace( [ '{{issue-number}}', '{{issue-name}}' ], [ $issue_title, $issue_name ], $footer_copy ) ?>
        </section><!-- /#about -->

        <section class="footer-nav">
            <?php // get menu name from WP
            $menu_id   = 146;
            $menu_object = wp_get_nav_menu_object( $menu_id );
            if ( $menu_object ) {
                $menu_name = $menu_object->name;
                ?>
                <nav>
                    <h6 class="footer-nav-title"><?= $menu_name ?></h6>
                    <?php wp_nav_menu( [ 'menu' => 'about-n+1', 'menu_class' => 'footer-nav-list', 'container' => false ] ); ?>
                </nav>
            <?php } ?>

            <?php // get menu name from WP
            $menu_id   = 145;
            $menu_object = wp_get_nav_menu_object( $menu_id );
            if ( $menu_object ) {
                $menu_name = $menu_object->name;
                ?>
                <nav>
                    <h6 class="footer-nav-title"><?= $menu_name ?></h6>
                    <ul class="footer-nav-list">
                        <li class="footer-nav-entry">
                            <a href="<?php echo N1_Magazine::get_current_issue_url() ?>">Current Issue</a>
                        </li>
                    </ul>
                    <?php wp_nav_menu( [ 'menu' => 'the-magazine', 'menu_class' => 'footer-nav-list', 'container' => false ] ); ?>
                </nav>
            <?php } ?>

            <div class="footer-divider"></div>

            <div class="footer-social">
                <?php the_widget( '\N1_Durable_Goods\Module_Newsletter' ) ?>
                <?php the_widget( '\N1_Durable_Goods\Module_Social' ) ?>
            </div>

        </section>

        <section class="footer-bottom">
            <p class="footer-copyright">Copyright &copy; <?php echo date( 'Y' ) ?> n+1 Foundation</p>
            <p class="footer-legal">
                <a href="/about/terms/">Terms &amp; Conditions</a>
                |
                <a href="/about/privacy/">Privacy Policy</a>
            </p>
        </section>
    </div>
</footer>

<?php wp_footer(); ?>

<!-- Chartbeat config -->
<script type='text/javascript'>

    /** SECTIONS/AUTHORS CONFIGURATION  **/
    //      _sf_async_config.sections = 'Politics, Reviews, Essays'; // CHANGE THIS -- Please pass a comma delimited String containing Section(s)
    //      _sf_async_config.authors = 'Jane Doe, John Smith'; // CHANGE THIS -- Please pass a comma delimited String containing Author(s)

    /** SUBSCRIBER CONFIGURATION **/
    var _cbq = window._cbq = (window._cbq || [])
    _cbq.push(['_acct', 'anon']); // Please push 'anon', 'lgdin' or 'paid' as the value in this push depending on user type

    (function () {
        function loadChartbeat () {
            window._sf_endpt = (new Date()).getTime()
            var e = document.createElement('script')
            e.setAttribute('language', 'javascript')
            e.setAttribute('type', 'text/javascript')
            e.setAttribute('async', '')
            e.setAttribute('src', '//static.chartbeat.com/js/chartbeat.js')
            document.body.appendChild(e)
        }

        loadChartbeat()
    })()
</script>

<script data-cfasync="false">
    (function (W, i, s, e, P, o, p) {
        W['WisePopsObject'] = P
        W[P] = W[P] || function () {
            (W[P].q = W[P].q || []).push(arguments)
        }, W[P].l = 1 * new Date()
        o = i.createElement(s), p = i.getElementsByTagName(s)[0]
        o.async = 1
        o.src = e
        p.parentNode.insertBefore(o, p)
    })(window, document, 'script', '//loader.wisepops.com/get-loader.js?v=1&site=FPD8NSLKuC', 'wisepops')
</script>
</body>
</html>
