<?php
$type = strip_tags( $instance[ 'type' ] );
$context_issue = N1_Magazine::Instance()->context_issue;
$current_issue = N1_Magazine::Instance()->current_issue;
$context_issue_obj = N1_Magazine::Instance()->get_issue_by_slug( $context_issue->post_name );
$current_issue_obj = N1_Magazine::Instance()->get_issue_by_slug( $current_issue->post_name );

switch ( $type ) {
    case 'home':
        ?>

        <?php if ( $current_issue_obj ) {
        $issue_art = get_field( 'issue_art', $current_issue_obj->ID ); ?>
        <section class="current-issue">
            <div class="wrapper">
                <figure class="issue thumb">
                    <a href="<?php echo home_url() ?>/magazine/">
                        <img src="<?php echo $issue_art[ 'sizes' ][ 'issue-art' ] ?>" alt="<?php _e( 'Art for' ) ?> <?php echo $current_issue->post_title ?>">
                    </a>
                </figure>
                <h2 class="current-issue category"><span class="module-hed"><?php _e( 'Current Issue' ) ?></span></h2>

                <h1 class="current-issue title">
                    <a href="<?php echo home_url() ?>/magazine/"><?php echo get_field( 'issue_name', $current_issue->ID ) ?></a>
                </h1>

                <h2 class="current-issue contents category"><span class="module-hed"><?php _e( 'Contents' ) ?></span>
                </h2>

                <?php the_widget( 'Module_Issue_TOC', array( 'type' => 'article' ) ) ?>
                <?php
                $multi_args = array(
                    'title'   => 'Featured Articles',
                    'flavor'  => 'featured-default',
                    'number'  => '2',
                    'orderby' => 'menu_order',
                    'order'   => 'ASC'
                );
                the_widget( 'Module_Multi', $multi_args );
                ?>

            </div>
            <!-- .wrapper -->
            <div class="current-issue jump">
                <a href="<?php echo N1_Magazine::Instance()->get_current_issue_url() ?>" class="jump"><?php _e( 'Read this Issue' ) ?></a>
            </div>
        </section>
    <?php }
        break;

    case 'home-TRASH':
        ?>

        <?php if ( $current_issue_obj ) {
        $issue_art = get_field( 'issue_art', $current_issue_obj->ID ); ?>
        <section class="current-issue">
            <div class="wrapper">
                <figure class="issue thumb">
                    <a href="<?php echo home_url() ?>/magazine/">
                        <img src="<?php echo $issue_art[ 'sizes' ][ 'issue-art' ] ?>" alt="<?php _e( 'Art for' ) ?> <?php echo $current_issue->post_title ?>">
                    </a>
                </figure>
                <h2 class="current-issue category"><span class="module-hed"><?php _e( 'Current Issue' ) ?></span></h2>

                <h1 class="current-issue title">
                    <a href="<?php echo home_url() ?>/magazine/"><?php echo get_field( 'issue_name', $current_issue->ID ) ?></a>
                </h1>

                <h2 class="current-issue contents category"><span class="module-hed"><?php _e( 'Contents' ) ?></span>
                </h2>

                <?php if ( get_field( 'issue_sections', $current_issue_obj->ID ) ) { ?>
                    <ul class="cf"><?php
                        while ( the_repeater_field( 'issue_sections', $current_issue_obj->ID ) ) {
                            $section = get_sub_field( 'issue_section' );
                            $section_posts = N1_Magazine::Instance()->get_section_posts( $section->slug );
                            foreach ( $section_posts as $sp ) {
                                ?>
                                <article class="current-issue">
                                    <a href="<?php echo get_permalink( $sp->ID )?>" title="<?php echo $sp->post_title?>">
                                        <div class="current-issue article category tis"><?php echo $section->name?></div>
                                        <p class="current-issue article title"><?php echo $sp->post_title?></p>

                                        <p class="current-issue article author"><?php echo N1_Magazine::Instance()->get_authors( $sp->ID, true, false )?></p>
                                    </a>
                                    <?php edit_post_link( __( 'Edit' ), '<span class="edit-link">', '</span>', $sp->ID ); ?>
                                </article><!-- /.current-issue -->
                            <?php } ?>
                        <?php } ?>
                    </ul><!-- .toc-sections -->
                <?php }
                $multi_args = array(
                    'title'   => 'Featured Articles',
                    'flavor'  => 'featured-default',
                    'number'  => '2',
                    'orderby' => 'menu_order',
                    'order'   => 'ASC'
                );
                the_widget( 'Module_Multi', $multi_args );
                ?>

            </div>
            <!-- .wrapper -->
            <div class="current-issue jump">
                <a href="<?php echo N1_Magazine::Instance()->get_current_issue_url() ?>" class="jump"><?php _e( 'Read this Issue' ) ?></a>
            </div>
        </section>
    <?php }
        break;

    case 'article':
        ?>
        <?php if ( $context_issue_obj ) { ?>
        <section class="article-toc cf">
            <?php while ( the_repeater_field( 'issue_sections', $context_issue_obj->ID ) ) {
                $section = get_sub_field( 'issue_section' );
                $section_posts = N1_Magazine::Instance()->get_section_posts( $section->slug, $context_issue_obj->post_name );
                if ( count( $section_posts ) ) {
                    ?>
                    <h3 class="post-category"><?php echo $section->name?></h3>
                    <?php
                    foreach ( $section_posts as $sp ) {
                        ?>
                        <article class="post">
                            <a class="article-link" href="<?php echo get_permalink( $sp->ID )?>" title="<?php echo $sp->post_title?>">
                                <h1 class="post-title"><?php echo $sp->post_title?></h1>

                                <p class="post-author"><?php echo N1_Magazine::Instance()->get_authors( $sp->ID, true, false )?></p>
                            </a>
                            <?php edit_post_link( __( 'Edit' ), '<span class="edit-link">', '</span>', $sp->ID ); ?>
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
        foreach ( N1_Magazine::Instance()->issues as $issue ) {
            N1_Magazine::Instance()->set_context_issue( $issue->post_name );
            $issue_art = get_field( 'issue_art', $issue->ID );?>
            <section class="issue">
                <div class="issue-meta">
                    <div class="issue-number"><?php echo $issue->post_title?></div>
                    <h2 class="issue-title">
                        <a href="<?php echo home_url()?>/magazine/<?php echo $issue->post_name?>"><?php echo get_field( 'issue_name', $issue->ID )?></a>
                    </h2>

                    <div class="issue-pubdate"><?php echo get_field( 'issue_date', $issue->ID )?></div>
                    <figure class="issue thumb">
                        <a href="<?php echo home_url()?>/magazine/<?php echo $issue->post_name?>">
                            <img src="<?php echo $issue_art[ 'sizes' ][ 'issue-art' ]?>" alt="<?php _e( 'Art for' )?> <?php echo $issue->post_title?>">
                        </a>
                    </figure>
                    <p class="issue-dek"><?php echo get_field( 'issue_dek', $issue->ID )?></p>

                    <div class="jump">
                        <a href="<?php echo home_url()?>/magazine/<?php echo $issue->post_name?>" class="jump"><?php $i++ == 0 ? _e( 'Read the Current Issue' ) : _e( 'Read this Issue' )?></a>
                    </div>
                    <?php if ( Module_Offline::has_download() ) {
                        Module_Offline::print_download_button();
                    }?>
                </div>
                <!-- .issue-meta -->
                <!-- issue ToC -->
                <?php the_widget( 'Module_Issue_TOC', array( 'type' => 'article' ) )?>
                <?php
                $multi_args = array(
                    'title'   => '',
                    'flavor'  => 'featured-default',
                    'number'  => '2',
                    'orderby' => 'menu_order',
                    'order'   => 'ASC'
                );
                the_widget( 'Module_Multi', $multi_args );?>
            </section><!-- /issue -->
        <?php }
        N1_Magazine::Instance()->set_context_issue( $temp_context->post_name );
        break;

    case 'landing-issue':
        ?>
        <?php
        $issue_art = get_field( 'issue_art', $context_issue_obj->ID );
        if ( $issue_art ) {
            ?>
            <figure class="issue thumb">
                <img src="<?php echo $issue_art[ 'sizes' ][ 'issue-art' ]?>" alt="<?php _e( 'Art for' )?> <?php echo $issue->post_title?>">
            </figure>
        <?php }?>
        <h1 class="section-title">Contents</h1>
        <?php //issue output per section, with section headers
        ?>
        <?php while ( the_repeater_field( 'issue_sections', $context_issue_obj->ID ) ) {
        $section = get_sub_field( 'issue_section' );
        $section_posts = N1_Magazine::Instance()->get_section_posts( $section->slug, $context_issue_obj->post_name );
        if ( count( $section_posts ) ) {
            ?>
            <div class="section-container">
            <h2 class="post-category"><?php echo $section->name?></h2>
            <?php
            foreach ( $section_posts as $sp ) {
                ?>
                <article class="post">
                    <p class="post-author"><?php echo N1_Magazine::get_authors( $sp->ID )?></p>

                    <h1 class="post-title">
                        <a href="<?php echo get_permalink( $sp->ID )?>"><?php echo $sp->post_title?></a></h1>

                    <div class="post-summary"><?php echo apply_filters( 'the_content', $sp->post_excerpt )?> </div>
                    <?php N1_Magazine::Instance()->print_post_tags( $sp->ID );?>
                    <?php edit_post_link( __( 'Edit' ), '<span class="edit-link">', '</span>', $sp->ID ); ?>
                </article><!-- .post -->
                <?php } ?>
            </div><!-- .section-container -->
       <?php } ?>
    <?php } ?>
        <?php break;
}