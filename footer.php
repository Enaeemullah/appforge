</main><!-- #main-content -->

<!-- ===== SITE FOOTER ===== -->
<footer class="site-footer" role="contentinfo">
    <div class="footer-top">
        <div class="container">
            <div class="footer-grid">

<!-- Brand Column -->
<div class="footer-brand">

    <div class="footer-logo">
        <?php if ( has_custom_logo() ) :
            the_custom_logo();
        else : ?>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                <span class="footer-logo-icon" aria-hidden="true">
                    <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="34" height="34" rx="9" fill="url(#footerLogoGradient)"/>
                        <path d="M19.5 7L11 18.5h5.2l-1 8.5L23 15.5h-5.2l1.7-8.5z" fill="#fff"/>
                        <defs>
                            <linearGradient id="footerLogoGradient" x1="0" y1="0" x2="34" y2="34" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#00C853"/>
                                <stop offset="1" stop-color="#6200ea"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </span>
                <span class="footer-logo-text">App<span><?php esc_html_e( 'Forge', 'appforge' ); ?></span></span>
            </a>
        <?php endif; ?>
    </div>

    <p>
        <?php
        echo esc_html(
            AppForge_Panel::get(
                'footer_about',
                'Your trusted source for safe, fast, and free APK downloads.'
            )
        );
        ?>
    </p>

    <?php
    $socials = appforge_social_platforms();

    $has_social = false;

    foreach ( $socials as $key => $s ) {
        if ( AppForge_Panel::get( 'social_' . $key, '' ) ) {
            $has_social = true;
            break;
        }
    }

    if ( $has_social ) :
    ?>
    <h5 class="footer-col-heading"><?php esc_html_e( 'Follow Us', 'appforge' ); ?></h5>
    <div class="footer-social">
        <?php foreach ( $socials as $key => $s ) :
            $url = AppForge_Panel::get( 'social_' . $key, '' );
            if ( empty( $url ) ) continue;
        ?>
            <a href="<?php echo esc_url( $url ); ?>" class="social-btn social-btn--<?php echo esc_attr( $key ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $s['label'] ); ?>">
                <?php echo $s['icon']; ?>
            </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
                <!-- Explore -->
                <div class="footer-col">
                    <h5 class="footer-col-heading"><?php esc_html_e( 'Explore', 'appforge' ); ?></h5>
                    <?php if ( has_nav_menu( 'footer' ) ) :
                        wp_nav_menu( array(
                            'theme_location' => 'footer',
                            'container'      => 'nav',
                            'container_attr' => array( 'aria-label' => esc_attr__( 'Footer navigation', 'appforge' ) ),
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ) );
                    else :
                        $explore_links = array(
                            array( 'label' => __( 'All Apps', 'appforge' ),       'url' => get_post_type_archive_link( 'app' ) ),
                            array( 'label' => __( 'Latest Apps', 'appforge' ),    'url' => appforge_page_url_by_template( 'page-latest-apps.php' ) ),
                            array( 'label' => __( 'Popular Apps', 'appforge' ),   'url' => appforge_page_url_by_template( 'page-popular-apps.php' ) ),
                            array( 'label' => __( 'Top Rated Apps', 'appforge' ), 'url' => appforge_page_url_by_template( 'page-top-apps.php' ) ),
                            array( 'label' => __( 'Hot Apps', 'appforge' ),       'url' => appforge_page_url_by_template( 'page-hot-apps.php' ) ),
                        );
                    ?>
                    <ul class="footer-link-list footer-link-list--arrow">
                        <?php foreach ( $explore_links as $link ) :
                            if ( empty( $link['url'] ) ) continue;
                        ?>
                        <li>
                            <a href="<?php echo esc_url( $link['url'] ); ?>">
                                <span><?php echo esc_html( $link['label'] ); ?></span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 6 15 12 9 18"/></svg>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>

                <!-- Categories -->
                <div class="footer-col">
                    <h5 class="footer-col-heading"><?php esc_html_e( 'Categories', 'appforge' ); ?></h5>
                    <?php
                    $footer_cats = get_terms( array(
                        'taxonomy'   => 'app_category',
                        'number'     => 5,
                        'hide_empty' => true,
                    ) );
                    if ( $footer_cats && ! is_wp_error( $footer_cats ) ) : ?>
                    <ul class="footer-link-list footer-link-list--icon">
                        <?php foreach ( $footer_cats as $cat ) : ?>
                        <li>
                            <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
                                <?php echo appforge_category_icon( $cat ); // phpcs:ignore ?>
                                <span><?php echo esc_html( $cat->name ); ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>

                <!-- Company -->
                <div class="footer-col">
                    <h5 class="footer-col-heading"><?php esc_html_e( 'Company', 'appforge' ); ?></h5>
                    <?php if ( has_nav_menu( 'footer-2' ) ) :
                        wp_nav_menu( array(
                            'theme_location' => 'footer-2',
                            'container'      => 'nav',
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ) );
                    else :
                        $company_links = array(
                            array(
                                'label' => __( 'About Us', 'appforge' ),
                                'url'   => appforge_page_url_by_slug( 'about' ),
                                'icon'  => '<path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>',
                            ),
                            array(
                                'label' => __( 'Contact Us', 'appforge' ),
                                'url'   => appforge_page_url_by_slug( 'contact' ),
                                'icon'  => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 6l-10 7L2 6"/>',
                            ),
                            array(
                                'label' => __( 'Privacy Policy', 'appforge' ),
                                'url'   => get_privacy_policy_url(),
                                'icon'  => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>',
                            ),
                            array(
                                'label' => __( 'Terms & Conditions', 'appforge' ),
                                'url'   => appforge_page_url_by_slug( 'terms' ),
                                'icon'  => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
                            ),
                        );
                    ?>
                    <ul class="footer-link-list footer-link-list--icon footer-link-list--muted">
                        <?php foreach ( $company_links as $link ) :
                            if ( empty( $link['url'] ) ) continue;
                        ?>
                        <li>
                            <a href="<?php echo esc_url( $link['url'] ); ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $link['icon']; // phpcs:ignore ?></svg>
                                <span><?php echo esc_html( $link['label'] ); ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>

            </div><!-- .footer-grid -->
        </div><!-- .container -->
    </div><!-- .footer-top -->

    <!-- Footer Bottom Bar -->
<div class="container">
    <div class="footer-bottom">

        <p class="footer-bottom-copyright">
            <span class="footer-bottom-shield" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </span>
            <?php
            $copyright = AppForge_Panel::get( 'footer_copyright' );

            if ( ! empty( $copyright ) ) {
                echo esc_html( $copyright );
            } else {
                echo '&copy; ' . esc_html( date( 'Y' ) ) . ' <strong>' . esc_html( get_bloginfo( 'name' ) ) . '</strong>. All rights reserved.';
            }
            ?>
        </p>

        <nav class="footer-bottom-legal" aria-label="<?php esc_attr_e( 'Legal links', 'appforge' ); ?>">

            <?php if ( AppForge_Panel::get( 'footer_show_credit', '1' ) === '1' ) :
                $author_name = get_theme_mod( 'appforge_author_name', 'Naeem Ullah' );
                $author_url  = get_theme_mod( 'appforge_author_website', 'https://naeem-ullah.com' );
            ?>

                <span class="footer-credit">
                    <?php esc_html_e( 'Designed by', 'appforge' ); ?>
                    <a href="<?php echo esc_url( $author_url ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html( $author_name ); ?>
                    </a>
                </span>

            <?php endif; ?>

            <?php $privacy_url = get_privacy_policy_url();
            if ( $privacy_url ) : ?>
            <a href="<?php echo esc_url( $privacy_url ); ?>">
                <?php esc_html_e( 'Privacy', 'appforge' ); ?>
            </a>
            <?php endif; ?>

            <?php $terms_url = appforge_page_url_by_slug( 'terms' );
            if ( $terms_url ) : ?>
            <a href="<?php echo esc_url( $terms_url ); ?>">
                <?php esc_html_e( 'Terms', 'appforge' ); ?>
            </a>
            <?php endif; ?>

            <?php if ( function_exists( 'wp_sitemaps_enabled' ) && wp_sitemaps_enabled() ) : ?>
            <a href="<?php echo esc_url( home_url( '/wp-sitemap.xml' ) ); ?>">
                <?php esc_html_e( 'Sitemap', 'appforge' ); ?>
            </a>
            <?php endif; ?>

        </nav>

    </div>
</div>

</footer><!-- .site-footer -->

<!-- Back to Top Button -->
<button class="back-to-top" id="backToTop" aria-label="<?php esc_attr_e( 'Back to top', 'appforge' ); ?>" hidden>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
        <polyline points="18 15 12 9 6 15"/>
    </svg>
</button>

<?php wp_footer(); ?>
</body>
</html>
