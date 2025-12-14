<?php
// Get video metadata
$raw_views = (int) get_post_meta(get_the_ID(), '_mt_view_count', true);
$upload_date = get_the_date('Y-m-d');

// Format views count - always show as X.XXK format
if ($raw_views >= 1000000) {
  $views_str = number_format($raw_views / 1000000, 2) . 'M';
} else {
  $views_str = number_format($raw_views / 1000, 2) . 'K';
}
?>

<div class="w-full flex space-x-2 relative">

  <div class="flex flex-col z-10">
    <div class="flex flex-row gap-2">
      <span class="px-1 py-0.5 text-white text-[10px] font-semibold rounded"
        style="background-color: rgb(101, 101, 101);padding: 0 2px;">
        <?php echo esc_html($upload_date); ?>
      </span>
      <span class="px-1 py-0.5 text-white text-[10px] font-semibold rounded flex items-center" style="background-color: #fff;
    color: #f65983;gap: 2px;padding: 0 2px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
          style="width: 10px; height: 10px;">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
        <span><?php echo esc_html($views_str); ?></span>
      </span>
    </div>
    <!-- Thumbnail with overlay -->
    <a href="<?php the_permalink(); ?>" class="relative block w-28 h-[80px] flex-shrink-0 rounded overflow-hidden">
      <?php if (has_post_thumbnail()) {
        the_post_thumbnail('thumbnail', array(
          'class' => 'w-28 h-[80px] object-cover'
        ));
      } else { ?>
                              <div class="w-28 h-[80px] bg-gray-800"></div>
                            <?php } ?>
  </div>

</a>


<!-- Content -->
  <div class="flex-1 pt-4r">
    <h3 class="text-sm font-medium leading-tight mb-1">
      <a href="<?php the_permalink(); ?>" class="hover:text-pink-500 transition-colors">
        <?php the_title(); ?>
      </a>
    </h3>
    <div class="text-xs text-gray-500">
      <?php echo get_the_date(); ?>
    </div>
  </div>
</div>

