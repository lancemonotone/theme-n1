<?php namespace N1_Durable_Goods;

$hero_module_id = $instance['hero_module'];

// Get latest or specific module as set in widget admin.
if($instance['always_latest'] === '1'){
    $the_args = array( 'order'=> 'DESC' );
} else {
    $the_args = array( 'p' => $hero_module_id );
}

// Merge query args and get_posts.
$hero_modules = get_posts(
    array_merge(
        $the_args,
        array(
            'post_type' 		=> 'module_hero',
            'post_status' 		=> 'publish',
            'posts_per_page'	=> -1
        )
    )
);

$hero_module = reset($hero_modules);
$title = $hero_module->post_title;
$preheader = get_field('hero_preheader', $hero_module->ID);
$dek = get_field('hero_dek', $hero_module->ID);
$bg_img = get_field('hero_bg_img', $hero_module->ID);
$bg_img_caption = get_field('hero_bg_img_caption', $hero_module->ID);
$url = get_field('hero_url', $hero_module->ID);
?>
<section id="hero" class="home hero">
    <div class="hero-wrapper">
        <div class="hero-cell">
            <?php if($url) {?><a href="<?php echo $url?>"><?php } ?>
                <div class="hero category"><span class="module-hed"><?php echo $preheader?></span></div>
                <h2 class="hero title"><?php echo $title?></h2>
                <div class="hero dek"><?php echo $dek?></div>
                <?php if($url) {?></a><?php } ?>
        </div>
    </div>
    <figure class="hero" id="home-hero" style="background-image: url(<?php echo $bg_img?>);">
        <figcaption class="hero"><?php echo $bg_img_caption?></figcaption>
    </figure>
</section>
