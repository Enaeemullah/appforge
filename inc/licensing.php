<?php
/**
 * AppForge Pro — License Activation
 *
 * Talks to your own license server (self-hosted). Define the endpoint in
 * wp-config.php:
 *
 *     define( 'APPFORGE_LICENSE_API_URL', 'https://your-store.com/wp-json/appforge-license/v1' );
 *
 * Expected REST contract on your server:
 *
 *   POST {API_URL}/activate
 *     body: { license_key, site_url, product }
 *     200: { success: true, status: "valid", expires: "2027-08-08", message: "" }
 *     200: { success: false, status: "invalid|expired|limit_reached", message: "human readable reason" }
 *
 *   POST {API_URL}/deactivate
 *     body: { license_key, site_url, product }
 *     200: { success: true }
 *
 *   POST {API_URL}/check
 *     body: { license_key, site_url, product }
 *     200: { success: true, status: "valid", expires: "2027-08-08" }
 *     200: { success: false, status: "invalid|expired|revoked" }
 *
 * Everything below is the client side only — build the server to match
 * this contract (or adjust request() if your API differs).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class AppForge_License {

    const OPT          = 'appforge_license';
    const PRODUCT_SLUG  = 'appforge-pro';
    const CRON_HOOK     = 'appforge_license_daily_check';

    private static $defaults = array(
        'key'     => '',
        'status'  => 'inactive', // inactive | valid | invalid | expired | limit_reached | error
        'expires' => '',
        'message' => '',
        'checked' => 0,
    );

    public function __construct() {
        add_action( 'admin_post_af_license_activate',   array( $this, 'handle_activate' ) );
        add_action( 'admin_post_af_license_deactivate', array( $this, 'handle_deactivate' ) );
        add_action( self::CRON_HOOK,                    array( $this, 'handle_cron_check' ) );
        add_action( 'after_setup_theme',                array( $this, 'maybe_schedule_cron' ) );
        add_action( 'switch_theme',                     array( $this, 'unschedule_cron' ) );
        add_action( 'admin_notices',                     array( $this, 'admin_notices' ) );
        add_action( 'template_redirect',                 array( $this, 'maybe_block_frontend' ), 1 );
    }

    // ------------------------------------------------------------
    // Public API
    // ------------------------------------------------------------
    public static function get_data() {
        return wp_parse_args( (array) get_option( self::OPT, array() ), self::$defaults );
    }

    public static function is_active() {
        return self::get_data()['status'] === 'valid';
    }

    // ------------------------------------------------------------
    // Remote request helper
    // ------------------------------------------------------------
    private function api_url( $endpoint ) {
        if ( ! defined( 'APPFORGE_LICENSE_API_URL' ) || ! APPFORGE_LICENSE_API_URL ) {
            return '';
        }
        return trailingslashit( APPFORGE_LICENSE_API_URL ) . $endpoint;
    }

    private function request( $endpoint, $license_key ) {
        $url = $this->api_url( $endpoint );
        if ( ! $url ) {
            return new WP_Error( 'af_license_not_configured', 'License API URL is not configured. Define APPFORGE_LICENSE_API_URL in wp-config.php.' );
        }

        $response = wp_remote_post( $url, array(
            'timeout' => 15,
            'body'    => array(
                'license_key' => $license_key,
                'site_url'    => home_url(),
                'product'     => self::PRODUCT_SLUG,
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( ! is_array( $body ) ) {
            return new WP_Error( 'af_license_bad_response', 'Unexpected response from the license server.' );
        }

        return $body;
    }

    // ------------------------------------------------------------
    // Activate / deactivate (admin-post handlers)
    // ------------------------------------------------------------
    public function handle_activate() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'af_license_activate' );

        $key = isset( $_POST['license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) : '';

        if ( $key === '' ) {
            $this->redirect( array( 'af_license_error' => rawurlencode( 'Enter a license key.' ) ) );
        }

        $result = $this->request( 'activate', $key );
        $data   = self::$defaults;
        $data['key'] = $key;

        if ( is_wp_error( $result ) ) {
            $data['status']  = 'error';
            $data['message'] = $result->get_error_message();
        } else {
            $data['status']  = ! empty( $result['success'] ) ? ( $result['status'] ?? 'valid' ) : ( $result['status'] ?? 'invalid' );
            $data['expires'] = $result['expires'] ?? '';
            $data['message'] = $result['message'] ?? '';
        }

        $data['checked'] = time();
        update_option( self::OPT, $data );

        $this->redirect( $data['status'] === 'valid'
            ? array( 'af_license_ok' => 1 )
            : array( 'af_license_error' => rawurlencode( $data['message'] ?: 'License could not be activated.' ) )
        );
    }

    public function handle_deactivate() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'af_license_deactivate' );

        $data = self::get_data();
        if ( $data['key'] !== '' ) {
            $this->request( 'deactivate', $data['key'] ); // best-effort — free the seat on the server
        }

        delete_option( self::OPT );
        $this->redirect( array( 'af_license_deactivated' => 1 ) );
    }

    private function redirect( array $args ) {
        $args['page']    = 'appforge_panel';
        $args['section'] = 'license';
        wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
        exit;
    }

    // ------------------------------------------------------------
    // Daily re-validation
    // ------------------------------------------------------------
    public function maybe_schedule_cron() {
        if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
            wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', self::CRON_HOOK );
        }
    }

    public function unschedule_cron() {
        wp_clear_scheduled_hook( self::CRON_HOOK );
    }

    public function handle_cron_check() {
        $data = self::get_data();
        if ( $data['key'] === '' ) return;

        $result = $this->request( 'check', $data['key'] );
        if ( is_wp_error( $result ) ) return; // don't downgrade status on a transient network failure

        $data['status']  = ! empty( $result['success'] ) ? ( $result['status'] ?? $data['status'] ) : ( $result['status'] ?? 'invalid' );
        $data['expires'] = $result['expires'] ?? $data['expires'];
        $data['checked'] = time();
        update_option( self::OPT, $data );
    }

    // ------------------------------------------------------------
    // Admin notice — nudge to activate
    // ------------------------------------------------------------
    public function admin_notices() {
        if ( ! current_user_can( 'manage_options' ) ) return;

        $screen = get_current_screen();
        if ( $screen && $screen->id === 'toplevel_page_appforge_panel' ) return; // already on the settings screen

        if ( self::is_active() ) return;

        $url = admin_url( 'admin.php?page=appforge_panel&section=license' );
        echo '<div class="notice notice-warning"><p>';
        echo 'AppForge Pro is not activated. <a href="' . esc_url( $url ) . '">Enter your license key</a> to enable updates and premium features.';
        echo '</p></div>';
    }

    // ------------------------------------------------------------
    // Frontend lockout — visitors see a lock page until a valid
    // license is entered. Logged-in admins are exempt so they can
    // still manage the site while activating.
    // ------------------------------------------------------------
    public function maybe_block_frontend() {
        if ( self::is_active() ) return;
        if ( current_user_can( 'manage_options' ) ) return;
        if ( wp_doing_ajax() || wp_doing_cron() ) return;
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) return;

        $this->render_lockout();
    }

    private function render_lockout() {
        status_header( 503 );
        nocache_headers();
        header( 'Retry-After: 3600' );

        $login_url = wp_login_url( admin_url( 'admin.php?page=appforge_panel&section=license' ) );
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php bloginfo( 'name' ); ?> — License Activation Required</title>
        <style>
            *{box-sizing:border-box;}
            html,body{height:100%;margin:0;}
            body{
                display:flex;align-items:center;justify-content:center;
                font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;
                background:linear-gradient(180deg,#f8f9fa 0%,#eef1f3 100%);
                color:#212529;padding:24px;
            }
            .af-card{
                background:#fff;max-width:440px;width:100%;
                border-radius:14px;padding:44px 36px 36px;
                box-shadow:0 4px 24px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);
                text-align:center;
            }
            .af-icon{
                width:56px;height:56px;margin:0 auto 20px;border-radius:50%;
                background:#e6f9ee;display:flex;align-items:center;justify-content:center;
            }
            .af-icon svg{width:26px;height:26px;}
            .af-site{
                font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                color:#00C853;margin:0 0 8px;
            }
            .af-card h1{font-size:20px;font-weight:700;margin:0 0 10px;color:#111;}
            .af-card p{font-size:14px;line-height:1.6;color:#666;margin:0 0 26px;}
            .af-btn{
                display:inline-block;background:#00C853;color:#fff;text-decoration:none;
                font-size:14px;font-weight:700;padding:12px 28px;border-radius:8px;
                transition:background .15s;
            }
            .af-btn:hover{background:#009624;}
            .af-footnote{margin-top:22px;font-size:12px;color:#999;}
        </style>
        </head>
        <body>
            <div class="af-card">
                <div class="af-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#00C853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="10" width="16" height="10" rx="2"></rect>
                        <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                    </svg>
                </div>
                <p class="af-site"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
                <h1>License Activation Required</h1>
                <p>This website's theme hasn't been activated yet. If you're the site owner, log in and enter your license key to bring the site back online.</p>
                <a class="af-btn" href="<?php echo esc_url( $login_url ); ?>">Activate License</a>
                <p class="af-footnote">Not the site owner? Please check back later.</p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    // ------------------------------------------------------------
    // Settings panel section (rendered from AppForge_Panel)
    // ------------------------------------------------------------
    public static function render_section() {
        $data = self::get_data();

        $status_labels = array(
            'inactive'      => array( 'label' => 'Not activated',  'color' => '#888' ),
            'valid'         => array( 'label' => 'Active',         'color' => '#00C853' ),
            'invalid'       => array( 'label' => 'Invalid key',    'color' => '#dc3545' ),
            'expired'       => array( 'label' => 'Expired',        'color' => '#dc3545' ),
            'limit_reached' => array( 'label' => 'Activation limit reached', 'color' => '#dc3545' ),
            'error'         => array( 'label' => 'Could not reach license server', 'color' => '#e67e22' ),
        );
        $st = $status_labels[ $data['status'] ] ?? $status_labels['inactive'];
        ?>
        <div class="af-section-title"><span class="dashicons dashicons-admin-network"></span> License</div>

        <?php if ( isset( $_GET['af_license_ok'] ) ) : ?>
            <div class="af-notice">✓ License activated successfully.</div>
        <?php elseif ( isset( $_GET['af_license_deactivated'] ) ) : ?>
            <div class="af-notice">License deactivated on this site.</div>
        <?php elseif ( isset( $_GET['af_license_error'] ) ) : ?>
            <div class="af-notice" style="background:#f8d7da;border-color:#f5c6cb;color:#721c24;">
                <?php echo esc_html( rawurldecode( wp_unslash( $_GET['af_license_error'] ) ) ); ?>
            </div>
        <?php endif; ?>

        <?php if ( ! defined( 'APPFORGE_LICENSE_API_URL' ) || ! APPFORGE_LICENSE_API_URL ) : ?>
            <div class="af-notice" style="background:#fff3cd;border-color:#ffeeba;color:#856404;">
                No license server configured. Add <code>define( 'APPFORGE_LICENSE_API_URL', 'https://your-store.com/wp-json/appforge-license/v1' );</code> to <code>wp-config.php</code>.
            </div>
        <?php endif; ?>

        <div class="af-card">
            <div class="af-row">
                <div class="af-row-label">
                    <strong>Status</strong>
                </div>
                <div class="af-row-control">
                    <span style="display:inline-block;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;color:#fff;background:<?php echo esc_attr( $st['color'] ); ?>;">
                        <?php echo esc_html( $st['label'] ); ?>
                    </span>
                    <?php if ( $data['expires'] ) : ?>
                        <span style="margin-left:10px;font-size:12px;color:#888;">Expires: <?php echo esc_html( $data['expires'] ); ?></span>
                    <?php endif; ?>
                    <?php if ( $data['checked'] ) : ?>
                        <span style="margin-left:10px;font-size:12px;color:#888;">Last checked: <?php echo esc_html( human_time_diff( $data['checked'] ) . ' ago' ); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="af-row">
                <div class="af-row-label">
                    <strong>License Key</strong>
                    <small>Enter the key you received at purchase.</small>
                </div>
                <div class="af-row-control">
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:flex;gap:10px;max-width:480px;">
                        <?php wp_nonce_field( 'af_license_activate' ); ?>
                        <input type="hidden" name="action" value="af_license_activate">
                        <input type="text" name="license_key" value="<?php echo esc_attr( $data['key'] ); ?>"
                               placeholder="XXXX-XXXX-XXXX-XXXX" style="flex:1;padding:8px 12px;border:1px solid #ddd;border-radius:5px;font-size:13px;">
                        <button type="submit" class="af-save-btn" style="margin-top:0;"><?php echo self::is_active() ? 'Re-activate' : 'Activate'; ?></button>
                    </form>

                    <?php if ( $data['key'] !== '' ) : ?>
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:10px;">
                        <?php wp_nonce_field( 'af_license_deactivate' ); ?>
                        <input type="hidden" name="action" value="af_license_deactivate">
                        <button type="submit" class="button">Deactivate</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
}

new AppForge_License();
