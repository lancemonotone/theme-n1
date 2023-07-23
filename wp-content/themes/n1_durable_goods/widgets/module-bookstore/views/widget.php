<?php namespace N1_Durable_Goods;
$products_module_id = $instance[ 'books_module' ];

$heading    = get_field( 'bookstore_heading', 'options' );
$subheading = get_field( 'bookstore_subheading', 'options' );
$dek        = get_field( 'bookstore_dek', 'options' );
$products   = get_field( 'bookstore_products', 'options' );
?>

<aside>
    <h3 class="title"><a href="<?= get_field( 'options_shopify_link', 'options' ) ?>" title="<?php _e( 'Browse the Shop' ) ?>"><?= $heading ?></a></h3>
    <?php if ( $subheading ) { ?>
        <p class="subheading"><?= $subheading ?></p>
    <?php } ?>
    <p class="dek"><?= $dek ?></p>

    <div class="books">
        <?php foreach ( $products as $product ) { ?>
            <div class="book">
                <a href="<?= $product[ 'bookstore_purchase_link' ] ?>" title="<?= $product[ 'bookstore_promo' ] ?>">
                    <figure>
                        <img src="<?= $product[ 'bookstore_image' ] ?>" alt="<?= $product[ 'bookstore_promo' ] ?>">
                    </figure>
                    <div class="promo"><?= $product[ 'bookstore_promo' ] ?></div>
                    <p class="price">
                        <?php if ( $product[ 'bookstore_from_price' ] ) { ?>
                            <span class="em"><?php _e( 'from' ) ?></span>
                        <?php } ?>
                        <?= $product[ 'bookstore_price' ] ?>
                    </p>
                </a>
            </div>
        <?php } ?>
    </div>

    <div class="jump">
        <a href="<?= get_field( 'options_shopify_link', 'options' ) ?>" title="<?php _e( 'Browse the Shop' ) ?>" class="button"><?php _e( 'Browse the Shop' ) ?></a>
    </div>
</aside>
