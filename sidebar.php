<?php
/**
 * Sidebar Template
 */
?>

<aside class="sidebar" id="secondary" aria-label="<?php esc_attr_e( 'Sidebar', 'appforge' ); ?>">

    <?php if ( is_active_sidebar( 'sidebar-main' ) ) :
        dynamic_sidebar( 'sidebar-main' );
    else : ?>

    <!-- Default sidebar when no widgets are set -->

    <!-- About Widget -->
    <div class="widget">
        <div class="widget__header">
            <h3 class="widget__title"><?php esc_html_e( 'About', 'appforge' ); ?></h3>
        </div>
        <div class="widget-about">
            <?php if ( has_custom_logo() ) : ?>
            <div class="widget-about__logo">
                <?php the_custom_logo(); ?>
            </div>
            <?php else : ?>
            <div class="widget-about__logo" aria-hidden="true">⚡</div>
            <h4 class="widget-about__name">App<span><?php esc_html_e( 'Forge', 'appforge' ); ?></span></h4>
            <?php endif; ?>
            <p class="widget-about__desc">
                <?php
                echo esc_html( AppForge_Panel::get(
                    'footer_about',
                    'Your trusted source for safe, fast, and free APK downloads. All apps are verified and tested.'
                ) );
                ?>
            </p>
        </div>
    </div>

    <!-- Search Widget -->
    <div class="widget">
        <div class="widget__header">
            <h3 class="widget__title"><?php esc_html_e( 'Search', 'appforge' ); ?></h3>
        </div>
        <div class="widget-search">
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-input-group">
                <label for="sidebar-search" class="sr-only"><?php esc_html_e( 'Search apps', 'appforge' ); ?></label>
                <input type="search" id="sidebar-search" name="s"
                       placeholder="<?php esc_attr_e( 'Search apps…', 'appforge' ); ?>" autocomplete="off">
                <input type="hidden" name="post_type" value="app">
                <button type="submit">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <?php esc_html_e( 'Go', 'appforge' ); ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Categories Widget -->
    <?php
    $cats = get_terms( array(
        'taxonomy'   => 'app_category',
        'orderby'    => 'count',
        'order'      => 'DESC',
        'hide_empty' => true,
        'number'     => 10,
    ) );
    if ( $cats && ! is_wp_error( $cats ) ) : ?>
    <div class="widget">
        <div class="widget__header">
            <h3 class="widget__title"><?php esc_html_e( 'Categories', 'appforge' ); ?></h3>
        </div>
        <ul class="widget-categories">
            <?php foreach ( $cats as $cat ) : ?>
            <li>
                <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
                    <?php echo esc_html( $cat->name ); ?>
                    <span class="cat-count"><?php echo esc_html( $cat->count ); ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Popular Apps Widget -->
    <?php
    $popular = new WP_Query( array(
        'post_type'      => 'app',
        'posts_per_page' => 5,
        'meta_key'       => '_app_views',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    ) );
    if ( $popular->have_posts() ) : ?>
    <div class="widget">
        <div class="widget__header">
            <h3 class="widget__title"><?php esc_html_e( 'Popular Apps', 'appforge' ); ?></h3>
        </div>
        <div class="widget-app-list">
            <?php while ( $popular->have_posts() ) : $popular->the_post();
                get_template_part( 'template-parts/app-card', 'small' );
            endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Popular Tags Widget -->
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
            <h3 class="widget__title"><?php esc_html_e( 'Popular Tags', 'appforge' ); ?></h3>
        </div>
        <div class="widget-tag-cloud">
            <?php foreach ( $pop_tags as $t ) : ?>
            <a href="<?php echo esc_url( get_tag_link( $t->term_id ) ); ?>" class="tag-pill"><?php echo esc_html( $t->name ); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; // end !is_active_sidebar ?>

</aside>
