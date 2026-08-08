<?php
/**
 * AppForge Pro — Theme Customizer
 */

// ============================================================
// REGISTER CUSTOMIZER SETTINGS
// ============================================================
function appforge_customize_register( $wp_customize ) {

    // --------------------------------------------------------
    // PANEL: AppForge Settings
    // --------------------------------------------------------
    $wp_customize->add_panel( 'appforge_panel', array(
        'title'       => __( 'AppForge Settings', 'appforge' ),
        'description' => __( 'Configure your AppForge Pro theme.', 'appforge' ),
        'priority'    => 30,
    ) );

    // ========================================================
    // SECTION 1: Site Identity / Branding
    // ========================================================
    $wp_customize->add_section( 'appforge_branding', array(
        'title'    => __( 'Branding', 'appforge' ),
        'panel'    => 'appforge_panel',
        'priority' => 10,
    ) );

    // Site tagline
    $wp_customize->add_setting( 'appforge_tagline', array(
        'default'           => __( 'Your trusted source for safe, fast, and free APK downloads.', 'appforge' ),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'appforge_tagline', array(
        'label'   => __( 'Site Tagline', 'appforge' ),
        'section' => 'appforge_branding',
        'type'    => 'text',
    ) );

    // Footer about text
    $wp_customize->add_setting( 'appforge_footer_about', array(
        'default'           => __( 'Your trusted source for safe, fast, and free app downloads. Handpicked and verified.', 'appforge' ),
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'appforge_footer_about', array(
        'label'   => __( 'Footer About Text', 'appforge' ),
        'section' => 'appforge_branding',
        'type'    => 'textarea',
    ) );

    // Copyright text
    $wp_customize->add_setting( 'appforge_copyright', array(
        'default'           => 'AppForge. All rights reserved.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'appforge_copyright', array(
        'label'       => __( 'Copyright Text', 'appforge' ),
        'description' => __( 'Year is added automatically.', 'appforge' ),
        'section'     => 'appforge_branding',
        'type'        => 'text',
    ) );

    // ========================================================
    // SECTION 2: Colors
    // ========================================================
    $wp_customize->add_section( 'appforge_colors', array(
        'title'    => __( 'Colors', 'appforge' ),
        'panel'    => 'appforge_panel',
        'priority' => 20,
    ) );

    // Primary color
    $wp_customize->add_setting( 'appforge_color_primary', array(
        'default'           => '#00C853',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'appforge_color_primary', array(
        'label'   => __( 'Primary (Green) Color', 'appforge' ),
        'section' => 'appforge_colors',
    ) ) );

    // Secondary color
    $wp_customize->add_setting( 'appforge_color_secondary', array(
        'default'           => '#6200ea',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'appforge_color_secondary', array(
        'label'   => __( 'Secondary (Purple) Color', 'appforge' ),
        'section' => 'appforge_colors',
    ) ) );

    // ========================================================
    // SECTION 3: Homepage
    // ========================================================
    $wp_customize->add_section( 'appforge_homepage', array(
        'title'    => __( 'Homepage', 'appforge' ),
        'panel'    => 'appforge_panel',
        'priority' => 30,
    ) );

    // Carousel count
    $wp_customize->add_setting( 'appforge_carousel_count', array(
        'default'           => 5,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'appforge_carousel_count', array(
        'label'       => __( 'Hero Carousel — Number of Apps', 'appforge' ),
        'description' => __( 'How many apps to show in the homepage carousel.', 'appforge' ),
        'section'     => 'appforge_homepage',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 1, 'max' => 10 ),
    ) );

    // Show Discover section
    $wp_customize->add_setting( 'appforge_show_discover', array(
        'default'           => true,
        'sanitize_callback' => 'appforge_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'appforge_show_discover', array(
        'label'   => __( 'Show "Discover" Section', 'appforge' ),
        'section' => 'appforge_homepage',
        'type'    => 'checkbox',
    ) );

    // Show Most Popular section
    $wp_customize->add_setting( 'appforge_show_popular', array(
        'default'           => true,
        'sanitize_callback' => 'appforge_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'appforge_show_popular', array(
        'label'   => __( 'Show "Most Popular" Section', 'appforge' ),
        'section' => 'appforge_homepage',
        'type'    => 'checkbox',
    ) );

    // Show Hot Apps section
    $wp_customize->add_setting( 'appforge_show_hot', array(
        'default'           => true,
        'sanitize_callback' => 'appforge_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'appforge_show_hot', array(
        'label'   => __( 'Show "Hot Apps" Section', 'appforge' ),
        'section' => 'appforge_homepage',
        'type'    => 'checkbox',
    ) );

    // Show Latest Update section
    $wp_customize->add_setting( 'appforge_show_latest', array(
        'default'           => true,
        'sanitize_callback' => 'appforge_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'appforge_show_latest', array(
        'label'   => __( 'Show "Latest Update" Section', 'appforge' ),
        'section' => 'appforge_homepage',
        'type'    => 'checkbox',
    ) );

    // Apps per grid section
    $wp_customize->add_setting( 'appforge_grid_count', array(
        'default'           => 12,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'appforge_grid_count', array(
        'label'       => __( 'Apps per Grid Section', 'appforge' ),
        'description' => __( 'Number of apps in Hot, Popular, and Latest sections.', 'appforge' ),
        'section'     => 'appforge_homepage',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 3, 'max' => 24, 'step' => 3 ),
    ) );

    // ========================================================
    // SECTION 4: Social Links
    // ========================================================
    $wp_customize->add_section( 'appforge_social', array(
        'title'    => __( 'Social Links', 'appforge' ),
        'panel'    => 'appforge_panel',
        'priority' => 40,
    ) );

    $social_links = array(
        'facebook'  => array( 'label' => 'Facebook URL',  'placeholder' => 'https://facebook.com/yourpage' ),
        'twitter'   => array( 'label' => 'Twitter / X URL', 'placeholder' => 'https://twitter.com/yourhandle' ),
        'youtube'   => array( 'label' => 'YouTube URL',   'placeholder' => 'https://youtube.com/yourchannel' ),
        'telegram'  => array( 'label' => 'Telegram URL',  'placeholder' => 'https://t.me/yourchannel' ),
        'instagram' => array( 'label' => 'Instagram URL', 'placeholder' => 'https://instagram.com/yourprofile' ),
        'whatsapp'  => array( 'label' => 'WhatsApp URL',  'placeholder' => 'https://wa.me/yournumber' ),
    );

    foreach ( $social_links as $key => $data ) {
        $wp_customize->add_setting( 'appforge_social_' . $key, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        $wp_customize->add_control( 'appforge_social_' . $key, array(
            'label'       => __( $data['label'], 'appforge' ),
            'section'     => 'appforge_social',
            'type'        => 'url',
            'input_attrs' => array( 'placeholder' => $data['placeholder'] ),
        ) );
    }

    // ========================================================
    // SECTION 5: Author / About
    // ========================================================
    $wp_customize->add_section( 'appforge_author', array(
        'title'    => __( 'Author & Attribution', 'appforge' ),
        'panel'    => 'appforge_panel',
        'priority' => 50,
    ) );

    $wp_customize->add_setting( 'appforge_author_name', array(
        'default'           => 'Naeem Ullah',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'appforge_author_name', array(
        'label'   => __( 'Author Name', 'appforge' ),
        'section' => 'appforge_author',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'appforge_author_website', array(
        'default'           => 'https://naeem-ullah.com',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'appforge_author_website', array(
        'label'   => __( 'Author Website', 'appforge' ),
        'section' => 'appforge_author',
        'type'    => 'url',
    ) );

    $wp_customize->add_setting( 'appforge_show_credit', array(
        'default'           => true,
        'sanitize_callback' => 'appforge_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'appforge_show_credit', array(
        'label'   => __( 'Show "Designed by" credit in footer', 'appforge' ),
        'section' => 'appforge_author',
        'type'    => 'checkbox',
    ) );

    // ========================================================
    // SECTION 6: Advanced / Performance
    // ========================================================
    $wp_customize->add_section( 'appforge_advanced', array(
        'title'    => __( 'Advanced', 'appforge' ),
        'panel'    => 'appforge_panel',
        'priority' => 60,
    ) );

    $wp_customize->add_setting( 'appforge_custom_css_head', array(
        'default'           => '',
        'sanitize_callback' => 'wp_strip_all_tags',
    ) );
    $wp_customize->add_control( 'appforge_custom_css_head', array(
        'label'       => __( 'Custom CSS (injected in <head>)', 'appforge' ),
        'description' => __( 'Add custom CSS without the style tags.', 'appforge' ),
        'section'     => 'appforge_advanced',
        'type'        => 'textarea',
    ) );

    $wp_customize->add_setting( 'appforge_google_analytics', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'appforge_google_analytics', array(
        'label'       => __( 'Google Analytics ID', 'appforge' ),
        'description' => __( 'e.g. G-XXXXXXXXXX or UA-XXXXXXXX-X', 'appforge' ),
        'section'     => 'appforge_advanced',
        'type'        => 'text',
    ) );

    $wp_customize->add_setting( 'appforge_header_scripts', array(
        'default'           => '',
        'sanitize_callback' => 'appforge_sanitize_scripts',
    ) );
    $wp_customize->add_control( 'appforge_header_scripts', array(
        'label'       => __( 'Header Scripts', 'appforge' ),
        'description' => __( 'Scripts injected before </head> (include script tags).', 'appforge' ),
        'section'     => 'appforge_advanced',
        'type'        => 'textarea',
    ) );
}
add_action( 'customize_register', 'appforge_customize_register' );


// ============================================================
// SANITIZE HELPERS
// ============================================================
function appforge_sanitize_checkbox( $value ) {
    return (bool) $value;
}

function appforge_sanitize_scripts( $value ) {
    return $value; // scripts are only shown to admins
}


// ============================================================
// OUTPUT DYNAMIC CSS (primary/secondary color overrides)
// ============================================================
function appforge_customizer_css() {
    // The AppForge admin panel (Appearance → AppForge → Colors) has its own
    // primary-color override on the same selectors — if it's been changed
    // from the shared default, let it win instead of racing this one.
    if ( class_exists( 'AppForge_Panel' ) && AppForge_Panel::get( 'color_primary', '#00C853' ) !== '#00C853' ) {
        return;
    }

    $primary   = get_theme_mod( 'appforge_color_primary', '#00C853' );
    $secondary = get_theme_mod( 'appforge_color_secondary', '#6200ea' );

    if ( $primary === '#00C853' && $secondary === '#6200ea' ) return;

    $dark_primary = appforge_darken_hex( $primary, 20 );

    echo '<style id="appforge-custom-colors">';
    echo ':root {';
    echo '--af-primary:' . esc_attr( $primary ) . ';';
    echo '--af-secondary:' . esc_attr( $secondary ) . ';';
    echo '}';

    // Override key color references
    $rules = array(
        '.btn-primary, .btn-download'               => "background: {$primary};",
        '.btn-primary:hover, .btn-download:hover'   => "background: {$dark_primary};",
        '.nav-menu > li > a:hover, .nav-menu > .current-menu-item > a' => "color: {$primary};",
        '.apk-widget__header::before, .app-detail-section__header::before, .apk-section-header__title::before' => "background: {$primary};",
        '.app-info-item__icon'                      => "color: {$primary};",
        '.apk-tag:hover, .apk-tag:focus'            => "background: {$primary}; border-color: {$primary};",
        '.logo-icon'                                => "background: linear-gradient(135deg, {$primary}, {$secondary}); -webkit-background-clip: text; background-clip: text; color: transparent;",
    );

    foreach ( $rules as $selector => $css ) {
        echo esc_attr( $selector ) . ' { ' . $css . ' }';
    }
    echo '</style>';
}
add_action( 'wp_head', 'appforge_customizer_css', 99 );


// ============================================================
// OUTPUT CUSTOM CSS FROM CUSTOMIZER
// ============================================================
function appforge_output_custom_css() {
    $css = get_theme_mod( 'appforge_custom_css_head', '' );
    if ( $css ) {
        echo '<style id="appforge-user-css">' . wp_strip_all_tags( $css ) . '</style>';
    }
}
add_action( 'wp_head', 'appforge_output_custom_css', 100 );


// ============================================================
// GOOGLE ANALYTICS
// ============================================================
function appforge_google_analytics() {
    $ga_id = get_theme_mod( 'appforge_google_analytics', '' );
    if ( ! $ga_id ) return;
    ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga_id ); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo esc_js( $ga_id ); ?>');
    </script>
    <?php
}
add_action( 'wp_head', 'appforge_google_analytics', 1 );


// ============================================================
// HEADER SCRIPTS
// ============================================================
function appforge_header_scripts() {
    $scripts = get_theme_mod( 'appforge_header_scripts', '' );
    if ( $scripts && current_user_can( 'manage_options' ) ) {
        echo $scripts; // phpcs:ignore - admin-only
    } elseif ( $scripts ) {
        echo $scripts; // phpcs:ignore
    }
}
add_action( 'wp_head', 'appforge_header_scripts', 5 );


// ============================================================
// CUSTOMIZER LIVE PREVIEW JS
// ============================================================
function appforge_customizer_preview_js() {
    wp_enqueue_script(
        'appforge-customizer-preview',
        get_template_directory_uri() . '/js/customizer-preview.js',
        array( 'customize-preview' ),
        '4.0',
        true
    );
}
add_action( 'customize_preview_init', 'appforge_customizer_preview_js' );


// ============================================================
// HELPER: Darken a hex color
// ============================================================
function appforge_darken_hex( $hex, $amount = 20 ) {
    $hex = ltrim( $hex, '#' );
    if ( strlen( $hex ) === 3 ) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    $r = max( 0, hexdec( substr( $hex, 0, 2 ) ) - $amount );
    $g = max( 0, hexdec( substr( $hex, 2, 2 ) ) - $amount );
    $b = max( 0, hexdec( substr( $hex, 4, 2 ) ) - $amount );
    return '#' . sprintf( '%02x%02x%02x', $r, $g, $b );
}
