<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<div class="container">
    <?php appforge_breadcrumbs(); ?>

    <div class="page-layout">

        <article <?php post_class( 'page-content' ); ?> id="page-<?php the_ID(); ?>">

            <!-- Page Header -->
            <div class="single-post-header">
                <?php if ( has_post_thumbnail() ) : ?>
                <div class="single-post-thumb">
                    <?php the_post_thumbnail( 'hero-thumb', array( 'loading' => 'eager', 'alt' => '' ) ); ?>
                </div>
                <?php endif; ?>

                <div class="single-post-body">
                    <h1 class="single-post-title"><?php the_title(); ?></h1>
                    <div class="entry-content">
                        <?php
                        the_content();
                        wp_link_pages( array(
                            'before' => '<nav class="page-links">',
                            'after'  => '</nav>',
                        ) );
                        ?>
                    </div>
                </div>
            </div>

            <?php
            if ( comments_open() || get_comments_number() ) {
                comments_template();
            }
            ?>

        </article>

        <?php if ( is_active_sidebar( 'sidebar-main' ) ) : ?>
        <?php get_sidebar(); ?>
        <?php endif; ?>

    </div>
</div>

<?php endwhile; ?>
<?php get_footer(); ?>
