</main><!-- #main-content -->

<!-- ===== SITE FOOTER ===== -->
<footer class="site-footer" role="contentinfo">
    <div class="footer-top">
        <div class="container">
            <div class="footer-grid">

                <!-- Brand Column -->
                <div class="footer-brand">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo" rel="home">
                        <span class="footer-logo-text">APPFORGE</span>
                    </a>
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
                    $socials = array(
                        'facebook'  => array(
                            'label' => 'Facebook',
                            'icon'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" width="16" height="16"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>',
                        ),
                        'twitter'   => array(
                            'label' => 'Twitter / X',
                            'icon'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" width="16" height="16"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>',
                        ),
                        'youtube'   => array(
                            'label' => 'YouTube',
                            'icon'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" width="16" height="16"><path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58a2.78 2.78 0 001.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.96A29 29 0 0023 12a29 29 0 00-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/></svg>',
                        ),
                        'telegram'  => array(
                            'label' => 'Telegram',
                            'icon'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" width="16" height="16"><path d="M21.2 2.5L2.3 9.6c-1.3.5-1.3 1.2-.2 1.5l4.8 1.5 11.1-7c.5-.3 1-.1.6.3L9 14.5v3.5l2.8-2.7 5.5 4c1 .6 1.7.3 2-1L22 3.5c.3-1.5-.5-2.1-1.3-1z"/></svg>',
                        ),
                        'instagram' => array(
                            'label' => 'Instagram',
                            'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" width="16" height="16"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>',
                        ),
                        'whatsapp'  => array(
                            'label' => 'WhatsApp',
                            'icon'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" width="16" height="16"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',
                        ),
                    );
                            $has_social = false;

                            foreach ( $socials as $key => $s ) {
                                if ( AppForge_Panel::get( 'social_' . $key, '' ) ) {
                                    $has_social = true;
                                    break;
                                }
                            }

                            if ( $has_social ) :
                            ?>
                            <div class="footer-social" role="list" aria-label="<?php esc_attr_e( 'Social media links', 'appforge' ); ?>">

                                <?php foreach ( $socials as $key => $s ) :

                                    $url = AppForge_Panel::get( 'social_' . $key, '' );

                                    if ( empty( $url ) ) {
                                        continue;
                                    }
                                ?>

                                <a href="<?php echo esc_url( $url ); ?>"
                                class="social-btn"
                                role="listitem"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="<?php echo esc_attr( $s['label'] ); ?>">

                                    <?php echo $s['icon']; ?>

                                </a>

                                <?php endforeach; ?>

                            </div>
                            <?php endif; ?>
                </div>

                <!-- Quick Links -->
                <div class="footer-col">
                    <h5><?php esc_html_e( 'Quick Links', 'appforge' ); ?></h5>
                    <?php if ( has_nav_menu( 'footer' ) ) :
                        wp_nav_menu( array(
                            'theme_location' => 'footer',
                            'container'      => 'nav',
                            'container_attr' => array( 'aria-label' => esc_attr__( 'Footer navigation', 'appforge' ) ),
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ) );
                    else : ?>
                    <ul>
                        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'appforge' ); ?></a></li>
                        <li><a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>"><?php esc_html_e( 'All Apps', 'appforge' ); ?></a></li>
                        <li><a href="<?php echo esc_url( get_tag_link( 'hot' ) ); ?>"><?php esc_html_e( 'Hot Apps', 'appforge' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Blog', 'appforge' ); ?></a></li>
                    </ul>
                    <?php endif; ?>
                </div>

                <!-- Categories -->
                <div class="footer-col">
                    <h5><?php esc_html_e( 'Categories', 'appforge' ); ?></h5>
                    <?php
                    $footer_cats = get_terms( array(
                        'taxonomy'   => 'app_category',
                        'number'     => 6,
                        'hide_empty' => true,
                    ) );
                    if ( $footer_cats && ! is_wp_error( $footer_cats ) ) : ?>
                    <ul>
                        <?php foreach ( $footer_cats as $cat ) : ?>
                        <li><a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else : ?>
                    <ul>
                        <li><a href="#"><?php esc_html_e( 'Games', 'appforge' ); ?></a></li>
                        <li><a href="#"><?php esc_html_e( 'Utilities', 'appforge' ); ?></a></li>
                        <li><a href="#"><?php esc_html_e( 'Social', 'appforge' ); ?></a></li>
                        <li><a href="#"><?php esc_html_e( 'Photography', 'appforge' ); ?></a></li>
                        <li><a href="#"><?php esc_html_e( 'Productivity', 'appforge' ); ?></a></li>
                    </ul>
                    <?php endif; ?>
                </div>

                <!-- Support -->
                <div class="footer-col">
                    <h5><?php esc_html_e( 'Support', 'appforge' ); ?></h5>
                    <?php if ( has_nav_menu( 'footer-2' ) ) :
                        wp_nav_menu( array(
                            'theme_location' => 'footer-2',
                            'container'      => 'nav',
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ) );
                    else : ?>
                    <ul>
                        <li><a href="<?php echo esc_url( home_url( '/help/' ) ); ?>"><?php esc_html_e( 'Help Center', 'appforge' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Us', 'appforge' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'appforge' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms of Use', 'appforge' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/dmca/' ) ); ?>"><?php esc_html_e( 'DMCA', 'appforge' ); ?></a></li>
                    </ul>
                    <?php endif; ?>
                </div>

            </div><!-- .footer-grid -->
        </div><!-- .container -->
    </div><!-- .footer-top -->

    <!-- Footer Bottom Bar -->
<div class="container">
    <div class="footer-bottom">

        <p>
            <?php
            $copyright = AppForge_Panel::get( 'footer_copyright' );

            if ( ! empty( $copyright ) ) {
                echo esc_html( $copyright );
            } else {
                echo '&copy; ' . esc_html( date( 'Y' ) ) . ' ' . esc_html( get_bloginfo( 'name' ) ) . '. All Rights Reserved.';
            }
            ?>
        </p>

        <nav aria-label="<?php esc_attr_e( 'Legal links', 'appforge' ); ?>">

            <?php if ( AppForge_Panel::get( 'footer_show_credit', '1' ) === '1' ) : ?>

                <?php
                $author_name = 'Naeem Ullah';
                $author_url  = 'https://naeem-ullah.com';
                ?>

                <span class="footer-credit">
                    <?php esc_html_e( 'Designed by', 'appforge' ); ?>
                    <a href="<?php echo esc_url( $author_url ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html( $author_name ); ?>
                    </a>
                </span>

            <?php endif; ?>

            <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">
                <?php esc_html_e( 'Privacy', 'appforge' ); ?>
            </a>

            <a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>">
                <?php esc_html_e( 'Terms', 'appforge' ); ?>
            </a>

            <a href="<?php echo esc_url( home_url( '/sitemap.xml' ) ); ?>">
                <?php esc_html_e( 'Sitemap', 'appforge' ); ?>
            </a>

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
