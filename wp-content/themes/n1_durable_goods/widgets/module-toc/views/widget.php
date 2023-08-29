<?php namespace N1_Durable_Goods;

$type = strip_tags( $instance[ 'type' ] );
$context_issue = N1_Magazine::get_context_issue();
$current_issue = N1_Magazine::get_current_issue();
$context_issue_obj = N1_Magazine::get_issue_by_slug( $context_issue->post_name );
$current_issue_obj = N1_Magazine::get_issue_by_slug( $current_issue->post_name );

switch ( $type ) {
    case 'home':
        if ( $current_issue_obj ) {
            $issue_art = get_field( 'issue_art', $current_issue_obj->ID ); ?>
            <section class="inner">
                <figure class="bug">
                    <a href="<?= home_url() ?>/magazine/">
                        <img src="<?= $issue_art[ 'sizes' ][ 'issue-art' ] ?>" alt="<?php _e( 'Art for' ) ?> <?= $current_issue->post_title ?>">
                    </a>
                </figure>

                <span class="hed"><?php _e( 'Current Issue' ) ?></span>

                <h2 class="issue-name">
                    <a href="<?= home_url() ?>/magazine/"><?= get_field( 'issue_name', $current_issue->ID ) ?></a>
                </h2>
            </section>

            <?php the_widget( '\N1_Durable_Goods\Module_Issue_TOC', [ 'type' => 'article' ] ) ?>

            <?php if ( Metered_Paywall::is_paywalled() ) { ?>
                <a class="subscribe-link button" href="<?= home_url( 'subscribe' ) ?>" class="jump"><?php _e( 'Subscribe' ) ?></a>
            <?php } ?>

            <?php the_widget( '\N1_Durable_Goods\Module_Multi', [
                'title'   => 'Featured Articles',
                'flavor'  => 'featured-default',
                'number'  => '2',
                'orderby' => 'menu_order',
                'order'   => 'ASC'
            ] ); ?>

        <?php }
        break;

    case 'article':
        if ( $context_issue_obj ) { ?>
            <section class="article-toc cf">
                <?php while ( the_repeater_field( 'issue_sections', $context_issue_obj->ID ) ) {
                    $section       = get_sub_field( 'issue_section' );
                    $section_posts = N1_Magazine::get_section_posts( $section->slug, $context_issue_obj->post_name );
                    if ( count( $section_posts ) ) { ?>
                        <h2 class="post-category"><?= $section->name ?></h2>
                        <?php foreach ( $section_posts as $sp ) {
                            ?>
                            <article>
                                <a class="article-link" href="<?= get_permalink( $sp->ID ) ?>" title="<?= $sp->post_title ?>">
                                    <h3 class="post-title"><?= $sp->post_title ?></h3>

                                    <p class="post-author"><?= N1_Magazine::get_authors( $sp->ID, true, false ) ?></p>
                                </a>
                                <?php edit_post_link( __( 'Edit' ), null, null, $sp->ID ); ?>
                            </article><!-- .post -->
                        <?php }
                    } ?>
                <?php } ?>
            </section><!-- .toc-sections -->
        <?php }
        break;

    case 'landing-magazine':
        $temp_context = $context_issue;
        $i = 0;
        foreach ( N1_Magazine::get_issues() as $issue ) {
            N1_Magazine::set_context_issue( $issue->post_name );
            $issue_art = get_field( 'issue_art', $issue->ID ); ?>
            <section class="issue">
                <div class="issue-meta">
                    <div class="issue-number"><?= $issue->post_title ?></div>
                    <h2 class="issue-title">
                        <a href="<?= home_url() ?>/magazine/<?= $issue->post_name ?>"><?= get_field( 'issue_name', $issue->ID ) ?></a>
                    </h2>

                    <div class="issue-pubdate"><?= get_field( 'issue_date', $issue->ID ) ?></div>
                    <figure class="issue thumb">
                        <a href="<?= home_url() ?>/magazine/<?= $issue->post_name ?>">
                            <img src="<?= $issue_art[ 'sizes' ][ 'issue-art' ] ?>" alt="<?php _e( 'Art for' ) ?> <?= $issue->post_title ?>">
                        </a>
                    </figure>
                    <p class="issue-dek"><?= get_field( 'issue_dek', $issue->ID ) ?></p>

                    <div class="jump">
                        <a href="<?= home_url() ?>/magazine/<?= $issue->post_name ?>" class="jump"><?php $i++ == 0 ? _e( 'Read the Current Issue' ) : _e( 'Read this Issue' ) ?></a>
                    </div>
                    <?php if ( Module_Offline::has_download() ) {
                        Module_Offline::print_download_button();
                    } ?>
                </div>
                <!-- .issue-meta -->
                <!-- issue ToC -->
                <?php the_widget( '\N1_Durable_Goods\Module_Issue_TOC', [ 'type' => 'article' ] ) ?>
                <?php
                $multi_args = [
                    'title'   => 'Featured',
                    'flavor'  => 'featured-default',
                    'number'  => '2',
                    'orderby' => 'menu_order',
                    'order'   => 'ASC'
                ];
                the_widget( '\N1_Durable_Goods\Module_Multi', $multi_args ); ?>
            </section><!-- /issue -->
        <?php }
        N1_Magazine::set_context_issue( $temp_context->post_name );
        break;

    case 'landing-issue':
        $issue_art = get_field( 'issue_art', $context_issue_obj->ID );
        if ( $issue_art ) {
            ?>
            <figure class="issue thumb">
                <img src="<?= $issue_art[ 'sizes' ][ 'issue-art' ] ?>" alt="<?php _e( 'Art for' ) ?> <?= $context_issue_obj->post_title ?>">
            </figure>
        <?php } ?>
        <h1 class="section-title">Contents</h1>
        <?php //issue output per section, with section headers
        ?>
        <?php while ( the_repeater_field( 'issue_sections', $context_issue_obj->ID ) ) {
        $section       = get_sub_field( 'issue_section' );
        $section_posts = N1_Magazine::get_section_posts( $section->slug, $context_issue_obj->post_name );
        if ( count( $section_posts ) ) {
            ?>
            <div class="section-container">
                <h2 class="post-category"><?= $section->name ?></h2>
                <?php
                foreach ( $section_posts as $sp ) {
                    ?>
                    <article class="post">
                        <p class="post-author"><?= N1_Magazine::get_authors( $sp->ID ) ?></p>

                        <h1 class="post-title">
                            <a href="<?= get_permalink( $sp->ID ) ?>"><?= $sp->post_title ?></a>
                        </h1>

                        <div class="post-summary"><?= apply_filters( 'the_content', $sp->post_excerpt ) ?> </div>
                        <?php N1_Magazine::print_post_tags( $sp->ID ); ?>
                        <?php edit_post_link( __( 'Edit' ), null, null, $sp->ID ); ?>
                    </article><!-- .post -->
                <?php } ?>
            </div><!-- .section-container -->
        <?php } ?>
    <?php } ?>
        <?php break;
}
