<?php
/**
 * AppForge Pro — Theme Functions
 * Version: 4.0
 */

// ============================================================
// THEME SETUP
// ============================================================
function appforge_setup() {
    load_theme_textdomain( 'appforge', get_template_directory() . '/languages' );

    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    register_nav_menus( array(
        'primary' => __( 'Main Menu', 'appforge' ),
        'footer'  => __( 'Footer Menu', 'appforge' ),
        'footer-2'=> __( 'Footer Menu 2', 'appforge' ),
    ) );

    add_image_size( 'app-icon',    300, 300, true );
    add_image_size( 'app-icon-sm', 100, 100, true );
    add_image_size( 'carousel',    800, 400, true );
    add_image_size( 'card-thumb',  600, 340, true );
    add_image_size( 'hero-thumb', 1200, 600, true );
}
add_action( 'after_setup_theme', 'appforge_setup' );

// ============================================================
// WIDGET AREAS
// ============================================================
function appforge_widgets_init() {
    $sidebars = array(
        array(
            'name'          => __( 'Main Sidebar', 'appforge' ),
            'id'            => 'sidebar-main',
            'description'   => __( 'Widgets in the main sidebar.', 'appforge' ),
        ),
        array(
            'name'          => __( 'Footer Column 1', 'appforge' ),
            'id'            => 'footer-1',
            'description'   => __( 'Footer widget area 1.', 'appforge' ),
        ),
        array(
            'name'          => __( 'Footer Column 2', 'appforge' ),
            'id'            => 'footer-2',
            'description'   => __( 'Footer widget area 2.', 'appforge' ),
        ),
    );

    $defaults = array(
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="widget__header"><h3 class="widget__title">',
        'after_title'   => '</h3></div><div class="widget__body">',
    );

    foreach ( $sidebars as $sidebar ) {
        register_sidebar( array_merge( $defaults, $sidebar ) );
    }
}
add_action( 'widgets_init', 'appforge_widgets_init' );

// ============================================================
// SCRIPTS & STYLES
// ============================================================
function appforge_scripts() {
    // Version by file modification time so browsers fetch fresh CSS/JS
    // the moment either file changes, instead of caching indefinitely.
    $style_path = get_stylesheet_directory() . '/style.css';
    $style_ver  = file_exists( $style_path ) ? filemtime( $style_path ) : '4.0';

    $js_path = get_template_directory() . '/js/main.js';
    $js_ver  = file_exists( $js_path ) ? filemtime( $js_path ) : '4.0';

    wp_enqueue_style(
        'appforge-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap',
        array(),
        null
    );

    wp_enqueue_style( 'appforge-style', get_stylesheet_uri(), array( 'appforge-fonts' ), $style_ver );

    wp_enqueue_script( 'appforge-js', get_template_directory_uri() . '/js/main.js', array(), $js_ver, true );

    wp_localize_script( 'appforge-js', 'appforgeData', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'appforge_nonce' ),
        'homeUrl' => home_url( '/' ),
    ) );

    if ( is_singular() && comments_open() ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'appforge_scripts' );

// Preconnect to Google Fonts for performance
function appforge_preconnect_fonts( $hints, $relation_type ) {
    if ( 'preconnect' === $relation_type ) {
        $hints[] = array( 'href' => 'https://fonts.googleapis.com' );
        $hints[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous' );
    }
    return $hints;
}
add_filter( 'wp_resource_hints', 'appforge_preconnect_fonts', 10, 2 );

// ============================================================
// CUSTOM POST TYPE: APP
// ============================================================
function appforge_register_apps() {
    register_post_type( 'app', array(
        'labels' => array(
            'name'               => __( 'Apps', 'appforge' ),
            'singular_name'      => __( 'App', 'appforge' ),
            'add_new'            => __( 'Add New App', 'appforge' ),
            'add_new_item'       => __( 'Add New App', 'appforge' ),
            'edit_item'          => __( 'Edit App', 'appforge' ),
            'new_item'           => __( 'New App', 'appforge' ),
            'view_item'          => __( 'View App', 'appforge' ),
            'search_items'       => __( 'Search Apps', 'appforge' ),
            'not_found'          => __( 'No apps found', 'appforge' ),
            'not_found_in_trash' => __( 'No apps in Trash', 'appforge' ),
        ),
        'public'       => true,
        'has_archive'  => true,
        'menu_icon'    => 'dashicons-smartphone',
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ),
        'rewrite'      => array( 'slug' => 'app' ),
        'show_in_rest' => true,
    ) );

    register_taxonomy( 'app_category', 'app', array(
        'labels' => array(
            'name'              => __( 'App Categories', 'appforge' ),
            'singular_name'     => __( 'App Category', 'appforge' ),
            'search_items'      => __( 'Search Categories', 'appforge' ),
            'all_items'         => __( 'All Categories', 'appforge' ),
            'parent_item'       => __( 'Parent Category', 'appforge' ),
            'parent_item_colon' => __( 'Parent Category:', 'appforge' ),
            'edit_item'         => __( 'Edit Category', 'appforge' ),
            'update_item'       => __( 'Update Category', 'appforge' ),
            'add_new_item'      => __( 'Add New Category', 'appforge' ),
            'new_item_name'     => __( 'New Category Name', 'appforge' ),
            'menu_name'         => __( 'Categories', 'appforge' ),
        ),
        'hierarchical'      => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'app-category' ),
    ) );
}
add_action( 'init', 'appforge_register_apps' );

// ============================================================
// META BOXES: APP DETAILS (see inc/meta-boxes.php)
// ============================================================
require_once get_template_directory() . '/inc/meta-boxes.php';

// ============================================================
// AUTO-ROUTE: regular Posts with APK meta → single-app.php
// ============================================================
add_filter( 'template_include', function ( $template ) {
    if ( is_singular( 'post' ) ) {
        global $post;
        $has_app = get_post_meta( $post->ID, '_download_url',   true )
                || get_post_meta( $post->ID, '_download_links', true );
        if ( $has_app ) {
            $t = get_template_directory() . '/single-app.php';
            if ( file_exists( $t ) ) return $t;
        }
    }
    return $template;
}, 99 );

// ============================================================
// APP VIEW COUNTER
// ============================================================
function appforge_track_app_views() {
    if ( ! is_singular( array( 'app', 'post' ) ) ) return;

    $post_id = get_the_ID();
    // For regular posts, only track if it has APK download meta
    if ( is_singular( 'post' ) && ! get_post_meta( $post_id, '_download_url', true )
         && ! get_post_meta( $post_id, '_download_links', true ) ) {
        return;
    }

    $views = (int) get_post_meta( $post_id, '_app_views', true );
    update_post_meta( $post_id, '_app_views', $views + 1 );
}
add_action( 'wp', 'appforge_track_app_views' );

// ============================================================
// HELPER FUNCTIONS
// ============================================================

/**
 * Social platform icons keyed by the AppForge_Panel option suffix
 * ('social_facebook', 'social_twitter', ...). Single source of truth
 * shared by the header top bar and the footer brand column, so a
 * platform added/fixed in one place can't drift out of sync with the
 * other.
 */
function appforge_social_platforms() {
    return array(
        'facebook'  => array(
            'label' => 'Facebook',
            'icon'  => '<svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>',
        ),
        'twitter'   => array(
            'label' => 'Twitter / X',
            'icon'  => '<svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>',
        ),
        'youtube'   => array(
            'label' => 'YouTube',
            'icon'  => '<svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58a2.78 2.78 0 001.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.96A29 29 0 0023 12a29 29 0 00-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/></svg>',
        ),
        'telegram'  => array(
            'label' => 'Telegram',
            'icon'  => '<svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M21.2 2.5L2.3 9.6c-1.3.5-1.3 1.2-.2 1.5l4.8 1.5 11.1-7c.5-.3 1-.1.6.3L9 14.5v3.5l2.8-2.7 5.5 4c1 .6 1.7.3 2-1L22 3.5c.3-1.5-.5-2.1-1.3-1z"/></svg>',
        ),
        'instagram' => array(
            'label' => 'Instagram',
            'icon'  => '<svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zm0 10.162a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
        ),
        'whatsapp'  => array(
            'label' => 'WhatsApp',
            'icon'  => '<svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M12.001 2C6.478 2 2 6.478 2 12c0 1.874.514 3.628 1.406 5.129L2 22l4.99-1.376A9.943 9.943 0 0012.001 22C17.523 22 22 17.522 22 12S17.523 2 12.001 2zm0 18.13a8.09 8.09 0 01-4.13-1.13l-.297-.176-3.02.833.81-2.996-.194-.309A8.087 8.087 0 013.91 12c0-4.464 3.63-8.094 8.094-8.094 4.464 0 8.094 3.63 8.094 8.094 0 4.465-3.634 8.13-8.097 8.13zm4.443-6.06c-.243-.122-1.44-.71-1.663-.79-.223-.082-.385-.122-.547.122-.162.244-.628.79-.77.952-.142.163-.284.183-.527.061-.243-.122-1.026-.378-1.955-1.207-.723-.645-1.212-1.441-1.354-1.685-.142-.244-.015-.375.107-.497.11-.109.244-.284.365-.426.122-.142.163-.244.244-.406.081-.163.04-.305-.02-.427-.06-.122-.548-1.32-.75-1.807-.198-.475-.399-.41-.548-.418-.142-.007-.304-.009-.466-.009a.893.893 0 00-.648.304c-.223.244-.85.83-.85 2.028s.87 2.354.99 2.517c.122.163 1.715 2.618 4.153 3.671.58.25 1.033.4 1.386.512.583.185 1.113.159 1.532.096.467-.07 1.44-.589 1.643-1.157.203-.568.203-1.055.142-1.157-.06-.102-.223-.163-.466-.285z"/></svg>',
        ),
    );
}

/**
 * Format large numbers for display (1.2M, 45K).
 */
function appforge_format_number( $num ) {
    $num = (int) $num;
    if ( $num >= 1000000 ) return round( $num / 1000000, 1 ) . 'M+';
    if ( $num >= 1000 )    return round( $num / 1000 )    . 'K+';
    return $num;
}

/**
 * Render star rating HTML.
 */
function appforge_stars( $rating, $max = 5 ) {
    $rating = (float) $rating;
    $output = '<span class="stars" aria-label="' . esc_attr( $rating . ' out of ' . $max ) . '">';
    for ( $i = 1; $i <= $max; $i++ ) {
        if ( $rating >= $i ) {
            $output .= '★';
        } elseif ( $rating >= $i - 0.5 ) {
            $output .= '½';
        } else {
            $output .= '☆';
        }
    }
    $output .= '</span>';
    return $output;
}

/**
 * Small line-icon (matching the header/footer icon style) for a given
 * app_category term, keyword-matched against its name/slug — falls
 * back to a generic grid icon for anything unrecognized. Used in the
 * footer's Categories column.
 */
function appforge_category_icon( $term ) {
    static $icons = array(
        'games'         => '<path d="M6 12h4M8 10v4"/><circle cx="15" cy="13" r="1"/><circle cx="18" cy="11" r="1"/><rect x="2" y="6" width="20" height="12" rx="6"/>',
        'entertainment' => '<rect x="2" y="6" width="14" height="12" rx="2"/><polygon points="22 8 16 12 22 16 22 8"/>',
        'video'         => '<rect x="2" y="6" width="14" height="12" rx="2"/><polygon points="22 8 16 12 22 16 22 8"/>',
        'security'      => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'vpn'           => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'productivity'  => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>',
        'business'      => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>',
        'tools'         => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>',
        'social'        => '<path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>',
        'communication' => '<path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>',
        'photo'         => '<path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/>',
        'photography'   => '<path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/>',
        'education'     => '<path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/>',
        'book'          => '<path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/>',
        'music'         => '<path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/>',
        'utilities'     => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>',
    );

    $haystack = strtolower( $term->slug . ' ' . $term->name );
    foreach ( $icons as $keyword => $path ) {
        if ( strpos( $haystack, $keyword ) !== false ) {
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
        }
    }

    // Fallback: generic app-grid icon
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>';
}

/**
 * Get a formatted download count string.
 */
function appforge_get_downloads( $post_id = null ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $downloads = get_post_meta( $post_id, '_downloads', true );
    return $downloads ? appforge_format_number( $downloads ) : false;
}

/**
 * Get the app rating.
 */
function appforge_get_rating( $post_id = null ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    return get_post_meta( $post_id, '_rating', true );
}

/**
 * Permalink of the first published page using a given page template file,
 * or '' if no such page exists yet (e.g. "page-hot-apps.php").
 */
function appforge_page_url_by_template( $template_file ) {
    static $cache = array();
    if ( isset( $cache[ $template_file ] ) ) return $cache[ $template_file ];

    $pages = get_posts( array(
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'meta_key'       => '_wp_page_template',
        'meta_value'     => $template_file,
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ) );

    return $cache[ $template_file ] = $pages ? get_permalink( $pages[0] ) : '';
}

/**
 * Permalink of a published page by slug, or '' if it doesn't exist.
 */
function appforge_page_url_by_slug( $slug ) {
    static $cache = array();
    if ( isset( $cache[ $slug ] ) ) return $cache[ $slug ];

    $page = get_page_by_path( $slug );
    return $cache[ $slug ] = ( $page && 'publish' === $page->post_status ) ? get_permalink( $page ) : '';
}

/**
 * Render breadcrumbs (no plugin required).
 */
function appforge_breadcrumbs() {
    if ( is_front_page() ) return;

    $sep = '<span class="breadcrumb__sep" aria-hidden="true">›</span>';
    $items = array();

    $items[] = '<a href="' . esc_url( home_url( '/' ) ) . '" class="breadcrumb__link">' . esc_html__( 'Home', 'appforge' ) . '</a>';

    if ( is_singular( 'app' ) ) {
        $terms = get_the_terms( get_the_ID(), 'app_category' );
        if ( $terms && ! is_wp_error( $terms ) ) {
            $items[] = '<a href="' . esc_url( get_term_link( $terms[0] ) ) . '" class="breadcrumb__link">' . esc_html( $terms[0]->name ) . '</a>';
        } else {
            $arc = get_post_type_archive_link( 'app' );
            if ( $arc ) $items[] = '<a href="' . esc_url( $arc ) . '" class="breadcrumb__link">' . esc_html__( 'Apps', 'appforge' ) . '</a>';
        }
        $items[] = '<span class="breadcrumb__current" aria-current="page">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_single() ) {
        // Regular post used as APK: show Apps archive link
        $post_id = get_the_ID();
        $is_app_post = get_post_meta( $post_id, '_download_url', true )
                    || get_post_meta( $post_id, '_download_links', true );
        if ( $is_app_post ) {
            $arc = get_post_type_archive_link( 'app' );
            if ( $arc ) $items[] = '<a href="' . esc_url( $arc ) . '" class="breadcrumb__link">' . esc_html__( 'Apps', 'appforge' ) . '</a>';
        } else {
            $cat = get_the_category();
            if ( $cat ) {
                $items[] = '<a href="' . esc_url( get_category_link( $cat[0]->term_id ) ) . '" class="breadcrumb__link">' . esc_html( $cat[0]->name ) . '</a>';
            }
        }
        $items[] = '<span class="breadcrumb__current" aria-current="page">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_tax( 'app_category' ) ) {
        $arc = get_post_type_archive_link( 'app' );
        if ( $arc ) $items[] = '<a href="' . esc_url( $arc ) . '" class="breadcrumb__link">' . esc_html__( 'Apps', 'appforge' ) . '</a>';
        $items[] = '<span class="breadcrumb__current" aria-current="page">' . esc_html( single_term_title( '', false ) ) . '</span>';
    } elseif ( is_category() ) {
        $items[] = '<span class="breadcrumb__current" aria-current="page">' . esc_html( single_cat_title( '', false ) ) . '</span>';
    } elseif ( is_tag() ) {
        $items[] = '<span class="breadcrumb__current" aria-current="page">' . esc_html( single_tag_title( '', false ) ) . '</span>';
    } elseif ( is_author() ) {
        $items[] = '<span class="breadcrumb__current" aria-current="page">' . esc_html( get_the_author() ) . '</span>';
    } elseif ( is_year() ) {
        $items[] = '<span class="breadcrumb__current" aria-current="page">' . esc_html( get_the_date( 'Y' ) ) . '</span>';
    } elseif ( is_month() ) {
        $items[] = '<a href="' . esc_url( get_year_link( get_the_date( 'Y' ) ) ) . '" class="breadcrumb__link">' . esc_html( get_the_date( 'Y' ) ) . '</a>';
        $items[] = '<span class="breadcrumb__current" aria-current="page">' . esc_html( get_the_date( 'F Y' ) ) . '</span>';
    } elseif ( is_search() ) {
        $items[] = '<span class="breadcrumb__current" aria-current="page">' . sprintf( esc_html__( 'Search: %s', 'appforge' ), get_search_query() ) . '</span>';
    } elseif ( is_page() ) {
        $items[] = '<span class="breadcrumb__current" aria-current="page">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_404() ) {
        $items[] = '<span class="breadcrumb__current">404</span>';
    }

    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'appforge' ) . '">';
    echo '<ol class="breadcrumbs__list">';
    foreach ( $items as $i => $item ) {
        echo '<li class="breadcrumbs__item">';
        if ( $i > 0 ) echo $sep; // phpcs:ignore
        echo $item; // phpcs:ignore
        echo '</li>';
    }
    echo '</ol>';
    echo '</nav>';
}

// ============================================================
// TABLE OF CONTENTS (blog posts)
// ============================================================

/**
 * Runs the post content through the normal `the_content` filter chain,
 * then injects an id="" into every H2/H3 that doesn't already have one
 * and collects them into a flat list for a table of contents.
 *
 * Returns array( 'content' => string, 'items' => array( array('level','text','slug') ) ).
 */
function appforge_content_with_toc() {
    $content     = apply_filters( 'the_content', get_the_content() );
    $items       = array();
    $used_slugs  = array();

    $content = preg_replace_callback(
        '/<h([23])([^>]*)>(.*?)<\/h\1>/is',
        function ( $m ) use ( &$items, &$used_slugs ) {
            $level = (int) $m[1];
            $attrs = $m[2];
            $inner = $m[3];
            $text  = trim( wp_strip_all_tags( $inner ) );

            if ( '' === $text ) {
                return $m[0];
            }

            if ( preg_match( '/\bid=["\']([^"\']+)["\']/i', $attrs, $idm ) ) {
                $slug = $idm[1];
            } else {
                $base = sanitize_title( $text ) ?: 'section';
                $slug = $base;
                $i    = 2;
                while ( in_array( $slug, $used_slugs, true ) ) {
                    $slug = $base . '-' . $i++;
                }
                $attrs = ' id="' . esc_attr( $slug ) . '"' . $attrs;
            }

            $used_slugs[] = $slug;
            $items[]      = array( 'level' => $level, 'text' => $text, 'slug' => $slug );

            return '<h' . $level . $attrs . '>' . $inner . '</h' . $level . '>';
        },
        $content
    );

    return array( 'content' => $content, 'items' => $items );
}

/**
 * Render the collapsible table-of-contents box. No-op if there are
 * fewer than 3 headings (not worth a ToC for a short post).
 */
function appforge_render_toc( $items ) {
    if ( count( $items ) < 3 ) return;
    ?>
    <nav class="toc" aria-label="<?php esc_attr_e( 'Table of contents', 'appforge' ); ?>">
        <button type="button" class="toc__toggle" aria-expanded="true">
            <span class="toc__title"><?php esc_html_e( 'Table of Contents', 'appforge' ); ?></span>
            <svg class="toc__chev" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </button>
        <div class="toc__body">
            <ol class="toc__list">
                <?php foreach ( $items as $item ) : ?>
                <li class="toc__item toc__item--h<?php echo esc_attr( $item['level'] ); ?>">
                    <a href="#<?php echo esc_attr( $item['slug'] ); ?>"><?php echo esc_html( $item['text'] ); ?></a>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </nav>
    <?php
}

// ============================================================
// EXCERPT
// ============================================================
function appforge_excerpt_length( $length ) {
    return is_admin() ? $length : 20;
}
add_filter( 'excerpt_length', 'appforge_excerpt_length' );

function appforge_excerpt_more( $more ) {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'appforge_excerpt_more' );

// ============================================================
// BODY CLASSES
// ============================================================
function appforge_body_classes( $classes ) {
    if ( is_singular() ) $classes[] = 'singular';
    if ( ! is_front_page() && is_home() ) $classes[] = 'blog-home';
    return $classes;
}
add_filter( 'body_class', 'appforge_body_classes' );

// ============================================================
// STRUCTURED DATA (JSON-LD)
// ============================================================
function appforge_app_schema() {
    if ( ! is_singular( 'app' ) ) return;

    $post_id    = get_the_ID();
    $developer  = get_post_meta( $post_id, '_developer',  true );
    $version    = get_post_meta( $post_id, '_version',    true );
    $size       = get_post_meta( $post_id, '_size',       true );
    $requires   = get_post_meta( $post_id, '_requires',   true );
    $released   = get_post_meta( $post_id, '_released',   true );
    $rating     = get_post_meta( $post_id, '_rating',     true );
    $votes      = get_post_meta( $post_id, '_votes',      true );
    $pricing    = get_post_meta( $post_id, '_app_pricing', true ) ?: 'free';
    $price      = $pricing === 'paid' ? get_post_meta( $post_id, '_app_price', true ) : '0';
    $currency   = get_post_meta( $post_id, '_app_currency', true ) ?: 'USD';
    $download   = get_post_meta( $post_id, '_download_url', true );
    $categories = get_the_terms( $post_id, 'app_category' );
    $category   = ( $categories && ! is_wp_error( $categories ) ) ? $categories[0]->name : '';
    $image      = has_post_thumbnail() ? get_the_post_thumbnail_url( $post_id, 'app-icon' ) : '';
    $excerpt    = get_the_excerpt();

    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'SoftwareApplication',
        'name'            => get_the_title(),
        'url'             => get_permalink(),
        'operatingSystem' => $requires ?: 'Android',
        'dateModified'    => get_the_modified_date( 'c' ),
    );

    if ( $image )     $schema['image']             = $image;
    if ( $excerpt )   $schema['description']       = $excerpt;
    if ( $category )  $schema['applicationCategory'] = $category;
    if ( $version )   $schema['softwareVersion']   = $version;
    if ( $size )       $schema['fileSize']          = $size;
    if ( $released )  $schema['datePublished']      = $released;
    if ( $download )  $schema['downloadUrl']        = $download;
    if ( $developer ) $schema['author']             = array(
        '@type' => 'Organization',
        'name'  => $developer,
    );
    if ( $rating && $votes ) {
        $schema['aggregateRating'] = array(
            '@type'       => 'AggregateRating',
            'ratingValue' => (string) $rating,
            'ratingCount' => (string) (int) $votes,
            'bestRating'  => '5',
            'worstRating' => '1',
        );
    }
    $schema['offers'] = array(
        '@type'         => 'Offer',
        'price'         => (string) $price,
        'priceCurrency' => $currency,
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n"; // phpcs:ignore
}
add_action( 'wp_head', 'appforge_app_schema' );

// ============================================================
// APP REVIEWS (star-rated comments on the 'app' post type)
// ============================================================

// One-time migration: existing app posts predate 'comments' support,
// which made WP store them with comment_status = 'closed'.
function appforge_enable_app_comments_once() {
    if ( get_option( 'appforge_app_comments_migrated' ) ) return;

    $ids = get_posts( array(
        'post_type'   => 'app',
        'post_status' => 'any',
        'numberposts' => -1,
        'fields'      => 'ids',
    ) );
    foreach ( $ids as $id ) {
        wp_update_post( array( 'ID' => $id, 'comment_status' => 'open' ) );
    }
    update_option( 'appforge_app_comments_migrated', 1 );
}
add_action( 'init', 'appforge_enable_app_comments_once', 20 );

// Save the 1-5 star rating submitted alongside a review.
function appforge_save_review_rating( $comment_id, $comment_approved, $commentdata ) {
    if ( get_post_type( $commentdata['comment_post_ID'] ) !== 'app' ) return;
    if ( empty( $_POST['app_rating'] ) ) return;

    $rating = max( 1, min( 5, (int) $_POST['app_rating'] ) );
    update_comment_meta( $comment_id, 'rating', $rating );
}
add_action( 'comment_post', 'appforge_save_review_rating', 10, 3 );

// Combined name/email row for the review form (matches .comment-form .form-row styling).
function appforge_review_author_fields() {
    $commenter = wp_get_current_commenter();
    return '<div class="form-row">'
        . '<div><label for="author">' . esc_html__( 'Name', 'appforge' ) . ' <span class="required">*</span></label>'
        . '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" required></div>'
        . '<div><label for="email">' . esc_html__( 'Email', 'appforge' ) . ' <span class="required">*</span></label>'
        . '<input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" required></div>'
        . '</div>';
}

// Star-rating input + review textarea for the review form.
function appforge_review_comment_field() {
    $stars = '';
    for ( $i = 5; $i >= 1; $i-- ) {
        $stars .= '<input type="radio" id="af-star-' . $i . '" name="app_rating" value="' . $i . '" required />'
                 . '<label for="af-star-' . $i . '" title="' . $i . '"></label>';
    }
    return '<div class="star-rating-input" role="radiogroup" aria-label="' . esc_attr__( 'Your rating', 'appforge' ) . '">' . $stars . '</div>'
        . '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your Review', 'appforge' ) . '</label>'
        . '<textarea id="comment" name="comment" rows="5" required></textarea></p>';
}

// ============================================================
// APP ARCHIVE FILTERS (category / OS / pricing / sort via $_GET)
// ============================================================

// Merge sanitized filter GET params into a WP_Query args array.
function appforge_app_filter_args( $args = array() ) {
    if ( ! empty( $_GET['app_category'] ) ) {
        $tax_query   = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
        $tax_query[] = array(
            'taxonomy' => 'app_category',
            'field'    => 'slug',
            'terms'    => sanitize_title( wp_unslash( $_GET['app_category'] ) ),
        );
        $args['tax_query'] = $tax_query;
    }

    $meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

    if ( ! empty( $_GET['os'] ) ) {
        $os = sanitize_key( wp_unslash( $_GET['os'] ) );
        if ( array_key_exists( $os, appforge_app_os_options() ) ) {
            $meta_query[] = array( 'key' => '_app_os', 'value' => $os );
        }
    }

    if ( ! empty( $_GET['pricing'] ) ) {
        $pricing = sanitize_key( wp_unslash( $_GET['pricing'] ) );
        if ( in_array( $pricing, array( 'free', 'paid' ), true ) ) {
            $meta_query[] = array( 'key' => '_app_pricing', 'value' => $pricing );
        }
    }

    if ( $meta_query ) {
        $args['meta_query'] = $meta_query;
    }

    if ( ! empty( $_GET['sort'] ) ) {
        switch ( sanitize_key( wp_unslash( $_GET['sort'] ) ) ) {
            case 'rating':
                $args['meta_key'] = '_rating';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'downloads':
                $args['meta_key'] = '_downloads';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'newest':
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
                break;
            case 'name':
                $args['orderby'] = 'title';
                $args['order']   = 'ASC';
                break;
        }
    }

    return $args;
}

function appforge_app_os_options() {
    return array(
        'android' => __( 'Android', 'appforge' ),
        'ios'     => __( 'iOS', 'appforge' ),
        'windows' => __( 'Windows', 'appforge' ),
        'mac'     => __( 'Mac', 'appforge' ),
        'linux'   => __( 'Linux', 'appforge' ),
    );
}

// Apply the same filters to the main query on the app archive / app_category pages.
function appforge_filter_main_apps_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) return;
    if ( ! ( $query->is_post_type_archive( 'app' ) || $query->is_tax( 'app_category' ) ) ) return;

    $args = appforge_app_filter_args();

    if ( ! empty( $args['tax_query'] ) ) {
        $existing = $query->get( 'tax_query' );
        $query->set( 'tax_query', $existing ? array_merge( (array) $existing, $args['tax_query'] ) : $args['tax_query'] );
    }
    if ( ! empty( $args['meta_query'] ) ) {
        $query->set( 'meta_query', $args['meta_query'] );
    }
    if ( ! empty( $args['orderby'] ) ) {
        $query->set( 'orderby', $args['orderby'] );
        $query->set( 'order', $args['order'] );
        if ( ! empty( $args['meta_key'] ) ) $query->set( 'meta_key', $args['meta_key'] );
    }
}
add_action( 'pre_get_posts', 'appforge_filter_main_apps_query' );

// ============================================================
// APP REPORT (broken link / malware / copyright, etc.)
// ============================================================
function appforge_report_reasons() {
    return array(
        'broken_link' => __( 'Download link is broken', 'appforge' ),
        'fake'        => __( 'Fake or misleading app', 'appforge' ),
        'malware'     => __( 'Contains malware or virus', 'appforge' ),
        'copyright'   => __( 'Copyright / DMCA violation', 'appforge' ),
        'other'       => __( 'Other', 'appforge' ),
    );
}

function appforge_handle_app_report() {
    check_ajax_referer( 'appforge_nonce', 'nonce' );

    $post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
    $reason  = isset( $_POST['reason'] )  ? sanitize_key( wp_unslash( $_POST['reason'] ) ) : '';
    $details = isset( $_POST['details'] ) ? sanitize_textarea_field( wp_unslash( $_POST['details'] ) ) : '';
    $reasons = appforge_report_reasons();

    if ( ! $post_id || get_post_type( $post_id ) !== 'app' || ! isset( $reasons[ $reason ] ) ) {
        wp_send_json_error( array( 'message' => __( 'Invalid report.', 'appforge' ) ) );
    }

    $comment_id = wp_insert_comment( array(
        'comment_post_ID'  => $post_id,
        'comment_author'   => 'App Report',
        'comment_content'  => '[' . $reasons[ $reason ] . '] ' . $details,
        'comment_type'     => 'app_report',
        'comment_approved' => 0,
        'user_id'          => get_current_user_id(),
    ) );

    if ( ! $comment_id ) {
        wp_send_json_error( array( 'message' => __( 'Something went wrong. Please try again.', 'appforge' ) ) );
    }

    update_comment_meta( $comment_id, 'report_reason', $reason );
    wp_send_json_success( array( 'message' => __( 'Thank you — we will review this app.', 'appforge' ) ) );
}
add_action( 'wp_ajax_appforge_report_app', 'appforge_handle_app_report' );
add_action( 'wp_ajax_nopriv_appforge_report_app', 'appforge_handle_app_report' );

// ============================================================
// ADMIN PANEL
// ============================================================
require_once get_template_directory() . '/inc/admin-panel.php';

// ============================================================
// CUSTOMIZER
// ============================================================
require_once get_template_directory() . '/inc/customizer.php';

// ============================================================
// REMOVE EMOJI SCRIPTS (PERFORMANCE)
// ============================================================
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
