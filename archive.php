<?php get_header(); ?>

<?php if ( ! is_tax( 'app_category' ) ) : ?>
<!-- Archive Banner -->
<div class="archive-banner">
    <div class="container">
        <div class="archive-banner__inner">
            <div class="archive-banner__icon" aria-hidden="true">
                <?php
                if ( is_post_type_archive( 'app' ) ) echo '📱';
                elseif ( is_category() ) echo '📂';
                elseif ( is_tag() ) echo '🏷️';
                elseif ( is_author() ) echo '👤';
                else echo '📋';
                ?>
            </div>
            <div>
                <p class="archive-banner__type">
                    <?php
                    if ( is_post_type_archive( 'app' ) )   esc_html_e( 'App Archive', 'appforge' );
                    elseif ( is_category() )                esc_html_e( 'Category', 'appforge' );
                    elseif ( is_tag() )                     esc_html_e( 'Tag', 'appforge' );
                    elseif ( is_author() )                  esc_html_e( 'Author', 'appforge' );
                    else                                    esc_html_e( 'Archive', 'appforge' );
                    ?>
                </p>
                <h1 class="archive-banner__title">
                    <?php
                    if ( is_post_type_archive( 'app' ) ) {
                        esc_html_e( 'All Apps', 'appforge' );
                    } else {
                        the_archive_title();
                    }
                    ?>
                </h1>
                <?php
                $desc = get_the_archive_description();
                if ( $desc ) : ?>
                <p class="archive-banner__desc"><?php echo wp_kses_post( $desc ); ?></p>
                <?php endif; ?>
                <span class="archive-banner__count">
                    <?php global $wp_query; printf( esc_html__( '%d items', 'appforge' ), $wp_query->found_posts ); ?>
                </span>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ( is_tax( 'app_category' ) ) :
    $cat_term  = get_queried_object();
    $cat_name  = $cat_term->name;
    $cat_link  = get_term_link( $cat_term );
    $initials  = strtoupper( mb_substr( $cat_name, 0, 2 ) );

    // ---- Top {Category}: most-downloaded, falling back to newest ----
    $top_base = array(
        'post_type'      => 'app',
        'posts_per_page' => 6,
        'tax_query'      => array( array(
            'taxonomy' => 'app_category',
            'field'    => 'term_id',
            'terms'    => $cat_term->term_id,
        ) ),
    );
    $top_apps = new WP_Query( array_merge( $top_base, array(
        'meta_key' => '_downloads',
        'orderby'  => 'meta_value_num',
        'order'    => 'DESC',
    ) ) );
    if ( ! $top_apps->have_posts() ) {
        $top_apps = new WP_Query( array_merge( $top_base, array(
            'orderby' => 'date',
            'order'   => 'DESC',
        ) ) );
    }

    // ---- Latest / Popular / Top Rated tabs, this category ----
    $tab_base_args = array(
        'post_type'      => 'app',
        'posts_per_page' => 6,
        'tax_query'      => array( array(
            'taxonomy' => 'app_category',
            'field'    => 'term_id',
            'terms'    => $cat_term->term_id,
        ) ),
    );
    $tab_queries = array(
        'latest'  => new WP_Query( array_merge( $tab_base_args, array( 'orderby' => 'date', 'order' => 'DESC' ) ) ),
        'popular' => new WP_Query( array_merge( $tab_base_args, array( 'meta_key' => '_downloads', 'orderby' => 'meta_value_num', 'order' => 'DESC' ) ) ),
        'rating'  => new WP_Query( array_merge( $tab_base_args, array( 'meta_key' => '_rating', 'orderby' => 'meta_value_num', 'order' => 'DESC' ) ) ),
    );
    $tab_labels = array(
        'latest'  => __( 'Latest', 'appforge' ),
        'popular' => __( 'Popular', 'appforge' ),
        'rating'  => __( 'Top Rated', 'appforge' ),
    );
    $any_tab_has_posts = $tab_queries['latest']->have_posts() || $tab_queries['popular']->have_posts() || $tab_queries['rating']->have_posts();
    ?>
<div class="container">
    <?php appforge_breadcrumbs(); ?>

    <!-- ===== CATEGORY HEADER ===== -->
    <section class="cat-header">
        <div class="cat-header__icon" aria-hidden="true"><?php echo esc_html( $initials ); ?></div>
        <div class="cat-header__body">
            <div class="cat-header__title-row">
                <h1 class="cat-header__title"><?php echo esc_html( $cat_name ); ?></h1>
                <span class="cat-header__count"><?php echo esc_html( sprintf( __( '%s Apps', 'appforge' ), number_format_i18n( $cat_term->count ) ) ); ?></span>
            </div>
            <?php $cat_desc = term_description( $cat_term, 'app_category' ); if ( $cat_desc ) : ?>
            <p class="cat-header__desc"><?php echo wp_kses_post( $cat_desc ); ?></p>
            <?php else : ?>
            <p class="cat-header__desc"><?php echo esc_html( sprintf( __( 'Discover the best %s apps across all genres. Download the top picks for free.', 'appforge' ), strtolower( $cat_name ) ) ); ?></p>
            <?php endif; ?>
            <div class="cat-header__badges">
                <div class="cat-badge">
                    <span class="cat-badge__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg></span>
                    <span>
                        <strong><?php esc_html_e( '100% Safe', 'appforge' ); ?></strong>
                        <small><?php esc_html_e( 'Scanned & verified', 'appforge' ); ?></small>
                    </span>
                </div>
                <div class="cat-badge">
                    <span class="cat-badge__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><polyline points="7 11 12 16 17 11"/><path d="M5 21h14"/></svg></span>
                    <span>
                        <strong><?php esc_html_e( 'Fast Downloads', 'appforge' ); ?></strong>
                        <small><?php esc_html_e( 'High speed servers', 'appforge' ); ?></small>
                    </span>
                </div>
                <div class="cat-badge">
                    <span class="cat-badge__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg></span>
                    <span>
                        <strong><?php esc_html_e( 'Regular Updates', 'appforge' ); ?></strong>
                        <small><?php esc_html_e( 'Latest game versions', 'appforge' ); ?></small>
                    </span>
                </div>
                <div class="cat-badge">
                    <span class="cat-badge__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></span>
                    <span>
                        <strong><?php esc_html_e( "Editor's Choice", 'appforge' ); ?></strong>
                        <small><?php esc_html_e( 'Handpicked games', 'appforge' ); ?></small>
                    </span>
                </div>
            </div>
        </div>
        <div class="cat-header__deco" aria-hidden="true"><?php echo appforge_category_icon( $cat_term ); ?></div>
    </section>

    <div class="page-layout">
        <div class="cat-main">

            <?php if ( $top_apps->have_posts() ) : ?>
            <!-- ===== TOP {CATEGORY} — carousel row ===== -->
            <section class="cat-section" id="catTopCarousel">
                <div class="cat-section__hd">
                    <h2 class="cat-section__title"><?php echo esc_html( sprintf( __( 'Top %s', 'appforge' ), $cat_name ) ); ?></h2>
                    <div class="cat-section__hd-actions">
                        <a href="<?php echo esc_url( add_query_arg( 'sort', 'downloads', $cat_link ) ); ?>" class="cat-section__link"><?php esc_html_e( 'View All', 'appforge' ); ?></a>
                        <button type="button" class="cat-carousel-btn" data-cat-top-prev aria-label="<?php esc_attr_e( 'Previous', 'appforge' ); ?>">&#8249;</button>
                        <button type="button" class="cat-carousel-btn" data-cat-top-next aria-label="<?php esc_attr_e( 'Next', 'appforge' ); ?>">&#8250;</button>
                    </div>
                </div>
                <div class="cat-top-row" data-cat-top-track>
                    <?php while ( $top_apps->have_posts() ) : $top_apps->the_post();
                        get_template_part( 'template-parts/category-top-card' );
                    endwhile; wp_reset_postdata(); ?>
                </div>
            </section>
            <?php endif; ?>

            <?php if ( $any_tab_has_posts ) : ?>
            <!-- ===== LATEST / POPULAR / TOP RATED — list ===== -->
            <section class="cat-section">
                <div class="cat-section__hd cat-section__hd--tabs">
                    <h2 class="cat-section__title"><?php echo esc_html( sprintf( __( 'Latest %s', 'appforge' ), $cat_name ) ); ?></h2>
                    <div class="cat-tabs" role="tablist">
                        <?php $first = true; foreach ( $tab_labels as $key => $label ) : ?>
                        <button type="button" class="cat-tab<?php echo $first ? ' active' : ''; ?>" role="tab" aria-selected="<?php echo $first ? 'true' : 'false'; ?>" data-tab="<?php echo esc_attr( $key ); ?>">
                            <?php echo esc_html( $label ); ?>
                        </button>
                        <?php $first = false; endforeach; ?>
                    </div>
                </div>
                <?php $first = true; foreach ( $tab_queries as $key => $tq ) :
                    if ( ! $tq->have_posts() ) { continue; }
                ?>
                <div class="cat-list cat-tab-panel<?php echo $first ? ' active' : ''; ?>" data-panel="<?php echo esc_attr( $key ); ?>" <?php echo $first ? '' : 'hidden'; ?>>
                    <?php while ( $tq->have_posts() ) : $tq->the_post();
                        get_template_part( 'template-parts/category-list-row' );
                    endwhile; wp_reset_postdata(); ?>
                </div>
                <?php $first = false; endforeach; ?>
                <div class="cat-section__more">
                    <a href="<?php echo esc_url( $cat_link ); ?>" class="btn btn-outline"><?php echo esc_html( sprintf( __( 'View All %s', 'appforge' ), $cat_name ) ); ?> &rarr;</a>
                </div>
            </section>
            <?php endif; ?>

            <?php if ( ! $top_apps->have_posts() && ! $any_tab_has_posts ) : ?>
            <?php get_template_part( 'template-parts/content', 'none' ); ?>
            <?php endif; ?>

            <!-- ===== NEWSLETTER ===== -->
            <section class="cat-newsletter">
                <div class="cat-newsletter__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M22 6l-10 7L2 6"/></svg>
                </div>
                <div class="cat-newsletter__body">
                    <h3><?php echo esc_html( sprintf( __( 'Stay Updated with New %s', 'appforge' ), $cat_name ) ); ?></h3>
                    <p><?php echo esc_html( sprintf( __( 'Get notified about new %s releases and updates.', 'appforge' ), strtolower( $cat_name ) ) ); ?></p>
                </div>
                <form class="cat-newsletter__form" method="post" action="">
                    <label for="cat-newsletter-email" class="sr-only"><?php esc_html_e( 'Email address', 'appforge' ); ?></label>
                    <input type="email" id="cat-newsletter-email" name="email" placeholder="<?php esc_attr_e( 'Enter your email', 'appforge' ); ?>" required>
                    <button type="submit" class="btn btn-primary"><?php esc_html_e( 'Subscribe', 'appforge' ); ?></button>
                </form>
            </section>

        </div><!-- .cat-main -->

        <!-- ===== SIDEBAR ===== -->
        <aside class="sidebar" id="secondary" aria-label="<?php esc_attr_e( 'Sidebar', 'appforge' ); ?>">

            <?php
            $cat_all = get_terms( array(
                'taxonomy'   => 'app_category',
                'orderby'    => 'count',
                'order'      => 'DESC',
                'hide_empty' => true,
                'number'     => 10,
            ) );
            if ( $cat_all && ! is_wp_error( $cat_all ) ) : ?>
            <div class="widget">
                <div class="widget__header">
                    <h3 class="widget__title"><?php esc_html_e( 'Categories', 'appforge' ); ?></h3>
                </div>
                <ul class="widget-categories">
                    <?php foreach ( $cat_all as $c ) : ?>
                    <li>
                        <a href="<?php echo esc_url( get_term_link( $c ) ); ?>" <?php echo $c->term_id === $cat_term->term_id ? ' aria-current="page"' : ''; ?>>
                            <?php echo esc_html( $c->name ); ?>
                            <span class="cat-count"><?php echo esc_html( $c->count ); ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="widget__footer">
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'app' ) ); ?>"><?php esc_html_e( 'View All Categories', 'appforge' ); ?></a>
                </div>
            </div>
            <?php endif; ?>

            <?php
            $popular_sw = new WP_Query( array(
                'post_type'      => 'app',
                'posts_per_page' => 5,
                'meta_key'       => '_app_views',
                'orderby'        => 'meta_value_num',
                'order'          => 'DESC',
                'tax_query'      => array( array(
                    'taxonomy' => 'app_category',
                    'field'    => 'term_id',
                    'terms'    => $cat_term->term_id,
                ) ),
            ) );
            if ( $popular_sw->have_posts() ) : ?>
            <div class="widget">
                <div class="widget__header">
                    <h3 class="widget__title"><?php echo esc_html( sprintf( __( 'Popular %s', 'appforge' ), $cat_name ) ); ?></h3>
                </div>
                <div class="widget-app-list">
                    <?php while ( $popular_sw->have_posts() ) : $popular_sw->the_post();
                        get_template_part( 'template-parts/app-card', 'small' );
                    endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
            <?php endif; ?>

            <?php
            $pop_tags = get_terms( array(
                'taxonomy'   => 'post_tag',
                'orderby'    => 'count',
                'order'      => 'DESC',
                'number'     => 15,
                'hide_empty' => true,
            ) );
            if ( $pop_tags && ! is_wp_error( $pop_tags ) ) : ?>
            <div class="widget">
                <div class="widget__header">
                    <h3 class="widget__title"><?php esc_html_e( 'Tags', 'appforge' ); ?></h3>
                </div>
                <div class="widget-tag-cloud">
                    <?php foreach ( $pop_tags as $t ) : ?>
                    <a href="<?php echo esc_url( get_tag_link( $t->term_id ) ); ?>" class="tag-pill"><?php echo esc_html( $t->name ); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </aside>
    </div>

    <!-- ===== TRUST STRIP ===== -->
    <div class="cat-trust-strip">
        <div class="cat-trust-item">
            <span class="cat-trust-item__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg></span>
            <span>
                <strong><?php esc_html_e( 'Safe & Secure', 'appforge' ); ?></strong>
                <small><?php esc_html_e( 'All apps are scanned and verified', 'appforge' ); ?></small>
            </span>
        </div>
        <div class="cat-trust-item">
            <span class="cat-trust-item__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><polyline points="7 11 12 16 17 11"/><path d="M5 21h14"/></svg></span>
            <span>
                <strong><?php esc_html_e( 'Fast Downloads', 'appforge' ); ?></strong>
                <small><?php esc_html_e( 'High speed servers for the best experience', 'appforge' ); ?></small>
            </span>
        </div>
        <div class="cat-trust-item">
            <span class="cat-trust-item__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
            <span>
                <strong><?php esc_html_e( 'Latest Versions', 'appforge' ); ?></strong>
                <small><?php esc_html_e( "Get updates as soon as they're available", 'appforge' ); ?></small>
            </span>
        </div>
        <div class="cat-trust-item">
            <span class="cat-trust-item__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
            <span>
                <strong><?php esc_html_e( '100% Free', 'appforge' ); ?></strong>
                <small><?php esc_html_e( 'All apps are completely free to download', 'appforge' ); ?></small>
            </span>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ( ! is_tax( 'app_category' ) ) : ?>
<div class="container">
    <?php appforge_breadcrumbs(); ?>
    <?php if ( is_post_type_archive( 'app' ) ) : ?>
        <?php get_template_part( 'template-parts/app-filter-bar' ); ?>
    <?php endif; ?>
    <div class="page-layout">
        <div>
            <?php if ( have_posts() ) : ?>
            <div class="<?php echo is_post_type_archive( 'app' ) ? 'apps-grid' : 'posts-grid'; ?>">
                <?php while ( have_posts() ) : the_post();
                    if ( get_post_type() === 'app' ) {
                        get_template_part( 'template-parts/app-card' );
                    } else {
                        get_template_part( 'template-parts/content', 'post' );
                    }
                endwhile; ?>
            </div>
            <?php the_posts_pagination( array(
                'prev_text' => '← ' . __( 'Previous', 'appforge' ),
                'next_text' => __( 'Next', 'appforge' ) . ' →',
            ) ); ?>
            <?php else : ?>
            <?php get_template_part( 'template-parts/content', 'none' ); ?>
            <?php endif; ?>
        </div>
        <?php get_sidebar(); ?>
    </div>
</div>
<?php endif; ?>

<?php get_footer(); ?>
