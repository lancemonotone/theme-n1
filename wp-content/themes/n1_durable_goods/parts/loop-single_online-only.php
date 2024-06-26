<?php namespace N1_Durable_Goods; ?>

<?php Adrotate::display( 3 ); ?>

<div id="main" class="cf">
    <div class="main content">

        <?php get_template_part( 'sidebars/sidebar', 'single_online-only' ); ?>
        <section class="content-post" id="content"> <!-- includes POST and PREV-NEXT -->
            <?php while ( have_posts() ) {
                the_post();
                $online_only_terms = wp_get_post_terms( $post->ID, 'online-only' );
                $section           = reset( $online_only_terms );
                $term_link         = get_term_link( $section, 'online-only' );
                $term_link         = is_wp_error( $term_link ) ? '#' : $term_link;
                // $issue_obj = N1_Magazine::get_issue_by_slug($issue);
                ?>
                <article class="post term-<?= $section->slug ?? '' ?>">
                    <script type="text/javascript">
                        var _sf_async_config = _sf_async_config || {}
                        _sf_async_config.authors = '<?=N1_Magazine::get_authors( $post->ID, true, false ) ?? '' ?>'
                    </script>
                    <div class="post-header">
                        <p class="post-category">
                            <a href="<?= $term_link ?? ''  ?>"><?= $section->name ?? ''  ?></a>
                        </p>
                        <p class="post-author"><?= N1_Magazine::get_authors( $post->ID ) ?? ''  ?></p>
                        <?php if ( $event_date = get_field( 'event_date', $post->ID ) ) { ?>
                            <p class="event-date"><?= date( 'F j, Y', strtotime( $event_date ) ) ?></p>
                        <?php } ?>
                        <h1 class="post-title"><?php the_title() ?></h1>
                        <?php if ( $article_subhead = get_field( 'article_subhead', $post->ID ) ) { ?>
                            <h2 class="post-subtitle"><?= $post->post_excerpt ?? ''  ?></h2>
                            <p class="post-dek"><?= $article_subhead ?></p>
                        <?php } ?>
                    </div><!-- .post-header-->

                    <?php
                    $img_id   = get_post_thumbnail_id( $post->ID );
                    $img_meta = wp_prepare_attachment_for_js( $img_id );
                    $img      = wp_get_attachment_image_src( $img_id, 'content-full' );
                    if ( $img ) {
                        ?>
                        <figure class="post-hero">
                            <img alt="<?= $img_meta[ 'alt' ] ?>" src="<?= $img[ 0 ] ?>">
                            <figcaption><?= $img_meta[ 'caption' ] ?></figcaption>
                        </figure>
                    <?php } ?>

                          <!-- in-article meta (is this OK?) -->
                    <div class="post-meta cf">
                        <div class="left">
                            <section class="post-meta-pubinfo">
                                <p class="post-meta-entry">
							<span class="post-date post-meta-hed runin">
								<?= date( 'F j, Y', strtotime( $post->post_date ) ) ?>
							</span>
                                </p>
                            </section> <!-- /publication-info -->
                            <?php N1_Magazine::print_post_tags( $post->ID, true ); ?>
                            <?php N1_Magazine::print_social( $post->ID ); ?>
                        </div>
                        <?php the_widget( '\N1_Durable_Goods\Module_Newsletter' ); ?>
                    </div><!-- .post-meta-->

                    <?php if ( $reviewed_items = get_field( 'reviewed_items', $post->ID ) ) { ?>
                        <div class="reviews">
                            <?= html_entity_decode( $reviewed_items ) ?>
                        </div>
                    <?php } ?>
                    <?php if ( $article_headnote = get_field( 'article_headnote', $post->ID ) ) { ?>
                        <div class="headnote">
                            <?= html_entity_decode( $article_headnote ) ?>
                        </div>
                    <?php } ?>

                    <div class="post-body">
                        <?php
                        $the_content = apply_filters( 'the_content', get_the_content() );
                        echo Utility::insert_advertisement( $the_content, 5, 2 );
                        ?>
                        <?php if ( $article_appendix = get_field( 'article_appendix', $post->ID ) ) { ?>
                            <div class="appendix">
                                <p><?= html_entity_decode( $article_appendix ) ?></p>
                            </div>
                        <?php } ?>

                        <!-- START SUBSCRIBE LINK -->
                        <?php if ( !empty($section) && $section->name !== 'Events' && $section->name !== 'Announcements' ) { ?>
                            <hr/>
                            <div style="text-align:center;font-size:1.22rem !important;line-height:1.5;">If you like this article, please
                                <a style="font-weight:600;text-decoration:none;border-bottom:1px solid;" href="https://www.nplusonemag.com/subscribe/?affid=article">subscribe</a>
                                                                                                         or leave a tax-deductible tip below to support n+1.
                            </div>
                            <br>
                            <br>
                            <div id="container" style="max-width: 600px; margin: 0 auto;"><!-- Begin Give Lively Fundraising Widget -->
                                <script>gl = document.createElement('script')
                                    gl.src = 'https://secure.givelively.org/widgets/simple_donation/n1-foundation-inc.js?show_suggested_amount_buttons=false&show_in_honor_of=false&address_required=false&has_required_custom_question=false&suggested_donation_amounts[]=25&suggested_donation_amounts[]=100&suggested_donation_amounts[]=250&suggested_donation_amounts[]=1000'
                                    document.getElementsByTagName('head')[0].appendChild(gl)</script>
                                <div id="give-lively-widget" class="gl-simple-donation-widget"></div>
                            </div>
                        <?php } ?>
                        <!-- END SUBSCRIBE LINK -->

                    </div><!-- .post-body-->
                </article><!-- /.post -->
            <?php } ?>
            <?php //issue navigation
            $prev_link = N1_Magazine::same_edition_and_section_adjacent_post_link( '%link', '%title', true, false );
            $next_link = N1_Magazine::same_edition_and_section_adjacent_post_link( '%link', '%title', false, false );

            if ( $prev_link || $next_link ) {
                ?>

                <nav class="prev-next">
                    <ul>
                        <?php if ( $prev_link ) { ?>
                            <li class="prev"><?= $prev_link; ?></li><?php } ?>
                        <?php if ( $next_link ) { ?>
                            <li class="next"><?= $next_link; ?></li><?php } ?>
                    </ul>
                </nav>
            <?php } // end if?>
        </section><!-- /#content -->

                  <!-- right sidebar content (currently blank) -->
        <section class="sidebar">
        </section>
    </div><!-- .main-content -->
</div><!-- #main -->
<?php get_template_part( 'sidebars/sidebar', 'single_online-only_bottom' ); ?>
