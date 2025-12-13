<?php
get_header();

// term ปัจจุบัน
$term  = get_queried_object();
$title = isset( $term->name ) ? $term->name : '';

$paged = get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : 1;

// sort param จาก GET ?sort=
$sort  = isset( $_GET['sort'] ) ? sanitize_text_field( wp_unslash( $_GET['sort'] ) ) : 'date_desc';
$order = ( $sort === 'date_asc' ) ? 'ASC' : 'DESC';

// query วิดีโอใน Actor นี้เท่านั้น
$q = new WP_Query( array(
    'post_type'      => 'video',
    'posts_per_page' => 18,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => $order,
    'tax_query'      => array(
        array(
            'taxonomy' => 'actor',
            'field'    => 'term_id',
            'terms'    => $term->term_id,
        ),
    ),
) );
?>

<header class="mb-6 flex flex-col items-center">
  <?php
    $image_id = get_term_meta( $term->term_id, 'mt_actor_image_id', true );
    if ( $image_id ) {
        $img_src = wp_get_attachment_image_url( $image_id, 'medium' );
        if ( $img_src ) {
            echo '<img src="' . esc_url( $img_src ) . '" alt="' . esc_attr( $title ) . '" class="w-24 h-24 md:w-32 md:h-32 rounded-full object-cover mb-4 border-2 border-pink-500 shadow-md">';
        }
    }
  ?>
  <h1 class="text-2xl md:text-3xl font-semibold text-center mb-2">
    <span class="text-pink-500"><?php echo esc_html( $title ); ?></span>
  </h1>

  <?php if ( term_description() ) : ?>
  <div class="text-gray-400 text-sm max-w-2xl text-center mb-6">
    <?php echo term_description(); ?>
  </div>
  <?php endif; ?>

  <div class="flex items-center justify-end text-sm text-gray-300 flex-wrap gap-3 w-full">
    <form method="get" class="flex items-center space-x-2 ml-auto">
      <span class="text-gray-400">เรียงตาม:</span>
      <select name="sort" class="bg-gray-900 border border-gray-700 text-xs px-2 py-1 rounded-md focus:outline-none"
        onchange="this.form.submit()">
        <option value="date_desc" <?php selected( $sort, 'date_desc' ); ?>>
          วันที่จำหน่าย (ใหม่สุด)
        </option>
        <option value="date_asc" <?php selected( $sort, 'date_asc' ); ?>>
          วันที่จำหน่าย (เก่าสุด)
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
            get_template_part( 'template-parts/content', 'card' );
        endwhile;
        ?>
</div>

<div class="mt-6">
  <?php
        echo paginate_links( array(
            'total'   => $q->max_num_pages,
            'current' => $paged,
            'mid_size'=> 2,
            'prev_text' => '« ก่อนหน้า',
            'next_text' => 'ถัดไป »',
        ) );
        ?>
</div>
<?php else : ?>
<p class="text-center text-gray-400 mt-10">ไม่พบวิดีโอของนักแสดงคนนี้</p>
<?php endif; ?>

<?php
wp_reset_postdata();
get_footer();
