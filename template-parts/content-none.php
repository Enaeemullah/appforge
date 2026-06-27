<?php
/**
 * Template Part: No Content / No Results
 */
?>

<div class="no-results">
    <div class="no-results__icon" aria-hidden="true">🔍</div>
    <h2 class="no-results__title">
        <?php
        if ( is_search() ) {
            printf( esc_html__( 'No results for "%s"', 'appforge' ), get_search_query() );
        } else {
            esc_html_e( 'Nothing found here', 'appforge' );
        }
        ?>
    </h2>
    <p class="no-results__desc">
        <?php
        if ( is_search() ) {
            esc_html_e( 'Try a different keyword or browse our categories below.', 'appforge' );
        } else {
            esc_html_e( 'It looks like nothing was found at this location.', 'appforge' );
        }
        ?>
    </p>
    <div class="error-404-search">
        <?php get_search_form(); ?>
    </div>
    <?php
    $suggestion_cats = get_terms( array( 'taxonomy' => 'app_category', 'number' => 8, 'hide_empty' => true ) );
    if ( $suggestion_cats && ! is_wp_error( $suggestion_cats ) ) : ?>
    <div class="no-results__suggestions">
        <p><?php esc_html_e( 'Try browsing these categories:', 'appforge' ); ?></p>
        <div class="tags-cloud" style="justify-content:center;">
            <?php foreach ( $suggestion_cats as $cat ) : ?>
            <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="tag"><?php echo esc_html( $cat->name ); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
