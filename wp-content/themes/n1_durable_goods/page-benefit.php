<?php namespace N1_Durable_Goods;
/**
 * Template Name: Benefit */
get_header();?>

<div id="main" class="cf benefit">
	<section id="content">
	<?php while ( have_posts() ) { the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<p class="post-author">Jonathan Franzen<br/>
				Malcolm Gladwell<br/>
				Mary Karr<br/>
				Norman and Elsa Rush<br/>
				Zadie Smith and Nick Laird<br/>
				James Wood and Claire Messud
			</p>
			<p class="invite">Invite you to</p>
			<div class="entry-header">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</div>
			<div class="entry-content">
				<div class="mm"><?php the_content(); ?></div>
				<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'twentytwelve' ), 'after' => '</div>' ) ); ?>
			</div><!-- .entry-content -->

			<div class="entry-meta">
				<?php edit_post_link( __( 'Edit' ) ); ?>
			</div><!-- .entry-meta -->
		</article><!-- #post -->
	<?php } ?>
	</section> <!-- #content -->
</div><!-- #main -->
<?php get_template_part( 'sidebars/sidebar', 'page' ); ?>

<?php get_footer();?>
