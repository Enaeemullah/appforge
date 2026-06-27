<?php get_header(); ?>


<div class="apk-homepage">

    <!-- ================================================================
         MAIN COLUMN
         ================================================================ -->
    <div class="apk-main">

        <!-- ===== HERO CAROUSEL ===== -->
        <section class="homepage-carousel" aria-label="<?php esc_attr_e( 'Featured apps', 'appforge' ); ?>">
            <div class="carousel-container" id="featuredCarousel" aria-live="polite">
                <?php
                $featured_apps = new WP_Query( array(
                    'post_type'      => 'app',
                    'posts_per_page' => 5,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                ) );
                $slide_index = 0;
                if ( $featured_apps->have_posts() ) :
                    while ( $featured_apps->have_posts() ) : $featured_apps->the_post();
                        $is_active = ( $slide_index === 0 ) ? 'active' : '';
                ?>
                <div class="carousel-slide <?php echo esc_attr( $is_active ); ?>" role="group"
                     aria-roledescription="slide"
                     aria-label="<?php echo esc_attr( $slide_index + 1 ); ?> of <?php echo esc_attr( $featured_apps->post_count ); ?>">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <img class="carousel-img"
                             src="<?php the_post_thumbnail_url( 'carousel' ); ?>"
                             alt="<?php the_title_attribute(); ?>"
                             loading="<?php echo $slide_index === 0 ? 'eager' : 'lazy'; ?>">
                    <?php else : ?>
                        <div class="carousel-placeholder" aria-hidden="true">📱</div>
                    <?php endif; ?>
                    <div class="carousel-overlay">
                        <h2><a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none;"><?php the_title(); ?></a></h2>
                        <p><?php
                            $excerpt = get_post_meta( get_the_ID(), '_developer', true );
                            if ( ! $excerpt ) $excerpt = wp_trim_words( get_the_excerpt(), 10 );
                            echo esc_html( $excerpt );
                        ?></p>
                    </div>
                </div>
                <?php $slide_index++; endwhile; ?>
                <?php else : ?>
                <div class="carousel-slide active">
                    <div class="carousel-placeholder" aria-hidden="true">📱</div>
                    <div class="carousel-overlay">
                        <h2><?php esc_html_e( 'Welcome to AppForge', 'appforge' ); ?></h2>
                        <p><?php esc_html_e( 'Your trusted source for app downloads', 'appforge' ); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                <?php wp_reset_postdata(); ?>

                <?php if ( $slide_index > 1 ) : ?>
                <button class="carousel-btn carousel-btn--prev" id="carouselPrev"
                        aria-label="<?php esc_attr_e( 'Previous slide', 'appforge' ); ?>">&#8249;</button>
                <button class="carousel-btn carousel-btn--next" id="carouselNext"
                        aria-label="<?php esc_attr_e( 'Next slide', 'appforge' ); ?>">&#8250;</button>
                <div class="carousel-dots" role="tablist"
                     aria-label="<?php esc_attr_e( 'Slide navigation', 'appforge' ); ?>">
                    <?php for ( $i = 0; $i < $slide_index; $i++ ) : ?>
                    <button class="carousel-dot <?php echo $i === 0 ? 'active' : ''; ?>"
                            role="tab"
                            aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                            aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'appforge' ), $i + 1 ) ); ?>"
                            data-slide="<?php echo esc_attr( $i ); ?>"></button>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div><!-- .carousel-container -->
        </section><!-- .homepage-carousel -->

        <!-- ===== DISCOVER ROW ===== -->
        <?php
        $discover_apps = new WP_Query( array(
            'post_type'      => 'app',
            'posts_per_page' => 16,
            'orderby'        => 'rand',
        ) );
        if ( $discover_apps->have_posts() ) : ?>
        <section class="discover-section" aria-label="<?php esc_attr_e( 'Discover apps', 'appforge' ); ?>">
            <div class="apk-section-header">
                <h2 class="apk-section-header__title"><?php esc_html_e( 'Discover', 'appforge' ); ?></h2>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>"
                   class="apk-section-header__link"><?php esc_html_e( 'View All', 'appforge' ); ?></a>
            </div>
            <div class="discover-row" role="list" aria-label="<?php esc_attr_e( 'App collection', 'appforge' ); ?>">
                <?php while ( $discover_apps->have_posts() ) : $discover_apps->the_post(); ?>
                <a href="<?php the_permalink(); ?>" class="app-tile" role="listitem"
                   aria-label="<?php the_title_attribute(); ?>">
                    <div class="app-tile__icon" aria-hidden="true">
                        <?php if ( has_post_thumbnail() ) :
                            the_post_thumbnail( 'app-icon-sm', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) );
                        else : ?>
                            📱
                        <?php endif; ?>
                    </div>
                    <span class="app-tile__name"><?php echo esc_html( wp_trim_words( get_the_title(), 2 ) ); ?></span>
                </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </section>
        <?php else : wp_reset_postdata(); endif; ?>

        <!-- ===== RANKED / POPULAR ===== -->
        <?php
        /* Use meta_query with NOT EXISTS fallback so posts without _app_views still appear */
        $popular_args = array(
            'post_type'      => 'app',
            'posts_per_page' => 12,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        /* Prefer ordering by _app_views if any post has it set */
        $views_check = new WP_Query( array( 'post_type' => 'app', 'posts_per_page' => 1, 'meta_key' => '_app_views', 'fields' => 'ids' ) );
        if ( $views_check->have_posts() ) {
            $popular_args['meta_key'] = '_app_views';
            $popular_args['orderby']  = 'meta_value_num';
        }
        wp_reset_postdata();
        $popular_apps = new WP_Query( $popular_args );
        ?>
        <?php if ( $popular_apps->have_posts() ) : ?>
        <section class="ranked-section" aria-label="<?php esc_attr_e( 'Popular apps', 'appforge' ); ?>">
            <div class="apk-section-header">
                <h2 class="apk-section-header__title"><?php esc_html_e( 'Most Popular', 'appforge' ); ?></h2>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>"
                   class="apk-section-header__link"><?php esc_html_e( 'More', 'appforge' ); ?></a>
            </div>
            <div class="ranked-row" role="list">
                <?php
                $rank = 1;
                while ( $popular_apps->have_posts() ) : $popular_apps->the_post();
                    $rating = appforge_get_rating();
                    $terms  = get_the_terms( get_the_ID(), 'app_category' );
                    $cat    = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
                ?>
                <a href="<?php the_permalink(); ?>" class="ranked-card" role="listitem"
                   aria-label="<?php echo esc_attr( sprintf( '#%d %s', $rank, get_the_title() ) ); ?>">
                    <span class="ranked-card__badge" aria-hidden="true"><?php echo esc_html( $rank ); ?></span>
                    <div class="ranked-card__icon" aria-hidden="true">
                        <?php if ( has_post_thumbnail() ) :
                            the_post_thumbnail( 'app-icon-sm', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) );
                        else : ?>
                            📱
                        <?php endif; ?>
                    </div>
                    <span class="ranked-card__name"><?php the_title(); ?></span>
                    <?php if ( $cat ) : ?>
                    <span class="ranked-card__cat"><?php echo esc_html( $cat ); ?></span>
                    <?php endif; ?>
                    <?php if ( $rating ) : ?>
                    <span class="ranked-card__stars" aria-label="<?php echo esc_attr( sprintf( __( 'Rating: %s', 'appforge' ), $rating ) ); ?>">
                        <?php echo str_repeat( '★', min( 5, round( (float) $rating ) ) ); ?>
                    </span>
                    <?php endif; ?>
                </a>
                <?php $rank++; endwhile; wp_reset_postdata(); ?>
            </div>
        </section>
        <?php else : wp_reset_postdata(); endif; ?>

        <!-- ===== HOT APPS — 3-col grid ===== -->
        <?php
        $hot_args = array(
            'post_type'      => 'app',
            'posts_per_page' => 12,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        $dl_check = new WP_Query( array( 'post_type' => 'app', 'posts_per_page' => 1, 'meta_key' => '_downloads', 'fields' => 'ids' ) );
        if ( $dl_check->have_posts() ) {
            $hot_args['meta_key'] = '_downloads';
            $hot_args['orderby']  = 'meta_value_num';
        }
        wp_reset_postdata();
        $hot_apps = new WP_Query( $hot_args );
        ?>
        <?php if ( $hot_apps->have_posts() ) : ?>
        <section class="hot-section" aria-label="<?php esc_attr_e( 'Hot apps', 'appforge' ); ?>">
            <div class="apk-section-header">
                <h2 class="apk-section-header__title"><?php esc_html_e( 'Hot Apps', 'appforge' ); ?></h2>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>"
                   class="apk-section-header__link"><?php esc_html_e( 'More', 'appforge' ); ?></a>
            </div>
            <div class="hot-apps-grid">
                <?php while ( $hot_apps->have_posts() ) : $hot_apps->the_post();
                    $downloads = appforge_get_downloads();
                    $terms     = get_the_terms( get_the_ID(), 'app_category' );
                    $cat       = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
                ?>
                <a href="<?php the_permalink(); ?>" class="hot-app-item"
                   aria-label="<?php the_title_attribute(); ?>">
                    <div class="hot-app-item__icon" aria-hidden="true">
                        <?php if ( has_post_thumbnail() ) :
                            the_post_thumbnail( 'app-icon-sm', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) );
                        else : ?>
                            📱
                        <?php endif; ?>
                    </div>
                    <div class="hot-app-item__info">
                        <span class="hot-app-item__name"><?php the_title(); ?></span>
                        <?php if ( $cat ) : ?>
                        <span class="hot-app-item__cat"><?php echo esc_html( $cat ); ?></span>
                        <?php endif; ?>
                        <?php if ( $downloads ) : ?>
                        <span class="hot-app-item__dl"><?php echo esc_html( $downloads ); ?></span>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </section>
        <?php else : wp_reset_postdata(); endif; ?>

        <!-- ===== LATEST UPDATE — 3-col grid ===== -->
        <?php
        $latest_apps = new WP_Query( array(
            'post_type'      => 'app',
            'posts_per_page' => 12,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );
        ?>
        <?php if ( $latest_apps->have_posts() ) : ?>
        <section class="latest-section" aria-label="<?php esc_attr_e( 'Latest updates', 'appforge' ); ?>">
            <div class="apk-section-header">
                <h2 class="apk-section-header__title"><?php esc_html_e( 'Latest Update', 'appforge' ); ?></h2>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>"
                   class="apk-section-header__link"><?php esc_html_e( 'More', 'appforge' ); ?></a>
            </div>
            <div class="latest-grid">
                <?php while ( $latest_apps->have_posts() ) : $latest_apps->the_post();
                    $version = get_post_meta( get_the_ID(), '_version', true );
                    $terms   = get_the_terms( get_the_ID(), 'app_category' );
                    $cat     = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
                ?>
                <a href="<?php the_permalink(); ?>" class="latest-app-item"
                   aria-label="<?php the_title_attribute(); ?>">
                    <div class="latest-app-item__icon" aria-hidden="true">
                        <?php if ( has_post_thumbnail() ) :
                            the_post_thumbnail( 'app-icon-sm', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) );
                        else : ?>
                            📱
                        <?php endif; ?>
                    </div>
                    <div class="latest-app-item__info">
                        <span class="latest-app-item__name"><?php the_title(); ?></span>
                        <?php if ( $cat ) : ?>
                        <span class="latest-app-item__cat"><?php echo esc_html( $cat ); ?></span>
                        <?php endif; ?>
                        <?php if ( $version ) : ?>
                        <span class="latest-app-item__ver">v<?php echo esc_html( $version ); ?></span>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </section>
        <?php else : wp_reset_postdata(); endif; ?>

        <!-- ===== LATEST BLOG POSTS ===== -->
        <?php
        $hide_blog  = AppForge_Panel::get( 'home_hide_blog',  '0' );
        $blog_count = (int) AppForge_Panel::get( 'home_blog_count', 6 );
        $blog_posts = null;
        if ( $hide_blog !== '1' ) {
            $blog_posts = new WP_Query( array(
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => $blog_count ?: 6,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ) );
        }
        if ( $blog_posts && $blog_posts->have_posts() ) : ?>
        <section class="blog-section" aria-label="<?php esc_attr_e( 'Latest posts', 'appforge' ); ?>">
            <div class="apk-section-header">
                <h2 class="apk-section-header__title"><?php esc_html_e( 'Latest Posts', 'appforge' ); ?></h2>
                <a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="apk-section-header__link">
                    <?php esc_html_e( 'View All', 'appforge' ); ?>
                </a>
            </div>
            <div class="blog-grid">
                <?php while ( $blog_posts->have_posts() ) : $blog_posts->the_post(); ?>
                <a href="<?php the_permalink(); ?>" class="blog-card" aria-label="<?php the_title_attribute(); ?>">
                    <div class="blog-card__thumb" aria-hidden="true">
                        <?php if ( has_post_thumbnail() ) :
                            the_post_thumbnail( 'card-thumb', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) );
                        else : ?>
                        <div class="blog-card__thumb-placeholder">📝</div>
                        <?php endif; ?>
                    </div>
                    <div class="blog-card__body">
                        <div class="blog-card__meta">
                            <?php
                            $cats = get_the_category();
                            if ( $cats ) : ?>
                            <span class="blog-card__cat"><?php echo esc_html( $cats[0]->name ); ?></span>
                            <?php endif; ?>
                            <time class="blog-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                <?php echo esc_html( get_the_date() ); ?>
                            </time>
                        </div>
                        <h3 class="blog-card__title"><?php the_title(); ?></h3>
                        <p class="blog-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 12 ) ); ?></p>
                    </div>
                </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </section>
        <?php endif; ?>

    </div><!-- .apk-main -->

    <!-- ================================================================
         SIDEBAR COLUMN
         ================================================================ -->
    <aside class="apk-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'appforge' ); ?>">

        <!-- Search Widget -->
        <div class="apk-widget">
            <div class="apk-widget__header">
                <h3 class="apk-widget__title"><?php esc_html_e( 'Search', 'appforge' ); ?></h3>
            </div>
            <div class="apk-widget__body">
                <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>"
                      class="search-input-group">
                    <label for="sidebar-search" class="sr-only"><?php esc_html_e( 'Search apps', 'appforge' ); ?></label>
                    <input type="search" id="sidebar-search" name="s"
                           placeholder="<?php esc_attr_e( 'Search apps…', 'appforge' ); ?>"
                           autocomplete="off" value="<?php echo esc_attr( get_search_query() ); ?>">
                    <input type="hidden" name="post_type" value="app">
                    <button type="submit" aria-label="<?php esc_attr_e( 'Submit search', 'appforge' ); ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Trending Tags Widget -->
        <?php
        $trending_cats = get_terms( array(
            'taxonomy'   => 'app_category',
            'number'     => 12,
            'orderby'    => 'count',
            'order'      => 'DESC',
            'hide_empty' => true,
        ) );
        if ( $trending_cats && ! is_wp_error( $trending_cats ) ) : ?>
        <div class="apk-widget">
            <div class="apk-widget__header">
                <h3 class="apk-widget__title"><?php esc_html_e( 'Trending', 'appforge' ); ?></h3>
            </div>
            <div class="apk-widget__body">
                <div class="apk-tag-cloud">
                    <?php foreach ( $trending_cats as $term ) : ?>
                    <a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="apk-tag">
                        <?php echo esc_html( $term->name ); ?>
                        <span style="margin-left:4px;font-size:10px;opacity:.65;"><?php echo esc_html( $term->count ); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- App Promo Widget (top rated app) -->
        <?php
        $promo_args = array(
            'post_type'      => 'app',
            'posts_per_page' => 1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        $rating_check = new WP_Query( array( 'post_type' => 'app', 'posts_per_page' => 1, 'meta_key' => '_rating', 'fields' => 'ids' ) );
        if ( $rating_check->have_posts() ) {
            $promo_args['meta_key'] = '_rating';
            $promo_args['orderby']  = 'meta_value_num';
        }
        wp_reset_postdata();
        $promo_app = new WP_Query( $promo_args );
        if ( $promo_app->have_posts() ) : $promo_app->the_post();
            $promo_rating  = appforge_get_rating();
            $promo_dl_url  = get_post_meta( get_the_ID(), '_download_url', true );
            $promo_terms   = get_the_terms( get_the_ID(), 'app_category' );
            $promo_cat     = ( $promo_terms && ! is_wp_error( $promo_terms ) ) ? $promo_terms[0]->name : '';
        ?>
        <div class="apk-promo-widget">
            <?php if ( has_post_thumbnail() ) :
                the_post_thumbnail( 'carousel', array( 'class' => 'apk-promo-widget__image', 'loading' => 'lazy', 'alt' => '' ) );
            else : ?>
                <div class="apk-promo-widget__image-placeholder" aria-hidden="true">📱</div>
            <?php endif; ?>
            <div class="apk-promo-widget__body">
                <div class="apk-promo-widget__name"><?php the_title(); ?></div>
                <?php if ( $promo_cat ) : ?>
                <div class="apk-promo-widget__cat"><?php echo esc_html( $promo_cat ); ?></div>
                <?php endif; ?>
                <div class="apk-promo-widget__meta">
                    <?php if ( $promo_rating ) : ?>
                    <span class="rating-row">
                        <span class="stars" aria-hidden="true">★</span>
                        <strong><?php echo esc_html( $promo_rating ); ?></strong>
                    </span>
                    <?php endif; ?>
                    <?php if ( $promo_dl_url ) : ?>
                    <a href="<?php echo esc_url( $promo_dl_url ); ?>" class="btn btn-primary btn-sm"
                       rel="nofollow noopener" target="_blank">
                        <?php esc_html_e( 'Download', 'appforge' ); ?>
                    </a>
                    <?php else : ?>
                    <a href="<?php the_permalink(); ?>" class="btn btn-outline btn-sm">
                        <?php esc_html_e( 'View', 'appforge' ); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php wp_reset_postdata(); endif; ?>

        <!-- Editor's Picks List -->
        <?php
        $editors_picks = new WP_Query( array(
            'post_type'      => 'app',
            'posts_per_page' => 8,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => 2,
        ) );
        if ( $editors_picks->have_posts() ) : ?>
        <div class="apk-widget">
            <div class="apk-widget__header">
                <h3 class="apk-widget__title"><?php esc_html_e( "Editor's Picks", 'appforge' ); ?></h3>
            </div>
            <div class="apk-widget__body" style="padding-top:4px;padding-bottom:4px;">
                <div class="editor-pick-list">
                    <?php
                    $pick_num = 1;
                    while ( $editors_picks->have_posts() ) : $editors_picks->the_post();
                        $ep_rating = appforge_get_rating();
                        $ep_terms  = get_the_terms( get_the_ID(), 'app_category' );
                        $ep_cat    = ( $ep_terms && ! is_wp_error( $ep_terms ) ) ? $ep_terms[0]->name : '';
                    ?>
                    <a href="<?php the_permalink(); ?>" class="editor-pick-item"
                       aria-label="<?php the_title_attribute(); ?>">
                        <span class="editor-pick-item__num" aria-hidden="true"><?php echo esc_html( $pick_num ); ?></span>
                        <div class="editor-pick-item__icon" aria-hidden="true">
                            <?php if ( has_post_thumbnail() ) :
                                the_post_thumbnail( 'app-icon-sm', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) );
                            else : ?>
                                📱
                            <?php endif; ?>
                        </div>
                        <div class="editor-pick-item__info">
                            <span class="editor-pick-item__name"><?php the_title(); ?></span>
                            <?php if ( $ep_cat ) : ?>
                            <span class="editor-pick-item__cat"><?php echo esc_html( $ep_cat ); ?></span>
                            <?php endif; ?>
                            <?php if ( $ep_rating ) : ?>
                            <span class="editor-pick-item__stars" aria-label="<?php echo esc_attr( sprintf( __( 'Rating: %s', 'appforge' ), $ep_rating ) ); ?>">
                                <?php echo str_repeat( '★', min( 5, round( (float) $ep_rating ) ) ); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php $pick_num++; endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </aside><!-- .apk-sidebar -->

</div><!-- .apk-homepage -->

<?php get_footer(); ?>
