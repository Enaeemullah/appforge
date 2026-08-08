<?php
/**
 * AppForge Pro — Admin Panel
 * Settings panel similar to Appyn/ApkPure-style theme options.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class AppForge_Panel {

    const OPT = 'appforge_options';

    private static $defaults = array(
        // General
        'social_facebook'    => '',
        'social_twitter'     => '',
        'social_youtube'     => '',
        'social_telegram'    => '',
        'social_instagram'   => '',
        'social_whatsapp'    => '',
        'header_code'        => '',
        'comments_type'      => 'wordpress',

        // Home
        'home_posts_per_page' => '12',
        'home_order'          => 'date',
        'home_hide_posts'     => '0',
        'home_blog_count'     => '6',
        'home_hide_blog'      => '0',

        // Single
        'single_read_more'    => 'collapsed',
        'single_dl_type'      => 'normal',
        'single_dl_timer'     => '5',
        'single_hide_social'  => '0',
        'single_show_telegram'=> '0',
        'single_telegram_url' => '',
        'single_app_info'     => array(
            'developer', 'released', 'updated', 'size', 'version', 'requirements', 'downloads', 'google_play',
        ),

        // Blog
        'blog_home_count'     => '6',
        'blog_page_count'     => '10',
        'blog_sidebar'        => '0',

        // Colors
        'color_primary'       => '#00C853',
        'color_download_btn'  => '#00C853',
        'color_style'         => 'light',

        // Sidebar
        'sidebar_active'      => '1',
        'sidebar_position'    => 'right',

        // Footer
        'footer_about'        => 'Your trusted source for safe, fast, and free APK downloads. All apps are verified and tested.',
        'footer_copyright'    => '',
        'footer_show_credit'  => '1',
    );

    // ----------------------------------------------------------------
    // Public API — read a setting anywhere in the theme
    // ----------------------------------------------------------------
    public static function get( $key, $fallback = null ) {
        static $opts = null;
        if ( $opts === null ) {
            $opts = (array) get_option( self::OPT, array() );
        }
        if ( isset( $opts[ $key ] ) ) return $opts[ $key ];
        if ( isset( self::$defaults[ $key ] ) ) return self::$defaults[ $key ];
        return $fallback;
    }

    // ----------------------------------------------------------------
    // Bootstrap
    // ----------------------------------------------------------------
    public function __construct() {
        add_action( 'admin_menu',            array( $this, 'add_menu' ) );
        add_action( 'admin_post_af_save',    array( $this, 'save' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
        add_action( 'wp_head',               array( $this, 'head_output' ), 5 );

        // Download page detection
        add_filter( 'template_include', array( $this, 'download_template' ) );

        // "Disable comments" option — gate every comments_open() check theme-wide
        add_filter( 'comments_open', array( $this, 'filter_comments_open' ), 20 );
    }

    public function filter_comments_open( $open ) {
        if ( self::get( 'comments_type' ) === 'disabled' ) return false;
        return $open;
    }

    // ----------------------------------------------------------------
    // Admin menu
    // ----------------------------------------------------------------
    public function add_menu() {
        add_menu_page(
            'AppForge',
            'AppForge',
            'manage_options',
            'appforge_panel',
            array( $this, 'render' ),
            'dashicons-smartphone',
            59
        );
    }

    // ----------------------------------------------------------------
    // Admin scripts / styles
    // ----------------------------------------------------------------
    public function scripts( $hook ) {
        if ( $hook !== 'toplevel_page_appforge_panel' ) return;
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_add_inline_style( 'wp-color-picker', $this->panel_css() );
        wp_add_inline_script( 'wp-color-picker', 'jQuery(function($){ $(".af-color-picker").wpColorPicker(); });' );
        wp_enqueue_media();
    }

    private function panel_css() {
        return '
        #wpcontent { padding-left:0; }
        .af-wrap { display:flex; min-height:100vh; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif; }
        .af-sidebar { width:220px; background:#1e1e2d; flex-shrink:0; padding:0; }
        .af-sidebar-head { padding:20px 16px 12px; border-bottom:1px solid rgba(255,255,255,.08); }
        .af-sidebar-head h2 { color:#fff; font-size:16px; font-weight:700; margin:0; display:flex; align-items:center; gap:8px; }
        .af-sidebar-head small { color:#888; font-size:11px; display:block; margin-top:3px; }
        .af-nav { list-style:none; margin:0; padding:8px 0; }
        .af-nav li a { display:flex; align-items:center; gap:8px; padding:11px 16px; color:#bbb; font-size:13px; text-decoration:none; transition:background .15s,color .15s; border-left:3px solid transparent; }
        .af-nav li a:hover { background:rgba(255,255,255,.06); color:#fff; }
        .af-nav li a.active { background:rgba(0,200,83,.12); color:#00C853; border-left-color:#00C853; font-weight:600; }
        .af-nav li a .dashicons { font-size:16px; width:18px; }
        .af-content { flex:1; background:#f1f1f1; }
        .af-content-inner { max-width:820px; padding:28px 32px; }
        .af-section { display:none; }
        .af-section.active { display:block; }
        .af-section-title { font-size:22px; font-weight:700; color:#1e1e2d; margin:0 0 24px; padding-bottom:12px; border-bottom:2px solid #00C853; display:flex; align-items:center; gap:10px; }
        .af-section-title .dashicons { color:#00C853; }
        .af-card { background:#fff; border-radius:8px; padding:0; margin-bottom:1px; border:1px solid #e5e7eb; overflow:hidden; }
        .af-row { display:flex; align-items:flex-start; padding:18px 20px; border-bottom:1px solid #f3f4f6; gap:24px; }
        .af-row:last-child { border-bottom:none; }
        .af-row-label { width:220px; flex-shrink:0; }
        .af-row-label strong { display:block; font-size:13px; color:#111; font-weight:600; margin-bottom:3px; }
        .af-row-label small { font-size:12px; color:#888; line-height:1.4; }
        .af-row-control { flex:1; }
        .af-row-control input[type=text],
        .af-row-control input[type=url],
        .af-row-control input[type=number],
        .af-row-control select,
        .af-row-control textarea { width:100%; max-width:480px; padding:8px 12px; border:1px solid #ddd; border-radius:5px; font-size:13px; box-sizing:border-box; }
        .af-row-control input[type=text]:focus,
        .af-row-control input[type=url]:focus,
        .af-row-control input[type=number]:focus,
        .af-row-control select:focus,
        .af-row-control textarea:focus { border-color:#00C853; outline:none; box-shadow:0 0 0 2px rgba(0,200,83,.15); }
        .af-row-control textarea { min-height:100px; resize:vertical; }
        .af-row-control input[type=number] { max-width:120px; }
        .af-toggle { position:relative; display:inline-block; width:44px; height:24px; flex-shrink:0; }
        .af-toggle input { opacity:0; width:0; height:0; }
        .af-toggle-slider { position:absolute; cursor:pointer; inset:0; background:#ccc; border-radius:24px; transition:.25s; }
        .af-toggle-slider:before { content:""; position:absolute; width:18px; height:18px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:.25s; }
        .af-toggle input:checked + .af-toggle-slider { background:#00C853; }
        .af-toggle input:checked + .af-toggle-slider:before { transform:translateX(20px); }
        .af-radio-group { display:flex; flex-direction:column; gap:8px; }
        .af-radio-group label { display:flex; align-items:center; gap:8px; font-size:13px; color:#333; cursor:pointer; }
        .af-checkbox-group { display:flex; flex-wrap:wrap; gap:8px; }
        .af-checkbox-group label { display:flex; align-items:center; gap:6px; font-size:13px; background:#f8f8f8; border:1px solid #e5e7eb; border-radius:5px; padding:6px 12px; cursor:pointer; }
        .af-checkbox-group label:hover { border-color:#00C853; background:#f0fff6; }
        .af-save-btn { background:#00C853; color:#fff; border:none; padding:11px 28px; font-size:14px; font-weight:700; border-radius:6px; cursor:pointer; transition:background .2s; margin-top:20px; }
        .af-save-btn:hover { background:#009624; }
        .af-notice { background:#d4edda; border:1px solid #c3e6cb; color:#155724; padding:10px 16px; border-radius:5px; margin-bottom:18px; font-size:13px; }
        .af-social-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; max-width:480px; }
        .af-social-item label { display:block; font-size:12px; color:#666; font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:.4px; }
        .af-color-row { display:flex; align-items:center; gap:12px; }
        ';
    }

    // ----------------------------------------------------------------
    // Save handler
    // ----------------------------------------------------------------
    public function save() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'af_save_settings' );

        $opts = (array) get_option( self::OPT, array() );
        $sec  = sanitize_key( $_POST['_section'] ?? 'general' );

        switch ( $sec ) {
            case 'general':
                $opts['social_facebook']  = esc_url_raw( $_POST['social_facebook']  ?? '' );
                $opts['social_twitter']   = esc_url_raw( $_POST['social_twitter']   ?? '' );
                $opts['social_youtube']   = esc_url_raw( $_POST['social_youtube']   ?? '' );
                $opts['social_telegram']  = esc_url_raw( $_POST['social_telegram']  ?? '' );
                $opts['social_instagram'] = esc_url_raw( $_POST['social_instagram'] ?? '' );
                $opts['social_whatsapp']  = esc_url_raw( $_POST['social_whatsapp']  ?? '' );
                $opts['comments_type']    = sanitize_key( $_POST['comments_type']   ?? 'wordpress' );
                $opts['header_code']      = wp_kses_post( $_POST['header_code']     ?? '' );
                break;

            case 'home':
                $opts['home_posts_per_page'] = absint( $_POST['home_posts_per_page'] ?? 12 );
                $opts['home_order']          = sanitize_key( $_POST['home_order']   ?? 'date' );
                $opts['home_hide_posts']     = isset( $_POST['home_hide_posts'] )    ? '1' : '0';
                $opts['home_blog_count']     = absint( $_POST['home_blog_count']     ?? 6 );
                $opts['home_hide_blog']      = isset( $_POST['home_hide_blog'] )     ? '1' : '0';
                break;

            case 'single':
                $opts['single_read_more']     = sanitize_key( $_POST['single_read_more']  ?? 'collapsed' );
                $opts['single_dl_type']       = sanitize_key( $_POST['single_dl_type']    ?? 'normal' );
                $opts['single_dl_timer']      = absint( $_POST['single_dl_timer']         ?? 5 );
                $opts['single_hide_social']   = isset( $_POST['single_hide_social'] )     ? '1' : '0';
                $opts['single_show_telegram'] = isset( $_POST['single_show_telegram'] )   ? '1' : '0';
                $opts['single_telegram_url']  = esc_url_raw( $_POST['single_telegram_url'] ?? '' );
                $info = isset( $_POST['single_app_info'] ) && is_array( $_POST['single_app_info'] )
                    ? array_map( 'sanitize_key', $_POST['single_app_info'] )
                    : array();
                $opts['single_app_info'] = $info;
                break;

            case 'blog':
                $opts['blog_home_count']  = absint( $_POST['blog_home_count']  ?? 6 );
                $opts['blog_page_count']  = absint( $_POST['blog_page_count']  ?? 10 );
                $opts['blog_sidebar']     = isset( $_POST['blog_sidebar'] )    ? '1' : '0';
                break;

            case 'colors':
                $opts['color_primary']      = sanitize_hex_color( $_POST['color_primary']      ?? '#00C853' ) ?: '#00C853';
                $opts['color_download_btn'] = sanitize_hex_color( $_POST['color_download_btn'] ?? '#00C853' ) ?: '#00C853';
                $opts['color_style']        = sanitize_key( $_POST['color_style']              ?? 'light' );
                break;

            case 'sidebar':
                $opts['sidebar_active']   = isset( $_POST['sidebar_active'] )  ? '1' : '0';
                $opts['sidebar_position'] = sanitize_key( $_POST['sidebar_position'] ?? 'right' );
                break;

            case 'footer':
                $opts['footer_about']       = sanitize_textarea_field( $_POST['footer_about']       ?? '' );
                $opts['footer_copyright']   = sanitize_text_field( $_POST['footer_copyright']       ?? '' );
                $opts['footer_show_credit'] = isset( $_POST['footer_show_credit'] ) ? '1' : '0';
                break;
        }

        update_option( self::OPT, $opts );
        wp_safe_redirect( admin_url( 'admin.php?page=appforge_panel&saved=1&section=' . $sec ) );
        exit;
    }

    // ----------------------------------------------------------------
    // Frontend: inject dynamic CSS and header code
    // ----------------------------------------------------------------
    public function head_output() {
        $primary  = self::get( 'color_primary',      '#00C853' );
        $dl_color = self::get( 'color_download_btn', '#00C853' );
        $code     = self::get( 'header_code', '' );

        $css = '';

        if ( $primary !== '#00C853' ) {
            $css .= "
            :root { --af-primary: {$primary}; }
            .btn-dl-apk, .app-cat-badge, .apk-section-header__link:hover,
            .apk-tag:hover, .social-btn:hover, .back-to-top, .app-section__hd,
            .sw-cat-icon, .app-read-more { background: {$primary} !important; }
            .app-cat-badge, .apk-section-header__title::before,
            .apk-widget__header::before, .footer-credit a,
            .nav-menu > li > a:hover, .nav-menu > .current-menu-item > a { color: {$primary} !important; }
            .apk-section-header__link { border-color: rgba(0,0,0,.15) !important; color: {$primary} !important; }
            ";
        }

        if ( $dl_color !== '#00C853' && $dl_color !== $primary ) {
            $css .= ".btn-dl-apk { background: {$dl_color} !important; }";
        }

        if ( $css ) {
            echo '<style id="af-dynamic-css">' . $css . '</style>' . "\n";
        }

        if ( $code ) {
            echo $code . "\n";
        }
    }

    // ----------------------------------------------------------------
    // Download timer template
    // ----------------------------------------------------------------
    public function download_template( $template ) {
        if ( is_singular( 'app' )
            && isset( $_GET['download'] )
            && $_GET['download'] === 'links'
            && self::get( 'single_dl_type' ) !== 'normal'
        ) {
            $t = get_template_directory() . '/template-download.php';
            if ( file_exists( $t ) ) return $t;
        }
        return $template;
    }

    // ----------------------------------------------------------------
    // Render admin panel
    // ----------------------------------------------------------------
    public function render() {
        $sec   = sanitize_key( $_GET['section'] ?? 'general' );
        $saved = isset( $_GET['saved'] );

        $nav = array(
            'general' => array( 'label' => 'General Options', 'icon' => 'dashicons-admin-settings' ),
            'home'    => array( 'label' => 'Home',            'icon' => 'dashicons-admin-home' ),
            'single'  => array( 'label' => 'Single',          'icon' => 'dashicons-smartphone' ),
            'blog'    => array( 'label' => 'Blog',            'icon' => 'dashicons-admin-post' ),
            'colors'  => array( 'label' => 'Colors',          'icon' => 'dashicons-art' ),
            'sidebar' => array( 'label' => 'Sidebar',         'icon' => 'dashicons-layout' ),
            'footer'  => array( 'label' => 'Footer',          'icon' => 'dashicons-admin-links' ),
            'license' => array( 'label' => 'License',         'icon' => 'dashicons-admin-network' ),
        );
        ?>
        <div class="af-wrap">

            <!-- Sidebar nav -->
            <div class="af-sidebar">
                <div class="af-sidebar-head">
                    <h2><span class="dashicons dashicons-smartphone"></span> AppForge</h2>
                    <small>Version 4.0</small>
                </div>
                <ul class="af-nav">
                    <?php foreach ( $nav as $key => $item ) : ?>
                    <li>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=appforge_panel&section=' . $key ) ); ?>"
                           class="<?php echo $sec === $key ? 'active' : ''; ?>">
                            <span class="dashicons <?php echo esc_attr( $item['icon'] ); ?>"></span>
                            <?php echo esc_html( $item['label'] ); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Content area -->
            <div class="af-content">
                <div class="af-content-inner">

                    <?php if ( $saved ) : ?>
                    <div class="af-notice">✓ Settings saved successfully.</div>
                    <?php endif; ?>

                    <?php if ( $sec === 'license' ) : ?>

                        <?php AppForge_License::render_section(); ?>

                    <?php else : ?>

                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                        <?php wp_nonce_field( 'af_save_settings' ); ?>
                        <input type="hidden" name="action" value="af_save">
                        <input type="hidden" name="_section" value="<?php echo esc_attr( $sec ); ?>">

                        <?php
                        switch ( $sec ) {
                            case 'general': $this->section_general(); break;
                            case 'home':    $this->section_home();    break;
                            case 'single':  $this->section_single();  break;
                            case 'blog':    $this->section_blog();    break;
                            case 'colors':  $this->section_colors();  break;
                            case 'sidebar': $this->section_sidebar(); break;
                            case 'footer':  $this->section_footer();  break;
                        }
                        ?>

                        <button type="submit" class="af-save-btn">Save changes</button>
                    </form>

                    <?php endif; ?>

                </div>
            </div>

        </div>
        <?php
    }

    // ----------------------------------------------------------------
    // Sections
    // ----------------------------------------------------------------

    private function section_general() {
        ?>
        <div class="af-section-title"><span class="dashicons dashicons-admin-settings"></span> General Options</div>

        <div class="af-card">
            <div class="af-row">
                <div class="af-row-label">
                    <strong>Social Networks</strong>
                    <small>Links displayed in the footer.</small>
                </div>
                <div class="af-row-control">
                    <div class="af-social-grid">
                        <?php
                        $socials = array(
                            'social_facebook'  => 'Facebook',
                            'social_twitter'   => 'Twitter / X',
                            'social_youtube'   => 'YouTube',
                            'social_telegram'  => 'Telegram',
                            'social_instagram' => 'Instagram',
                            'social_whatsapp'  => 'WhatsApp',
                        );
                        foreach ( $socials as $key => $label ) : ?>
                        <div class="af-social-item">
                            <label><?php echo esc_html( $label ); ?></label>
                            <input type="url" name="<?php echo esc_attr( $key ); ?>"
                                   value="<?php echo esc_attr( self::get( $key ) ); ?>"
                                   placeholder="https://...">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="af-row">
                <div class="af-row-label">
                    <strong>Comments</strong>
                    <small>Comment system to use on app pages.</small>
                </div>
                <div class="af-row-control">
                    <div class="af-radio-group">
                        <?php
                        $types = array( 'wordpress' => 'WordPress comments', 'disabled' => 'Disable comments' );
                        $cur   = self::get( 'comments_type' );
                        foreach ( $types as $val => $lab ) : ?>
                        <label>
                            <input type="radio" name="comments_type" value="<?php echo esc_attr( $val ); ?>"
                                   <?php checked( $cur, $val ); ?>>
                            <?php echo esc_html( $lab ); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="af-row">
                <div class="af-row-label">
                    <strong>Header codes</strong>
                    <small>Place code in &lt;head&gt; (e.g. Google Analytics, GTM).</small>
                </div>
                <div class="af-row-control">
                    <textarea name="header_code" placeholder="<!-- Google Analytics -->"><?php echo esc_textarea( self::get( 'header_code' ) ); ?></textarea>
                </div>
            </div>
        </div>
        <?php
    }

    private function section_home() {
        ?>
        <div class="af-section-title"><span class="dashicons dashicons-admin-home"></span> Home</div>

        <div class="af-card">
            <div class="af-row">
                <div class="af-row-label">
                    <strong>Posts per page</strong>
                    <small>Number of apps in each homepage section.</small>
                </div>
                <div class="af-row-control">
                    <input type="number" name="home_posts_per_page" min="1" max="50"
                           value="<?php echo esc_attr( self::get( 'home_posts_per_page', '12' ) ); ?>">
                </div>
            </div>

            <div class="af-row">
                <div class="af-row-label">
                    <strong>Order posts</strong>
                    <small>Sort homepage sections.</small>
                </div>
                <div class="af-row-control">
                    <div class="af-radio-group">
                        <?php
                        $orders = array( 'date' => 'By date (Default)', 'modified' => 'By last modified date', 'rand' => 'Random' );
                        $cur    = self::get( 'home_order' );
                        foreach ( $orders as $val => $lab ) : ?>
                        <label>
                            <input type="radio" name="home_order" value="<?php echo esc_attr( $val ); ?>"
                                   <?php checked( $cur, $val ); ?>>
                            <?php echo esc_html( $lab ); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="af-row">
                <div class="af-row-label">
                    <strong>Hide Apps sections</strong>
                    <small>Hide Discover, Popular, Hot, Latest sections.</small>
                </div>
                <div class="af-row-control">
                    <label class="af-toggle">
                        <input type="checkbox" name="home_hide_posts" value="1"
                               <?php checked( self::get( 'home_hide_posts' ), '1' ); ?>>
                        <span class="af-toggle-slider"></span>
                    </label>
                </div>
            </div>

            <div class="af-row">
                <div class="af-row-label">
                    <strong>Blog Section on Home</strong>
                    <small>Number of blog posts to show.</small>
                </div>
                <div class="af-row-control">
                    <input type="number" name="blog_home_count" min="0" max="20"
                           value="<?php echo esc_attr( self::get( 'blog_home_count', '6' ) ); ?>">
                </div>
            </div>

            <div class="af-row">
                <div class="af-row-label">
                    <strong>Hide Blog section</strong>
                    <small>Remove the Latest Posts section from home.</small>
                </div>
                <div class="af-row-control">
                    <label class="af-toggle">
                        <input type="checkbox" name="home_hide_blog" value="1"
                               <?php checked( self::get( 'home_hide_blog' ), '1' ); ?>>
                        <span class="af-toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>
        <?php
    }

    private function section_single() {
        $app_info_fields = array(
            'developer'    => 'Developer',
            'released'     => 'Released on',
            'updated'      => 'Updated',
            'size'         => 'Size',
            'version'      => 'Version',
            'requirements' => 'Requirements',
            'downloads'    => 'Downloads',
            'google_play'  => 'Get it on',
        );
        $cur_info = (array) self::get( 'single_app_info', array_keys( $app_info_fields ) );
        ?>
        <div class="af-section-title"><span class="dashicons dashicons-smartphone"></span> Single</div>

        <div class="af-card">

            <div class="af-row">
                <div class="af-row-label">
                    <strong>Read more</strong>
                    <small>Show all content or collapse with a "Read more" button.</small>
                </div>
                <div class="af-row-control">
                    <div class="af-radio-group">
                        <label><input type="radio" name="single_read_more" value="collapsed"
                                      <?php checked( self::get( 'single_read_more' ), 'collapsed' ); ?>>
                            Read more (default)</label>
                        <label><input type="radio" name="single_read_more" value="all"
                                      <?php checked( self::get( 'single_read_more' ), 'all' ); ?>>
                            Show all</label>
                    </div>
                </div>
            </div>

            <div class="af-row">
                <div class="af-row-label">
                    <strong>Download links</strong>
                    <small>How the download button works.</small>
                </div>
                <div class="af-row-control">
                    <div class="af-radio-group">
                        <label><input type="radio" name="single_dl_type" value="normal"
                                      <?php checked( self::get( 'single_dl_type' ), 'normal' ); ?>>
                            Normal (direct link)</label>
                        <label><input type="radio" name="single_dl_type" value="timer"
                                      <?php checked( self::get( 'single_dl_type' ), 'timer' ); ?>>
                            Internal page with countdown timer</label>
                    </div>
                </div>
            </div>

            <div class="af-row">
                <div class="af-row-label">
                    <strong>Download timer</strong>
                    <small>Countdown seconds before download starts.</small>
                </div>
                <div class="af-row-control">
                    <input type="number" name="single_dl_timer" min="1" max="60"
                           value="<?php echo esc_attr( self::get( 'single_dl_timer', '5' ) ); ?>"> seconds
                </div>
            </div>

            <div class="af-row">
                <div class="af-row-label">
                    <strong>Hide social share buttons</strong>
                    <small>Hide Facebook, Twitter etc. share row.</small>
                </div>
                <div class="af-row-control">
                    <label class="af-toggle">
                        <input type="checkbox" name="single_hide_social" value="1"
                               <?php checked( self::get( 'single_hide_social' ), '1' ); ?>>
                        <span class="af-toggle-slider"></span>
                    </label>
                </div>
            </div>

            <div class="af-row">
                <div class="af-row-label">
                    <strong>Telegram button</strong>
                    <small>Show a global Telegram group button on all app pages.</small>
                </div>
                <div class="af-row-control">
                    <label class="af-toggle" style="margin-bottom:10px;">
                        <input type="checkbox" name="single_show_telegram" value="1"
                               <?php checked( self::get( 'single_show_telegram' ), '1' ); ?>>
                        <span class="af-toggle-slider"></span>
                    </label>
                    <input type="url" name="single_telegram_url"
                           value="<?php echo esc_attr( self::get( 'single_telegram_url' ) ); ?>"
                           placeholder="https://t.me/your_group" style="margin-top:8px;">
                    <small style="display:block;color:#888;margin-top:4px;">Leave URL empty to use per-app Telegram URL from App Details.</small>
                </div>
            </div>

            <div class="af-row">
                <div class="af-row-label">
                    <strong>App Information</strong>
                    <small>Choose which meta fields to display.</small>
                </div>
                <div class="af-row-control">
                    <div class="af-checkbox-group">
                        <?php foreach ( $app_info_fields as $val => $lab ) : ?>
                        <label>
                            <input type="checkbox" name="single_app_info[]"
                                   value="<?php echo esc_attr( $val ); ?>"
                                   <?php checked( in_array( $val, $cur_info, true ), true ); ?>>
                            <?php echo esc_html( $lab ); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
        <?php
    }

    private function section_blog() {
        ?>
        <div class="af-section-title"><span class="dashicons dashicons-admin-post"></span> Blog</div>
        <div class="af-card">
            <div class="af-row">
                <div class="af-row-label">
                    <strong>Blog Section on Home</strong>
                    <small>Number of posts shown on the homepage.</small>
                </div>
                <div class="af-row-control">
                    <input type="number" name="blog_home_count" min="0" max="20"
                           value="<?php echo esc_attr( self::get( 'blog_home_count', '6' ) ); ?>"> Posts
                </div>
            </div>
            <div class="af-row">
                <div class="af-row-label">
                    <strong>Blog Page</strong>
                    <small>Posts per page on the blog archive.</small>
                </div>
                <div class="af-row-control">
                    <input type="number" name="blog_page_count" min="1" max="50"
                           value="<?php echo esc_attr( self::get( 'blog_page_count', '10' ) ); ?>"> Posts
                </div>
            </div>
            <div class="af-row">
                <div class="af-row-label">
                    <strong>Blog Sidebar</strong>
                    <small>Show sidebar on blog post pages.</small>
                </div>
                <div class="af-row-control">
                    <label class="af-toggle">
                        <input type="checkbox" name="blog_sidebar" value="1"
                               <?php checked( self::get( 'blog_sidebar' ), '1' ); ?>>
                        <span class="af-toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>
        <?php
    }

    private function section_colors() {
        ?>
        <div class="af-section-title"><span class="dashicons dashicons-art"></span> Colors</div>
        <div class="af-card">
            <div class="af-row">
                <div class="af-row-label">
                    <strong>Color Style</strong>
                    <small>Overall theme color tone.</small>
                </div>
                <div class="af-row-control">
                    <div class="af-radio-group">
                        <?php
                        $styles = array( 'light' => 'Light', 'dark' => 'Dark' );
                        $cur = self::get( 'color_style', 'light' );
                        foreach ( $styles as $val => $lab ) : ?>
                        <label>
                            <input type="radio" name="color_style" value="<?php echo esc_attr( $val ); ?>"
                                   <?php checked( $cur, $val ); ?>>
                            <?php echo esc_html( $lab ); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="af-row">
                <div class="af-row-label">
                    <strong>Main color</strong>
                    <small>Primary accent color used throughout the theme.</small>
                </div>
                <div class="af-row-control">
                    <div class="af-color-row">
                        <input type="text" name="color_primary" class="af-color-picker"
                               value="<?php echo esc_attr( self::get( 'color_primary', '#00C853' ) ); ?>">
                    </div>
                </div>
            </div>
            <div class="af-row">
                <div class="af-row-label">
                    <strong>Download button color</strong>
                    <small>Background color of the Download APK button.</small>
                </div>
                <div class="af-row-control">
                    <div class="af-color-row">
                        <input type="text" name="color_download_btn" class="af-color-picker"
                               value="<?php echo esc_attr( self::get( 'color_download_btn', '#00C853' ) ); ?>">
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    private function section_sidebar() {
        ?>
        <div class="af-section-title"><span class="dashicons dashicons-layout"></span> Sidebar</div>
        <div class="af-card">
            <div class="af-row">
                <div class="af-row-label">
                    <strong>Active</strong>
                    <small>Show the sidebar on app pages.</small>
                </div>
                <div class="af-row-control">
                    <label class="af-toggle">
                        <input type="checkbox" name="sidebar_active" value="1"
                               <?php checked( self::get( 'sidebar_active', '1' ), '1' ); ?>>
                        <span class="af-toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="af-row">
                <div class="af-row-label">
                    <strong>Sidebar position</strong>
                </div>
                <div class="af-row-control">
                    <div class="af-radio-group">
                        <label><input type="radio" name="sidebar_position" value="right"
                                      <?php checked( self::get( 'sidebar_position', 'right' ), 'right' ); ?>> Right</label>
                        <label><input type="radio" name="sidebar_position" value="left"
                                      <?php checked( self::get( 'sidebar_position', 'right' ), 'left' ); ?>> Left</label>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    private function section_footer() {
        ?>
        <div class="af-section-title"><span class="dashicons dashicons-admin-links"></span> Footer</div>
        <div class="af-card">
            <div class="af-row">
                <div class="af-row-label">
                    <strong>About text</strong>
                    <small>Footer brand description.</small>
                </div>
                <div class="af-row-control">
                    <textarea name="footer_about"><?php echo esc_textarea( self::get( 'footer_about' ) ); ?></textarea>
                </div>
            </div>
            <div class="af-row">
                <div class="af-row-label">
                    <strong>Copyright text</strong>
                    <small>Shown in the bottom bar. Leave blank for auto.</small>
                </div>
                <div class="af-row-control">
                    <input type="text" name="footer_copyright"
                           value="<?php echo esc_attr( self::get( 'footer_copyright' ) ); ?>"
                           placeholder="<?php echo esc_attr( get_bloginfo( 'name' ) . ' © ' . gmdate( 'Y' ) ); ?>">
                </div>
            </div>
            <div class="af-row">
                <div class="af-row-label">
                    <strong>Show designer credit</strong>
                    <small>"Designed by Naeem Ullah" in footer.</small>
                </div>
                <div class="af-row-control">
                    <label class="af-toggle">
                        <input type="checkbox" name="footer_show_credit" value="1"
                               <?php checked( self::get( 'footer_show_credit', '1' ), '1' ); ?>>
                        <span class="af-toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>
        <?php
    }
}

// Boot
new AppForge_Panel();
