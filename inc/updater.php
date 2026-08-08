<?php
/**
 * AppForge Pro — Auto-updates
 *
 * Hooks the theme into WordPress's native "Update Now" flow (Dashboard →
 * Updates / Appearance → Themes) by injecting update info into the
 * update_themes transient, same mechanism core themes use. Only offered
 * when the license is active — no license, no updates.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class AppForge_Updater {

    const CACHE_KEY = 'appforge_update_info';

    public function __construct() {
        add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update' ) );
        add_action( 'upgrader_process_complete', array( $this, 'purge_cache' ), 10, 2 );
        add_action( 'switch_theme', array( $this, 'purge_cache' ) );
    }

    public function check_for_update( $transient ) {
        if ( empty( $transient->checked ) ) return $transient;
        if ( ! AppForge_License::is_active() ) return $transient;

        $stylesheet       = get_stylesheet();
        $current_version  = wp_get_theme( $stylesheet )->get( 'Version' );

        $info = get_site_transient( self::CACHE_KEY );
        if ( $info === false ) {
            $info = $this->fetch_update_info( $current_version );
            set_site_transient( self::CACHE_KEY, $info, 12 * HOUR_IN_SECONDS );
        }

        if ( ! empty( $info['has_update'] ) && version_compare( $info['version'], $current_version, '>' ) ) {
            $transient->response[ $stylesheet ] = array(
                'theme'       => $stylesheet,
                'new_version' => $info['version'],
                'url'         => home_url( '/' ),
                'package'     => $info['download'],
            );
        }

        return $transient;
    }

    private function fetch_update_info( $current_version ) {
        if ( ! defined( 'APPFORGE_LICENSE_API_URL' ) || ! APPFORGE_LICENSE_API_URL ) {
            return array( 'has_update' => false );
        }

        $data = AppForge_License::get_data();
        if ( ! $data['key'] ) return array( 'has_update' => false );

        $response = wp_remote_post( trailingslashit( APPFORGE_LICENSE_API_URL ) . 'update-check', array(
            'timeout' => 15,
            'body'    => array(
                'license_key' => $data['key'],
                'site_url'    => home_url(),
                'product'     => AppForge_License::PRODUCT_SLUG,
                'version'     => $current_version,
            ),
        ) );

        if ( is_wp_error( $response ) ) return array( 'has_update' => false );

        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( ! is_array( $body ) || empty( $body['success'] ) ) return array( 'has_update' => false );

        return $body;
    }

    public function purge_cache( $upgrader = null, $hook_extra = null ) {
        delete_site_transient( self::CACHE_KEY );
    }
}

new AppForge_Updater();
