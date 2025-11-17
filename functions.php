<?php
// กันไม่ให้เรียกไฟล์ตรง ๆ
if (! defined('ABSPATH')) {
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
'mt-video-action',
get_template_directory_uri() . '/dist/css/app.css',
array(),
'2.2.19'
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
$release_date = get_post_meta($post->ID, '_mt_video_release_date', true);
$video_code = get_post_meta($post->ID, '_mt_video_code', true);

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
</style>
<div class="mt-meta-grid">
  <p>
    <label for="mt_video_url">Video URL (mp4 หรือ embed URL):</label>
    <input type="text" id="mt_video_url" name="mt_video_url" value="<?php echo esc_attr($video_url); ?>">
    </p>
    <p>
      <label for="mt_video_release_date">Release date (เช่น 2025-04-14):</label>
      <input type="text" id="mt_video_release_date" name="mt_video_release_date"
      value="<?php echo esc_attr($release_date); ?>" placeholder="YYYY-MM-DD">
      </p>
      <p>
        <label for="mt_video_code">Code (เช่น CUS-2557):</label>
    <input type="text" id="mt_video_code" name="mt_video_code" value="<?php echo esc_attr($video_code); ?>">
    </p>
    <p style="margin-top:12px;color:#666;font-size:12px;">
      * Tag ด้านล่าง (After the..., Thai girl, Elephant Media ฯลฯ) แนะนำให้ใช้ Post Tags ปกติของ WordPress
    </p>
    </div>
    <?php
}

function mt_save_video_meta($post_id)
{
if (! isset($_POST['mt_video_meta_nonce'])) {
return;
}
  if (! wp_verify_nonce($_POST['mt_video_meta_nonce'], 'mt_save_video_meta')) {
  return;
  }
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
  return;
  }
  if (! current_user_can('edit_post', $post_id)) {
  return;
  }

    $fields = array(
    '_mt_video_url' => 'mt_video_url',
    '_mt_video_label_1' => 'mt_video_label_1',
    '_mt_video_label_2' => 'mt_video_label_2',
    '_mt_video_release_date' => 'mt_video_release_date',
    '_mt_video_code' => 'mt_video_code',
    );

    foreach ( $fields as $meta_key => $form_key ) {
    if ( isset( $_POST[ $form_key ] ) ) {
    $raw = wp_unslash( $_POST[ $form_key ] );

    if ( $meta_key === '_mt_video_url' ) {
    // อนุญาตให้เก็บทั้ง URL หรือโค้ด iframe
    $allowed_tags = array(
    'iframe' => array(
    'src' => true,
    'width' => true,
    'height' => true,
    'frameborder' => true,
    'allow' => true,
    'allowfullscreen'=> true,
    'style' => true,
    'loading' => true,
    ),
    );

    // ถ้า user ใส่แค่ URL เฉย ๆ ก็จะไม่โดนตัดอะไร
    $value = wp_kses( $raw, $allowed_tags );
    } else {
    // ฟิลด์อื่น sanitize ปกติ
    $value = sanitize_text_field( $raw );
    }

    update_post_meta( $post_id, $meta_key, $value );
    } else {
    delete_post_meta( $post_id, $meta_key );
    }
    }
    }
add_action('save_post', 'mt_save_video_meta');


/**
* Helper: primary category name
*/
function mt_get_primary_category_name()
{
$cats = get_the_category();
  if (! empty($cats)) {
  return esc_html($cats[0]->name);
  }
  return '';
  }

function mt_get_user_list($user_id, $meta_key)
{
$list = get_user_meta($user_id, $meta_key, true);
if (! is_array($list)) {
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

  if (! is_user_logged_in()) {
  wp_send_json_error('login_required');
  }

  $user_id = get_current_user_id();
  $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

  if (! $post_id) {
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

  if (! is_user_logged_in()) {
  wp_send_json_error('login_required');
  }

  $user_id = get_current_user_id();
  $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

  if (! $post_id) {
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
  * SEO meta box: title + description
  */
  function mt_add_seo_meta_box()
  {
  // post types ที่อยากให้มี field SEO
  $post_types = array('post', 'page', 'video');

  foreach ($post_types as $pt) {
  add_meta_box(
  'mt_seo_meta',
  __('SEO Settings', 'missav-tailwind'),
  'mt_seo_meta_box_callback',
  $pt,
  'normal',
  'low'
  );
  }
  }
  add_action('add_meta_boxes', 'mt_add_seo_meta_box');

  function mt_seo_meta_box_callback($post)
  {
  $seo_title = get_post_meta($post->ID, '_mt_seo_title', true);
  $seo_desc = get_post_meta($post->ID, '_mt_seo_description', true);

  wp_nonce_field('mt_save_seo_meta', 'mt_seo_meta_nonce');
  ?>
  <style>
    .mt-seo-field label {
      display: block;
      font-weight: 600;
      margin-bottom: 4px;
    }

    .mt-seo-field input,
    .mt-seo-field textarea {
      width: 100%;
      max-width: 600px;
    }

    .mt-seo-note {
      font-size: 11px;
      color: #666;
    }
  </style>
  <div class="mt-seo-field">
    <p>
      <label for="mt_seo_title">SEO Title</label>
      <input type="text" id="mt_seo_title" name="mt_seo_title" value="<?php echo esc_attr($seo_title); ?>">
      <span class="mt-seo-note">แนะนำไม่เกิน ~60 ตัวอักษร</span>
    </p>

    <p>
      <label for="mt_seo_description">SEO Description</label>
      <textarea id="mt_seo_description" name="mt_seo_description"
        rows="3"><?php echo esc_textarea($seo_desc); ?></textarea>
      <span class="mt-seo-note">แนะนำไม่เกิน ~155 ตัวอักษร</span>
    </p>
  </div>
  <?php
}

function mt_save_seo_meta($post_id)
{
  if (! isset($_POST['mt_seo_meta_nonce'])) {
    return;
  }
  if (! wp_verify_nonce($_POST['mt_seo_meta_nonce'], 'mt_save_seo_meta')) {
    return;
  }
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }
  if (! current_user_can('edit_post', $post_id)) {
    return;
  }

  $fields = array(
    '_mt_seo_title'       => 'mt_seo_title',
    '_mt_seo_description' => 'mt_seo_description',
  );

  foreach ($fields as $meta_key => $form_key) {
    if (isset($_POST[$form_key])) {
      $value = ($meta_key === '_mt_seo_description')
        ? sanitize_textarea_field(wp_unslash($_POST[$form_key]))
        : sanitize_text_field(wp_unslash($_POST[$form_key]));

      update_post_meta($post_id, $meta_key, $value);
    } else {
      delete_post_meta($post_id, $meta_key);
    }
  }
}
add_action('save_post', 'mt_save_seo_meta');

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
add_filter('pre_get_document_title', 'mt_filter_document_title');

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
  if (! $description) {
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
