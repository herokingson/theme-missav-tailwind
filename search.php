<?php
get_header();

$keyword = get_search_query();

// หน้า pagination ปัจจุบัน
$paged = get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : 1;

// sort จาก query string ?sort=
$sort_param = isset( $_GET['sort'] ) ? sanitize_text_field( wp_unslash( $_GET['sort'] ) ) : 'date_desc';
$order      = ( $sort_param === 'date_asc' ) ? 'ASC' : 'DESC';

// query วิดีโอที่ match คำค้นหา
$q = new WP_Query( array(
    'post_type'      => 'video',      // ถ้าอยากให้รวม post ปกติด้วยก็เพิ่ม 'post'
    's'              => $keyword,
    'posts_per_page' => 18,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => $order,
) );
?>

<header class="mb-6">
  <h1 class="text-2xl md:text-3xl font-semibold text-center mb-6">
    Search result of
    <span class="text-pink-500">
      <?php echo $keyword ? esc_html( $keyword ) : 'all'; ?>
    </span>
  </h1>

  <div class="flex items-center justify-end text-sm text-gray-300 flex-wrap gap-3">


    <form method="get" class="flex items-center space-x-2">
      <?php if ( $keyword ) : ?>
      <input type="hidden" name="s" value="<?php echo esc_attr( $keyword ); ?>">
      <?php endif; ?>
      <span class="text-gray-400">Sort by:</span>
      <select name="sort" class="bg-gray-900 border border-gray-700 text-xs px-2 py-1 rounded-md focus:outline-none"
        onchange="this.form.submit()">
        <option value="date_desc" <?php selected( $sort_param, 'date_desc' ); ?>>
          Release date (newest)
        </option>
        <option value="date_asc" <?php selected( $sort_param, 'date_asc' ); ?>>
          Release date (oldest)
        </option>
      </select>
    </form>
  </div>
</header>

<?php if ( $q->have_posts() ) : ?>

<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3 mb-8">
  <?php
        while ( $q->have_posts() ) :
            $q->the_post();
            // ใช้การ์ดเดียวกับหน้าอื่น
            get_template_part( 'template-parts/content', 'card' );
        endwhile;
        ?>
</div>

<div class="mt-6">
  <?php
        echo paginate_links( array(
            'total'     => $q->max_num_pages,
            'current'   => $paged,
            'mid_size'  => 2,
            'prev_text' => '« Prev',
            'next_text' => 'Next »',
        ) );
        ?>
</div>

<?php else : ?>

<p class="text-center text-gray-400 mt-10">
  No videos found for
  <span class="text-pink-400 font-semibold">
    <?php echo $keyword ? esc_html( $keyword ) : 'this search'; ?>
  </span>
</p>

<?php endif; ?>

<?php
wp_reset_postdata();
get_footer();
