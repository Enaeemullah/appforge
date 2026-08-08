<?php
/**
 * Template Part: Category Top Card
 * Icon-forward card used in the "Top {Category}" horizontal carousel row
 * on app_category taxonomy archives.
 */
$rating     = appforge_get_rating();
$categories = get_the_terms( get_the_ID(), 'app_category' );
$cat_names  = array();
if ( $categories && ! is_wp_error( $categories ) ) {
    foreach ( array_slice( $categories, 0, 2 ) as $c ) {
        $cat_names[] = $c->name;
    }
}
?>
<a href="<?php the_permalink(); ?>" class="cat-top-card" aria-label="<?php the_title_attribute(); ?>">
    <div class="cat-top-card__icon" aria-hidden="true">
        <?php if ( has_post_thumbnail() ) :
            the_post_thumbnail( 'app-icon-sm', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) );
        else : ?>📱<?php endif; ?>
    </div>
    <span class="cat-top-card__name"><?php the_title(); ?></span>
    <?php if ( $cat_names ) : ?>
    <span class="cat-top-card__cat"><?php echo esc_html( implode( ' • ', $cat_names ) ); ?></span>
    <?php endif; ?>
    <?php if ( $rating ) : ?>
    <span class="cat-top-card__rating">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        <?php echo esc_html( $rating ); ?>
    </span>
    <?php endif; ?>
</a>
