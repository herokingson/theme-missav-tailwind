<div class="w-full flex space-x-2">
  <a href="<?php the_permalink(); ?>" class="w-28 flex-shrink-0">
    <?php if (has_post_thumbnail()) {
      the_post_thumbnail('thumbnail', array(
        'class' => 'w-28 h-16 object-cover rounded'
      ));
    } else { ?>
      <div class="w-28 h-16 bg-gray-800 rounded"></div>
    <?php } ?>
  </a>
  <div class="flex-1">
    <h3 class="text-sm font-medium leading-tight mb-1">
      <a href="<?php the_permalink(); ?>" class="hover:text-pink-500 transition-colors">
        <?php the_title(); ?>
      </a>
    </h3>
    <div class="text-xs text-gray-500 space-x-1">
      <?php echo get_the_date(); ?>
      <?php if (function_exists('mt_get_post_views')): ?>
        <span>|</span>
        <span>
          <?php echo mt_get_post_views(get_the_ID()); ?> views
        </span>
      <?php endif; ?>
    </div>
  </div>
</div>
