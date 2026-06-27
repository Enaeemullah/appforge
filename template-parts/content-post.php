<?php
/**
 * Template Part: Blog Post Card
 * Used by: search.php, index.php (when post type = post)
 */
$categories = get_the_category();
?>

<article <?php post_class( 'post-card' ); ?> id="post-<?php the_ID(); ?>">

    <?php if ( has_post_thumbnail() ) : ?>
    <a href="<?php the_permalink(); ?>" class="post-card__thumb" aria-hidden="true" tabindex="-1">
        <?php the_post_thumbnail( 'card-thumb', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) ); ?>
        <?php if ( $categories ) : ?>
        <span class="post-card__category-badge"><?php echo esc_html( $categories[0]->name ); ?></span>
        <?php endif; ?>
    </a>
    <?php else : ?>
    <a href="<?php the_permalink(); ?>" class="post-card__thumb post-card__thumb--placeholder" aria-hidden="true" tabindex="-1">
        📰
        <?php if ( $categories ) : ?>
        <span class="post-card__category-badge"><?php echo esc_html( $categories[0]->name ); ?></span>
        <?php endif; ?>
    </a>
    <?php endif; ?>

    <div class="post-card__body">
        <h2 class="post-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>
        <p class="post-card__excerpt"><?php the_excerpt(); ?></p>
        <div class="post-card__meta">
            <div class="post-card__author">
                <?php echo get_avatar( get_the_author_meta( 'ID' ), 28, '', '', array( 'class' => '' ) ); ?>
                <span><?php the_author(); ?></span>
            </div>
            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                <?php echo esc_html( get_the_date() ); ?>
            </time>
            <a href="<?php the_permalink(); ?>" class="read-more" aria-label="<?php echo esc_attr( sprintf( __( 'Read more about %s', 'appforge' ), get_the_title() ) ); ?>">
                <?php esc_html_e( 'Read more →', 'appforge' ); ?>
            </a>
        </div>
    </div>

</article>
