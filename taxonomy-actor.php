<?php
get_header();

// term ปัจจุบัน
$term  = get_queried_object();
$title = isset( $term->name ) ? $term->name : '';
$description = term_description();

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

<div class="container mx-auto px-4 py-6">

    <!-- Actor Profile Header -->
    <header class="mb-10 bg-gray-800/50 backdrop-blur-md rounded-xl p-6 md:p-10 relative overflow-hidden">

        <!-- Background decorative elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none opacity-20">
            <div class="absolute top-[-50px] left-[-50px] w-40 h-40 bg-pink-500 rounded-full blur-[80px]"></div>
            <div class="absolute bottom-[-50px] right-[-50px] w-40 h-40 bg-purple-500 rounded-full blur-[80px]"></div>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6 md:gap-10">
            <!-- Actor Image -->
            <?php
            $image_id = get_term_meta($term->term_id, 'mt_actor_image_id', true);
            $img_url = '';
            if ($image_id) {
              $img_src = wp_get_attachment_image_url($image_id, 'medium');
              if ($img_src)
                $img_url = $img_src;
            }
            // Fallback image if needed, or just show icon
            ?>
<div class="flex-shrink-0">
  <?php if ($img_url): ?>
    <div class="w-32 h-32 md:w-40 md:h-40 rounded-full p-1 bg-gradient-to-tr from-pink-500 to-purple-600 shadow-xl">
      <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($title); ?>"
        class="w-full h-full rounded-full object-cover">
    </div>
  <?php else: ?>
    <div class="w-32 h-32 md:w-40 md:h-40 rounded-full bg-gray-700 flex items-center justify-center shadow-inner">
      <span class="text-4xl text-gray-400 font-bold uppercase"><?php echo mb_substr($title, 0, 1); ?></span>
    </div>
  <?php endif; ?>
</div>

<!-- Actor Info -->
<div class="flex-grow text-center md:text-left">
  <h1 class="text-3xl md:text-4xl font-bold mb-3 text-white tracking-tight">
    <?php echo esc_html($title); ?>
  </h1>

  <?php if ($description): ?>
    <div class="text-gray-300 text-sm md:text-base max-w-2xl leading-relaxed">
      <?php echo $description; ?>
  </div>
  <?php endif; ?>

                <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-4 text-xs font-mono text-gray-400">
                    <span class="bg-gray-900/50 px-3 py-1 rounded-full border border-gray-700">
                    <?php echo $q->found_posts; ?> Videos
                  </span>
                  <!-- Add more stats here if available -->
                </div>
                </div>
                
                <!-- Sort/Filter (Desktop Right) -->
                <div class="w-full md:w-auto mt-4 md:mt-0 flex justify-center md:justify-end">
                  <form method="get" class="flex items-center space-x-2 bg-gray-900/80 p-2 rounded-lg">
                    <span class="text-gray-400 text-xs uppercase font-semibold pl-2">Sort by:</span>
                    <select name="sort"
                      class="flex-1 px-3 py-2 rounded-l-md bg-gray-900 border border-gray-700 text-sm focus:outline-none cursor-pointer"
                      onchange="this.form.submit()">
                      <option value="date_desc" <?php selected($sort, 'date_desc'); ?>>Newest First</option>
                      <option value="date_asc" <?php selected($sort, 'date_asc'); ?>>Oldest First</option>
                      </select>
                      </form>
                      </div>
        </div>
    </header>

    <!-- Video Grid -->
    <?php if ($q->have_posts()): ?>
      <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-5 xl:grid-cols-8 gap-4 md:gap-6 mb-12">
        <?php
        while ($q->have_posts()):
          $q->the_post();
          get_template_part('template-parts/content', 'card');
        endwhile;
        ?>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-8">
          <?php
          // Custom Styling for Pagination
          $pagination = paginate_links(array(
            'total' => $q->max_num_pages,
            'current' => $paged,
            'mid_size' => 1,
            'prev_text' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>',
            'next_text' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>',
            'type' => 'array',
          ));

          if (is_array($pagination)) {
            echo '<ul class="flex items-center space-x-2">';
            foreach ($pagination as $page) {
              // Check if it's the current page
              if (strpos($page, 'current') !== false) {
                echo '<li><span class="px-3 py-2 bg-pink-600 text-white rounded-md shadow-md text-sm font-medium">' . strip_tags($page) . '</span></li>';
              } else {
                // Add generic styling to links
                $page = str_replace('page-numbers', 'page-numbers px-3 py-2 bg-gray-800 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md transition-colors text-sm font-medium flex items-center justify-center', $page);
                echo '<li>' . $page . '</li>';
              }
            }
            echo '</ul>';
          }
          ?>
        </div>
<?php else: ?>
  <div class="text-center py-20 bg-gray-800/30 rounded-xl border border-dashed border-gray-700">
    <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z">
      </path>
    </svg>
    <p class="text-xl text-gray-400 font-semibold">No videos found for this actor.</p>
    <p class="text-gray-500 mt-2">Try checking back later.</p>
  </div>
<?php endif; ?>

</div>

<?php
wp_reset_postdata();
get_footer();
