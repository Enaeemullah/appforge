<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<div class="container">
    <?php appforge_breadcrumbs(); ?>

    <div class="page-layout">

        <!-- Main Article -->
        <article <?php post_class( 'single-post' ); ?> id="post-<?php the_ID(); ?>">

            <!-- Post Header Card -->
            <div class="single-post-header">

                <?php if ( has_post_thumbnail() ) : ?>
                <div class="single-post-thumb">
                    <?php the_post_thumbnail( 'hero-thumb', array( 'loading' => 'eager', 'alt' => get_the_title() ) ); ?>
                </div>
                <?php else : ?>
                <div class="single-post-thumb single-post-thumb--placeholder" aria-hidden="true">📰</div>
                <?php endif; ?>

                <div class="single-post-meta-bar">
                    <div class="post-meta">
                        <?php
                        $categories = get_the_category();
                        if ( $categories ) : ?>
                        <span>
                            <?php foreach ( $categories as $cat ) : ?>
                            <a href="<?php echo esc_url( get_category_link( $cat ) ); ?>"
                               class="badge badge-primary"><?php echo esc_html( $cat->name ); ?></a>
                            <?php endforeach; ?>
                        </span>
                        <?php endif; ?>
                        <span class="sep" aria-hidden="true">·</span>
                        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                            <?php echo esc_html( get_the_date() ); ?>
                        </time>
                        <span class="sep" aria-hidden="true">·</span>
                        <span><?php echo esc_html( get_the_author() ); ?></span>
                    </div>
                    <div>
                        <?php the_tags( '<span class="post-meta">', '', '</span>' ); ?>
                    </div>
                </div>

                <div class="single-post-body">
                    <h1 class="single-post-title"><?php the_title(); ?></h1>
                    <?php
                    $toc = appforge_content_with_toc();
                    appforge_render_toc( $toc['items'] );
                    ?>
                    <div class="entry-content">
                        <?php echo $toc['content']; // phpcs:ignore ?>
                    </div>

                    <!-- Page links for multi-page posts -->
                    <?php
                    wp_link_pages( array(
                        'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page navigation', 'appforge' ) . '"><span>' . esc_html__( 'Pages:', 'appforge' ) . '</span>',
                        'after'  => '</nav>',
                    ) );
                    ?>
                </div>

            </div><!-- .single-post-header -->

            <!-- Tags -->
            <?php
            $tags = get_the_tags();
            if ( $tags ) : ?>
            <div style="margin-bottom:24px;">
                <span class="text-muted" style="font-size:13px;margin-right:8px;"><?php esc_html_e( 'Tags:', 'appforge' ); ?></span>
                <?php foreach ( $tags as $tag ) : ?>
                <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="tag-pill"><?php echo esc_html( $tag->name ); ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Author Box -->
            <div class="author-box">
                <div class="author-box__avatar">
                    <?php echo get_avatar( get_the_author_meta( 'ID' ), 72, '', get_the_author(), array( 'class' => '' ) ); ?>
                </div>
                <div class="author-box__info">
                    <p class="author-box__label"><?php esc_html_e( 'Written by', 'appforge' ); ?></p>
                    <h4 class="author-box__name">
                        <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                            <?php the_author(); ?>
                        </a>
                    </h4>
                    <?php if ( get_the_author_meta( 'description' ) ) : ?>
                    <p class="author-box__bio"><?php the_author_meta( 'description' ); ?></p>
                    <?php endif; ?>
                    <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"
                       class="btn btn-sm btn-outline">
                        <?php esc_html_e( 'View All Posts', 'appforge' ); ?>
                    </a>
                </div>
            </div>

            <!-- Post Navigation -->
            <nav class="post-navigation" aria-label="<?php esc_attr_e( 'Post navigation', 'appforge' ); ?>">
                <?php
                $prev = get_previous_post();
                $next = get_next_post();
                ?>
                <div class="nav-previous">
                    <?php if ( $prev ) : ?>
                    <a href="<?php echo esc_url( get_permalink( $prev ) ); ?>">
                        <span class="nav-label">← <?php esc_html_e( 'Previous Post', 'appforge' ); ?></span>
                        <span class="nav-title"><?php echo esc_html( get_the_title( $prev ) ); ?></span>
                    </a>
                    <?php endif; ?>
                </div>
                <div class="nav-next">
                    <?php if ( $next ) : ?>
                    <a href="<?php echo esc_url( get_permalink( $next ) ); ?>">
                        <span class="nav-label"><?php esc_html_e( 'Next Post', 'appforge' ); ?> →</span>
                        <span class="nav-title"><?php echo esc_html( get_the_title( $next ) ); ?></span>
                    </a>
                    <?php endif; ?>
                </div>
            </nav>

            <!-- Comments -->
            <?php
            if ( comments_open() || get_comments_number() ) {
                comments_template();
            }
            ?>

        </article>

        <!-- Sidebar -->
        <?php get_sidebar(); ?>

    </div><!-- .page-layout -->
</div><!-- .container -->

<?php endwhile; ?>
<?php get_footer(); ?>
