<?php namespace N1_Durable_Goods;
/**
 * Template Name: MemberMouse Core Page
 */
// check for $_GET["message"] to see if there is an error message. if not, set to false
$is_login_error = $_GET["message"] ?? false;

get_header();?>

<div id="main" class="cf">
	<section id="content">
	<?php while ( have_posts() ) { the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="entry-header">
				<?php the_post_thumbnail(); ?>
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</div>

			<div class="entry-content">
				<div class="mm"><?php the_content(); ?></div>
				<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'twentytwelve' ), 'after' => '</div>' ) ); ?>
			</div><!-- .entry-content -->

			<div class="entry-meta">
				<?php edit_post_link( __( 'Edit', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?>
			</div><!-- .entry-meta -->
		</article><!-- #post -->
	<?php } ?>
	</section> <!-- #content -->
</div><!-- #main -->
<?php get_template_part( 'sidebars/sidebar', 'page' ); ?>

<?php get_footer();?>
