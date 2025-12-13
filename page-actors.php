<?php
/**
 * Template Name: Actors List
 */
get_header();
?>

<div class="container mx-auto px-4 py-6">
  <header class="mb-8 text-center">
    <h1 class="text-3xl font-bold mb-2">Actors List</h1>
    <p class="text-gray-400 text-sm">Actors ALl</p>
  </header>

  <?php
  // ดึงรายการ actor ทั้งหมด
  $terms = get_terms( array(
      'taxonomy'   => 'actor',
      'hide_empty' => false, // show all even if no videos
      'orderby'    => 'name',
      'order'      => 'ASC',
  ) );

  if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
  ?>
  <div class="grid grid-cols-2 md:grid-cols-8 lg:grid-cols-10 gap-6">
    <?php foreach ( $terms as $term ) :
        $term_link = get_term_link( $term );
        if ( is_wp_error( $term_link ) ) continue;

        $image_id = get_term_meta( $term->term_id, 'mt_actor_image_id', true );
        $img_src  = '';
        if ( $image_id ) {
            $img_src = wp_get_attachment_image_url( $image_id, 'medium' );
        }
    ?>
    <a href="<?php echo esc_url( $term_link ); ?>" class="group flex flex-col items-center">
      <div class="w-[100px] h-[100px] md:w-32 md:h-32 rounded-full overflow-hidden border-2 border-gray-800 group-hover:border-pink-500 transition-colors mb-3 relative bg-gray-900">
        <?php if ( $img_src ) : ?>
        <img src="<?php echo esc_url( $img_src ); ?>" alt="<?php echo esc_attr( $term->name ); ?>"
          class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
        <?php else : ?>
        <div class="w-full h-full flex items-center justify-center text-gray-600">
          <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd">
            </path>
          </svg>
        </div>
        <?php endif; ?>
      </div>
      <h3 class="text-sm font-medium text-gray-300 group-hover:text-pink-500 text-center">
        <?php echo esc_html( $term->name ); ?>
      </h3>
      <span class="text-xs text-gray-500 mt-1">
        <?php echo $term->count; ?> วิดีโอ
      </span>
    </a>
    <?php endforeach; ?>
  </div>
  <?php else : ?>
  <p class="text-center text-gray-500">ไม่พบนักแสดง</p>
  <?php endif; ?>
</div>

<?php
get_footer();
