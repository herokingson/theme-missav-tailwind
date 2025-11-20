<?php
get_header();
?>

<section class="mb-8 mx-auto text-center">
  <h1 class="text-2xl font-semibold mb-1">Search any <span class="text-gray-700">Video</span></h1>
  <p class="text-sm text-gray-400 mb-4">Example: Big Tit, Blonde, Schoolgirl</p>
  <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="flex max-w-xl mx-auto">
    <input type="text" name="s" class="flex-1 px-3 py-2 rounded-l-md bg-gray-900 border border-gray-700 text-sm"
      placeholder="Search...">
    <button type="submit" class="px-4 py-2 rounded-r-md bg-gray-700 text-sm">
      Search
    </button>
  </form>
</section>

<?php
// ดึง "video" ตาม category slug
if ( ! function_exists( 'mt_render_section_by_category' ) ) :
function mt_render_section_by_category( $slug, $title ) {

    // หา term category จาก slug
    $cat = get_term_by( 'slug', $slug, 'category' );
    if ( ! $cat ) {
        return;
    }

    $q = new WP_Query( array(
        'post_type'           => 'video',        // CPT ของคุณ
        'posts_per_page'      => 6,
        'ignore_sticky_posts' => true,
        'tax_query'           => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => $cat->term_id,
            ),
        ),
    ) );

    if ( $q->have_posts() ) : ?>
<section class="mb-10">
  <div class="flex items-center justify-between mb-2">
    <h2 class="text-xl font-semibold"><?php echo esc_html( $title ); ?></h2>

  </div>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pb-5">
    <?php
            while ( $q->have_posts() ) : $q->the_post();
                get_template_part( 'template-parts/content', 'card' );
            endwhile;
            ?>
  </div>
  <div class="flex justify-center mx-auto">
    <a href=" <?php echo esc_url( get_category_link( $cat ) ); ?>"
      class="text-xl text-gray-400 hover:text-gray-800 hover">
      Load more »
    </a>
  </div>
</section>
<?php
    endif;

    wp_reset_postdata();
}
endif;

// ดึงทุก category ที่มีโพสต์ (และใช้กับ video CPT ด้วย)
$terms = get_terms( array(
'taxonomy' => 'category',
'hide_empty' => true,
) );

if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
foreach ( $terms as $term ) {
// ใช้ slug และชื่อ category เป็น title
mt_render_section_by_category( $term->slug, $term->name );
}
}

get_footer();
?>