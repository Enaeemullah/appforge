<?php
/**
 * Date Archive Template
 */
get_header();
?>

<div class="container">
    <?php appforge_breadcrumbs(); ?>

    <!-- Date Archive Header -->
    <div style="display:flex;align-items:center;gap:24px;padding:32px 0;">
        <?php if ( is_year() ) : ?>
        <div style="background:var(--gradient-primary);border-radius:var(--radius-xl);padding:16px 24px;text-align:center;color:#fff;flex-shrink:0;">
            <div style="font-size:40px;font-weight:800;line-height:1;"><?php echo esc_html( get_the_date( 'Y' ) ); ?></div>
        </div>
        <div>
            <p style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:1.5px;color:var(--color-primary);margin-bottom:8px;">
                <?php esc_html_e( 'Year Archive', 'appforge' ); ?>
            </p>
            <h1 style="font-size:32px;font-weight:800;"><?php printf( esc_html__( 'Posts from %s', 'appforge' ), get_the_date( 'Y' ) ); ?></h1>
        </div>
        <?php elseif ( is_month() ) : ?>
        <div style="background:var(--gradient-primary);border-radius:var(--radius-xl);padding:16px 24px;text-align:center;color:#fff;min-width:80px;flex-shrink:0;">
            <div style="font-size:13px;font-weight:600;opacity:.85;text-transform:uppercase;"><?php echo esc_html( get_the_date( 'M' ) ); ?></div>
            <div style="font-size:40px;font-weight:800;line-height:1;"><?php echo esc_html( get_the_date( 'Y' ) ); ?></div>
        </div>
        <div>
            <p style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:1.5px;color:var(--color-primary);margin-bottom:8px;">
                <?php esc_html_e( 'Monthly Archive', 'appforge' ); ?>
            </p>
            <h1 style="font-size:32px;font-weight:800;"><?php echo esc_html( get_the_date( 'F Y' ) ); ?></h1>
        </div>
        <?php else : ?>
        <div>
            <p style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:1.5px;color:var(--color-primary);margin-bottom:8px;">
                <?php esc_html_e( 'Daily Archive', 'appforge' ); ?>
            </p>
            <h1 style="font-size:32px;font-weight:800;"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></h1>
        </div>
        <?php endif; ?>
    </div>

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
