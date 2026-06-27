<?php
/**
 * Template Part: App Card (Small / List variant)
 * Used by: sidebars, widgets, "also check" sections
 */

$rating    = appforge_get_rating();
$downloads = appforge_get_downloads();
?>

<a href="<?php the_permalink(); ?>" class="app-card-list">
    <div class="app-card-list__icon" aria-hidden="true">
        <?php if ( has_post_thumbnail() ) :
            the_post_thumbnail( 'app-icon-sm', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) );
        else : ?>
            📱
        <?php endif; ?>
    </div>
    <div class="app-card-list__info">
        <div class="app-card-list__title"><?php the_title(); ?></div>
        <div class="app-card-list__meta">
            <?php if ( $rating ) : ?>
            <span class="stars" aria-hidden="true">★</span>
            <span><?php echo esc_html( $rating ); ?></span>
            <?php endif; ?>
            <?php if ( $downloads ) : ?>
            <span><?php echo esc_html( $downloads ); ?></span>
            <?php endif; ?>
        </div>
    </div>
</a>
