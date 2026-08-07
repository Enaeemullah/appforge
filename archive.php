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
    $cat_term        = get_queried_object();
    $featured_base   = array(
        'post_type'      => 'app',
        'posts_per_page' => 5,
        'tax_query'      => array( array(
            'taxonomy' => 'app_category',
            'field'    => 'term_id',
            'terms'    => $cat_term->term_id,
        ) ),
    );
    // Prefer the most-downloaded apps; fall back to newest if none have a
    // download count yet (meta_key + orderby=meta_value_num otherwise
    // excludes posts lacking that meta entirely).
    $featured_apps = new WP_Query( array_merge( $featured_base, array(
        'meta_key' => '_downloads',
        'orderby'  => 'meta_value_num',
        'order'    => 'DESC',
    ) ) );
    if ( ! $featured_apps->have_posts() ) {
        $featured_apps = new WP_Query( array_merge( $featured_base, array(
            'orderby' => 'date',
            'order'   => 'DESC',
        ) ) );
    }
    ?>
<div class="container">
    <?php appforge_breadcrumbs(); ?>

    <?php if ( $featured_apps->have_posts() ) : ?>
    <section class="cat-hero-carousel" id="catHeroCarousel" aria-label="<?php echo esc_attr( sprintf( __( 'Featured %s apps', 'appforge' ), $cat_term->name ) ); ?>">
        <?php $slide_index = 0; if ( $featured_apps->post_count > 1 ) : ?>
        <button class="cat-hero-carousel__btn cat-hero-carousel__btn--prev" data-cat-hero-prev aria-label="<?php esc_attr_e( 'Previous', 'appforge' ); ?>">&#8249;</button>
        <button class="cat-hero-carousel__btn cat-hero-carousel__btn--next" data-cat-hero-next aria-label="<?php esc_attr_e( 'Next', 'appforge' ); ?>">&#8250;</button>
        <?php endif; ?>
        <div class="cat-hero-track" data-cat-hero-track aria-live="polite">
            <?php
            while ( $featured_apps->have_posts() ) : $featured_apps->the_post();
                $slide_dl_url = get_post_meta( get_the_ID(), '_download_url', true );
            ?>
            <div class="cat-hero-card" role="group"
                 aria-roledescription="slide"
                 aria-label="<?php echo esc_attr( $slide_index + 1 ); ?> of <?php echo esc_attr( $featured_apps->post_count ); ?>">
                <?php if ( has_post_thumbnail() ) : ?>
                    <img class="cat-hero-card__img"
                         src="<?php the_post_thumbnail_url( 'carousel' ); ?>"
                         alt="<?php the_title_attribute(); ?>"
                         loading="<?php echo $slide_index === 0 ? 'eager' : 'lazy'; ?>">
                <?php else : ?>
                    <div class="cat-hero-card__placeholder" aria-hidden="true">📱</div>
                <?php endif; ?>
                <div class="cat-hero-card__overlay">
                    <span class="cat-hero-card__name"><?php the_title(); ?></span>
                    <?php if ( $slide_dl_url ) : ?>
                    <a href="<?php echo esc_url( $slide_dl_url ); ?>" class="btn-carousel-dl" rel="nofollow noopener" target="_blank">
                        <?php esc_html_e( 'Download', 'appforge' ); ?>
                    </a>
                    <?php endif; ?>
                </div>
                <a href="<?php the_permalink(); ?>" class="cat-hero-card__link" aria-label="<?php the_title_attribute(); ?>"></a>
            </div>
            <?php $slide_index++; endwhile; wp_reset_postdata(); ?>
        </div>
    </section>
    <?php wp_reset_postdata(); endif; ?>

    <?php
    // ---- Hot [Category] Apps: highly rated + most downloaded, this category ----
    $hot_cat_apps = new WP_Query( array(
        'post_type'      => 'app',
        'posts_per_page' => 6,
        'meta_key'       => '_downloads',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
        'tax_query'      => array( array(
            'taxonomy' => 'app_category',
            'field'    => 'term_id',
            'terms'    => $cat_term->term_id,
        ) ),
        'meta_query'     => array( array(
            'key'     => '_rating',
            'value'   => 4,
            'compare' => '>=',
        ) ),
    ) );
    if ( $hot_cat_apps->have_posts() ) : ?>
    <div class="cat-section">
        <div class="cat-section__hd">
            <h2 class="cat-section__title"><?php echo esc_html( sprintf( __( 'Hot %s Apps in 24h', 'appforge' ), $cat_term->name ) ); ?></h2>
        </div>
        <div class="cat-app-grid">
            <?php while ( $hot_cat_apps->have_posts() ) : $hot_cat_apps->the_post();
                get_template_part( 'template-parts/category-app-row' );
            endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php
    // ---- Latest Update / Downloads / Rating tabs, this category ----
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
        'latest'    => new WP_Query( array_merge( $tab_base_args, array( 'orderby' => 'date', 'order' => 'DESC' ) ) ),
        'downloads' => new WP_Query( array_merge( $tab_base_args, array( 'meta_key' => '_downloads', 'orderby' => 'meta_value_num', 'order' => 'DESC' ) ) ),
        'rating'    => new WP_Query( array_merge( $tab_base_args, array( 'meta_key' => '_rating', 'orderby' => 'meta_value_num', 'order' => 'DESC' ) ) ),
    );
    $tab_labels = array(
        'latest'    => __( 'Latest Update', 'appforge' ),
        'downloads' => __( 'Downloads', 'appforge' ),
        'rating'    => __( 'Rating', 'appforge' ),
    );
    $any_tab_has_posts = $tab_queries['latest']->have_posts() || $tab_queries['downloads']->have_posts() || $tab_queries['rating']->have_posts();
    if ( $any_tab_has_posts ) : ?>
    <div class="cat-section">
        <div class="cat-section__hd cat-section__hd--tabs">
            <h2 class="cat-section__title"><?php echo esc_html( sprintf( __( 'Latest Update %s Apps', 'appforge' ), $cat_term->name ) ); ?></h2>
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
        <div class="cat-app-grid cat-tab-panel<?php echo $first ? ' active' : ''; ?>" data-panel="<?php echo esc_attr( $key ); ?>" <?php echo $first ? '' : 'hidden'; ?>>
            <?php while ( $tq->have_posts() ) : $tq->the_post();
                get_template_part( 'template-parts/category-app-row' );
            endwhile; wp_reset_postdata(); ?>
        </div>
        <?php $first = false; endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="container">
    <?php if ( ! is_tax( 'app_category' ) ) : ?>
        <?php appforge_breadcrumbs(); ?>
    <?php endif; ?>
    <?php if ( is_post_type_archive( 'app' ) || is_tax( 'app_category' ) ) : ?>
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

<?php get_footer(); ?>
