<?php
/*
Template Name: Top Rated Apps
*/
get_header(); ?>

<div class="container">
    <h1 class="page-title">⭐ Top Rated Apps</h1>
    <p class="page-subtitle">Highest rated apps by our community</p>

    <?php get_template_part( 'template-parts/app-filter-bar' ); ?>

    <div class="apps-grid">
        <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $top_rated = array(
            'post_type' => 'app',
            'posts_per_page' => 12,
            'paged' => $paged,
            'meta_key' => '_rating',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'meta_query' => array(
                array(
                    'key' => '_rating',
                    'value' => 0,
                    'compare' => '>'
                )
            )
        );
        $top_rated = appforge_app_filter_args( $top_rated );
        $query = new WP_Query($top_rated);
        
        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post();
                get_template_part('template-parts/app-card');
            endwhile;
            
            echo '<div class="pagination">';
            echo paginate_links(array(
                'total' => $query->max_num_pages,
                'current' => $paged,
                'prev_text' => '« Previous',
                'next_text' => 'Next »'
            ));
            echo '</div>';
            
            wp_reset_postdata();
        else : ?>
            <p>No rated apps yet. Add ratings to your apps!</p>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>