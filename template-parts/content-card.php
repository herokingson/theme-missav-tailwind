<?php
$video_url = get_post_meta( get_the_ID(), '_mt_video_url', true );
?>
<article
  class="group bg-gray-900 rounded-md overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 ring-1 ring-gray-800 hover:ring-pink-900">
  <div class="relative w-full h-40 overflow-hidden bg-gray-800">
    <a href="<?php the_permalink(); ?>" class="block w-full h-full">
      <?php if (has_post_thumbnail()): ?>
        <?php the_post_thumbnail('medium', array(
          'class' => 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-500'
        )); ?>
      <?php else: ?>
        <div class="w-full h-full flex items-center justify-center text-gray-600 text-xs">
          No Thumbnail
        </div>
      <?php endif; ?>
    </a>

    <!-- Overlays -->
    <div class="absolute inset-x-0 top-0 p-2 flex justify-between items-start pointer-events-none z-10">

      <!-- Date: Top Left -->
      <div class="px-1.5 py-0.5 bg-black bg-opacity-70 backdrop-blur-sm text-[10px] text-gray-200 rounded-sm font-medium tracking-wide shadow-sm p-1">
    <?php echo get_the_date('Y-m-d'); ?>
  </div>

  <!-- Views: Top Right -->
  <?php if (function_exists('mt_get_post_views')):
    $raw_views = (int) get_post_meta(get_the_ID(), '_mt_view_count', true);
    if ($raw_views >= 1000000) {
      $views_str = number_format($raw_views / 1000000, 2) . 'M';
    } elseif ($raw_views >= 1000) {
      $views_str = number_format($raw_views / 1000, 1) . 'K';
    } else {
      $views_str = $raw_views;
    }
    ?>
    <div
      class="px-1.5 py-0.5 bg-pink-600 bg-opacity-90 text-[10px] text-white rounded-sm font-bold shadow-sm flex items-center space-x-1 p-1">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
        style="width:10px;height:10px;" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
      </svg>
      <span><?php echo esc_html($views_str) . ' view'; ?></span>
    </div>
  <?php endif; ?>
</div>

<!-- Hover Overlay Gradient -->
<a href="<?php the_permalink(); ?>"
  class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-0 group-hover:opacity-60 transition-opacity duration-300"></a>
</div>

<div class="p-2">
  <h3
    class="text-sm font-medium leading-snug text-gray-200 group-hover:text-pink-500 transition-colors line-clamp-2 h-10">
    <a href="<?php the_permalink(); ?>">
        <?php the_title(); ?>
      </a>
    </h3>
  </div>
</article>
