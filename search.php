<?php
/**
 * Search Results Template
 */
get_header();
?>

<div class="container">
    <?php appforge_breadcrumbs(); ?>

    <div class="search-results-header">
        <?php if ( have_posts() ) : ?>
        <h1>
            <?php
            printf(
                esc_html__( 'Results for: %s', 'appforge' ),
                '<span>' . esc_html( get_search_query() ) . '</span>'
            );
            ?>
        </h1>
        <p class="search-count">
            <?php
            global $wp_query;
            printf(
                esc_html( _n( '%d result found', '%d results found', $wp_query->found_posts, 'appforge' ) ),
                esc_html( $wp_query->found_posts )
            );
            ?>
        </p>
        <?php else : ?>
        <h1><?php printf( esc_html__( 'No results for: %s', 'appforge' ), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?></h1>
        <?php endif; ?>
    </div>

    <div class="page-layout">
        <div>
            <?php if ( have_posts() ) : ?>

            <!-- Separate apps and posts -->
            <?php
            $app_posts  = array();
            $blog_posts = array();
            while ( have_posts() ) : the_post();
                if ( get_post_type() === 'app' ) {
                    $app_posts[]  = get_the_ID();
                } else {
                    $blog_posts[] = get_the_ID();
                }
            endwhile;
            rewind_posts();
            ?>

            <?php if ( ! empty( $app_posts ) ) : ?>
            <div style="margin-bottom:40px;">
                <h2 style="font-size:20px;font-weight:700;margin-bottom:20px;padding-bottom:12px;border-bottom:2px solid var(--gray-200);">
                    <?php printf( esc_html__( 'Apps (%d)', 'appforge' ), count( $app_posts ) ); ?>
                </h2>
                <div class="apps-grid">
                    <?php foreach ( $app_posts as $app_id ) :
                        $post = get_post( $app_id );
                        setup_postdata( $post );
                        get_template_part( 'template-parts/app-card' );
                    endforeach;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ( ! empty( $blog_posts ) ) : ?>
            <div>
                <h2 style="font-size:20px;font-weight:700;margin-bottom:20px;padding-bottom:12px;border-bottom:2px solid var(--gray-200);">
                    <?php printf( esc_html__( 'Articles (%d)', 'appforge' ), count( $blog_posts ) ); ?>
                </h2>
                <div class="posts-grid">
                    <?php foreach ( $blog_posts as $post_id ) :
                        $post = get_post( $post_id );
                        setup_postdata( $post );
                        get_template_part( 'template-parts/content', 'post' );
                    endforeach;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
            <?php endif; ?>

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
