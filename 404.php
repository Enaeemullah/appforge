<?php
/**
 * 404 Error Page Template
 */
get_header();
?>

<div class="error-404-page">
    <div class="error-404-inner">
        <div class="error-404-graphic" aria-hidden="true">🔍</div>
        <div class="error-404-number" aria-hidden="true">404</div>
        <h1 class="error-404-title"><?php esc_html_e( "Page Not Found", 'appforge' ); ?></h1>
        <p class="error-404-desc">
            <?php esc_html_e( "The page you're looking for doesn't exist or has been moved. Try searching for what you need.", 'appforge' ); ?>
        </p>

        <!-- Search -->
        <div class="error-404-search">
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-input-group">
                <label for="search-404" class="sr-only"><?php esc_html_e( 'Search', 'appforge' ); ?></label>
                <input type="search" id="search-404" name="s"
                       placeholder="<?php esc_attr_e( 'Search apps or articles…', 'appforge' ); ?>"
                       autocomplete="off" autofocus>
                <button type="submit"><?php esc_html_e( 'Search', 'appforge' ); ?></button>
            </form>
        </div>

        <!-- Quick links -->
        <div class="error-404-links">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
                🏠 <?php esc_html_e( 'Go Home', 'appforge' ); ?>
            </a>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>" class="btn btn-outline">
                📱 <?php esc_html_e( 'Browse Apps', 'appforge' ); ?>
            </a>
            <a href="javascript:history.back()" class="btn btn-ghost">
                ← <?php esc_html_e( 'Go Back', 'appforge' ); ?>
            </a>
        </div>

        <!-- Popular categories -->
        <?php
        $cats = get_terms( array(
            'taxonomy'   => 'app_category',
            'orderby'    => 'count',
            'order'      => 'DESC',
            'number'     => 6,
            'hide_empty' => true,
        ) );
        if ( $cats && ! is_wp_error( $cats ) ) : ?>
        <div style="margin-top:48px;padding-top:32px;border-top:1px solid var(--gray-200);">
            <p style="font-size:13px;color:var(--gray-500);margin-bottom:16px;"><?php esc_html_e( 'Popular categories:', 'appforge' ); ?></p>
            <div class="tags-cloud" style="justify-content:center;">
                <?php foreach ( $cats as $cat ) : ?>
                <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="tag"><?php echo esc_html( $cat->name ); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>
