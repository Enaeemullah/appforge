<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
    (function () {
        try {
            var saved = localStorage.getItem( 'appforge-theme' );
            var theme = saved || '<?php echo esc_js( class_exists( 'AppForge_Panel' ) ? AppForge_Panel::get( 'color_style', 'light' ) : 'light' ); ?>';
            if ( theme === 'dark' ) document.documentElement.setAttribute( 'data-theme', 'dark' );
        } catch ( e ) {}
    }());
    </script>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a href="#main-content" class="skip-link"><?php esc_html_e( 'Skip to main content', 'appforge' ); ?></a>

<?php $apps_archive_url = get_post_type_archive_link( 'app' ); ?>

<!-- ===== TOP UTILITY BAR ===== -->
<div class="top-bar">
    <div class="top-bar__inner">

        <div class="top-bar__trust">
            <span class="top-bar__trust-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
            </span>
            <span><?php esc_html_e( 'Safe Downloads', 'appforge' ); ?></span>
            <span aria-hidden="true">&bull;</span>
            <span><?php esc_html_e( 'Verified Apps', 'appforge' ); ?></span>
            <span aria-hidden="true">&bull;</span>
            <span><?php esc_html_e( '100% Secure', 'appforge' ); ?></span>
        </div>

        <div class="top-bar__right">

            <?php
            $top_bar_socials = appforge_social_platforms();
            $has_top_bar_social = false;
            foreach ( $top_bar_socials as $key => $s ) {
                if ( AppForge_Panel::get( 'social_' . $key, '' ) ) { $has_top_bar_social = true; break; }
            }
            if ( $has_top_bar_social ) : ?>
            <div class="top-bar__social">
                <?php foreach ( $top_bar_socials as $key => $s ) :
                    $url = AppForge_Panel::get( 'social_' . $key, '' );
                    if ( empty( $url ) ) continue;
                ?>
                <a href="<?php echo esc_url( $url ); ?>" class="top-bar__social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $s['label'] ); ?>"><?php echo $s['icon']; // phpcs:ignore ?></a>
                <?php endforeach; ?>
            </div>
            <span class="top-bar__divider" aria-hidden="true"></span>
            <?php endif; ?>

            <!-- Dark / Light theme toggle -->
            <button type="button" class="theme-toggle" id="themeToggle"
                    aria-label="<?php esc_attr_e( 'Toggle dark mode', 'appforge' ); ?>" aria-pressed="false">
                <svg class="theme-toggle__icon theme-toggle__icon--sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                    <circle cx="12" cy="12" r="4"/>
                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                </svg>
                <svg class="theme-toggle__icon theme-toggle__icon--moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
            </button>

        </div>

    </div><!-- .top-bar__inner -->
</div><!-- .top-bar -->

<!-- ===== TOP NAVIGATION ===== -->
<header class="top-nav" role="banner">
    <div class="nav-container">

        <!-- Logo -->
        <div class="nav-logo">
            <?php if ( has_custom_logo() ) :
                the_custom_logo();
            else : ?>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                <span class="logo-text">APPFORGE</span>
            </a>
            <?php endif; ?>
        </div>

        <!-- Desktop Navigation -->
        <nav class="main-nav" id="site-navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'appforge' ); ?>">
            <?php
            $primary_menu_html = wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'nav-menu',
                'fallback_cb'    => false,
                'echo'           => false,
            ) );

            // Auto-populated "Categories" dropdown — data-driven from the
            // app_category taxonomy rather than requiring the admin to
            // rebuild it by hand in Appearance → Menus every time a
            // category is added.
            $nav_cats = get_terms( array(
                'taxonomy'   => 'app_category',
                'number'     => 8,
                'orderby'    => 'count',
                'order'      => 'DESC',
                'hide_empty' => true,
            ) );

            if ( $nav_cats && ! is_wp_error( $nav_cats ) ) {
                $cats_html  = '<li class="menu-item menu-item-has-children nav-cats-dropdown">';
                $cats_html .= '<a href="' . esc_url( $apps_archive_url ) . '">' . esc_html__( 'Categories', 'appforge' ) . '</a>';
                $cats_html .= '<ul class="sub-menu">';
                foreach ( $nav_cats as $cat ) {
                    $cats_html .= '<li class="menu-item' . ( is_tax( 'app_category', $cat ) ? ' current-menu-item' : '' ) . '">'
                                . '<a href="' . esc_url( get_term_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a></li>';
                }
                $cats_html .= '</ul></li>';

                if ( $primary_menu_html ) {
                    $last_ul_pos = strrpos( $primary_menu_html, '</ul>' );
                    $primary_menu_html = substr_replace( $primary_menu_html, $cats_html . '</ul>', $last_ul_pos, 5 );
                } else {
                    $primary_menu_html = '<ul class="nav-menu">' . $cats_html . '</ul>';
                }
            }

            echo $primary_menu_html; // phpcs:ignore
            ?>
        </nav>

        <!-- Inline Header Search (small by default, expands to center on focus) -->
        <form class="nav-search-bar" id="navSearchBar" role="search" method="get"
              action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <label for="nav-search-input" class="sr-only"><?php esc_html_e( 'Search', 'appforge' ); ?></label>
            <button type="submit" aria-label="<?php esc_attr_e( 'Submit search', 'appforge' ); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                    <circle cx="11" cy="11" r="7"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </button>
            <input type="search" id="nav-search-input" name="s"
                   placeholder="<?php esc_attr_e( 'Search apps, games…', 'appforge' ); ?>"
                   autocomplete="off" value="<?php echo esc_attr( get_search_query() ); ?>">
        </form>

    </div><!-- .nav-container -->
</header><!-- .top-nav -->

<main id="main-content" tabindex="-1">
