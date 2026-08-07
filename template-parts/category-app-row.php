<?php
/**
 * Template Part: Category App Row
 * A compact icon + name + developer + rating row, used 3-up in a grid
 * on category archive pages (Hot Apps / Latest Update tabs).
 */
$developer = get_post_meta( get_the_ID(), '_developer', true );
$rating    = appforge_get_rating();
?>
<a href="<?php the_permalink(); ?>" class="cat-app-row">
    <div class="cat-app-row__icon" aria-hidden="true">
        <?php if ( has_post_thumbnail() ) :
            the_post_thumbnail( 'app-icon-sm', array( 'loading' => 'lazy', 'alt' => '' ) );
        else : ?>📱<?php endif; ?>
    </div>
    <div class="cat-app-row__info">
        <span class="cat-app-row__name"><?php the_title(); ?></span>
        <?php if ( $developer ) : ?>
        <span class="cat-app-row__dev"><?php echo esc_html( $developer ); ?></span>
        <?php endif; ?>
    </div>
    <?php if ( $rating ) : ?>
    <span class="cat-app-row__rating">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        <?php echo esc_html( $rating ); ?>
    </span>
    <?php endif; ?>
</a>
