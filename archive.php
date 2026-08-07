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
    <?php if ( $featured_apps->have_posts() ) : ?>
    <section class="homepage-carousel category-hero-carousel" aria-label="<?php echo esc_attr( sprintf( __( 'Featured %s apps', 'appforge' ), $cat_term->name ) ); ?>">
        <div class="carousel-container" id="featuredCarousel" aria-live="polite">
            <?php
            $slide_index = 0;
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
                    <p>
                        <?php
                        $slide_rating = appforge_get_rating();
                        if ( $slide_rating ) echo appforge_stars( $slide_rating ) . ' &middot; '; // phpcs:ignore
                        $slide_downloads = appforge_get_downloads();
                        if ( $slide_downloads ) echo esc_html( sprintf( __( '%s downloads', 'appforge' ), $slide_downloads ) );
                        ?>
                    </p>
                </div>
            </div>
            <?php $slide_index++; endwhile; wp_reset_postdata(); ?>

            <?php if ( $slide_index > 1 ) : ?>
            <button class="carousel-btn carousel-btn--prev" id="carouselPrev" aria-label="<?php esc_attr_e( 'Previous slide', 'appforge' ); ?>">&#8249;</button>
            <button class="carousel-btn carousel-btn--next" id="carouselNext" aria-label="<?php esc_attr_e( 'Next slide', 'appforge' ); ?>">&#8250;</button>
            <div class="carousel-dots" role="tablist" aria-label="<?php esc_attr_e( 'Slide navigation', 'appforge' ); ?>">
                <?php for ( $i = 0; $i < $slide_index; $i++ ) : ?>
                <button class="carousel-dot <?php echo $i === 0 ? 'active' : ''; ?>" role="tab"
                        aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                        aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'appforge' ), $i + 1 ) ); ?>"
                        data-slide="<?php echo esc_attr( $i ); ?>"></button>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php wp_reset_postdata(); endif; ?>

    <div class="category-hero-info">
        <h1 class="category-hero-info__title"><?php the_archive_title(); ?></h1>
        <?php $cat_desc = get_the_archive_description();
        if ( $cat_desc ) : ?>
        <p class="category-hero-info__desc"><?php echo wp_kses_post( $cat_desc ); ?></p>
        <?php endif; ?>
        <span class="category-hero-info__count">
            <?php global $wp_query; printf( esc_html__( '%d apps', 'appforge' ), $wp_query->found_posts ); ?>
        </span>
    </div>
</div>
<?php endif; ?>

<div class="container">
    <?php appforge_breadcrumbs(); ?>
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
