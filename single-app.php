<?php get_header(); ?>

<?php while ( have_posts() ) : the_post();
    $download_url = get_post_meta( get_the_ID(), '_download_url', true );
    $rating       = appforge_get_rating();
    $downloads    = appforge_get_downloads();
    $version      = get_post_meta( get_the_ID(), '_version', true );
    $developer    = get_post_meta( get_the_ID(), '_developer', true );
    $file_size    = get_post_meta( get_the_ID(), '_size', true );
    $requires     = get_post_meta( get_the_ID(), '_requires', true );
    $categories   = get_the_terms( get_the_ID(), 'app_category' );
?>

<!-- ===== APP HERO ===== -->
<section class="single-app-hero" aria-label="<?php the_title_attribute(); ?>">
    <div class="container">
        <?php appforge_breadcrumbs(); ?>
        <div class="app-hero-grid" style="margin-top:24px;">

            <!-- App Icon -->
            <div class="app-hero-icon">
                <?php if ( has_post_thumbnail() ) :
                    the_post_thumbnail( 'app-icon', array( 'loading' => 'eager', 'alt' => get_the_title() . ' icon' ) );
                else : ?>
                    📱
                <?php endif; ?>
            </div>

            <!-- App Info -->
            <div class="app-hero-info">
                <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
                <span class="app-hero-info__category"><?php echo esc_html( $categories[0]->name ); ?></span>
                <?php endif; ?>

                <h1><?php the_title(); ?></h1>

                <?php if ( $developer ) : ?>
                <p class="app-hero-info__dev"><?php echo esc_html( $developer ); ?></p>
                <?php endif; ?>

                <div class="app-hero-info__stats">
                    <?php if ( $rating ) : ?>
                    <span>
                        <?php echo appforge_stars( $rating ); // phpcs:ignore ?>
                        <strong style="color:#fff;"><?php echo esc_html( $rating ); ?></strong>
                    </span>
                    <?php endif; ?>
                    <?php if ( $downloads ) : ?>
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 16l-5-5h3V4h4v7h3l-5 5zm5 2H7v2h10v-2z"/></svg>
                        <?php echo esc_html( $downloads ); ?> <?php esc_html_e( 'downloads', 'appforge' ); ?>
                    </span>
                    <?php endif; ?>
                    <?php if ( $version ) : ?>
                    <span>v<?php echo esc_html( $version ); ?></span>
                    <?php endif; ?>
                    <?php if ( $file_size ) : ?>
                    <span><?php echo esc_html( $file_size ); ?></span>
                    <?php endif; ?>
                </div>

                <div class="app-hero-info__actions">
                    <?php if ( $download_url ) : ?>
                    <a href="<?php echo esc_url( $download_url ); ?>"
                       class="btn-download btn-lg"
                       rel="nofollow noopener" target="_blank"
                       aria-label="<?php echo esc_attr( sprintf( __( 'Download %s APK', 'appforge' ), get_the_title() ) ); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 16l-5-5h3V4h4v7h3l-5 5zm5 2H7v2h10v-2z"/></svg>
                        <?php esc_html_e( 'Download APK', 'appforge' ); ?>
                    </a>
                    <?php endif; ?>
                    <a href="#description" class="btn btn-outline btn-lg" style="border-color:rgba(255,255,255,.5);color:#fff;">
                        <?php esc_html_e( 'More Info', 'appforge' ); ?>
                    </a>
                </div>

            </div><!-- .app-hero-info -->
        </div><!-- .app-hero-grid -->
    </div><!-- .container -->
</section>

<!-- ===== APP CONTENT ===== -->
<div class="container">
    <div class="page-layout" style="padding-top:40px;">

        <!-- Main Column -->
        <div>

            <!-- Description -->
            <div class="app-detail-section" id="description">
                <div class="app-detail-section__header">
                    <h2><?php esc_html_e( 'About This App', 'appforge' ); ?></h2>
                </div>
                <div class="app-detail-section__body entry-content">
                    <?php the_content(); ?>
                </div>
            </div>

            <!-- App Information Grid -->
            <?php if ( $version || $developer || $file_size || $requires ) : ?>
            <div class="app-detail-section">
                <div class="app-detail-section__header">
                    <h2><?php esc_html_e( 'App Information', 'appforge' ); ?></h2>
                </div>
                <div class="app-info-grid">
                    <?php if ( $version ) : ?>
                    <div class="app-info-item">
                        <span class="app-info-item__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><circle cx="7" cy="7" r="1" fill="currentColor"/></svg>
                        </span>
                        <span class="app-info-item__label"><?php esc_html_e( 'Version', 'appforge' ); ?></span>
                        <span class="app-info-item__value"><?php echo esc_html( $version ); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ( $developer ) : ?>
                    <div class="app-info-item">
                        <span class="app-info-item__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        <span class="app-info-item__label"><?php esc_html_e( 'Developer', 'appforge' ); ?></span>
                        <span class="app-info-item__value"><?php echo esc_html( $developer ); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ( $file_size ) : ?>
                    <div class="app-info-item">
                        <span class="app-info-item__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                        </span>
                        <span class="app-info-item__label"><?php esc_html_e( 'File Size', 'appforge' ); ?></span>
                        <span class="app-info-item__value"><?php echo esc_html( $file_size ); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ( $requires ) : ?>
                    <div class="app-info-item">
                        <span class="app-info-item__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><circle cx="12" cy="18" r="1" fill="currentColor"/></svg>
                        </span>
                        <span class="app-info-item__label"><?php esc_html_e( 'Requires Android', 'appforge' ); ?></span>
                        <span class="app-info-item__value"><?php echo esc_html( $requires ); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ( $rating ) : ?>
                    <div class="app-info-item">
                        <span class="app-info-item__icon app-info-item__icon--star" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="0.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        </span>
                        <span class="app-info-item__label"><?php esc_html_e( 'Rating', 'appforge' ); ?></span>
                        <span class="app-info-item__value"><?php echo esc_html( $rating ); ?> / 5</span>
                    </div>
                    <?php endif; ?>
                    <?php if ( $downloads ) : ?>
                    <div class="app-info-item">
                        <span class="app-info-item__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        </span>
                        <span class="app-info-item__label"><?php esc_html_e( 'Downloads', 'appforge' ); ?></span>
                        <span class="app-info-item__value"><?php echo esc_html( $downloads ); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="app-info-item">
                        <span class="app-info-item__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </span>
                        <span class="app-info-item__label"><?php esc_html_e( 'Updated', 'appforge' ); ?></span>
                        <span class="app-info-item__value"><time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( get_the_modified_date() ); ?></time></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tags -->
            <?php
            $app_tags = get_the_tags();
            if ( $app_tags ) : ?>
            <div class="app-detail-section">
                <div class="app-detail-section__header">
                    <h3><?php esc_html_e( 'Tags', 'appforge' ); ?></h3>
                </div>
                <div class="app-detail-section__body">
                    <div class="tags-cloud">
                        <?php foreach ( $app_tags as $tag ) : ?>
                        <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="tag"><?php echo esc_html( $tag->name ); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Download CTA -->
            <?php if ( $download_url ) : ?>
            <div style="background:linear-gradient(135deg,#667eea,#00C853);border-radius:var(--radius-2xl);padding:40px;text-align:center;margin-bottom:32px;">
                <h3 style="color:#fff;font-size:24px;margin-bottom:8px;"><?php printf( esc_html__( 'Download %s Now', 'appforge' ), esc_html( get_the_title() ) ); ?></h3>
                <p style="color:rgba(255,255,255,.85);margin-bottom:24px;"><?php esc_html_e( 'Free download — safe and verified APK.', 'appforge' ); ?></p>
                <a href="<?php echo esc_url( $download_url ); ?>"
                   class="btn btn-white btn-xl"
                   rel="nofollow noopener" target="_blank">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 16l-5-5h3V4h4v7h3l-5 5zm5 2H7v2h10v-2z"/></svg>
                    <?php esc_html_e( 'Download Free', 'appforge' ); ?>
                </a>
            </div>
            <?php endif; ?>

        </div><!-- main column -->

        <!-- Sidebar -->
        <?php get_sidebar(); ?>

    </div><!-- .page-layout -->
</div><!-- .container -->

<?php endwhile; ?>

<?php get_footer(); ?>
