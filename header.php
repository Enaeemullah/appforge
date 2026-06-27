<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a href="#main-content" class="skip-link"><?php esc_html_e( 'Skip to main content', 'appforge' ); ?></a>

<!-- ===== TOP NAVIGATION ===== -->
<header class="top-nav" role="banner">
    <div class="nav-container">

        <!-- Logo -->
        <div class="nav-logo">
            <?php if ( has_custom_logo() ) :
                the_custom_logo();
            else : ?>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                <span class="logo-icon" aria-hidden="true">⚡</span>
                <span class="logo-text">App<span>Forge</span></span>
            </a>
            <?php endif; ?>
        </div>

        <!-- Desktop Navigation -->
        <nav class="main-nav" id="site-navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'appforge' ); ?>">
            <?php wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'nav-menu',
                'fallback_cb'    => false,
            ) ); ?>
        </nav>

        <!-- Nav Actions -->
        <div class="nav-actions">
            <!-- Search Toggle -->
            <button class="nav-search-btn" id="searchToggle"
                    aria-label="<?php esc_attr_e( 'Open search', 'appforge' ); ?>"
                    aria-expanded="false" aria-controls="searchOverlay">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                    <circle cx="11" cy="11" r="7"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </button>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-btn" id="mobileMenuBtn"
                    aria-label="<?php esc_attr_e( 'Toggle navigation menu', 'appforge' ); ?>"
                    aria-expanded="false" aria-controls="mobileNav">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

    </div><!-- .nav-container -->
</header><!-- .top-nav -->

<!-- ===== CATEGORY NAV BAR ===== -->
<?php
$nav_cats = get_terms( array(
    'taxonomy'   => 'app_category',
    'number'     => 8,
    'orderby'    => 'count',
    'order'      => 'DESC',
    'hide_empty' => true,
) );
if ( $nav_cats && ! is_wp_error( $nav_cats ) ) : ?>
<nav class="cat-nav-bar" aria-label="<?php esc_attr_e( 'App categories', 'appforge' ); ?>">
    <div class="cat-nav-inner">
        <a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>"
           class="nav-cat-link <?php echo is_post_type_archive( 'app' ) ? 'active' : ''; ?>">
            <?php esc_html_e( 'All Apps', 'appforge' ); ?>
        </a>
        <?php foreach ( $nav_cats as $cat ) : ?>
        <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"
           class="nav-cat-link <?php echo is_tax( 'app_category', $cat ) ? 'active' : ''; ?>">
            <?php echo esc_html( $cat->name ); ?>
        </a>
        <?php endforeach; ?>
    </div>
</nav>
<?php endif; ?>

<!-- ===== MOBILE NAV DRAWER ===== -->
<div class="mobile-nav" id="mobileNav" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Mobile navigation', 'appforge' ); ?>" hidden>
    <div class="mobile-nav__inner">
        <?php wp_nav_menu( array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'mobile-menu',
            'fallback_cb'    => false,
        ) ); ?>
    </div>
</div>

<!-- ===== SEARCH OVERLAY ===== -->
<div class="search-overlay" id="searchOverlay" role="dialog" aria-modal="true"
     aria-label="<?php esc_attr_e( 'Search', 'appforge' ); ?>" hidden>
    <div class="search-overlay__inner">
        <p class="search-overlay__label"><?php esc_html_e( 'Search for apps, articles or topics…', 'appforge' ); ?></p>
        <form class="search-overlay__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <label for="overlay-search" class="sr-only"><?php esc_html_e( 'Search', 'appforge' ); ?></label>
            <input type="search" id="overlay-search" name="s"
                   placeholder="<?php esc_attr_e( 'Type to search…', 'appforge' ); ?>"
                   autocomplete="off" value="<?php echo esc_attr( get_search_query() ); ?>">
            <button type="submit" aria-label="<?php esc_attr_e( 'Submit search', 'appforge' ); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                    <circle cx="11" cy="11" r="7"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </button>
        </form>
    </div>
    <button class="search-close-btn" id="searchClose"
            aria-label="<?php esc_attr_e( 'Close search', 'appforge' ); ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true" focusable="false">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
    </button>
</div>

<main id="main-content" tabindex="-1">
