<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]><html class="ie ie7" <?php language_attributes(); ?>><![endif]-->
<!--[if IE 8]><html class="ie ie8" <?php language_attributes(); ?>><![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->

<head>
    <!-- Google tag (gtag.js) -->
    <script async
            src="https://www.googletagmanager.com/gtag/js?id=G-3NRFPFK8X6"></script>
    <script>
      window.dataLayer = window.dataLayer || []
      function gtag() {dataLayer.push(arguments)}
      gtag('js', new Date())
      gtag('config', 'G-3NRFPFK8X6')

      /* Load Typekit ansynchronously. http://www.tomjn.com/153/typekit-besbswy/ and http://blog.typekit.com/2011/05/25/loading-typekit-fonts-asynchronously/ */
      (function(){var b={kitId:'oqd3oiw',scriptTimeout:3000};var f=document.getElementsByTagName('html')[0];f.className+=' wf-loading';var c=setTimeout(function(){f.className=f.className.replace(/(\s|^)wf-loading(\s|$)/g,' ');f.className+=' wf-inactive';},b.scriptTimeout);var g=false;var a=document.createElement('script');a.src='//use.typekit.net/'+b.kitId+'.js';a.type='text/javascript';a.async='true';a.onload=a.onreadystatechange=function(){var d=this.readyState;if(g||d&&d!='complete'&&d!='loaded'){return}g=true;clearTimeout(c);try{Typekit.load(b)}catch(h){}};var e=document.getElementsByTagName('script')[0];e.parentNode.insertBefore(a,e);})()
    </script>
    <link rel="canonical" href="<?php echo home_url() ?><?php echo $_SERVER['REQUEST_URI']; ?>"/>
    <link type="application/opensearchdescription+xml" rel="search" href="/opensearch.osdd"/>
    <meta charset="<?php bloginfo( 'charset' ); ?>"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>

    <title><?php wp_title( '|', TRUE, 'right' ); ?></title>
    <link rel="profile"
          href="http://gmpg.org/xfn/11"/>
	<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
    <!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
	<![endif]-->
	<?php wp_head(); ?>

    <script>
      var _gaq = _gaq || []
      _gaq.push(['_setAccount', 'UA-8175093-1'])
      _gaq.push(['_trackPageview']);
      (function() {
        var ga = document.createElement('script')
        ga.type = 'text/javascript'
        ga.async = true
        ga.src = ('https:' == document.location.protocol ?
            'https://ssl' :
            'http://www') + '.google-analytics.com/ga.js'
        var s = document.getElementsByTagName('script')[0]
        s.parentNode.insertBefore(ga, s)
      })()

      var _sf_async_config = _sf_async_config || {}

      /** ACCOUNT CONFIGURATION START **/
      _sf_async_config.uid = 65751
      _sf_async_config.domain = 'nplusonemag.com'
      _sf_async_config.flickerControl = false
      _sf_async_config.useCanonical = true
      /** ACCOUNT CONFIGURATION END **/
    </script>
    <script async
            src="//static.chartbeat.com/js/chartbeat_mab.js"></script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async
            src="https://www.googletagmanager.com/gtag/js?id=UA-152018343-2">
    </script>
    <script>
      window.dataLayer = window.dataLayer || []
      function gtag() {dataLayer.push(arguments)}
      gtag('js', new Date())
      gtag('config', 'UA-152018343-2')
    </script>
    <meta name="facebook-domain-verification"
          content="ku6hcqhqvzemw704rsv5mt4sog908v"/>
</head>
<?php if ( ! empty( $post ) ) {
	$post_slug = $post->post_name;
} else {
	$post_slug = '';
} ?>
<body <?php body_class( N1_Magazine::Instance()->page_class . ' ' . $post_slug ); ?>>
<!--[if lt IE 9]>
<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
<![endif]-->
<header id="header-main">
    <div id="header-nav">
        <nav class="nav-actions">
            <ul>
                <li class="signin<?php echo is_user_logged_in() ? ' signout' : '' ?>"
                    id="nav-actions-signin">
					<?php wp_loginout( site_url() ) ?><!--Sign In--></li>
                <li class="account">
                    <a href="#">Your Account</a>
                </li>
            </ul>
        </nav>
        <nav class="nav-sections">
			<?php wp_nav_menu( array( 'menu' => 'nav-sections', 'container' => FALSE ) ); ?>
            <a class="navmenu" href="#">Full Navigation</a>
        </nav>
    </div>

    <section id="header-login">
        <h3>Sign In to n+1</h3>
        <form action="<?php echo home_url() ?>/wp-login.php?redirect_to=<?php echo urlencode( site_url( $_SERVER['REQUEST_URI'] ) ) ?>"
              method="POST">
            <input type="text"
                   id="log"
                   name="log"
                   placeholder="Email"/>

            <input type="password"
                   id="pwd"
                   name="pwd"
                   placeholder="Password"/>

            <input type="submit"
                   value="<?php _e( is_user_logged_in() ? 'Sign Out' : 'Sign In' ) ?>"/>
        </form>
        <a href="<?php echo home_url() ?>/forgot-password/">Forgot Password</a>
        <a href="<?php echo home_url() ?>/subscribe/">Subscribe Now</a>

        <div id="header-signin-close">Close</div>
    </section>

    <section id="header-search">
        <h3>Search</h3>
        <form action="<?php echo esc_url( home_url( '/' ) ) ?>">
            <label for="s">Search n+1</label>
            <input name="s"
                   id="s"
                   type="text"
                   data-swplive="true"
                   value="<?php echo isset( $_GET['s'] ) ? $_GET['s'] : '' ?>"
                   placeholder="Search n+1"
                   autocomplete="off"
                   autocorrect="off"
                   autocapitalize="off"
                   spellcheck="false"/>
        </form>
    </section>

</header><!-- #header-main -->
