<?php get_header(); ?>

<div class="container">
    <?php appforge_breadcrumbs(); ?>

    <div class="page-layout">
        <!-- Main Content -->
        <div>
            <div class="section__header" style="padding:32px 0 0;">
                <div>
                    <h1 class="section__title"><?php esc_html_e( 'All Apps', 'appforge' ); ?></h1>
                    <p class="section__subtitle"><?php esc_html_e( 'Browse our full collection of apps', 'appforge' ); ?></p>
                </div>
            </div>

            <div class="apps-grid" style="margin-top:24px;">
                <?php
                $apps_query = new WP_Query( array(
                    'post_type'      => 'app',
                    'posts_per_page' => 12,
                    'paged'          => get_query_var( 'paged', 1 ),
                ) );

                if ( $apps_query->have_posts() ) :
                    while ( $apps_query->have_posts() ) : $apps_query->the_post();
                        get_template_part( 'template-parts/app-card' );
                    endwhile;
                    wp_reset_postdata();
                else :
                    get_template_part( 'template-parts/content', 'none' );
                endif;
                ?>
            </div>

            <?php
            echo paginate_links( array(
                'total'   => isset( $apps_query ) ? $apps_query->max_num_pages : 1,
                'current' => get_query_var( 'paged', 1 ),
                'prev_text' => '← ' . __( 'Previous', 'appforge' ),
                'next_text' => __( 'Next', 'appforge' ) . ' →',
            ) );
            ?>
        </div>

        <!-- Sidebar -->
        <?php get_sidebar(); ?>
    </div><!-- .page-layout -->
</div><!-- .container -->

<?php get_footer(); ?>
