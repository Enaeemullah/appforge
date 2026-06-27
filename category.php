<?php
/**
 * Category Archive Template
 */
$category = get_queried_object();
get_header();
?>

<div class="archive-banner">
    <div class="container">
        <div class="archive-banner__inner">
            <div class="archive-banner__icon" aria-hidden="true">📂</div>
            <div>
                <p class="archive-banner__type"><?php esc_html_e( 'Category', 'appforge' ); ?></p>
                <h1 class="archive-banner__title"><?php single_cat_title(); ?></h1>
                <?php if ( category_description() ) : ?>
                <p class="archive-banner__desc"><?php echo wp_kses_post( category_description() ); ?></p>
                <?php endif; ?>
                <span class="archive-banner__count">
                    <?php printf( esc_html__( '%d posts', 'appforge' ), $category->count ); ?>
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
            <div class="posts-grid">
                <?php while ( have_posts() ) : the_post();
                    get_template_part( 'template-parts/content', 'post' );
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
