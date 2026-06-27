<?php
/**
 * AppForge Pro — Rich Meta Boxes for App CPT
 * Matches Appyn-style admin UI.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// ================================================================
// REGISTER META BOXES
// ================================================================
add_action( 'add_meta_boxes', function () {
    $post_type = 'app';
    add_meta_box( 'af_mb_versions',  __( 'Versions', 'appforge' ),              'af_mb_versions_html',  $post_type, 'normal', 'high' );
    add_meta_box( 'af_mb_info',      __( 'App Information', 'appforge' ),        'af_mb_info_html',      $post_type, 'normal', 'high' );
    add_meta_box( 'af_mb_download',  __( 'Download links of app', 'appforge' ),  'af_mb_download_html',  $post_type, 'normal', 'high' );
    add_meta_box( 'af_mb_images',    __( 'App Images', 'appforge' ),             'af_mb_images_html',    $post_type, 'normal', 'default' );
    add_meta_box( 'af_mb_video',     __( 'App Video', 'appforge' ),              'af_mb_video_html',     $post_type, 'normal', 'default' );
} );

// ================================================================
// ADMIN ASSETS
// ================================================================
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) return;
    global $post;
    if ( ! $post || $post->post_type !== 'app' ) return;

    wp_enqueue_media();

    // CSS
    wp_add_inline_style( 'wp-admin', af_mb_css() );

    // JS — needs jQuery (wp-admin already loads it)
    wp_add_inline_script( 'jquery', af_mb_js() );
} );

// ================================================================
// SHARED CSS
// ================================================================
function af_mb_css() {
    return '
    /* ---- Meta box shared ---- */
    .af-mb { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }

    .af-field { margin-bottom: 14px; }
    .af-field label { display: block; font-size: 12px; font-weight: 600; color: #555;
                      text-transform: uppercase; letter-spacing: .4px; margin-bottom: 5px; }
    .af-input, .af-select, .af-textarea {
        width: 100%; border: 1px solid #ddd; border-radius: 4px;
        padding: 7px 10px; font-size: 13px; box-sizing: border-box;
    }
    .af-input:focus, .af-select:focus, .af-textarea:focus {
        border-color: #00C853; outline: none; box-shadow: 0 0 0 2px rgba(0,200,83,.15);
    }
    .af-textarea { min-height: 100px; resize: vertical; }
    .af-input-sm { max-width: 140px; }
    .af-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .af-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
    .af-row { padding: 12px 0; border-bottom: 1px solid #f0f0f0; }
    .af-row:last-child { border-bottom: none; }
    .af-radio-group, .af-check-group { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-top: 4px; }
    .af-radio-group label, .af-check-group label { font-weight: normal; text-transform: none;
                                                    letter-spacing: 0; display: flex; align-items: center; gap: 5px; font-size: 13px; color: #333; cursor: pointer; }
    .af-separator { height: 1px; background: #f0f0f0; margin: 14px 0; }

    /* ---- Versions table ---- */
    .af-versions-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 8px; }
    .af-versions-table th { background: #f8f8f8; padding: 8px 12px; text-align: left;
                             font-size: 12px; font-weight: 600; color: #555; border-bottom: 1px solid #e0e0e0; }
    .af-versions-table td { padding: 6px 8px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    .af-versions-table tr:last-child td { border-bottom: none; }
    .af-versions-table input { border: 1px solid #ddd; border-radius: 3px; padding: 5px 8px;
                                font-size: 12px; width: 100%; box-sizing: border-box; }
    .af-versions-table input:focus { border-color: #00C853; outline: none; }
    .af-versions-table .af-rm-row { cursor: pointer; color: #a00; border: none; background: none;
                                     font-size: 16px; padding: 0 4px; line-height: 1; }
    .af-no-versions { padding: 14px; color: #888; font-style: italic; font-size: 13px;
                      background: #fafafa; border: 1px dashed #ddd; border-radius: 4px; text-align: center; }

    /* ---- Download tabs ---- */
    .af-tabs { display: flex; gap: 2px; margin-bottom: 16px; border-bottom: 2px solid #e0e0e0; }
    .af-tab-btn { background: #f4f4f4; border: 1px solid #ddd; border-bottom: none;
                  padding: 8px 18px; font-size: 13px; font-weight: 500; cursor: pointer;
                  border-radius: 4px 4px 0 0; color: #555; transition: background .15s; }
    .af-tab-btn.active { background: #fff; border-bottom: 2px solid #fff; margin-bottom: -2px;
                          color: #00C853; font-weight: 700; border-top: 2px solid #00C853; }
    .af-tab-pane { display: none; }
    .af-tab-pane.active { display: block; }

    /* ---- Download links table ---- */
    .af-dl-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 8px; }
    .af-dl-table th { background: #f8f8f8; padding: 8px 12px; text-align: left;
                       font-size: 12px; font-weight: 600; color: #555; border-bottom: 1px solid #e0e0e0; }
    .af-dl-table td { padding: 6px 8px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    .af-dl-table tr:last-child td { border-bottom: none; }
    .af-dl-table input[type=url], .af-dl-table input[type=text] { border: 1px solid #ddd; border-radius: 3px;
        padding: 5px 8px; font-size: 12px; width: 100%; box-sizing: border-box; }
    .af-dl-table input:focus { border-color: #00C853; outline: none; }
    .af-drag-handle { cursor: grab; color: #aaa; font-size: 16px; padding: 0 4px; }
    .af-file-type { display: flex; gap: 14px; margin-bottom: 14px; }
    .af-file-type label { display: flex; align-items: center; gap: 5px; font-size: 13px; font-weight: normal; cursor: pointer; }
    .af-dl-note { font-size: 12px; color: #888; margin-bottom: 10px; }

    /* ---- App Images ---- */
    .af-img-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
    .af-img-row input[type=url] { flex: 1; border: 1px solid #ddd; border-radius: 3px;
                                    padding: 6px 10px; font-size: 12px; }
    .af-img-row input:focus { border-color: #00C853; outline: none; }
    .af-img-row .af-rm-row { cursor: pointer; color: #a00; border: none; background: none;
                               font-size: 16px; padding: 0 2px; }
    .af-img-preview { width: 48px; height: 48px; object-fit: cover; border-radius: 3px;
                       border: 1px solid #ddd; display: none; }
    .af-add-btn { background: none; border: 1px dashed #00C853; color: #00C853;
                  padding: 7px 16px; border-radius: 4px; font-size: 13px; font-weight: 600;
                  cursor: pointer; margin-top: 6px; transition: background .15s; }
    .af-add-btn:hover { background: rgba(0,200,83,.06); }

    /* ---- Free/Paid ---- */
    .af-price-wrap { display: flex; align-items: center; gap: 8px; }
    .af-price-input { max-width: 90px !important; }
    ';
}

// ================================================================
// SHARED JS
// ================================================================
function af_mb_js() {
    return <<<'JS'
(function($) {

    /* ---- Versions repeater ---- */
    var versionsIdx = 0;

    function afVersionsReindex() {
        $('#af-versions-tbody tr').each(function(i) {
            $(this).find('input').each(function() {
                this.name = this.name.replace(/\[\d+\]/, '[' + i + ']');
            });
        });
        versionsIdx = $('#af-versions-tbody tr').length;
        $('#af-no-versions').toggle($('#af-versions-tbody tr').length === 0);
    }

    $(document).on('click', '#af-add-version', function() {
        var idx = versionsIdx++;
        var tr = $('<tr>').html(
            '<td><input type="text" name="_versions[' + idx + '][version]" placeholder="1.0.0" /></td>' +
            '<td><input type="text" name="_versions[' + idx + '][title]" placeholder="What changed…" /></td>' +
            '<td><input type="text" name="_versions[' + idx + '][date]" placeholder="Jan 1, 2024" /></td>' +
            '<td style="text-align:center;"><button type="button" class="af-rm-row" title="Remove">✕</button></td>'
        );
        $('#af-versions-tbody').append(tr);
        $('#af-no-versions').hide();
    });

    $(document).on('click', '#af-versions-tbody .af-rm-row', function() {
        $(this).closest('tr').remove();
        afVersionsReindex();
    });

    /* ---- Download links repeater ---- */
    var dlIdx = parseInt($('#af-dl-links-tbody').data('idx') || 0);

    $(document).on('click', '#af-add-dl-link', function() {
        var idx = dlIdx++;
        var tr = $('<tr>').html(
            '<td style="width:24px;"><span class="af-drag-handle">⊕</span></td>' +
            '<td><input type="url" name="_download_links[' + idx + '][url]" placeholder="https://…" /></td>' +
            '<td><input type="text" name="_download_links[' + idx + '][text]" placeholder="Download v1.0" /></td>' +
            '<td style="text-align:center;"><label><input type="checkbox" name="_download_links[' + idx + '][follow]" value="1" /> Follow</label></td>' +
            '<td style="text-align:center;"><button type="button" class="af-rm-row" title="Remove">✕</button></td>'
        );
        $('#af-dl-links-tbody').append(tr);
    });

    $(document).on('click', '#af-dl-links-tbody .af-rm-row', function() {
        $(this).closest('tr').remove();
    });

    /* ---- Download tabs ---- */
    $(document).on('click', '.af-tab-btn', function() {
        var tab = $(this).data('tab');
        $('.af-tab-btn').removeClass('active');
        $('.af-tab-pane').removeClass('active');
        $(this).addClass('active');
        $('#' + tab).addClass('active');
        $('input[name="_download_type"]').val(tab);
    });

    /* ---- App Images repeater ---- */
    var imgIdx = parseInt($('#af-screenshots-wrap').data('idx') || 0);

    $(document).on('click', '#af-add-screenshot', function() {
        var idx = imgIdx++;
        var row = $('<div class="af-img-row">').html(
            '<input type="url" name="_app_screenshots[' + idx + ']" placeholder="https://… or Upload" />' +
            '<button type="button" class="button button-small af-upload-img">Upload</button>' +
            '<button type="button" class="af-rm-row" title="Remove">✕</button>' +
            '<img class="af-img-preview" src="" alt="" />'
        );
        $('#af-screenshots-wrap').append(row);
    });

    $(document).on('click', '#af-screenshots-wrap .af-rm-row', function() {
        $(this).closest('.af-img-row').remove();
    });

    /* ---- Media uploader ---- */
    var mediaFrame = null;
    $(document).on('click', '.af-upload-img', function(e) {
        e.preventDefault();
        var $input = $(this).prev('input[type=url]');
        var $preview = $(this).siblings('.af-img-preview');

        if (mediaFrame) mediaFrame.close();
        mediaFrame = wp.media({
            title: 'Select Image',
            button: { text: 'Use this image' },
            multiple: false,
            library: { type: 'image' }
        });
        mediaFrame.on('select', function() {
            var a = mediaFrame.state().get('selection').first().toJSON();
            $input.val(a.url);
            if ($preview.length) {
                $preview.attr('src', a.url).show();
            }
        });
        mediaFrame.open();
    });

    /* ---- Show image preview on load ---- */
    $('.af-img-row input[type=url]').each(function() {
        var v = $(this).val();
        if (v) $(this).siblings('.af-img-preview').attr('src', v).show();
    });

    /* ---- Free / Payment toggle ---- */
    $(document).on('change', 'input[name="_app_pricing"]', function() {
        if ($(this).val() === 'paid') {
            $('#af-price-fields').show();
        } else {
            $('#af-price-fields').hide();
        }
    });
    // init on load
    if ($('input[name="_app_pricing"]:checked').val() !== 'paid') {
        $('#af-price-fields').hide();
    }

}(jQuery));
JS;
}

// ================================================================
// RENDER: VERSIONS
// ================================================================
function af_mb_versions_html( $post ) {
    wp_nonce_field( 'af_mb_save', 'af_mb_nonce' );

    $raw   = get_post_meta( $post->ID, '_versions', true );
    $items = $raw ? json_decode( $raw, true ) : array();
    if ( ! is_array( $items ) ) $items = array();
    ?>
    <div class="af-mb">
        <table class="af-versions-table">
            <thead>
                <tr>
                    <th style="width:130px;"><?php esc_html_e( 'Version', 'appforge' ); ?></th>
                    <th><?php esc_html_e( 'Title', 'appforge' ); ?></th>
                    <th style="width:150px;"><?php esc_html_e( 'Update date', 'appforge' ); ?></th>
                    <th style="width:36px;"></th>
                </tr>
            </thead>
            <tbody id="af-versions-tbody">
                <?php foreach ( $items as $i => $v ) : ?>
                <tr>
                    <td><input type="text" name="_versions[<?php echo esc_attr( $i ); ?>][version]" value="<?php echo esc_attr( $v['version'] ?? '' ); ?>" placeholder="1.0.0" /></td>
                    <td><input type="text" name="_versions[<?php echo esc_attr( $i ); ?>][title]" value="<?php echo esc_attr( $v['title'] ?? '' ); ?>" placeholder="What changed…" /></td>
                    <td><input type="text" name="_versions[<?php echo esc_attr( $i ); ?>][date]" value="<?php echo esc_attr( $v['date'] ?? '' ); ?>" placeholder="Jan 1, 2024" /></td>
                    <td style="text-align:center;"><button type="button" class="af-rm-row" title="<?php esc_attr_e( 'Remove', 'appforge' ); ?>">✕</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ( empty( $items ) ) : ?>
        <p id="af-no-versions" class="af-no-versions"><?php esc_html_e( 'No versions', 'appforge' ); ?></p>
        <?php else : ?>
        <p id="af-no-versions" class="af-no-versions" style="display:none;"><?php esc_html_e( 'No versions', 'appforge' ); ?></p>
        <?php endif; ?>

        <button type="button" id="af-add-version" class="button button-secondary" style="margin-top:8px;">
            + <?php esc_html_e( 'Add Version', 'appforge' ); ?>
        </button>
    </div>
    <?php
}

// ================================================================
// RENDER: APP INFORMATION
// ================================================================
function af_mb_info_html( $post ) {
    $status    = get_post_meta( $post->ID, '_app_status',   true );
    $app_type  = get_post_meta( $post->ID, '_app_type',     true );
    $os        = get_post_meta( $post->ID, '_app_os',       true ) ?: 'android';
    $pricing   = get_post_meta( $post->ID, '_app_pricing',  true ) ?: 'free';
    $price     = get_post_meta( $post->ID, '_app_price',    true ) ?: '1.00';
    $currency  = get_post_meta( $post->ID, '_app_currency', true ) ?: 'USD';
    $developer = get_post_meta( $post->ID, '_developer',    true );
    $dev_url   = get_post_meta( $post->ID, '_developer_url',true );
    $version   = get_post_meta( $post->ID, '_version',      true );
    $size      = get_post_meta( $post->ID, '_size',         true );
    $requires  = get_post_meta( $post->ID, '_requires',     true );
    $released  = get_post_meta( $post->ID, '_released',     true );
    $updated   = get_post_meta( $post->ID, '_updated',      true );
    $gplay     = get_post_meta( $post->ID, '_google_play_url', true );
    $tg_url    = get_post_meta( $post->ID, '_telegram_url', true );
    $votes     = get_post_meta( $post->ID, '_votes',        true );
    $rating    = get_post_meta( $post->ID, '_rating',       true );
    $downloads = get_post_meta( $post->ID, '_downloads',    true );
    $whats_new = get_post_meta( $post->ID, '_whats_new',    true );
    ?>
    <div class="af-mb">

        <div class="af-grid-3">
            <!-- App Status -->
            <div class="af-field">
                <label for="_app_status"><?php esc_html_e( 'App status', 'appforge' ); ?> <span class="dashicons dashicons-editor-help" style="font-size:14px;vertical-align:middle;" title="Displayed as a badge on app pages."></span></label>
                <select name="_app_status" id="_app_status" class="af-select">
                    <option value=""     <?php selected( $status, '' ); ?>><?php esc_html_e( 'None', 'appforge' ); ?></option>
                    <option value="mod"  <?php selected( $status, 'mod' ); ?>><?php esc_html_e( 'MOD', 'appforge' ); ?></option>
                    <option value="pro"  <?php selected( $status, 'pro' ); ?>><?php esc_html_e( 'Pro', 'appforge' ); ?></option>
                    <option value="premium" <?php selected( $status, 'premium' ); ?>><?php esc_html_e( 'Premium', 'appforge' ); ?></option>
                    <option value="cracked" <?php selected( $status, 'cracked' ); ?>><?php esc_html_e( 'Cracked', 'appforge' ); ?></option>
                    <option value="unlocked" <?php selected( $status, 'unlocked' ); ?>><?php esc_html_e( 'Unlocked', 'appforge' ); ?></option>
                </select>
            </div>

            <!-- App Type -->
            <div class="af-field">
                <label for="_app_type"><?php esc_html_e( 'App type', 'appforge' ); ?></label>
                <select name="_app_type" id="_app_type" class="af-select">
                    <option value="normal" <?php selected( $app_type, 'normal' ); ?>><?php esc_html_e( 'Normal', 'appforge' ); ?></option>
                    <option value="game"   <?php selected( $app_type, 'game' ); ?>><?php esc_html_e( 'Game', 'appforge' ); ?></option>
                    <option value="tool"   <?php selected( $app_type, 'tool' ); ?>><?php esc_html_e( 'Tool', 'appforge' ); ?></option>
                </select>
            </div>

            <!-- App Version -->
            <div class="af-field">
                <label for="_version"><?php esc_html_e( 'Version', 'appforge' ); ?></label>
                <input type="text" id="_version" name="_version" class="af-input" value="<?php echo esc_attr( $version ); ?>" placeholder="1.0.0" />
            </div>
        </div>

        <!-- Operating System -->
        <div class="af-field">
            <label><?php esc_html_e( 'Operating system', 'appforge' ); ?></label>
            <div class="af-radio-group">
                <?php
                $os_options = array( 'android' => 'Android', 'ios' => 'iOS', 'mac' => 'Mac', 'windows' => 'Windows', 'linux' => 'Linux' );
                foreach ( $os_options as $val => $label ) : ?>
                <label>
                    <input type="radio" name="_app_os" value="<?php echo esc_attr( $val ); ?>" <?php checked( $os, $val ); ?> />
                    <?php echo esc_html( $label ); ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Free / Payment -->
        <div class="af-field">
            <div class="af-price-wrap">
                <label style="display:inline-flex;align-items:center;gap:5px;font-weight:normal;letter-spacing:0;text-transform:none;font-size:13px;">
                    <input type="radio" name="_app_pricing" value="free" <?php checked( $pricing, 'free' ); ?> /> <?php esc_html_e( 'Free', 'appforge' ); ?>
                </label>
                <label style="display:inline-flex;align-items:center;gap:5px;font-weight:normal;letter-spacing:0;text-transform:none;font-size:13px;">
                    <input type="radio" name="_app_pricing" value="paid" <?php checked( $pricing, 'paid' ); ?> /> <?php esc_html_e( 'Payment', 'appforge' ); ?>
                </label>
                <span id="af-price-fields" style="display:flex;align-items:center;gap:6px;">
                    <input type="number" name="_app_price" class="af-input af-price-input" value="<?php echo esc_attr( $price ); ?>" step="0.01" min="0" style="width:80px;" />
                    <select name="_app_currency" class="af-select" style="width:90px;">
                        <?php foreach ( array( 'USD', 'EUR', 'GBP', 'PKR', 'INR', 'AED' ) as $cur ) : ?>
                        <option value="<?php echo esc_attr( $cur ); ?>" <?php selected( $currency, $cur ); ?>><?php echo esc_html( $cur ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </span>
            </div>
        </div>

        <div class="af-separator"></div>

        <div class="af-grid-2">
            <div class="af-field">
                <label for="_developer"><?php esc_html_e( 'Developer', 'appforge' ); ?></label>
                <input type="text" id="_developer" name="_developer" class="af-input" value="<?php echo esc_attr( $developer ); ?>" placeholder="Google LLC" />
            </div>
            <div class="af-field">
                <label for="_developer_url"><?php esc_html_e( 'Developer URL', 'appforge' ); ?></label>
                <input type="url" id="_developer_url" name="_developer_url" class="af-input" value="<?php echo esc_attr( $dev_url ); ?>" placeholder="https://…" />
            </div>
            <div class="af-field">
                <label for="_size"><?php esc_html_e( 'File size', 'appforge' ); ?></label>
                <input type="text" id="_size" name="_size" class="af-input" value="<?php echo esc_attr( $size ); ?>" placeholder="45 MB" />
            </div>
            <div class="af-field">
                <label for="_requires"><?php esc_html_e( 'Requirements', 'appforge' ); ?></label>
                <input type="text" id="_requires" name="_requires" class="af-input" value="<?php echo esc_attr( $requires ); ?>" placeholder="Android 5.0+" />
            </div>
            <div class="af-field">
                <label for="_released"><?php esc_html_e( 'Released on', 'appforge' ); ?></label>
                <input type="text" id="_released" name="_released" class="af-input" value="<?php echo esc_attr( $released ); ?>" placeholder="Jan 1, 2024" />
            </div>
            <div class="af-field">
                <label for="_updated"><?php esc_html_e( 'Updated', 'appforge' ); ?></label>
                <input type="text" id="_updated" name="_updated" class="af-input" value="<?php echo esc_attr( $updated ); ?>" placeholder="Jun 1, 2024" />
            </div>
            <div class="af-field">
                <label for="_telegram_url"><?php esc_html_e( 'Telegram URL', 'appforge' ); ?></label>
                <input type="url" id="_telegram_url" name="_telegram_url" class="af-input" value="<?php echo esc_attr( $tg_url ); ?>" placeholder="https://t.me/…" />
            </div>
            <div class="af-field">
                <label for="_google_play_url"><?php esc_html_e( 'Get it on (Google Play URL)', 'appforge' ); ?></label>
                <input type="url" id="_google_play_url" name="_google_play_url" class="af-input" value="<?php echo esc_attr( $gplay ); ?>" placeholder="https://play.google.com/…" />
            </div>
            <div class="af-field">
                <label for="_votes"><?php esc_html_e( 'Rating (Number of votes)', 'appforge' ); ?></label>
                <input type="number" id="_votes" name="_votes" class="af-input" value="<?php echo esc_attr( $votes ); ?>" min="0" placeholder="0" />
            </div>
            <div class="af-field">
                <label for="_rating"><?php esc_html_e( 'Rating (Average)', 'appforge' ); ?></label>
                <input type="number" id="_rating" name="_rating" class="af-input" value="<?php echo esc_attr( $rating ); ?>" min="0" max="5" step="0.1" placeholder="0" />
            </div>
            <div class="af-field">
                <label for="_downloads"><?php esc_html_e( 'Downloads', 'appforge' ); ?></label>
                <input type="text" id="_downloads" name="_downloads" class="af-input" value="<?php echo esc_attr( $downloads ); ?>" placeholder="1000000" />
            </div>
            <div class="af-field">
                <label for="_app_views"><?php esc_html_e( 'View count (auto)', 'appforge' ); ?></label>
                <input type="number" id="_app_views" name="_app_views" class="af-input" value="<?php echo esc_attr( get_post_meta( $post->ID, '_app_views', true ) ); ?>" min="0" placeholder="0" />
            </div>
        </div>

        <div class="af-separator"></div>

        <!-- What's New -->
        <div class="af-field">
            <label><?php esc_html_e( "What's new:", 'appforge' ); ?></label>
            <?php
            wp_editor( $whats_new, 'af_whats_new_editor', array(
                'textarea_name' => '_whats_new',
                'media_buttons' => true,
                'textarea_rows' => 6,
                'teeny'         => false,
                'quicktags'     => true,
            ) );
            ?>
        </div>

    </div>
    <?php
}

// ================================================================
// RENDER: DOWNLOAD LINKS
// ================================================================
function af_mb_download_html( $post ) {
    $dl_type      = get_post_meta( $post->ID, '_download_type',      true ) ?: 'links';
    $file_type    = get_post_meta( $post->ID, '_download_file_type', true ) ?: 'apk';
    $raw_links    = get_post_meta( $post->ID, '_download_links',     true );
    $links        = $raw_links ? json_decode( $raw_links, true ) : array();
    if ( ! is_array( $links ) ) $links = array();

    $redirect_url = get_post_meta( $post->ID, '_redirect_url',       true );
    $direct_url   = get_post_meta( $post->ID, '_download_url',       true );
    ?>
    <div class="af-mb">
        <input type="hidden" name="_download_type" value="<?php echo esc_attr( $dl_type ); ?>" />

        <!-- Tabs -->
        <div class="af-tabs">
            <button type="button" class="af-tab-btn <?php echo $dl_type === 'links' ? 'active' : ''; ?>" data-tab="af-tab-links">
                <?php esc_html_e( 'Download links', 'appforge' ); ?>
            </button>
            <button type="button" class="af-tab-btn <?php echo $dl_type === 'redirect' ? 'active' : ''; ?>" data-tab="af-tab-redirect">
                <?php esc_html_e( 'Direct link / Redirection', 'appforge' ); ?>
            </button>
            <button type="button" class="af-tab-btn <?php echo $dl_type === 'direct' ? 'active' : ''; ?>" data-tab="af-tab-direct">
                <?php esc_html_e( 'Direct download', 'appforge' ); ?>
            </button>
        </div>

        <!-- Tab: Download links -->
        <div id="af-tab-links" class="af-tab-pane <?php echo $dl_type === 'links' ? 'active' : ''; ?>">

            <!-- File type -->
            <div class="af-file-type">
                <strong style="font-size:13px;"><?php esc_html_e( 'Files type', 'appforge' ); ?></strong>
                <span class="dashicons dashicons-editor-help" style="font-size:14px;" title="Choose the type of file."></span>
                &nbsp;
                <?php foreach ( array( 'apk' => 'APK', 'apk_obb' => 'APK + OBB', 'zip' => 'ZIP' ) as $val => $label ) : ?>
                <label>
                    <input type="radio" name="_download_file_type" value="<?php echo esc_attr( $val ); ?>" <?php checked( $file_type, $val ); ?> />
                    <?php echo esc_html( $label ); ?>
                </label>
                <?php endforeach; ?>
            </div>

            <p class="af-dl-note">
                <?php esc_html_e( "To delete a field just leave it empty.", 'appforge' ); ?><br>
                <?php esc_html_e( "Links 'nofollow' for default.", 'appforge' ); ?>
            </p>

            <table class="af-dl-table">
                <thead>
                    <tr>
                        <th style="width:24px;"></th>
                        <th><?php esc_html_e( 'Link', 'appforge' ); ?></th>
                        <th style="width:200px;"><?php esc_html_e( 'Text', 'appforge' ); ?></th>
                        <th style="width:110px;"><?php esc_html_e( 'Attribute', 'appforge' ); ?></th>
                        <th style="width:36px;"></th>
                    </tr>
                </thead>
                <tbody id="af-dl-links-tbody" data-idx="<?php echo esc_attr( count( $links ) ); ?>">
                    <?php foreach ( $links as $i => $lnk ) : ?>
                    <tr>
                        <td><span class="af-drag-handle">⊕</span></td>
                        <td><input type="url" name="_download_links[<?php echo esc_attr( $i ); ?>][url]" value="<?php echo esc_attr( $lnk['url'] ?? '' ); ?>" placeholder="https://…" /></td>
                        <td><input type="text" name="_download_links[<?php echo esc_attr( $i ); ?>][text]" value="<?php echo esc_attr( $lnk['text'] ?? '' ); ?>" /></td>
                        <td style="text-align:center;">
                            <label><input type="checkbox" name="_download_links[<?php echo esc_attr( $i ); ?>][follow]" value="1" <?php checked( ! empty( $lnk['follow'] ) ); ?> /> Follow</label>
                        </td>
                        <td style="text-align:center;"><button type="button" class="af-rm-row" title="<?php esc_attr_e( 'Remove', 'appforge' ); ?>">✕</button></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if ( empty( $links ) ) : ?>
                    <tr>
                        <td><span class="af-drag-handle">⊕</span></td>
                        <td><input type="url" name="_download_links[0][url]" placeholder="https://…" /></td>
                        <td><input type="text" name="_download_links[0][text]" /></td>
                        <td style="text-align:center;"><label><input type="checkbox" name="_download_links[0][follow]" value="1" /> Follow</label></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><span class="af-drag-handle">⊕</span></td>
                        <td><input type="url" name="_download_links[1][url]" placeholder="https://…" /></td>
                        <td><input type="text" name="_download_links[1][text]" /></td>
                        <td style="text-align:center;"><label><input type="checkbox" name="_download_links[1][follow]" value="1" /> Follow</label></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><span class="af-drag-handle">⊕</span></td>
                        <td><input type="url" name="_download_links[2][url]" placeholder="https://…" /></td>
                        <td><input type="text" name="_download_links[2][text]" /></td>
                        <td style="text-align:center;"><label><input type="checkbox" name="_download_links[2][follow]" value="1" /> Follow</label></td>
                        <td></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <button type="button" id="af-add-dl-link" class="af-add-btn">
                + <?php esc_html_e( 'Add Link', 'appforge' ); ?>
            </button>

            <p style="margin-top:10px;">
                <a href="#" onclick="return false;" style="font-size:12px;color:#2271b1;">
                    <?php esc_html_e( 'See in the documentation', 'appforge' ); ?>
                </a>
            </p>
        </div>

        <!-- Tab: Redirect -->
        <div id="af-tab-redirect" class="af-tab-pane <?php echo $dl_type === 'redirect' ? 'active' : ''; ?>">
            <div class="af-field" style="max-width:600px;">
                <label for="_redirect_url"><?php esc_html_e( 'Redirect URL', 'appforge' ); ?></label>
                <input type="url" id="_redirect_url" name="_redirect_url" class="af-input"
                       value="<?php echo esc_attr( $redirect_url ); ?>" placeholder="https://…" />
                <p style="font-size:12px;color:#888;margin-top:4px;">
                    <?php esc_html_e( 'User will be redirected to this URL when they click Download.', 'appforge' ); ?>
                </p>
            </div>
        </div>

        <!-- Tab: Direct Download -->
        <div id="af-tab-direct" class="af-tab-pane <?php echo $dl_type === 'direct' ? 'active' : ''; ?>">
            <div class="af-field" style="max-width:600px;">
                <label for="_download_url"><?php esc_html_e( 'Direct download URL', 'appforge' ); ?></label>
                <input type="url" id="_download_url" name="_download_url" class="af-input"
                       value="<?php echo esc_attr( $direct_url ); ?>" placeholder="https://…" />
                <p style="font-size:12px;color:#888;margin-top:4px;">
                    <?php esc_html_e( 'File will be downloaded directly when user clicks Download.', 'appforge' ); ?>
                </p>
            </div>
        </div>

    </div>
    <?php
}

// ================================================================
// RENDER: APP IMAGES (Screenshots)
// ================================================================
function af_mb_images_html( $post ) {
    $raw       = get_post_meta( $post->ID, '_app_screenshots', true );
    $screens   = $raw ? json_decode( $raw, true ) : array();
    if ( ! is_array( $screens ) ) $screens = array();

    // Show at least 3 empty rows
    $total = max( count( $screens ), 10 );
    ?>
    <div class="af-mb">
        <div id="af-screenshots-wrap" data-idx="<?php echo esc_attr( $total ); ?>">
            <?php for ( $i = 0; $i < $total; $i++ ) :
                $val = $screens[ $i ] ?? ''; ?>
            <div class="af-img-row">
                <input type="url" name="_app_screenshots[<?php echo esc_attr( $i ); ?>]"
                       value="<?php echo esc_attr( $val ); ?>" placeholder="https://… or Upload" />
                <button type="button" class="button button-small af-upload-img"><?php esc_html_e( 'Upload', 'appforge' ); ?></button>
                <button type="button" class="af-rm-row" title="<?php esc_attr_e( 'Remove', 'appforge' ); ?>">✕</button>
                <img class="af-img-preview" src="<?php echo esc_url( $val ); ?>"
                     alt="" <?php echo $val ? '' : 'style="display:none;"'; ?> />
            </div>
            <?php endfor; ?>
        </div>

        <button type="button" id="af-add-screenshot" class="af-add-btn">
            + <?php esc_html_e( 'Add Images', 'appforge' ); ?>
        </button>
    </div>
    <?php
}

// ================================================================
// RENDER: APP VIDEO
// ================================================================
function af_mb_video_html( $post ) {
    $yt_id = get_post_meta( $post->ID, '_youtube_id', true );
    ?>
    <div class="af-mb">
        <div class="af-field">
            <label for="_youtube_id"><?php esc_html_e( 'ID YouTube', 'appforge' ); ?></label>
            <input type="text" id="_youtube_id" name="_youtube_id" class="af-input"
                   value="<?php echo esc_attr( $yt_id ); ?>" placeholder="TkErUvyVlhA"
                   style="max-width:400px;" />
            <?php if ( $yt_id ) : ?>
            <p style="margin-top:6px;">
                <a href="https://www.youtube.com/watch?v=<?php echo esc_attr( $yt_id ); ?>" target="_blank" rel="noopener" style="font-size:12px;">
                    <?php esc_html_e( 'Preview on YouTube ↗', 'appforge' ); ?>
                </a>
            </p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

// ================================================================
// SAVE
// ================================================================
add_action( 'save_post_app', 'af_mb_save' );
function af_mb_save( $post_id ) {
    if ( ! isset( $_POST['af_mb_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['af_mb_nonce'], 'af_mb_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    /* ---- Versions ---- */
    $versions = array();
    if ( isset( $_POST['_versions'] ) && is_array( $_POST['_versions'] ) ) {
        foreach ( $_POST['_versions'] as $v ) {
            $ver = sanitize_text_field( $v['version'] ?? '' );
            if ( $ver === '' ) continue;
            $versions[] = array(
                'version' => $ver,
                'title'   => sanitize_text_field( $v['title'] ?? '' ),
                'date'    => sanitize_text_field( $v['date']  ?? '' ),
            );
        }
    }
    update_post_meta( $post_id, '_versions', wp_json_encode( $versions ) );

    /* ---- App Information ---- */
    $text_fields = array( '_app_status', '_app_type', '_app_os', '_app_pricing', '_app_currency',
                          '_version', '_developer', '_size', '_requires', '_released', '_updated' );
    foreach ( $text_fields as $f ) {
        if ( isset( $_POST[ $f ] ) ) {
            update_post_meta( $post_id, $f, sanitize_text_field( $_POST[ $f ] ) );
        }
    }

    $url_fields = array( '_developer_url', '_telegram_url', '_google_play_url' );
    foreach ( $url_fields as $f ) {
        if ( isset( $_POST[ $f ] ) ) {
            update_post_meta( $post_id, $f, esc_url_raw( $_POST[ $f ] ) );
        }
    }

    $num_fields = array( '_votes', '_rating', '_downloads', '_app_views', '_app_price' );
    foreach ( $num_fields as $f ) {
        if ( isset( $_POST[ $f ] ) ) {
            update_post_meta( $post_id, $f, sanitize_text_field( $_POST[ $f ] ) );
        }
    }

    if ( isset( $_POST['_whats_new'] ) ) {
        update_post_meta( $post_id, '_whats_new', wp_kses_post( $_POST['_whats_new'] ) );
    }

    /* ---- Download links ---- */
    $dl_type = sanitize_key( $_POST['_download_type'] ?? 'links' );
    update_post_meta( $post_id, '_download_type',      $dl_type );
    update_post_meta( $post_id, '_download_file_type', sanitize_key( $_POST['_download_file_type'] ?? 'apk' ) );

    $links = array();
    if ( isset( $_POST['_download_links'] ) && is_array( $_POST['_download_links'] ) ) {
        foreach ( $_POST['_download_links'] as $lnk ) {
            $url = esc_url_raw( $lnk['url'] ?? '' );
            if ( ! $url ) continue;
            $links[] = array(
                'url'    => $url,
                'text'   => sanitize_text_field( $lnk['text']  ?? '' ),
                'follow' => ! empty( $lnk['follow'] ),
            );
        }
    }
    update_post_meta( $post_id, '_download_links', wp_json_encode( $links ) );

    // Populate _download_url for backward compat (used by single-app.php)
    if ( $dl_type === 'links' ) {
        $first_url = ! empty( $links ) ? $links[0]['url'] : '';
        update_post_meta( $post_id, '_download_url', $first_url );
    } elseif ( $dl_type === 'redirect' ) {
        $ru = esc_url_raw( $_POST['_redirect_url'] ?? '' );
        update_post_meta( $post_id, '_redirect_url',  $ru );
        update_post_meta( $post_id, '_download_url',  $ru );
    } elseif ( $dl_type === 'direct' ) {
        $du = esc_url_raw( $_POST['_download_url'] ?? '' );
        update_post_meta( $post_id, '_download_url', $du );
    }

    /* ---- Screenshots ---- */
    $screens = array();
    if ( isset( $_POST['_app_screenshots'] ) && is_array( $_POST['_app_screenshots'] ) ) {
        foreach ( $_POST['_app_screenshots'] as $url ) {
            $url = esc_url_raw( $url );
            if ( $url ) $screens[] = $url;
        }
    }
    update_post_meta( $post_id, '_app_screenshots', wp_json_encode( $screens ) );

    /* ---- Video ---- */
    if ( isset( $_POST['_youtube_id'] ) ) {
        update_post_meta( $post_id, '_youtube_id', sanitize_text_field( $_POST['_youtube_id'] ) );
    }
}
