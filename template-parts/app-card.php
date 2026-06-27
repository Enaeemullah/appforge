<?php
/**
 * Template Part: App Card (Grid View)
 * Used by: index.php, page-hot-apps.php, page-latest-apps.php, etc.
 */

$download_url = get_post_meta( get_the_ID(), '_download_url', true );
$rating       = appforge_get_rating();
$downloads    = appforge_get_downloads();
$version      = get_post_meta( get_the_ID(), '_version', true );
$categories   = get_the_terms( get_the_ID(), 'app_category' );
?>

<article <?php post_class( 'app-card' ); ?> id="app-<?php the_ID(); ?>">

    <?php if ( has_post_thumbnail() ) : ?>
    <a href="<?php the_permalink(); ?>" class="app-card__thumb" aria-hidden="true" tabindex="-1">
        <?php the_post_thumbnail( 'card-thumb', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) ); ?>
    </a>
    <?php endif; ?>

    <div class="app-card__body">

        <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;">
            <!-- App icon -->
            <div class="app-card__icon" aria-hidden="true">
                <?php if ( has_post_thumbnail() ) :
                    the_post_thumbnail( 'app-icon-sm', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) );
                else : ?>
                    📱
                <?php endif; ?>
            </div>
            <!-- Title + category -->
            <div style="flex:1;min-width:0;">
                <h2 class="app-card__title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
                <span class="app-card__category"><?php echo esc_html( $categories[0]->name ); ?></span>
                <?php endif; ?>
            </div>
        </div>

        <?php if ( has_excerpt() ) : ?>
        <p class="app-card__excerpt"><?php the_excerpt(); ?></p>
        <?php endif; ?>

        <div class="app-card__meta">
            <?php if ( $rating ) : ?>
            <span class="app-card__rating">
                <span class="stars" aria-hidden="true">★</span>
                <span><?php echo esc_html( $rating ); ?></span>
            </span>
            <?php endif; ?>

            <?php if ( $downloads ) : ?>
            <span class="app-card__downloads">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 16l-5-5h3V4h4v7h3l-5 5zm5 2H7v2h10v-2z"/></svg>
                <?php echo esc_html( $downloads ); ?>
            </span>
            <?php endif; ?>

            <?php if ( $version ) : ?>
            <span class="badge badge-light" style="font-size:10px;">v<?php echo esc_html( $version ); ?></span>
            <?php endif; ?>
        </div>

    </div><!-- .app-card__body -->

    <?php if ( $download_url ) : ?>
    <div class="app-card__footer">
        <a href="<?php echo esc_url( $download_url ); ?>"
           class="btn-download" style="width:100%;justify-content:center;"
           rel="nofollow noopener" target="_blank"
           aria-label="<?php echo esc_attr( sprintf( __( 'Download %s', 'appforge' ), get_the_title() ) ); ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 16l-5-5h3V4h4v7h3l-5 5zm5 2H7v2h10v-2z"/></svg>
            <?php esc_html_e( 'Download', 'appforge' ); ?>
        </a>
    </div>
    <?php endif; ?>

</article>
