<?php
get_header();

if (have_posts()):
  while (have_posts()):
    the_post();

    $video_url = get_post_meta(get_the_ID(), '_mt_video_url', true);

    $video_code = get_post_meta(get_the_ID(), '_mt_video_code', true);


    $is_logged = is_user_logged_in();
    $user_id = get_current_user_id();
    $favorites = $is_logged ? mt_get_user_list($user_id, '_mt_favorite_videos') : array();
    $playlist = $is_logged ? mt_get_user_list($user_id, '_mt_playlist_videos') : array();
    $video_description = get_post_meta(get_the_ID(), '_mt_seo_description', true);
    $is_favorite = $is_logged && in_array(get_the_ID(), $favorites, true);
    $in_playlist = $is_logged && in_array(get_the_ID(), $playlist, true);
    ?>
    <div class="md:flex md:space-x-6">
      <!-- ด้านซ้าย: player + title + actions + details -->
      <div class="md:w-2/3">
        <div class="bg-black rounded-md overflow-hidden mb-4 relative">
          <?php
          $video_raw = get_post_meta(get_the_ID(), '_mt_video_url', true);
          $video_raw = trim((string) $video_raw);

          if ($video_raw):

            // 1) ถ้า user แปะโค้ด <iframe ...> มา → ใช้ตามนั้นเลย
            if (stripos($video_raw, '<iframe') !== false): ?>

              <div class="relative w-full h-[400px]">
                <div class="absolute inset-0 [&>iframe]:h-full">
                  <?php echo $video_raw; ?>
                </div>
              </div>

              <?php
              // 2) ถ้าเป็น mp4 → <video>
            elseif (preg_match('/\.mp4($|\?)/i', $video_raw)): ?>

              <video class="w-full" controls poster="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>">
                <source src="<?php echo esc_url($video_raw); ?>" type="video/mp4">
              </video>
              <?php
              // 3) ที่เหลือค่อยเล่น YouTube / oEmbed ตามเดิม (optional)
            else:

              $parsed = wp_parse_url($video_raw);
              $host = isset($parsed['host']) ? strtolower($parsed['host']) : '';
              $path = isset($parsed['path']) ? $parsed['path'] : '';
              $embed_url = '';

              if ($host && (strpos($host, 'youtube.com') !== false || strpos($host, 'youtu.be') !== false)) {
                if (strpos($host, 'youtube.com') !== false && !empty($parsed['query'])) {
                  parse_str($parsed['query'], $query_args);
                  if (!empty($query_args['v'])) {
                    $embed_url = 'https://www.youtube.com/embed/' . $query_args['v'];
                  }
                }
                if (!$embed_url && strpos($host, 'youtu.be') !== false && !empty($path)) {
                  $video_id = trim($path, '/');
                  $embed_url = 'https://www.youtube.com/embed/' . $video_id;
                }
                if (!$embed_url && strpos($path, '/embed/') !== false) {
                  $embed_url = 'https://www.youtube.com' . $path;
                }
              }

              if ($embed_url): ?>

                <div class="relative w-full h-[400px]">
                  <iframe class="absolute inset-0 w-full h-[400px]" src="<?php echo esc_url($embed_url); ?>" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
                  </div>

              <?php else:

                $embed_html = wp_oembed_get($video_raw, array(
                  'width' => 1280,
                  'height' => 720,
                ));

                if ($embed_html): ?>
                              <div class="relative w-full h-[400px]">
                                <div class="absolute inset-0">
                                  <?php echo $embed_html; ?>
                    </div>
                  </div>
                <?php else: ?>
                <div
                  class="w-full h-64 md:h-[420px] bg-gray-900 flex flex-col items-center justify-center text-gray-400 text-sm px-4 text-center">
                  <p>Cannot embed this video URL automatically.</p>
                  <p class="mt-2">
                      <a href="<?php echo esc_url($video_raw); ?>" target="_blank" class="text-pink-500 underline">
                        Open video in new tab
                      </a>
                      </p>
                      </div>
                      <?php endif; ?>
                      <?php endif; ?>

            <?php endif; ?>

          <?php else: ?>
          <div class="w-full h-64 md:h-[420px] bg-gray-900 flex items-center justify-center text-gray-500">
        ไม่มีลิงก์วิดีโอ
      </div>
      <?php endif; ?>
    </div>

    <!-- ชื่อเรื่องใต้ player -->
    <h1 class="text-lg md:text-xl font-semibold mb-2">
      <?php the_title(); ?>
      <?php if ($video_code): ?>
        <span class="ml-2 inline-block px-2 py-0.5 rounded bg-pink-600 text-white text-xs align-middle">
          <?php echo esc_html($video_code); ?>
        </span>
      <?php endif; ?>
    </h1>

    <!-- Video Stats -->
    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-400 mb-4">


      <?php
        $upload_date = get_post_meta(get_the_ID(), '_mt_upload_date', true);
        if ($upload_date):
          ?>
        <div class="flex items-center">
          <span class="font-semibold text-gray-300 mr-1">Latest Date:</span>
          <?php echo esc_html($upload_date); ?>
        </div>
      <?php endif; ?>

      <?php
      $view_count = get_post_meta(get_the_ID(), '_mt_view_count', true);
      $view_count_week = get_post_meta(get_the_ID(), '_mt_view_count_week', true);
      ?>

      <div class="flex items-center">
        <span class="font-semibold text-gray-300 mr-1">Total Views: </span>
        <?php echo mt_format_views($view_count); ?>
      </div>

      <div class="flex items-center">
        <span class="font-semibold text-gray-300 mr-1">Weekly Views: </span>
        <?php echo mt_format_views($view_count_week); ?>
      </div>
    </div>

    <!-- action row: Save / Playlist / Download / Share -->
    <div
      class="flex items-center justify-start flex-wrap gap-3 text-xs text-gray-300 mb-6 border-b border-gray-800 pb-4"
      data-post-id="<?php the_ID(); ?>">
      <!-- Save -->
      <div>
        <button type="button"
          class="group flex items-center space-x-1.5 hover:text-pink-500 transition-colors js-mt-save <?php echo $is_favorite ? 'text-pink-500' : ''; ?>">
          <span class="text-base">★</span>
          <span class="js-label font-medium"><?php echo $is_favorite ? 'บันทึกแล้ว' : 'บันทึก'; ?></span>
        </button>
      </div>

      <!-- Playlist -->
      <div>
        <button type="button"
          class="group flex items-center space-x-1.5 hover:text-pink-500 transition-colors js-mt-playlist <?php echo $in_playlist ? 'text-pink-500' : ''; ?>">
          <span class="text-base">▶</span>
          <span class="js-label font-medium"><?php echo $in_playlist ? 'ในเพลย์ลิสต์' : 'เพลย์ลิสต์'; ?></span>
        </button>

      </div>

      <!-- Download -->
      <?php if ($video_url && preg_match('/\\.mp4($|\\?)/', $video_url)): ?>
        <div>
          <a href="<?php echo esc_url($video_url); ?>"
          class="group flex items-center space-x-1.5 hover:text-pink-500 transition-colors js-mt-download" download>
          <span class="text-base">⬇</span>
          <span class="font-medium">ดาวน์โหลด</span>
        </a>

        </div>
      <?php endif; ?>

      <!-- Share -->
      <div class="relative">
        <button type="button"
          class="group flex items-center space-x-1.5 hover:text-pink-500 transition-colors js-mt-share-toggle">
          <span class="text-base">⤴</span>
          <span class="font-medium">แชร์</span>
        </button>
        <div id="mt-share-panel"
          class="hidden absolute z-20 mt-2 w-56 rounded-md bg-gray-900 border border-gray-700 p-3 text-xs text-gray-200">
          <p class="mb-2">แชร์วิดีโอนี้</p>
          <div class="space-y-1 mb-2">
            <a target="_blank" rel="noopener"
              href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode(get_permalink()); ?>&text=<?php echo rawurlencode(get_the_title()); ?>"
              class="block hover:text-pink-500">
              Twitter / X
            </a>
            <a target="_blank" rel="noopener"
              href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode(get_permalink()); ?>"
              class="block hover:text-pink-500">
              Facebook
            </a>
          </div>
          <div class="flex items-center space-x-1">
            <input type="text" readonly class="flex-1 bg-gray-800 border border-gray-700 px-2 py-1 text-[11px]"
              value="<?php echo esc_attr(get_permalink()); ?>" id="mt-share-url">
            <button type="button" class="px-2 py-1 text-[11px] bg-pink-600 hover:bg-pink-700 rounded js-mt-copy-link">
              คัดลอก
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Details block -->
    <section class="mt-6">
      <div class="flex items-center space-x-2 mb-2">
        <span class="w-2 h-2 rounded-full bg-red-500"></span>
        <span class="uppercase text-xs tracking-widest text-red-400">รายละเอียด</span>
      </div>

      <div class="space-y-1 text-sm">
            <!-- <?php //if ( $label_1 ) :
                ?>
        <div class="flex justify-between border-b border-gray-800 py-1">
          <span class="text-gray-300"><?php //echo esc_html( $label_1 );
                ?>
          </span>
          <span class="text-gray-400">
            <?php //echo esc_html( $label_1 );
                ?>
          </span>
        </div>
        <?php //endif;
            ?>
        <?php //if ( $label_2 ) :
            ?>
        <div class="flex justify-between border-b border-gray-800 py-1">
          <span class="text-gray-300"><?php //echo esc_html( $label_2 );
                ?>
          </span>
          <span class="text-gray-400">
            <?php //echo esc_html( $label_2 );
                ?>
          </span>
        </div> -->
            <?php //endif;
                ?>
<?php
      $studio = get_post_meta(get_the_ID(), '_mt_video_studio', true);
      $duration = get_post_meta(get_the_ID(), '_mt_video_duration', true);
      ?>

<?php if ($studio): ?>
<div class="flex justify-between border-b border-gray-800 py-1">
                <span class="text-gray-400">สตูดิโอ</span>
                <span class="text-gray-200"><?php echo esc_html($studio); ?></span>
              </div>
              <?php endif; ?>
<?php if ($duration): ?>
  <div class="flex justify-between border-b border-gray-800 py-1">
    <span class="text-gray-400">ความยาววิดีโอ</span>
    <span class="text-gray-200"><?php echo esc_html($duration); ?></span>
  </div>
<?php endif; ?>

              <?php
              $actors = get_the_term_list(get_the_ID(), 'actor', '', ', ');
              if ($actors && !is_wp_error($actors)):
                ?>
              <div class="flex justify-between border-b border-gray-800 py-1">
                <span class="text-gray-400">นักแสดง</span>
                <span class="text-pink-500 hover:text-pink-400 transition-colors [&>a]:text-pink-500 [&>a:hover]:text-pink-400">
                  <?php echo $actors; ?>
                </span>
              </div>
            <?php endif; ?>
            <?php if ($video_code): ?>
        <div class="flex justify-between border-b border-gray-800 py-1">
          <span class="text-gray-400">รหัส</span>
                <span class="text-gray-200"><?php echo esc_html($video_code); ?></span>
          </div>
          <?php endif; ?>

          <div class="flex justify-between border-b border-gray-800 py-1">
                  <span class="text-gray-400">ชื่อเรื่อง</span>
                  <span class="text-gray-200"><?php the_title(); ?></span>
          </div>
          <?php if ($video_description): ?>
            <div class="flex h-auto justify-between border-b border-gray-800 py-1">
              <span class="block text-gray-400 mb-1">รายละเอียด</span>
              <div class="text-gray-200 text-sm ">
                <?php echo esc_html($video_description); ?>
              </div>
            </div>
          <?php endif; ?>
                  <div class="border-b border-gray-800 py-2">
                  <span class="text-gray-400">แท็ก: </span>
                  <?php
                  $tags_list = get_the_tag_list('', ', ');
                  if ($tags_list) {
                    echo '<span class="text-yellow-300 text-sm">' . $tags_list . '</span>';
                  } else {
                    echo '<span class="text-gray-500 text-xs">ไม่มีแท็ก</span>';
                  }
                  ?>
            </div>
          </div>
        </section>


        <!-- เนื้อหาเต็ม ถ้ามี -->
        <?php if (get_the_content()): ?>
          <div class="prose prose-invert max-w-none text-sm mt-6">
            <?php the_content(); ?>
          </div>
        <?php endif; ?>
        </div>

      <!-- ด้านขวา: วิดีโออื่นๆ (sidebar) -->
      <aside class="md:w-1/3 mt-6 md:mt-0">
        <!-- Tab Headers -->
        <div class="flex w-full border-b border-gray-800 mb-4">
          <button type="button" class="flex-1 py-3 text-sm font-semibold text-pink-500 border-b-2 border-pink-500 transition-colors js-tab-btn" data-target="#tab-latest">
            ล่าสุด
          </button>
          <button type="button" class="flex-1 py-3 text-sm font-semibold text-gray-400 hover:text-gray-200 border-b-2 border-transparent hover:border-gray-700 transition-colors js-tab-btn" data-target="#tab-popular">
            ยอดนิยม
          </button>
          <button type="button" class="flex-1 py-3 text-sm font-semibold text-gray-400 hover:text-gray-200 border-b-2 border-transparent hover:border-gray-700 transition-colors js-tab-btn" data-target="#tab-week">
            สัปดาห์
          </button>
        </div>

        <!-- Tab Content: Latest -->
        <div id="tab-latest" class="space-y-3 js-tab-content">
          <?php
          $sidebar_latest = new WP_Query(array(
            'post_type' => 'video',
            'posts_per_page' => 8,
            'post__not_in' => array(get_the_ID()),
            'ignore_sticky_posts' => 1,
          ));

          if ($sidebar_latest->have_posts()):
            while ($sidebar_latest->have_posts()):
              $sidebar_latest->the_post();
              get_template_part('template-parts/content', 'sidebar-item');
            endwhile;
            wp_reset_postdata();
          else: ?>
                  <p class="text-xs text-gray-500">ไม่พบวิดีโอ</p>
          <?php endif; ?>
        </div>

        <!-- Tab Content: Popular -->
        <div id="tab-popular" class="space-y-3 hidden js-tab-content">
          <?php
          $sidebar_popular = new WP_Query(array(
            'post_type' => 'video',
            'posts_per_page' => 8,
            'post__not_in' => array(get_the_ID()),
            'meta_key' => '_mt_view_count',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'ignore_sticky_posts' => 1,
          ));

          if ($sidebar_popular->have_posts()):
            while ($sidebar_popular->have_posts()):
              $sidebar_popular->the_post();
              get_template_part('template-parts/content', 'sidebar-item');
            endwhile;
            wp_reset_postdata();
          else: ?>
                  <p class="text-xs text-gray-500">ไม่มีข้อมูลยอดนิยม</p>
          <?php endif; ?>
        </div>

        <!-- Tab Content: Week -->
        <div id="tab-week" class="space-y-3 hidden js-tab-content">
          <?php
    $sidebar_week = new WP_Query(array(
            'post_type' => 'video',
            'posts_per_page' => 8,
            'post__not_in' => array(get_the_ID()),
            'meta_key' => '_mt_view_count_week',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'ignore_sticky_posts' => 1,
          ));

          if ($sidebar_week->have_posts()):
            while ($sidebar_week->have_posts()):
              $sidebar_week->the_post();
              get_template_part('template-parts/content', 'sidebar-item');
            endwhile;
            wp_reset_postdata();
          else: ?>
            <p class="text-xs text-gray-500">ไม่มีข้อมูลในสัปดาห์นี้</p>
          <?php endif; ?>
        </div>
      </aside>
    </div>

    <!-- Related / Random bottom grid -->
    <section class="mt-8 border-t border-gray-800 pt-6">
      <h2 class="text-lg font-semibold mb-4 text-gray-200">คุณอาจชอบสิ่งนี้</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <?php
        $bottom_q = new WP_Query(array(
          'post_type' => 'video',
          'posts_per_page' => 10,
          'orderby' => 'rand',
          'post__not_in' => array(get_the_ID()),
        ));

        if ($bottom_q->have_posts()):
          while ($bottom_q->have_posts()):
            $bottom_q->the_post();
            // Get video metadata for overlay
            $raw_views = (int) get_post_meta(get_the_ID(), '_mt_view_count', true);
            $upload_date = get_the_date('Y-m-d');

            // Format views count using helper function
            $views_str = mt_format_views($raw_views);
            ?>
            <article class="group bg-gray-900 rounded-md overflow-hidden hover:shadow-lg transition">
              <div class="relative">
                <!-- Date & Views Overlay -->
                <div class="flex flex-row gap-1 mb-1 gap-2">
                  <span class="px-1 py-0.5 text-white text-[10px] font-semibold rounded"
                    style="background-color: rgb(101, 101, 101); padding: 0 2px;">
                    <?php echo esc_html($upload_date); ?>
                  </span>
                  <span class="px-1 py-0.5 text-white text-[10px] font-semibold rounded flex items-center"
                    style="background-color: #fff; color: #f65983; gap: 2px; padding: 0 2px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                      style="width: 10px; height: 10px;">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <span><?php echo esc_html($views_str); ?></span>
                  </span>
                </div>

                <!-- Thumbnail -->
                <a href="<?php the_permalink(); ?>" class="block">
                  <?php if (has_post_thumbnail()) {
                    the_post_thumbnail('medium', array(
                      'class' => 'w-full h-32 object-cover group-hover:scale-105 transform transition rounded'
                    ));
                  } else { ?>
                                                  <div class="w-full h-32 bg-gray-800 rounded"></div>
                                        <?php } ?>
                </a>
              </div>
              <div class="p-2">
                <h3 class="text-sm font-medium text-gray-200 group-hover:text-pink-500 transition-colors line-clamp-2">
                  <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>
              </div>
            </article>
            <?php
          endwhile;
          wp_reset_postdata();
        else: ?>
                    <p class="text-sm text-gray-500">ไม่มีวิดีโอเพิ่มเติม</p>
        <?php endif; ?>
      </div>
    </section>

    <?php
  endwhile;
endif;

get_footer();
