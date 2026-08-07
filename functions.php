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
