<?php
/**
 * Template Part: App Filter Bar
 * GET-based category / OS / pricing / sort filters. Reused on the app
 * archive, app_category archives, and the curated Hot/Latest/Popular/Top
 * app pages.
 */

$show_category = ! is_tax( 'app_category' );
$current_cat   = isset( $_GET['app_category'] ) ? sanitize_title( wp_unslash( $_GET['app_category'] ) ) : '';
$current_os    = isset( $_GET['os'] )       ? sanitize_key( wp_unslash( $_GET['os'] ) )      : '';
$current_price = isset( $_GET['pricing'] )  ? sanitize_key( wp_unslash( $_GET['pricing'] ) ) : '';
$current_sort  = isset( $_GET['sort'] )     ? sanitize_key( wp_unslash( $_GET['sort'] ) )    : '';

$has_filters = $current_cat || $current_os || $current_price || $current_sort;

$cat_terms = $show_category ? get_terms( array(
    'taxonomy'   => 'app_category',
    'hide_empty' => true,
) ) : array();
?>
<form class="app-filter-bar" method="get">

    <?php if ( $show_category && $cat_terms && ! is_wp_error( $cat_terms ) ) : ?>
    <select name="app_category" class="app-filter-bar__select" onchange="this.form.submit()">
        <option value=""><?php esc_html_e( 'All Categories', 'appforge' ); ?></option>
        <?php foreach ( $cat_terms as $term ) : ?>
        <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $current_cat, $term->slug ); ?>>
            <?php echo esc_html( $term->name ); ?>
        </option>
        <?php endforeach; ?>
    </select>
    <?php endif; ?>

    <select name="os" class="app-filter-bar__select" onchange="this.form.submit()">
        <option value=""><?php esc_html_e( 'All Platforms', 'appforge' ); ?></option>
        <?php foreach ( appforge_app_os_options() as $val => $label ) : ?>
        <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $current_os, $val ); ?>>
            <?php echo esc_html( $label ); ?>
        </option>
        <?php endforeach; ?>
    </select>

    <select name="pricing" class="app-filter-bar__select" onchange="this.form.submit()">
        <option value=""><?php esc_html_e( 'Free & Paid', 'appforge' ); ?></option>
        <option value="free" <?php selected( $current_price, 'free' ); ?>><?php esc_html_e( 'Free', 'appforge' ); ?></option>
        <option value="paid" <?php selected( $current_price, 'paid' ); ?>><?php esc_html_e( 'Paid', 'appforge' ); ?></option>
    </select>

    <select name="sort" class="app-filter-bar__select" onchange="this.form.submit()">
        <option value=""><?php esc_html_e( 'Default Order', 'appforge' ); ?></option>
        <option value="newest"    <?php selected( $current_sort, 'newest' ); ?>><?php esc_html_e( 'Newest', 'appforge' ); ?></option>
        <option value="rating"    <?php selected( $current_sort, 'rating' ); ?>><?php esc_html_e( 'Highest Rated', 'appforge' ); ?></option>
        <option value="downloads" <?php selected( $current_sort, 'downloads' ); ?>><?php esc_html_e( 'Most Downloaded', 'appforge' ); ?></option>
        <option value="name"      <?php selected( $current_sort, 'name' ); ?>><?php esc_html_e( 'Name (A-Z)', 'appforge' ); ?></option>
    </select>

    <button type="submit" class="app-filter-bar__apply"><?php esc_html_e( 'Filter', 'appforge' ); ?></button>

    <?php if ( $has_filters ) : ?>
    <a href="<?php echo esc_url( remove_query_arg( array( 'app_category', 'os', 'pricing', 'sort', 'paged' ) ) ); ?>" class="app-filter-bar__clear">
        <?php esc_html_e( 'Clear filters', 'appforge' ); ?>
    </a>
    <?php endif; ?>

</form>
