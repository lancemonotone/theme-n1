<?php
$sidebars = array(
	// 'sidebar-hero'					=> __( 'Hero Modules'),
	'sidebar-home-0' 				=> __( 'Home Left Modules'),
	'sidebar-home-1' 				=> __( 'Home Center Modules'),
	'sidebar-home-2' 				=> __( 'Home Right Modules'),
	'sidebar-home-3' 				=> __( 'Home Bottom Modules'),
	'sidebar-online-only-0'			=> __( 'Single Online Only Left Modules'),
	'sidebar-online-only-1'			=> __( 'Single Online Only Bottom Modules'),
	'sidebar-article-0'				=> __( 'Single Magazine Left Modules'),
	'sidebar-article-1'				=> __( 'Single Magazine Bottom Modules'),
	'sidebar-page-0' 				=> __( 'Page Modules'),
	'sidebar-archive-0' 			=> __( 'Archive Listing Modules'),
	'sidebar-toc-0' 				=> __( 'Issue TOC Left Modules'),
	'sidebar-toc-1' 				=> __( 'Issue TOC Center Modules'),
	'sidebar-toc-2' 				=> __( 'Issue TOC Right Modules'),
	'sidebar-landing-magazine-0' 	=> __( 'Magazine Landing Modules'),
);
foreach($sidebars as $id => $name){
	register_sidebar( array(
		'name' => $name,
		'id' => $id,
		'description' => __( '' ),
		'before_widget' => '<div class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
?>
