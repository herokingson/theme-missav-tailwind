<?php
$video_url = get_post_meta( get_the_ID(), '_mt_video_url', true );
?>
<article class="group bg-gray-900 rounded-md overflow-hidden shadow hover:shadow-lg transition">
  <a href="<?php the_permalink(); ?>" class="block relative">
    <?php if ( has_post_thumbnail() ) : ?>
    <?php the_post_thumbnail( 'medium', array(
                'class' => 'w-full h-40 object-cover group-hover:scale-105 transform transition'
            ) ); ?>
    <?php else : ?>
    <div class="w-full h-40 bg-gray-800 flex items-center justify-center text-gray-500 text-xs">
      No thumbnail
    </div>
    <?php endif; ?>
  </a>
  <div class="p-2">
    <h3 class="text-sm font-semibold mb-1">
      <a href="<?php the_permalink(); ?>" class="hover:text-pink-500">
        <?php the_title(); ?>
      </a>
    </h3>
    <!-- <div class="text-[11px] text-gray-400 flex justify-between">
            <span><?php //echo get_the_date(); ?></span>
            <span><?php //echo mt_get_primary_category_name(); ?></span>
        </div> -->
  </div>
</article>