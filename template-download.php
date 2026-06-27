<?php
/**
 * Download countdown timer page.
 * Served when: is_singular('app') && ?download=links && single_dl_type !== 'normal'
 */

if ( ! defined( 'ABSPATH' ) ) exit;

global $post;

$download_url = get_post_meta( $post->ID, '_download_url', true );
$timer_secs   = (int) AppForge_Panel::get( 'single_dl_timer', 5 );

if ( ! $download_url ) {
    wp_redirect( get_permalink( $post->ID ) );
    exit;
}

get_header();
?>

<div class="dl-page-wrap">

    <div class="dl-card">

        <div class="dl-app-info">
            <?php if ( has_post_thumbnail( $post->ID ) ) : ?>
            <div class="dl-app-icon">
                <?php echo get_the_post_thumbnail( $post->ID, 'app-icon', array( 'alt' => esc_attr( get_the_title( $post->ID ) ) ) ); ?>
            </div>
            <?php endif; ?>
            <div class="dl-app-meta">
                <h1 class="dl-app-name"><?php echo esc_html( get_the_title( $post->ID ) ); ?></h1>
                <?php
                $version = get_post_meta( $post->ID, '_version', true );
                if ( $version ) : ?>
                <p class="dl-app-version"><?php echo esc_html( 'Version ' . $version ); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="dl-progress-wrap" aria-live="polite" aria-atomic="true">
            <svg class="dl-spinner" viewBox="0 0 44 44" aria-hidden="true">
                <circle class="dl-spinner__track" cx="22" cy="22" r="18" fill="none" stroke-width="4"/>
                <circle class="dl-spinner__fill" cx="22" cy="22" r="18" fill="none" stroke-width="4"
                        stroke-dasharray="113"
                        stroke-dashoffset="113"/>
            </svg>
            <span class="dl-countdown" id="dlCountdown"><?php echo esc_html( $timer_secs ); ?></span>
        </div>

        <p class="dl-status" id="dlStatus">
            <?php echo esc_html( sprintf( __( 'Your download will start in %d seconds…', 'appforge' ), $timer_secs ) ); ?>
        </p>

        <a href="<?php echo esc_url( $download_url ); ?>"
           class="btn-dl-apk dl-manual-btn" id="dlManualBtn"
           rel="nofollow noopener" style="display:none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 16l-5-5h3V4h4v7h3l-5 5zm5 2H7v2h10v-2z"/></svg>
            <?php esc_html_e( 'Click here if download does not start', 'appforge' ); ?>
        </a>

        <p class="dl-back-link">
            <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
                &larr; <?php echo esc_html( get_the_title( $post->ID ) ); ?>
            </a>
        </p>

    </div><!-- .dl-card -->

</div><!-- .dl-page-wrap -->

<script>
(function () {
    var secs     = <?php echo (int) $timer_secs; ?>;
    var url      = <?php echo wp_json_encode( $download_url ); ?>;
    var total    = secs;
    var circumf  = 2 * Math.PI * 18; // 113.097...
    var fill     = document.querySelector('.dl-spinner__fill');
    var countdown= document.getElementById('dlCountdown');
    var status   = document.getElementById('dlStatus');
    var manual   = document.getElementById('dlManualBtn');

    function tick() {
        secs--;
        countdown.textContent = secs > 0 ? secs : '✓';

        var progress = (total - secs) / total;
        if (fill) fill.style.strokeDashoffset = circumf * (1 - progress);

        if (secs <= 0) {
            status.textContent = <?php echo wp_json_encode( __( 'Downloading… If nothing happens, use the button below.', 'appforge' ) ); ?>;
            manual.style.display = 'inline-flex';
            window.location.href = url;
        }
    }

    var timer = setInterval(function () {
        tick();
        if (secs <= 0) clearInterval(timer);
    }, 1000);
}());
</script>

<?php get_footer(); ?>
