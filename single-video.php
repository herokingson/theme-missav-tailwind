<?php
get_header();

if ( have_posts() ) :
    while ( have_posts() ) : the_post();

        $video_url    = get_post_meta( get_the_ID(), '_mt_video_url', true );
        $release_date = get_post_meta( get_the_ID(), '_mt_video_release_date', true );
        $video_code   = get_post_meta( get_the_ID(), '_mt_video_code', true );

        $is_logged = is_user_logged_in();
        $user_id = get_current_user_id();
        $favorites = $is_logged ? mt_get_user_list( $user_id, '_mt_favorite_videos' ) : array();
        $playlist = $is_logged ? mt_get_user_list( $user_id, '_mt_playlist_videos' ) : array();
        $is_favorite = $is_logged && in_array( get_the_ID(), $favorites, true );
        $in_playlist = $is_logged && in_array( get_the_ID(), $playlist, true );
        ?>
<div class="md:flex md:space-x-6">
  <!-- ด้านซ้าย: player + title + actions + details -->
  <div class="md:w-2/3">
    <div class="bg-black rounded-md overflow-hidden mb-4 relative">
      <?php
    $video_raw = get_post_meta( get_the_ID(), '_mt_video_url', true );
    $video_raw = trim( (string) $video_raw );

    if ( $video_raw ) :

        // 1) ถ้า user แปะโค้ด <iframe ...> มา → ใช้ตามนั้นเลย
        if ( stripos( $video_raw, '<iframe' ) !== false ) : ?>

      <div class="relative w-full h-[400px]">
        <div class="absolute inset-0">
          <?php echo $video_raw; ?>
        </div>
      </div>

      <?php
        // 2) ถ้าเป็น mp4 → <video>
        elseif ( preg_match( '/\.mp4($|\?)/i', $video_raw ) ) : ?>

      <video class="w-full" controls
        poster="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ); ?>">
        <source src="<?php echo esc_url( $video_raw ); ?>" type="video/mp4">
      </video>
      <?php
        // 3) ที่เหลือค่อยเล่น YouTube / oEmbed ตามเดิม (optional)
        else :

            $parsed    = wp_parse_url( $video_raw );
            $host      = isset( $parsed['host'] ) ? strtolower( $parsed['host'] ) : '';
            $path      = isset( $parsed['path'] ) ? $parsed['path'] : '';
            $embed_url = '';

            if ( $host && ( strpos( $host, 'youtube.com' ) !== false || strpos( $host, 'youtu.be' ) !== false ) ) {
                if ( strpos( $host, 'youtube.com' ) !== false && ! empty( $parsed['query'] ) ) {
                    parse_str( $parsed['query'], $query_args );
                    if ( ! empty( $query_args['v'] ) ) {
                        $embed_url = 'https://www.youtube.com/embed/' . $query_args['v'];
                    }
                }
                if ( ! $embed_url && strpos( $host, 'youtu.be' ) !== false && ! empty( $path ) ) {
                    $video_id  = trim( $path, '/' );
                    $embed_url = 'https://www.youtube.com/embed/' . $video_id;
                }
                if ( ! $embed_url && strpos( $path, '/embed/' ) !== false ) {
                    $embed_url = 'https://www.youtube.com' . $path;
                }
            }

            if ( $embed_url ) : ?>

      <div class="relative w-full">
        <iframe class="absolute inset-0 w-full h-[400px]" src="<?php echo esc_url( $embed_url ); ?>" frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen></iframe>
      </div>

      <?php else :

                $embed_html = wp_oembed_get( $video_raw, array(
                    'width'  => 1280,
                    'height' => 720,
                ) );

                if ( $embed_html ) : ?>
      <div class="relative w-full h-[400px]">
        <div class="absolute inset-0">
          <?php echo $embed_html; ?>
        </div>
      </div>
      <?php else : ?>
      <div
        class="w-full h-64 md:h-[420px] bg-gray-900 flex flex-col items-center justify-center text-gray-400 text-sm px-4 text-center">
        <p>Cannot embed this video URL automatically.</p>
        <p class="mt-2">
          <a href="<?php echo esc_url( $video_raw ); ?>" target="_blank" class="text-pink-500 underline">
            Open video in new tab
          </a>
        </p>
      </div>
      <?php endif; ?>
      <?php endif; ?>

      <?php endif; ?>

      <?php else : ?>
      <div class="w-full h-64 md:h-[420px] bg-gray-900 flex items-center justify-center text-gray-500">
        No video URL set.
      </div>
      <?php endif; ?>
    </div>

    <!-- ชื่อเรื่องใต้ player -->
    <h1 class="text-lg md:text-xl font-semibold mb-2">
      <?php the_title(); ?>
    </h1>

    <!-- action row: Save / Playlist / Download / Share -->
    <!-- <div class="flex items-center space-x-4 text-xs text-gray-300 mb-4">
      <button class="flex items-center space-x-1 hover:text-pink-500">
        <span>★</span><span>Save</span>
      </button>
      <button class="flex items-center space-x-1 hover:text-pink-500">
        <span>▶</span><span>Playlist</span>
      </button>
      <button class="flex items-center space-x-1 hover:text-pink-500">
        <span>⬇</span><span>Download</span>
      </button>
      <button class="flex items-center space-x-1 hover:text-pink-500">
        <span>⤴</span><span>Share</span>
      </button>
    </div> -->
    <div class="flex items-center flex-wrap gap-4 text-xs text-gray-300 mb-4" data-post-id="<?php the_ID(); ?>">
      <!-- Save -->
      <button type="button"
        class="flex items-center space-x-1 hover:text-pink-500 js-mt-save <?php echo $is_favorite ? 'text-pink-500' : ''; ?>">
        <span>★</span>
        <span class="js-label"><?php echo $is_favorite ? 'Saved' : 'Save'; ?></span>
      </button>

      <!-- Playlist -->
      <button type="button"
        class="flex items-center space-x-1 hover:text-pink-500 js-mt-playlist <?php echo $in_playlist ? 'text-pink-500' : ''; ?>">
        <span>▶</span>
        <span class="js-label"><?php echo $in_playlist ? 'In playlist' : 'Playlist'; ?></span>
      </button>

      <!-- Download: ใช้ Video URL meta -->
      <?php if ( $video_url && preg_match( '/\\.mp4($|\\?)/', $video_url ) ) : ?>
      <a href="<?php echo esc_url( $video_url ); ?>"
        class="flex items-center space-x-1 hover:text-pink-500 js-mt-download" download>
        <span>⬇</span><span>Download</span>
      </a>
      <?php endif; ?>

      <!-- Share -->
      <div class="relative">
        <button type="button" class="flex items-center space-x-1 hover:text-pink-500 js-mt-share-toggle">
          <span>⤴</span><span>Share</span>
        </button>

        <div id="mt-share-panel"
          class="hidden absolute z-20 mt-2 w-56 rounded-md bg-gray-900 border border-gray-700 p-3 text-xs text-gray-200">
          <p class="mb-2">Share this video</p>
          <div class="space-y-1 mb-2">
            <a target="_blank" rel="noopener"
              href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode( get_permalink() ); ?>&text=<?php echo rawurlencode( get_the_title() ); ?>"
              class="block hover:text-pink-500">
              Twitter / X
            </a>
            <a target="_blank" rel="noopener"
              href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( get_permalink() ); ?>"
              class="block hover:text-pink-500">
              Facebook
            </a>
          </div>
          <div class="flex items-center space-x-1">
            <input type="text" readonly class="flex-1 bg-gray-800 border border-gray-700 px-2 py-1 text-[11px]"
              value="<?php echo esc_attr( get_permalink() ); ?>" id="mt-share-url">
            <button type="button" class="px-2 py-1 text-[11px] bg-pink-600 hover:bg-pink-700 rounded js-mt-copy-link">
              Copy
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Details block -->
    <section class="mt-6">
      <div class="flex items-center space-x-2 mb-2">
        <span class="w-2 h-2 rounded-full bg-red-500"></span>
        <span class="uppercase text-xs tracking-widest text-red-400">Details</span>
      </div>

      <div class="space-y-1 text-sm">
        <!-- <?php //if ( $label_1 ) : ?>
        <div class="flex justify-between border-b border-gray-800 py-1">
          <span class="text-gray-300"><?php //echo esc_html( $label_1 ); ?></span>
          <span class="text-gray-400"><?php //echo esc_html( $label_1 ); ?></span>
        </div>
        <?php //endif; ?>
        <?php //if ( $label_2 ) : ?>
        <div class="flex justify-between border-b border-gray-800 py-1">
          <span class="text-gray-300"><?php //echo esc_html( $label_2 ); ?></span>
          <span class="text-gray-400"><?php //echo esc_html( $label_2 ); ?></span>
        </div> -->
        <?php //endif; ?>
        <?php if ( $release_date ) : ?>
        <div class="flex justify-between border-b border-gray-800 py-1">
          <span class="text-gray-400">Release date</span>
          <span class="text-gray-200"><?php echo esc_html( $release_date ); ?></span>
        </div>
        <?php endif; ?>
        <?php if ( $video_code ) : ?>
        <div class="flex justify-between border-b border-gray-800 py-1">
          <span class="text-gray-400">Code</span>
          <span class="text-gray-200"><?php echo esc_html( $video_code ); ?></span>
        </div>
        <?php endif; ?>
        <div class="flex justify-between border-b border-gray-800 py-1">
          <span class="text-gray-400">Title</span>
          <span class="text-gray-200"><?php the_title(); ?></span>
        </div>
        <div class="border-b border-gray-800 py-2">
          <span class="text-gray-400">Tag: </span>
          <?php
                            $tags_list = get_the_tag_list( '', ', ' );
                            if ( $tags_list ) {
                                echo '<span class="text-yellow-300 text-sm">' . $tags_list . '</span>';
                            } else {
                                echo '<span class="text-gray-500 text-xs">No tags</span>';
                            }
                            ?>
        </div>
      </div>
    </section>

    <!-- เนื้อหาเต็ม ถ้ามี -->
    <?php if ( get_the_content() ) : ?>
    <div class="prose prose-invert max-w-none text-sm mt-6">
      <?php the_content(); ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- ด้านขวา: วิดีโออื่นๆ (sidebar) -->
  <aside class="md:w-1/3 mt-6 md:mt-0">
    <h2 class="text-sm font-semibold mb-3 text-gray-200">More videos</h2>
    <div class="space-y-3">
      <?php
                    $sidebar_q = new WP_Query( array(
                        'post_type'      => 'video',
                        'posts_per_page' => 8,
                        'post__not_in'   => array( get_the_ID() ),
                    ) );

                    if ( $sidebar_q->have_posts() ) :
                        while ( $sidebar_q->have_posts() ) : $sidebar_q->the_post(); ?>
      <div class="flex space-x-2">
        <a href="<?php the_permalink(); ?>" class="w-28 flex-shrink-0">
          <?php if ( has_post_thumbnail() ) {
                                        the_post_thumbnail( 'thumbnail', array(
                                            'class' => 'w-28 h-16 object-cover rounded'
                                        ) );
                                    } else { ?>
          <div class="w-28 h-16 bg-gray-800 rounded"></div>
          <?php } ?>
        </a>
        <div class="text-xs">
          <a href="<?php the_permalink(); ?>" class="font-semibold line-clamp-2 hover:text-pink-500">
            <?php the_title(); ?>
          </a>
          <div class="text-[10px] text-gray-400 mt-1">
            <?php echo get_post_meta( get_the_ID(), '_mt_video_code', true ); ?>
          </div>
        </div>
      </div>
      <?php
                        endwhile;
                        wp_reset_postdata();
                    else : ?>
      <p class="text-xs text-gray-500">No videos.</p>
      <?php endif; ?>
    </div>
  </aside>
</div>

<!-- ด้านล่าง: grid วิดีโออื่น (related) -->
<section class="mt-10">
  <h2 class="text-lg font-semibold mb-3 text-gray-200">More from this site</h2>
  <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
    <?php
                $bottom_q = new WP_Query( array(
                    'post_type'      => 'video',
                    'posts_per_page' => 10,
                    'post__not_in'   => array( get_the_ID() ),
                ) );

                if ( $bottom_q->have_posts() ) :
                    while ( $bottom_q->have_posts() ) : $bottom_q->the_post(); ?>
    <article class="group bg-gray-900 rounded-md overflow-hidden hover:shadow-lg transition">
      <a href="<?php the_permalink(); ?>" class="block relative">
        <?php if ( has_post_thumbnail() ) {
                                    the_post_thumbnail( 'medium', array(
                                        'class' => 'w-full h-32 object-cover group-hover:scale-105 transform transition'
                                    ) );
                                } else { ?>
        <div class="w-full h-32 bg-gray-800"></div>
        <?php } ?>
      </a>
      <div class="p-2">
        <h3 class="text-xs font-semibold line-clamp-2 mb-1">
          <a href="<?php the_permalink(); ?>" class="hover:text-pink-500">
            <?php the_title(); ?>
          </a>
        </h3>
        <div class="text-[10px] text-gray-400 flex justify-between">
          <span><?php echo get_post_meta( get_the_ID(), '_mt_video_code', true ); ?></span>
        </div>
      </div>
    </article>
    <?php
                    endwhile;
                    wp_reset_postdata();
                else : ?>
    <p class="text-sm text-gray-500">No more videos.</p>
    <?php endif; ?>
  </div>
</section>

<?php
    endwhile;
endif;

get_footer();
