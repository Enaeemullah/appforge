<?php
/**
 * Comments Template — blog posts & pages.
 */

if ( post_password_required() ) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if ( have_comments() ) : ?>
    <h2 class="comments-title">
        <?php
        $comment_count = get_comments_number();
        printf(
            esc_html( _n( '%s Comment', '%s Comments', $comment_count, 'appforge' ) ),
            esc_html( number_format_i18n( $comment_count ) )
        );
        ?>
    </h2>

    <ol class="comment-list">
        <?php
        wp_list_comments( array(
            'style'       => 'ol',
            'short_ping'  => true,
            'avatar_size' => 44,
        ) );
        ?>
    </ol>

    <?php the_comments_pagination(); ?>
    <?php endif; ?>

    <?php if ( ! comments_open() && get_comments_number() ) : ?>
    <p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'appforge' ); ?></p>
    <?php endif; ?>

    <?php
    $commenter = wp_get_current_commenter();
    comment_form( array(
        'class_form'           => 'comment-form',
        'class_submit'         => 'btn-review-submit',
        'title_reply'          => __( 'Leave a Comment', 'appforge' ),
        'title_reply_to'       => __( 'Leave a Comment', 'appforge' ),
        'label_submit'         => __( 'Post Comment', 'appforge' ),
        'comment_notes_before' => '',
        'fields'               => array(
            'author' => '<div class="form-row"><div><label for="author">' . esc_html__( 'Name', 'appforge' ) . ' <span class="required">*</span></label>'
                . '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" required></div>'
                . '<div><label for="email">' . esc_html__( 'Email', 'appforge' ) . ' <span class="required">*</span></label>'
                . '<input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" required></div></div>',
            'url'    => '<p class="comment-form-url"><label for="url">' . esc_html__( 'Website', 'appforge' ) . '</label>'
                . '<input id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '"></p>',
        ),
    ) );
    ?>

</div><!-- #comments -->
