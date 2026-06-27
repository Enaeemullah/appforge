<?php get_header(); ?>

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

<div class="container">
    <?php appforge_breadcrumbs(); ?>
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
