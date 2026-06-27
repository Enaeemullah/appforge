<?php
/*
Template Name: Popular Apps
*/
get_header(); ?>

<div class="container">
    <h1 class="page-title">🔥 Popular Apps</h1>
    <p class="page-subtitle">Most viewed apps in the last 24 hours</p>
    
    <div class="apps-grid">
        <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $popular = array(
            'post_type' => 'app',
            'posts_per_page' => 12,
            'paged' => $paged,
            'meta_key' => '_app_views',
            'orderby' => 'meta_value_num',
            'order' => 'DESC'
        );
        $query = new WP_Query($popular);
        
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
            <p>No popular apps yet.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.page-title {
    font-size: 40px;
    margin: 30px 0 10px;
}
.page-subtitle {
    color: #666;
    margin-bottom: 40px;
    font-size: 18px;
}
.pagination {
    text-align: center;
    margin: 50px 0;
}
.pagination a, .pagination span {
    display: inline-block;
    padding: 8px 16px;
    margin: 0 4px;
    background: white;
    border-radius: 6px;
    text-decoration: none;
    color: #333;
}
.pagination .current {
    background: #ff5722;
    color: white;
}
</style>

<?php get_footer(); ?>