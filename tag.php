<?php
/**
 * Tag Archive Template
 */
$tag = get_queried_object();
get_header();
?>

<div class="archive-banner">
    <div class="container">
        <div class="archive-banner__inner">
            <div class="archive-banner__icon" aria-hidden="true">🏷️</div>
            <div>
                <p class="archive-banner__type"><?php esc_html_e( 'Tag', 'appforge' ); ?></p>
                <h1 class="archive-banner__title"><?php single_tag_title( '#' ); ?></h1>
                <?php if ( tag_description() ) : ?>
                <p class="archive-banner__desc"><?php echo wp_kses_post( tag_description() ); ?></p>
                <?php endif; ?>
                <span class="archive-banner__count">
                    <?php printf( esc_html__( '%d posts', 'appforge' ), $tag->count ); ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php appforge_breadcrumbs(); ?>

    <!-- Related tags -->
    <?php
    $related_tags = get_terms( array(
        'taxonomy'   => 'post_tag',
        'number'     => 12,
        'orderby'    => 'count',
        'order'      => 'DESC',
        'exclude'    => $tag->term_id,
        'hide_empty' => true,
    ) );
    if ( $related_tags && ! is_wp_error( $related_tags ) ) : ?>
    <div style="padding: 16px 0;">
        <div class="tags-cloud">
            <?php foreach ( $related_tags as $t ) : ?>
            <a href="<?php echo esc_url( get_tag_link( $t->term_id ) ); ?>" class="tag-pill"><?php echo esc_html( $t->name ); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="page-layout">
        <div>
            <?php if ( have_posts() ) : ?>
            <div class="posts-grid">
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
