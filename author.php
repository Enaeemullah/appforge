<?php
/**
 * Author Archive Template
 */
$author = get_queried_object();
get_header();
?>

<div class="container">
    <?php appforge_breadcrumbs(); ?>

    <!-- Author Profile Card -->
    <div class="author-profile-card" style="margin-top:24px;">
        <div class="author-profile-card__inner">
            <div class="author-profile-card__avatar">
                <?php echo get_avatar( $author->ID, 100, '', esc_attr( $author->display_name ), array( 'class' => '' ) ); ?>
            </div>
            <div>
                <p class="author-profile-card__role"><?php esc_html_e( 'Author', 'appforge' ); ?></p>
                <h1 class="author-profile-card__name"><?php echo esc_html( $author->display_name ); ?></h1>
                <?php if ( $author->description ) : ?>
                <p class="author-profile-card__bio"><?php echo esc_html( $author->description ); ?></p>
                <?php endif; ?>
                <div style="display:flex;gap:24px;flex-wrap:wrap;">
                    <div>
                        <dt style="font-size:24px;font-weight:800;color:var(--gray-900);"><?php echo esc_html( count_user_posts( $author->ID ) ); ?></dt>
                        <dd style="font-size:11px;color:var(--gray-500);"><?php esc_html_e( 'Posts', 'appforge' ); ?></dd>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-layout">
        <div>
            <div class="section__header" style="padding-bottom:0;">
                <h2 class="section__title"><?php printf( esc_html__( 'Posts by %s', 'appforge' ), esc_html( $author->display_name ) ); ?></h2>
            </div>

            <?php if ( have_posts() ) : ?>
            <div class="posts-grid" style="margin-top:24px;">
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
