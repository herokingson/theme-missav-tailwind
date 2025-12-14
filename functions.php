<?php
// กันไม่ให้เรียกไฟล์ตรง ๆ
if (!defined('ABSPATH')) {
  exit;
}

/**
 * Theme setup
 */
function mt_setup_theme()
{
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', array('search-form', 'gallery', 'caption'));

  // ใช้ Site Logo
  add_theme_support('custom-logo', array(
    'height' => 64,
    'width' => 200,
    'flex-height' => true,
    'flex-width' => true,
  ));

  register_nav_menus(array(
    'primary' => __('Primary Menu', 'missav-tailwind'),
  ));
}
add_action('after_setup_theme', 'mt_setup_theme');

/**
 * Enqueue Tailwind & main style
 */
function mt_enqueue_assets()
{
  wp_enqueue_style(
    'app-css',
    get_template_directory_uri() . '/dist/css/app.css',
    [],
    filemtime(get_template_directory() . '/dist/css/app.css')
  );

  wp_enqueue_style(
    'mt-style',
    get_stylesheet_uri(),
    array('tailwind'),
    '1.0'
  ); // โหลดสคริปต์เฉพาะหน้า single post / single video
  if (is_singular(array('post', 'video'))) {
    wp_enqueue_script(
      'mt-video-actions',
      get_template_directory_uri() . '/dist/js/app.js',
      array('jquery'),
      '1.0',
      true
    );

    wp_enqueue_script(
      'mt-sidebar-tabs',
      get_template_directory_uri() . '/assets/js/sidebar-tabs.js',
      array(), // No dependency on jQuery needed for this vanilla JS
      '1.0',
      true
    );

    wp_localize_script(
      'mt-video-actions',
      'mtVideoActions',
      array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('mt_video_actions'),
        'post_id' => get_the_ID(),
        'is_logged' => is_user_logged_in(),
        'permalink' => get_permalink(),
      )
    );
  }
}
add_action('wp_enqueue_scripts', 'mt_enqueue_assets');

/**
 * Register "video" post type
 */
function mt_register_video_post_type()
{
  $labels = array(
    'name' => __('Videos', 'missav-tailwind'),
    'singular_name' => __('Video', 'missav-tailwind'),
    'add_new' => __('Add New Video', 'missav-tailwind'),
    'add_new_item' => __('Add New Video', 'missav-tailwind'),
    'edit_item' => __('Edit Video', 'missav-tailwind'),
    'new_item' => __('New Video', 'missav-tailwind'),
    'view_item' => __('View Video', 'missav-tailwind'),
    'search_items' => __('Search Videos', 'missav-tailwind'),
    'not_found' => __('No videos found', 'missav-tailwind'),
    'not_found_in_trash' => __('No videos found in Trash', 'missav-tailwind'),
    'menu_name' => __('Videos', 'missav-tailwind'),
  );

  $args = array(
    'label' => __('Video', 'missav-tailwind'),
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true, // สำคัญสำหรับ single
    'show_ui' => true,
    'show_in_menu' => true,
    'show_in_rest' => true,
    'menu_icon' => 'dashicons-video-alt2',
    'supports' => array('title', 'editor', 'thumbnail', 'comments'),
    'has_archive' => true,
    'rewrite' => array(
      'slug' => 'videos', // /videos/video-16/
      'with_front' => false,
    ),
    'taxonomies' => array('category', 'post_tag'),
    'query_var' => 'video',
  );

  register_post_type('video', $args);
}
add_action('init', 'mt_register_video_post_type');

/**
 * Register "actor" taxonomy
 */
function mt_register_actor_taxonomy()
{
  $labels = array(
    'name' => __('Actors', 'missav-tailwind'),
    'singular_name' => __('Actor', 'missav-tailwind'),
    'search_items' => __('Search Actors', 'missav-tailwind'),
    'all_items' => __('All Actors', 'missav-tailwind'),
    'parent_item' => __('Parent Actor', 'missav-tailwind'),
    'parent_item_colon' => __('Parent Actor:', 'missav-tailwind'),
    'edit_item' => __('Edit Actor', 'missav-tailwind'),
    'update_item' => __('Update Actor', 'missav-tailwind'),
    'add_new_item' => __('Add New Actor', 'missav-tailwind'),
    'new_item_name' => __('New Actor Name', 'missav-tailwind'),
    'view_item' => __('View Actor', 'missav-tailwind'),
    'menu_name' => __('Actors', 'missav-tailwind'),
  );

  $args = array(
    'hierarchical' => false, // false = like tags (no parent/child), true = like categories
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array('slug' => 'actor'),
    'show_in_rest' => true, // Gutenberg editor support
  );

  register_taxonomy('actor', array('video'), $args);
}
add_action('init', 'mt_register_actor_taxonomy');

/**
 * Actor Taxonomy: Image Field
 */

// 1. Enqueue Media on Term Edit screen
function mt_load_media_on_actor_taxonomy($hook)
{
  $screen = get_current_screen();
  if ($screen->taxonomy === 'actor') {
    wp_enqueue_media();
    add_action('admin_footer', 'mt_actor_image_script');
  }
}
add_action('admin_enqueue_scripts', 'mt_load_media_on_actor_taxonomy');

// 2. JS for Media Uploader
function mt_actor_image_script()
{
  ?>
    <script>
      jQuery(document).ready(function ($) {
        // Upload button
        $('body').on('click', '.mt-upload-actor-image', function (e) {
          e.preventDefault();
          var button = $(this);
          var custom_uploader = wp.media({
            title: 'Select Actor Image',
            library: {
              type: 'image'
            },
            button: {
              text: 'Use this image'
            },
            multiple: false
          }).on('select', function () {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#mt_actor_image_id').val(attachment.id);
            $('#mt-actor-image-preview').html('<img src="' + attachment.url +
              '" style="max-width:150px;height:auto;margin-top:10px;">');
            $('.mt-remove-actor-image').show();
          }).open();
        });

        // Remove button
        $('body').on('click', '.mt-remove-actor-image', function (e) {
          e.preventDefault();
          $('#mt_actor_image_id').val('');
          $('#mt-actor-image-preview').html('');
          $(this).hide();
        });
      });
    </script>
    <?php
}

// 3. Add Form Field (New Actor)
function mt_actor_add_form_field()
{
  ?>
    <div class="form-field term-group">
      <label for="mt_actor_image_id"><?php _e('Actor Image', 'missav-tailwind'); ?></label>
      <input type="hidden" id="mt_actor_image_id" name="mt_actor_image_id" value="">
      <div id="mt-actor-image-preview"></div>
      <p>
        <button type="button" class="button mt-upload-actor-image"><?php _e('Upload/Add Image', 'missav-tailwind'); ?></button>
        <button type="button" class="button mt-remove-actor-image" style="display:none;color:#a00;"><?php _e('Remove Image', 'missav-tailwind'); ?></button>
      </p>
    </div>
    <?php
}
add_action('actor_add_form_fields', 'mt_actor_add_form_field');

// 4. Edit Form Field (By User)
function mt_actor_edit_form_field($term)
{
  $image_id = get_term_meta($term->term_id, 'mt_actor_image_id', true);
  $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
  ?>
    <tr class="form-field term-group-wrap">
      <th scope="row"><label for="mt_actor_image_id"><?php _e('Actor Image', 'missav-tailwind'); ?></label></th>
      <td>
        <input type="hidden" id="mt_actor_image_id" name="mt_actor_image_id" value="<?php echo esc_attr($image_id); ?>">
        <div id="mt-actor-image-preview">
          <?php if ($image_url): ?>
              <img src="<?php echo esc_url($image_url); ?>" style="max-width:150px;height:auto;margin-bottom:10px;">
          <?php endif; ?>
        </div>
        <p>
          <button type="button" class="button mt-upload-actor-image"><?php _e('Upload/Add Image', 'missav-tailwind'); ?></button>
          <button type="button" class="button mt-remove-actor-image" style="<?php echo $image_url ? '' : 'display:none;'; ?>color:#a00;"><?php _e('Remove Image', 'missav-tailwind'); ?></button>
        </p>
      </td>
    </tr>
    <?php
}
add_action('actor_edit_form_fields', 'mt_actor_edit_form_field');

// 5. Save Logic
function mt_save_actor_image($term_id)
{
  if (isset($_POST['mt_actor_image_id'])) {
    update_term_meta($term_id, 'mt_actor_image_id', absint($_POST['mt_actor_image_id']));
  }
}
add_action('created_actor', 'mt_save_actor_image');
add_action('edited_actor', 'mt_save_actor_image');

// 6. Admin Column
function mt_manage_actor_columns($columns)
{
  $new_columns = array();
  $new_columns['cb'] = $columns['cb']; // checkbox
  $new_columns['mt_thumb'] = __('Image', 'missav-tailwind');
  unset($columns['cb']); // remove old cb to reorder
  return array_merge($new_columns, $columns);
}
add_filter('manage_edit-actor_columns', 'mt_manage_actor_columns');

function mt_manage_actor_custom_column($content, $column_name, $term_id)
{
  if ($column_name === 'mt_thumb') {
    $image_id = get_term_meta($term_id, 'mt_actor_image_id', true);
    if ($image_id) {
      $img = wp_get_attachment_image_src($image_id, 'thumbnail');
      if ($img) {
        $content = '<img src="' . esc_url($img[0]) . '" style="width:50px;height:50px;object-fit:cover;border-radius:4px;">';
      }
    } else {
      $content = '<span style="color:#ccc;">—</span>';
    }
  }
  return $content;
}
add_filter('manage_actor_custom_column', 'mt_manage_actor_custom_column', 10, 3);


function mt_flush_video_rewrite_on_switch()
{
  mt_register_video_post_type();
  flush_rewrite_rules();
}
add_action('after_switch_theme', 'mt_flush_video_rewrite_on_switch');

/**
 * META BOX: Video Data (ใช้ทั้ง post ปกติ และ video)
 */
function mt_add_video_meta_box()
{
  add_meta_box(
    'mt_video_meta',
    __('Video Data', 'missav-tailwind'),
    'mt_video_meta_box_callback',
    array('post', 'video'),
    'normal',
    'high'
  );
}
add_action('add_meta_boxes', 'mt_add_video_meta_box');

function mt_video_meta_box_callback($post)
{
  $video_url = get_post_meta($post->ID, '_mt_video_url', true);
  // $label_1 = get_post_meta( $post->ID, '_mt_video_label_1', true );
  // $label_2 = get_post_meta( $post->ID, '_mt_video_label_2', true );
  $video_code = get_post_meta($post->ID, '_mt_video_code', true);
  $video_description = get_post_meta($post->ID, '_mt_seo_description', true);
  $view_count = get_post_meta($post->ID, '_mt_view_count', true);
  $view_count_week = get_post_meta($post->ID, '_mt_view_count_week', true);
  $studio = get_post_meta($post->ID, '_mt_video_studio', true);
  $duration = get_post_meta($post->ID, '_mt_video_duration', true);

  wp_nonce_field('mt_save_video_meta', 'mt_video_meta_nonce');
  ?>
  <style>
    .mt-meta-grid label {
      display: block;
      font-weight: 600;
      margin-bottom: 4px;
    }

    .mt-meta-grid input {
      width: 100%;
      max-width: 400px;
    }
    .mt-meta-row {
        display: flex; gap: 20px;
    }
  </style>
  <div class="mt-meta-grid">
    <p>
      <label for="mt_video_url">Video URL (mp4 หรือ embed URL):</label>
      <input type="text" id="mt_video_url" name="mt_video_url" value="<?php echo esc_attr($video_url); ?>">
    </p>
    <p>
      <label for="mt_video_code">Code (เช่น CUS-2557):</label>
      <input type="text" id="mt_video_code" name="mt_video_code" value="<?php echo esc_attr($video_code); ?>">
    </p>

    <div class="mt-meta-row">
      <p style="flex:1;">
        <label for="mt_video_studio">Studio:</label>
        <input type="text" id="mt_video_studio" name="mt_video_studio" value="<?php echo esc_attr($studio); ?>">
  </p>
  <p style="flex:1;">
    <label for="mt_video_duration">Video Length (เช่น 120 min):</label>
    <input type="text" id="mt_video_duration" name="mt_video_duration" value="<?php echo esc_attr($duration); ?>">
  </p>
</div>

<p>
  <label for="mt_upload_date">Latest Upload Date:</label>
  <input type="text" id="mt_upload_date" name="mt_upload_date"
    value="<?php echo esc_attr(get_post_meta($post->ID, '_mt_upload_date', true)); ?>" placeholder="YYYY-MM-DD">
</p>

<div class="mt-meta-row">
  <p>
    <label for="mt_view_count">Total Views:</label>
    <input type="number" id="mt_view_count" name="mt_view_count"
      value="<?php echo esc_attr($view_count ? $view_count : 0); ?>">
  </p>
  <p>
    <label for="mt_view_count_week">Weekly Views:</label>
    <input type="number" id="mt_view_count_week" name="mt_view_count_week"
      value="<?php echo esc_attr($view_count_week ? $view_count_week : 0); ?>">
  </p>
</div>

    <p>
      <label for="mt_video_description">Description:</label>
      <textarea id="mt_seo_description" name="mt_seo_description" rows="3" style="width:100%;"><?php echo esc_textarea($video_description); ?></textarea>
    </p>
    <p style="margin-top:12px;color:#666;font-size:12px;">
      * Tag ด้านล่าง (After the..., Thai girl, Elephant Media ฯลฯ) แนะนำให้ใช้ Post Tags ปกติของ WordPress
    </p>
  </div>
<?php
}

function mt_save_video_meta($post_id)
{
  if (!isset($_POST['mt_video_meta_nonce'])) {
    return;
  }
  if (!wp_verify_nonce($_POST['mt_video_meta_nonce'], 'mt_save_video_meta')) {
    return;
  }
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }
  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  $fields = array(
    '_mt_video_url' => 'mt_video_url',
    '_mt_video_label_1' => 'mt_video_label_1',
    '_mt_video_label_2' => 'mt_video_label_2',
    '_mt_video_code' => 'mt_video_code',
    '_mt_video_studio' => 'mt_video_studio',
    '_mt_video_duration' => 'mt_video_duration',
    '_mt_seo_description' => 'mt_seo_description', // Map text area to SEO desc
    '_mt_view_count' => 'mt_view_count',
    '_mt_view_count_week' => 'mt_view_count_week',
    '_mt_upload_date' => 'mt_upload_date',
  );

  foreach ($fields as $meta_key => $form_key) {
    if (isset($_POST[$form_key])) {
      $raw = wp_unslash($_POST[$form_key]);

      if ($meta_key === '_mt_video_url') {
        // อนุญาตให้เก็บทั้ง URL หรือโค้ด iframe
        $allowed_tags = array(
          'iframe' => array(
            'src' => true,
            'width' => true,
            'height' => true,
            'frameborder' => true,
            'allow' => true,
            'allowfullscreen' => true,
            'style' => true,
            'loading' => true,
          ),
        );

        // ถ้า user ใส่แค่ URL เฉย ๆ ก็จะไม่โดนตัดอะไร
        $value = wp_kses($raw, $allowed_tags);
      } else if ($meta_key === '_mt_seo_description') {
        $value = sanitize_textarea_field($raw);
      } else {
        // ฟิลด์อื่น sanitize ปกติ
        $value = sanitize_text_field($raw);
      }

      update_post_meta($post_id, $meta_key, $value);
    } else {
      // delete_post_meta($post_id, $meta_key); // Commented out to prevent accidental deletion if field missing
    }
  }
}
add_action('save_post', 'mt_save_video_meta');

/**
 * View Counting Logic
 */
function mt_track_post_views()
{
  if (!is_singular('video'))
    return;

  global $post;
  if (empty($post->ID))
    return;

  $post_id = $post->ID;

  // Prevent counting bots (optional but good)
  // if ( ... ) return;

  // 1. Total View
  $count = (int) get_post_meta($post_id, '_mt_view_count', true);
  $count++;
  update_post_meta($post_id, '_mt_view_count', $count);

  // 2. Weekly View
  // Logic: Store '_mt_week_number' (e.g. "2023-42" -> Year-Week).
  // If current week != stored week, reset count to 0.

  $current_week_id = date('W-Y'); // e.g., "50-2025"
  $stored_week_id = get_post_meta($post_id, '_mt_week_number', true);
  $week_count = (int) get_post_meta($post_id, '_mt_view_count_week', true);

  if ($stored_week_id !== $current_week_id) {
    // New week -> Reset
    $week_count = 0;
    update_post_meta($post_id, '_mt_week_number', $current_week_id);
  }

  $week_count++;
  update_post_meta($post_id, '_mt_view_count_week', $week_count);
}
add_action('wp_head', 'mt_track_post_views');

function mt_get_post_views($post_id)
{
  $count = get_post_meta($post_id, '_mt_view_count', true);
  return $count ? number_format((int) $count) : '0';
}

/**
 * Format views count for display
 * - 0-999: show plain number
 * - 1,000+: show as X.XXK format
 * - 1,000,000+: show as X.XXM format
 *
 * @param int $views Raw view count
 * @return string Formatted view string
 */
function mt_format_views($views)
{
  $views = (int) $views;

  if ($views >= 1000000) {
    // Format as M with up to 2 decimal places, trim trailing zeros
    $formatted = $views / 1000000;
    return rtrim(rtrim(number_format($formatted, 2), '0'), '.') . 'M';
  } elseif ($views >= 1000) {
    // Format as K with up to 2 decimal places, trim trailing zeros
    $formatted = $views / 1000;
    return rtrim(rtrim(number_format($formatted, 2), '0'), '.') . 'K';
  } else {
    // Below 1000, show the plain number
    return (string) $views;
  }
}

/**
 * Helper: primary category name
 */
function mt_get_primary_category_name()
{
  $cats = get_the_category();
  if (!empty($cats)) {
    return esc_html($cats[0]->name);
  }
  return '';
}

function mt_get_user_list($user_id, $meta_key)
{
  $list = get_user_meta($user_id, $meta_key, true);
  if (!is_array($list)) {
    $list = array();
  }
  return $list;
}

function mt_update_user_list($user_id, $meta_key, $list)
{
  $list = array_values(array_unique(array_map('intval', $list)));
  update_user_meta($user_id, $meta_key, $list);
}


/**
 * AJAX: toggle favorite (Save)
 */
function mt_ajax_toggle_favorite()
{
  check_ajax_referer('mt_video_actions', 'nonce');

  if (!is_user_logged_in()) {
    wp_send_json_error('login_required');
  }

  $user_id = get_current_user_id();
  $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

  if (!$post_id) {
    wp_send_json_error('invalid_post');
  }

  $meta_key = '_mt_favorite_videos';
  $list = mt_get_user_list($user_id, $meta_key);

  if (in_array($post_id, $list, true)) {
    // remove
    $list = array_diff($list, array($post_id));
    mt_update_user_list($user_id, $meta_key, $list);
    wp_send_json_success(array('is_favorite' => false));
  } else {
    // add
    $list[] = $post_id;
    mt_update_user_list($user_id, $meta_key, $list);
    wp_send_json_success(array('is_favorite' => true));
  }
}
add_action('wp_ajax_mt_toggle_favorite', 'mt_ajax_toggle_favorite');

/**
 * AJAX: toggle playlist
 */
function mt_ajax_toggle_playlist()
{
  check_ajax_referer('mt_video_actions', 'nonce');

  if (!is_user_logged_in()) {
    wp_send_json_error('login_required');
  }

  $user_id = get_current_user_id();
  $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

  if (!$post_id) {
    wp_send_json_error('invalid_post');
  }

  $meta_key = '_mt_playlist_videos';
  $list = mt_get_user_list($user_id, $meta_key);

  if (in_array($post_id, $list, true)) {
    $list = array_diff($list, array($post_id));
    mt_update_user_list($user_id, $meta_key, $list);
    wp_send_json_success(array('in_playlist' => false));
  } else {
    $list[] = $post_id;
    mt_update_user_list($user_id, $meta_key, $list);
    wp_send_json_success(array('in_playlist' => true));
  }
}
add_action('wp_ajax_mt_toggle_playlist', 'mt_ajax_toggle_playlist');

/**
 * Footer widget areas
 */
function mt_register_footer_sidebars()
{

  // คอลัมน์ซ้าย: โลโก้ + description
  register_sidebar(array(
    'name' => __('Footer About', 'missav-tailwind'),
    'id' => 'footer-about',
    'description' => __('About text under logo in footer.', 'missav-tailwind'),
    'before_widget' => '<div class="mt-footer-about">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="text-base font-semibold mb-3">',
    'after_title' => '</h3>',
  ));

  // VIDEOS
  register_sidebar(array(
    'name' => __('Footer Videos', 'missav-tailwind'),
    'id' => 'footer-videos',
    'description' => __('Footer column: VIDEOS links.', 'missav-tailwind'),
    'before_widget' => '<div class="mt-footer-col">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="text-xs font-semibold tracking-[0.15em] text-gray-400 mb-3 uppercase">',
    'after_title' => '</h3>',
  ));

  // SEARCH
  register_sidebar(array(
    'name' => __('Footer Search', 'missav-tailwind'),
    'id' => 'footer-search',
    'description' => __('Footer column: SEARCH links.', 'missav-tailwind'),
    'before_widget' => '<div class="mt-footer-col">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="text-xs font-semibold tracking-[0.15em] text-gray-400 mb-3 uppercase">',
    'after_title' => '</h3>',
  ));

  // LINKS
  register_sidebar(array(
    'name' => __('Footer Links', 'missav-tailwind'),
    'id' => 'footer-links',
    'description' => __('Footer column: LINKS.', 'missav-tailwind'),
    'before_widget' => '<div class="mt-footer-col">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="text-xs font-semibold tracking-[0.15em] text-gray-400 mb-3 uppercase">',
    'after_title' => '</h3>',
  ));

  // SEE ALSO
  register_sidebar(array(
    'name' => __('Footer See Also', 'missav-tailwind'),
    'id' => 'footer-seealso',
    'description' => __('Footer column: SEE ALSO.', 'missav-tailwind'),
    'before_widget' => '<div class="mt-footer-col">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="text-xs font-semibold tracking-[0.15em] text-gray-400 mb-3 uppercase">',
    'after_title' => '</h3>',
  ));
}
add_action('widgets_init', 'mt_register_footer_sidebars');



/**
 * Override document title ด้วย SEO Title ถ้ามี
 */
function mt_filter_document_title($title)
{
  if (is_singular(array('post', 'page', 'video'))) {
    $seo_title = get_post_meta(get_the_ID(), '_mt_seo_title', true);
    if ($seo_title) {
      return $seo_title;
    }
  }
  return $title;
}
// add_filter('pre_get_document_title', 'mt_filter_document_title');

/**
 * Output meta description จาก SEO Description
 */
function mt_output_meta_description()
{
  if (is_admin() || is_feed() || is_robots()) {
    return;
  }

  $description = '';

  if (is_singular(array('post', 'page', 'video'))) {
    $seo_desc = get_post_meta(get_the_ID(), '_mt_seo_description', true);
    if ($seo_desc) {
      $description = $seo_desc;
    }
  }

  // ถ้าไม่มี description จาก meta ใช้ excerpt/description ปกติเป็น fallback ก็ได้
  if (!$description) {
    if (is_singular()) {
      $description = wp_strip_all_tags(get_the_excerpt(), true);
    } elseif (is_home() || is_front_page()) {
      $description = get_bloginfo('description');
    }
  }

  if ($description) {
    $description = esc_attr(wp_trim_words($description, 40, ''));
    echo '
  <meta name="description" content="' . $description . " \" />\n";
  }
}
add_action('wp_head', 'mt_output_meta_description', 1);

/**
 * Fix Pagination for Archives (Category, Tag, Actor)
 * Ensures main query matches custom posts_per_page to prevent 404s.
 */
function mt_modify_archive_query($query) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( is_category() || is_tag() || is_tax('actor') ) {
    $query->set('posts_per_page', 8);
        $query->set('post_type', 'video');
    }
}
add_action( 'pre_get_posts', 'mt_modify_archive_query' );

require_once get_template_directory() . '/flush_rules.php';
