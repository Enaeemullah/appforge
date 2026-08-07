<?php get_header(); ?>

<?php while ( have_posts() ) : the_post();

    $post_id       = get_the_ID();
    $download_url  = get_post_meta( $post_id, '_download_url',    true );
    $telegram_url  = get_post_meta( $post_id, '_telegram_url',    true );
    $gplay_url     = get_post_meta( $post_id, '_google_play_url', true );
    $dev_url       = get_post_meta( $post_id, '_developer_url',   true );
    $developer     = get_post_meta( $post_id, '_developer',       true );
    $released      = get_post_meta( $post_id, '_released',        true );
    $version       = get_post_meta( $post_id, '_version',         true );
    $file_size     = get_post_meta( $post_id, '_size',            true );
    $requires      = get_post_meta( $post_id, '_requires',        true );
    $whats_new     = get_post_meta( $post_id, '_whats_new',       true );
    $versions_raw  = get_post_meta( $post_id, '_versions',        true );
    $votes         = get_post_meta( $post_id, '_votes',           true );
    $rating        = appforge_get_rating();
    $downloads     = appforge_get_downloads();
    $updated       = get_the_modified_date( 'M j, Y' );
    $categories = get_the_terms( $post_id, 'app_category' );
    if ( ! $categories || is_wp_error( $categories ) ) {
        // Fallback: use standard post categories
        $wp_cats    = get_the_category( $post_id );
        $categories = $wp_cats ?: array();
    }
    $first_cat = ! empty( $categories ) ? $categories[0] : null;
    // For standard categories, get_term_link() works on both WP_Term objects
    $versions_list = $versions_raw ? json_decode( $versions_raw, true ) : array();
    $share_url     = urlencode( get_permalink() );
    $share_title   = urlencode( get_the_title() );

    // Panel settings
    $dl_type       = AppForge_Panel::get( 'single_dl_type',      'normal' );
    $read_more     = AppForge_Panel::get( 'single_read_more',     'collapsed' );
    $hide_social   = AppForge_Panel::get( 'single_hide_social',   '0' );
    $show_tg_btn   = AppForge_Panel::get( 'single_show_telegram', '0' );
    $panel_tg_url  = AppForge_Panel::get( 'single_telegram_url',  '' );
    $dl_timer      = (int) AppForge_Panel::get( 'single_dl_timer', 5 );
    $app_info      = (array) AppForge_Panel::get( 'single_app_info', array( 'developer', 'released', 'updated', 'size', 'version', 'requirements', 'downloads', 'google_play' ) );

    // Determine effective download href
    $dl_href = ( $dl_type === 'timer' && $download_url )
        ? add_query_arg( 'download', 'links', get_permalink() )
        : $download_url;

    // Telegram button: per-post URL takes precedence; panel global URL is fallback if panel shows it
    $effective_tg_url = $telegram_url ?: ( $show_tg_btn === '1' ? $panel_tg_url : '' );

?>

<div class="app-page-wrap">

    <?php appforge_breadcrumbs(); ?>

    <div class="app-detail-layout">

        <!-- ====================================================
             MAIN COLUMN
             ==================================================== -->
        <div class="app-detail-main">

            <!-- APP HEADER CARD -->
            <div class="app-header-card">
                <div class="app-header-top">

                    <!-- Left: icon + info + CTAs -->
                    <div class="app-header-left">

                        <div class="app-icon-box">
                            <?php if ( has_post_thumbnail() ) :
                                the_post_thumbnail( 'app-icon', array( 'loading' => 'eager', 'alt' => esc_attr( get_the_title() ) ) );
                            else : ?>
                                📱
                            <?php endif; ?>
                        </div>

                        <div class="app-header-details">

                            <h1 class="app-header-name">
                                <?php the_title(); ?>
                                <?php if ( $version ) : ?>
                                <span class="app-version"><?php echo esc_html( $version ); ?></span>
                                <?php endif; ?>
                            </h1>

                            <?php if ( $first_cat ) : ?>
                            <a href="<?php echo esc_url( get_term_link( $first_cat ) ); ?>" class="app-cat-badge">
                                <?php echo esc_html( $first_cat->name ); ?>
                            </a>
                            <?php endif; ?>

                            <?php
                            $tagline = get_the_excerpt();
                            if ( $tagline ) : ?>
                            <p class="app-tagline"><?php echo esc_html( $tagline ); ?></p>
                            <?php endif; ?>

                            <!-- CTAs -->
                            <div class="app-cta-row">
                                <?php if ( $dl_href ) : ?>
                                <a href="<?php echo esc_url( $dl_href ); ?>"
                                   class="btn-dl-apk"
                                   <?php echo $dl_type !== 'timer' ? 'rel="nofollow noopener" target="_blank"' : ''; ?>>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 16l-5-5h3V4h4v7h3l-5 5zm5 2H7v2h10v-2z"/></svg>
                                    <?php esc_html_e( 'Download APK', 'appforge' ); ?>
                                </a>
                                <?php endif; ?>

                                <?php if ( $effective_tg_url ) : ?>
                                <a href="<?php echo esc_url( $effective_tg_url ); ?>"
                                   class="btn-telegram-dl"
                                   rel="nofollow noopener" target="_blank">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.013 9.487c-.145.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.537-.194 1.006.131.876.734z"/></svg>
                                    <?php esc_html_e( 'Telegram', 'appforge' ); ?>
                                </a>
                                <?php endif; ?>
                            </div>

                            <!-- Rating -->
                            <?php if ( $rating ) : ?>
                            <div class="app-rating-row">
                                <span class="stars" aria-label="<?php echo esc_attr( $rating . ' out of 5' ); ?>">
                                    <?php echo appforge_stars( $rating ); // phpcs:ignore ?>
                                </span>
                                <strong><?php echo esc_html( $rating ); ?>/5</strong>
                                <?php if ( $votes ) : ?>
                                <span class="votes"><?php echo esc_html( 'Votes: ' . number_format( (int) $votes ) ); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                            <button class="app-report-link" type="button" onclick="alert('<?php esc_attr_e( 'Thank you for your report. We will review this app.', 'appforge' ); ?>')">
                                ⚑ <?php esc_html_e( 'Report', 'appforge' ); ?>
                            </button>

                        </div><!-- .app-header-details -->
                    </div><!-- .app-header-left -->

                    <!-- Right: meta panel -->
                    <div class="app-meta-panel">
                        <?php if ( $developer && in_array( 'developer', $app_info, true ) ) : ?>
                        <div class="app-meta-item">
                            <span class="app-meta-key"><?php esc_html_e( 'Developer', 'appforge' ); ?></span>
                            <span class="app-meta-val">
                                <?php if ( $dev_url ) : ?>
                                <a href="<?php echo esc_url( $dev_url ); ?>" rel="nofollow noopener" target="_blank"><?php echo esc_html( $developer ); ?></a>
                                <?php else : echo esc_html( $developer ); endif; ?>
                            </span>
                        </div>
                        <?php endif; ?>

                        <?php if ( $released && in_array( 'released', $app_info, true ) ) : ?>
                        <div class="app-meta-item">
                            <span class="app-meta-key"><?php esc_html_e( 'Released on', 'appforge' ); ?></span>
                            <span class="app-meta-val"><?php echo esc_html( $released ); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if ( in_array( 'updated', $app_info, true ) ) : ?>
                        <div class="app-meta-item">
                            <span class="app-meta-key"><?php esc_html_e( 'Updated', 'appforge' ); ?></span>
                            <span class="app-meta-val"><?php echo esc_html( $updated ); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if ( $file_size && in_array( 'size', $app_info, true ) ) : ?>
                        <div class="app-meta-item">
                            <span class="app-meta-key"><?php esc_html_e( 'Size', 'appforge' ); ?></span>
                            <span class="app-meta-val"><?php echo esc_html( $file_size ); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if ( $version && in_array( 'version', $app_info, true ) ) : ?>
                        <div class="app-meta-item">
                            <span class="app-meta-key"><?php esc_html_e( 'Version', 'appforge' ); ?></span>
                            <span class="app-meta-val"><?php echo esc_html( $version ); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if ( $requires && in_array( 'requirements', $app_info, true ) ) : ?>
                        <div class="app-meta-item">
                            <span class="app-meta-key"><?php esc_html_e( 'Requirements', 'appforge' ); ?></span>
                            <span class="app-meta-val"><?php echo esc_html( $requires ); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if ( $downloads && in_array( 'downloads', $app_info, true ) ) : ?>
                        <div class="app-meta-item">
                            <span class="app-meta-key"><?php esc_html_e( 'Downloads', 'appforge' ); ?></span>
                            <span class="app-meta-val"><?php echo esc_html( $downloads ); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if ( $gplay_url && in_array( 'google_play', $app_info, true ) ) : ?>
                        <div class="app-meta-item">
                            <span class="app-meta-key"><?php esc_html_e( 'Get it on', 'appforge' ); ?></span>
                            <span class="app-meta-val">
                                <a href="<?php echo esc_url( $gplay_url ); ?>" class="btn-gplay" rel="nofollow noopener" target="_blank">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="#fbfcfb" aria-hidden="true"><path d="M3.18 23.76c.27.14.58.17.88.08L13.94 12 6.06 4.12 3.18 3.24A1 1 0 0 0 2 4.12v15.76a1 1 0 0 0 1.18.88zm17.64-9.76-3.17-1.83-3.08 3.08 3.08 3.08 3.18-1.84a1 1 0 0 0 0-1.49zm-14.89 8.5 9.04-9.04-3.08-3.08z"/></svg>
                                    Google Play
                                </a>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div><!-- .app-meta-panel -->

                </div><!-- .app-header-top -->

                <!-- Social Share Bar -->
                <?php if ( $hide_social !== '1' ) : ?>
                <div class="app-share-bar">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>"
                       class="app-share-btn app-share-btn--fb" rel="nofollow noopener" target="_blank">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
                        Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>"
                       class="app-share-btn app-share-btn--tw" rel="nofollow noopener" target="_blank">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        Twitter
                    </a>
                    <a href="https://pinterest.com/pin/create/button/?url=<?php echo $share_url; ?>&description=<?php echo $share_title; ?>"
                       class="app-share-btn app-share-btn--pin" rel="nofollow noopener" target="_blank">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>
                        Pinterest
                    </a>
                    <a href="https://t.me/share/url?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>"
                       class="app-share-btn app-share-btn--tg" rel="nofollow noopener" target="_blank">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248-2.013 9.487c-.145.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.537-.194 1.006.131.876.734z"/></svg>
                        Telegram
                    </a>
                    <a href="https://wa.me/?text=<?php echo $share_title; ?>%20<?php echo $share_url; ?>"
                       class="app-share-btn app-share-btn--wa" rel="nofollow noopener" target="_blank">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                        Whatsapp
                    </a>
                </div><!-- .app-share-bar -->
                <?php endif; ?>

            </div><!-- .app-header-card -->

            <!-- DESCRIPTION -->
            <div class="app-section" id="description">
                <div class="app-section__hd">
                    <h2 class="app-section__title"><?php esc_html_e( 'Description', 'appforge' ); ?></h2>
                </div>
                <div class="app-section__body">
                    <div class="app-desc-collapse<?php echo $read_more === 'all' ? ' expanded' : ''; ?>" id="appDescCollapse">
                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </div>
                    <?php if ( $read_more !== 'all' ) : ?>
                    <button class="app-read-more" data-target="appDescCollapse" type="button">
                        <?php esc_html_e( 'Read more', 'appforge' ); ?>
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- WHAT'S NEW -->
            <?php if ( $whats_new ) : ?>
            <div class="app-section">
                <div class="app-section__hd">
                    <h2 class="app-section__title"><?php esc_html_e( "What's New", 'appforge' ); ?></h2>
                </div>
                <div class="app-section__body">
                    <div class="entry-content">
                        <?php echo wpautop( esc_html( $whats_new ) ); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- VERSIONS TABLE -->
            <?php if ( ! empty( $versions_list ) ) : ?>
            <div class="app-section">
                <div class="app-section__hd">
                    <h2 class="app-section__title"><?php esc_html_e( 'Versions', 'appforge' ); ?></h2>
                </div>
                <div class="app-section__body" style="padding:0;">
                    <table class="versions-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Version', 'appforge' ); ?></th>
                                <th><?php esc_html_e( 'Title', 'appforge' ); ?></th>
                                <th><?php esc_html_e( 'Date', 'appforge' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $versions_list as $ver ) : ?>
                            <tr>
                                <td><?php echo esc_html( $ver['version'] ?? '' ); ?></td>
                                <td><?php echo esc_html( $ver['title'] ?? ( $ver['size'] ?? '' ) ); ?></td>
                                <td><?php echo esc_html( $ver['date'] ?? '' ); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- SCREENSHOTS -->
            <?php
            $screens_raw  = get_post_meta( $post_id, '_app_screenshots', true );
            $screenshots  = $screens_raw ? json_decode( $screens_raw, true ) : array();
            $screenshots  = is_array( $screenshots ) ? array_filter( $screenshots ) : array();
            if ( ! empty( $screenshots ) ) : ?>
            <div class="app-section">
                <div class="app-section__hd">
                    <h2 class="app-section__title"><?php esc_html_e( 'Screenshots', 'appforge' ); ?></h2>
                </div>
                <div class="app-section__body">
                    <div class="app-screenshots-row">
                        <?php foreach ( $screenshots as $src ) : ?>
                        <a href="<?php echo esc_url( $src ); ?>" class="app-screenshot-thumb" target="_blank" rel="noopener">
                            <img src="<?php echo esc_url( $src ); ?>" alt="<?php esc_attr_e( 'Screenshot', 'appforge' ); ?>" loading="lazy" />
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- YOUTUBE VIDEO -->
            <?php $yt_id = get_post_meta( $post_id, '_youtube_id', true ); ?>
            <?php if ( $yt_id ) : ?>
            <div class="app-section">
                <div class="app-section__hd">
                    <h2 class="app-section__title"><?php esc_html_e( 'Video', 'appforge' ); ?></h2>
                </div>
                <div class="app-section__body" style="padding:0;">
                    <div class="app-yt-wrap">
                        <iframe src="https://www.youtube.com/embed/<?php echo esc_attr( $yt_id ); ?>?rel=0"
                                frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen loading="lazy" title="<?php the_title_attribute(); ?>"></iframe>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div><!-- .app-detail-main -->

        <!-- ====================================================
             SIDEBAR
             ==================================================== -->
        <aside class="app-detail-sidebar" aria-label="<?php esc_attr_e( 'App sidebar', 'appforge' ); ?>">

            <!-- All Categories -->
            <?php
            $all_cats = get_terms( array(
                'taxonomy'   => 'app_category',
                'hide_empty' => true,
                'number'     => 20,
                'orderby'    => 'name',
            ) );
            if ( $all_cats && ! is_wp_error( $all_cats ) ) : ?>
            <div class="app-sw">
                <div class="app-sw__hd">
                    <h3 class="app-sw__title"><?php esc_html_e( 'Categories', 'appforge' ); ?></h3>
                </div>
                <div class="app-sw__body">
                    <div class="sw-cat-grid">
                        <?php foreach ( $all_cats as $cat ) :
                            $initial = mb_strtoupper( mb_substr( $cat->name, 0, 1 ) );
                        ?>
                        <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="sw-cat-link">
                            <span class="sw-cat-icon" aria-hidden="true"><?php echo esc_html( $initial ); ?></span>
                            <?php echo esc_html( $cat->name ); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Most Rated Apps -->
            <?php
            $rated_apps = new WP_Query( array(
                'post_type'      => 'app',
                'posts_per_page' => 5,
                'meta_key'       => '_rating',
                'orderby'        => 'meta_value_num',
                'order'          => 'DESC',
                'post__not_in'   => array( $post_id ),
            ) );
            if ( $rated_apps->have_posts() ) : ?>
            <div class="app-sw">
                <div class="app-sw__hd">
                    <h3 class="app-sw__title"><?php esc_html_e( 'Most Rated Apps', 'appforge' ); ?></h3>
                </div>
                <div class="app-sw__body">
                    <div class="sw-app-list">
                        <?php while ( $rated_apps->have_posts() ) : $rated_apps->the_post();
                            $r = appforge_get_rating();
                            $d = get_post_meta( get_the_ID(), '_developer', true );
                        ?>
                        <a href="<?php the_permalink(); ?>" class="sw-app-item">
                            <div class="sw-app-icon" aria-hidden="true">
                                <?php if ( has_post_thumbnail() ) :
                                    the_post_thumbnail( 'app-icon-sm', array( 'loading' => 'lazy', 'alt' => '' ) );
                                else : ?>📱<?php endif; ?>
                            </div>
                            <div class="sw-app-info">
                                <span class="sw-app-name"><?php the_title(); ?></span>
                                <?php if ( $d ) : ?><span class="sw-app-dev"><?php echo esc_html( $d ); ?></span><?php endif; ?>
                                <?php if ( $r ) : ?><span class="sw-app-stars"><?php echo str_repeat( '★', min( 5, round( (float) $r ) ) ); ?></span><?php endif; ?>
                            </div>
                        </a>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Most Viewed Apps -->
            <?php
            $viewed_apps = new WP_Query( array(
                'post_type'      => 'app',
                'posts_per_page' => 5,
                'meta_key'       => '_app_views',
                'orderby'        => 'meta_value_num',
                'order'          => 'DESC',
                'post__not_in'   => array( $post_id ),
            ) );
            if ( $viewed_apps->have_posts() ) : ?>
            <div class="app-sw">
                <div class="app-sw__hd">
                    <h3 class="app-sw__title"><?php esc_html_e( 'Most Viewed Apps', 'appforge' ); ?></h3>
                </div>
                <div class="app-sw__body">
                    <div class="sw-app-list">
                        <?php while ( $viewed_apps->have_posts() ) : $viewed_apps->the_post();
                            $d = get_post_meta( get_the_ID(), '_developer', true );
                            $r = appforge_get_rating();
                        ?>
                        <a href="<?php the_permalink(); ?>" class="sw-app-item">
                            <div class="sw-app-icon" aria-hidden="true">
                                <?php if ( has_post_thumbnail() ) :
                                    the_post_thumbnail( 'app-icon-sm', array( 'loading' => 'lazy', 'alt' => '' ) );
                                else : ?>📱<?php endif; ?>
                            </div>
                            <div class="sw-app-info">
                                <span class="sw-app-name"><?php the_title(); ?></span>
                                <?php if ( $d ) : ?><span class="sw-app-dev"><?php echo esc_html( $d ); ?></span><?php endif; ?>
                                <?php if ( $r ) : ?><span class="sw-app-stars"><?php echo str_repeat( '★', min( 5, round( (float) $r ) ) ); ?></span><?php endif; ?>
                            </div>
                        </a>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </aside><!-- .app-detail-sidebar -->

    </div><!-- .app-detail-layout -->
</div><!-- .app-page-wrap -->

<?php endwhile; ?>
<?php get_footer(); ?>
