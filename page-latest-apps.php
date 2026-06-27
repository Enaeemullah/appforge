<?php
/*
Template Name: Latest Apps
*/
get_header(); ?>

<div class="container">
    <h1 class="page-title">🔄 Latest Update Games & Apps</h1>
    <p class="page-subtitle">Recently added and updated apps</p>
    
    <div class="apps-grid">
        <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $latest = array(
            'post_type' => 'app',
            'posts_per_page' => 12,
            'paged' => $paged,
            'orderby' => 'date',
            'order' => 'DESC'
        );
        $query = new WP_Query($latest);
        
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
            <p>No apps yet. <a href="<?php echo admin_url('post-new.php?post_type=app'); ?>">Add your first app</a></p>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>