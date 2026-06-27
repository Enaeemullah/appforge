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
    $ver = '4.0';

    wp_enqueue_style(
        'appforge-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap',
        array(),
        null
    );

    wp_enqueue_style( 'appforge-style', get_stylesheet_uri(), array( 'appforge-fonts' ), $ver );

    wp_enqueue_script( 'appforge-js', get_template_directory_uri() . '/js/main.js', array(), $ver, true );

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
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
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
// META BOXES: APP DETAILS
// ============================================================
function appforge_meta_boxes() {
    add_meta_box( 'app_details', __( 'App Details', 'appforge' ), 'appforge_details_html', 'app', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'appforge_meta_boxes' );

function appforge_details_html( $post ) {
    wp_nonce_field( 'appforge_save_details', 'appforge_details_nonce' );

    $inputs = array(
        '_download_url'    => array( 'label' => 'Download URL (APK)', 'type' => 'url', 'placeholder' => 'https://...' ),
        '_telegram_url'    => array( 'label' => 'Telegram URL', 'type' => 'url', 'placeholder' => 'https://t.me/...' ),
        '_google_play_url' => array( 'label' => 'Google Play URL', 'type' => 'url' ),
        '_developer_url'   => array( 'label' => 'Developer Website', 'type' => 'url' ),
        '_rating'          => array( 'label' => 'Rating (1–5)', 'type' => 'number', 'attrs' => 'step="0.1" min="0" max="5"' ),
        '_votes'           => array( 'label' => 'Vote Count', 'type' => 'number' ),
        '_downloads'       => array( 'label' => 'Downloads Count', 'type' => 'number' ),
        '_app_views'       => array( 'label' => 'View Count', 'type' => 'number' ),
        '_version'         => array( 'label' => 'App Version', 'type' => 'text', 'placeholder' => '1.0.0' ),
        '_developer'       => array( 'label' => 'Developer Name', 'type' => 'text' ),
        '_size'            => array( 'label' => 'File Size (e.g. 45 MB)', 'type' => 'text' ),
        '_requires'        => array( 'label' => 'Requires Android', 'type' => 'text', 'placeholder' => '5.0+' ),
        '_released'        => array( 'label' => 'Release Date', 'type' => 'text', 'placeholder' => 'Jan 1, 2024' ),
    );

    $textareas = array(
        '_whats_new' => array( 'label' => "What's New", 'placeholder' => 'Bug fixes and performance improvements...' ),
        '_versions'  => array( 'label' => 'Version History (JSON)', 'placeholder' => '[{"version":"1.0","size":"45 MB","requirements":"5.0","date":"2024-01-01"}]' ),
    );
    ?>
    <style>
        .af-meta { padding: 4px 0; }
        .af-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px; }
        .af-meta label { display: block; font-weight: 600; margin-bottom: 3px; font-size: 11px; color: #555; text-transform: uppercase; letter-spacing: .5px; }
        .af-meta input, .af-meta textarea { width: 100%; border: 1px solid #ddd; border-radius: 4px; padding: 6px 10px; font-size: 13px; box-sizing: border-box; }
        .af-meta input:focus, .af-meta textarea:focus { border-color: #00C853; outline: none; box-shadow: 0 0 0 2px rgba(0,200,83,.15); }
        .af-meta textarea { min-height: 80px; resize: vertical; font-family: monospace; font-size: 12px; }
        .af-meta-ta { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    </style>
    <div class="af-meta">
        <div class="af-meta-grid">
            <?php foreach ( $inputs as $key => $field ) :
                $value       = get_post_meta( $post->ID, $key, true );
                $attrs       = isset( $field['attrs'] ) ? $field['attrs'] : '';
                $placeholder = isset( $field['placeholder'] ) ? 'placeholder="' . esc_attr( $field['placeholder'] ) . '"' : '';
            ?>
            <div>
                <label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                <input type="<?php echo esc_attr( $field['type'] ); ?>"
                       id="<?php echo esc_attr( $key ); ?>"
                       name="<?php echo esc_attr( $key ); ?>"
                       value="<?php echo esc_attr( $value ); ?>"
                       <?php echo $attrs; ?>
                       <?php echo $placeholder; ?>>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="af-meta-ta">
            <?php foreach ( $textareas as $key => $field ) :
                $value = get_post_meta( $post->ID, $key, true );
            ?>
            <div>
                <label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                <textarea id="<?php echo esc_attr( $key ); ?>"
                          name="<?php echo esc_attr( $key ); ?>"
                          placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function appforge_save_details( $post_id ) {
    if ( ! isset( $_POST['appforge_details_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['appforge_details_nonce'], 'appforge_save_details' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $url_fields  = array( '_download_url', '_telegram_url', '_google_play_url', '_developer_url' );
    $text_fields = array( '_rating', '_votes', '_downloads', '_app_views', '_version', '_developer', '_size', '_requires', '_released' );
    $ta_fields   = array( '_whats_new', '_versions' );

    foreach ( $url_fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $field, esc_url_raw( $_POST[ $field ] ) );
        }
    }
    foreach ( $text_fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( $_POST[ $field ] ) );
        }
    }
    foreach ( $ta_fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, $field, sanitize_textarea_field( $_POST[ $field ] ) );
        }
    }
}
add_action( 'save_post', 'appforge_save_details' );

// ============================================================
// APP VIEW COUNTER
// ============================================================
function appforge_track_app_views() {
    if ( ! is_singular( 'app' ) ) return;

    $post_id = get_the_ID();
    $views   = (int) get_post_meta( $post_id, '_app_views', true );
    update_post_meta( $post_id, '_app_views', $views + 1 );
}
add_action( 'wp', 'appforge_track_app_views' );

// ============================================================
// HELPER FUNCTIONS
// ============================================================

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
        }
        $items[] = '<span class="breadcrumb__current" aria-current="page">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_single() ) {
        $cat = get_the_category();
        if ( $cat ) {
            $items[] = '<a href="' . esc_url( get_category_link( $cat[0]->term_id ) ) . '" class="breadcrumb__link">' . esc_html( $cat[0]->name ) . '</a>';
        }
        $items[] = '<span class="breadcrumb__current" aria-current="page">' . esc_html( get_the_title() ) . '</span>';
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
