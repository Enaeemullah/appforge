<?php
/**
 * Template Part: Category List Row
 * Full-width row (icon, name, category, description, rating, size,
 * download button) used in the Latest/Popular/Top Rated list on
 * app_category taxonomy archives.
 */
$rating       = appforge_get_rating();
$size         = get_post_meta( get_the_ID(), '_size', true );
$download_url = get_post_meta( get_the_ID(), '_download_url', true );
$categories   = get_the_terms( get_the_ID(), 'app_category' );
?>
<div class="cat-list-row">
    <a href="<?php the_permalink(); ?>" class="cat-list-row__icon" aria-hidden="true" tabindex="-1">
        <?php if ( has_post_thumbnail() ) :
            the_post_thumbnail( 'app-icon-sm', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) );
        else : ?>📱<?php endif; ?>
    </a>
    <div class="cat-list-row__info">
        <a href="<?php the_permalink(); ?>" class="cat-list-row__name"><?php the_title(); ?></a>
        <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
        <span class="cat-list-row__cat"><?php echo esc_html( $categories[0]->name ); ?></span>
        <?php endif; ?>
        <?php if ( has_excerpt() ) : ?>
        <p class="cat-list-row__desc"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 12 ) ); ?></p>
        <?php endif; ?>
    </div>
    <?php if ( $rating ) : ?>
    <span class="cat-list-row__rating">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        <?php echo esc_html( $rating ); ?>
    </span>
    <?php endif; ?>
    <?php if ( $size ) : ?>
    <span class="cat-list-row__size"><?php echo esc_html( $size ); ?></span>
    <?php endif; ?>
    <?php if ( $download_url ) : ?>
    <a href="<?php echo esc_url( $download_url ); ?>" class="btn-download cat-list-row__dl"
       rel="nofollow noopener" target="_blank"
       aria-label="<?php echo esc_attr( sprintf( __( 'Download %s', 'appforge' ), get_the_title() ) ); ?>">
        <?php esc_html_e( 'Download', 'appforge' ); ?>
    </a>
    <?php else : ?>
    <a href="<?php the_permalink(); ?>" class="btn-download-outline cat-list-row__dl">
        <?php esc_html_e( 'View', 'appforge' ); ?>
    </a>
    <?php endif; ?>
</div>
