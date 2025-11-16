</main>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var toggle = document.getElementById('mt-search-toggle');
  var bar = document.getElementById('mt-search-bar');

  if (!toggle || !bar) return;

  toggle.addEventListener('click', function() {
    bar.classList.toggle('hidden');
  });
});
</script>
<footer class="border-t border-gray-800 mt-8 bg-black">
  <div class="max-w-6xl mx-auto px-4 md:px-0 py-10">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-10 text-sm text-gray-300">

      <!-- คอลัมน์ซ้าย: โลโก้ + about -->
      <div class="md:col-span-1">
        <div class="mb-4 w-10">
          <?php if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) : ?>
          <div class="mb-2">
            <?php the_custom_logo(); ?>
          </div>
          <?php else : ?>
          <span class="text-2xl font-semibold">
            MISS<span class="text-pink-500">AV</span>
          </span>
          <?php endif; ?>
        </div>

        <?php if ( is_active_sidebar( 'footer-about' ) ) : ?>
        <?php dynamic_sidebar( 'footer-about' ); ?>
        <?php else : ?>
        <p class="text-sm text-gray-400 leading-relaxed">
          Best Japan AV site, free forever, high speed, no lag,
          over 100,000 videos, daily update, no ads while playing video.
        </p>
        <?php endif; ?>
      </div>

      <!-- VIDEOS -->
      <div>
        <?php if ( is_active_sidebar( 'footer-videos' ) ) : ?>
        <?php dynamic_sidebar( 'footer-videos' ); ?>
        <?php else : ?>
        <h3 class="text-xs font-semibold tracking-[0.15em] text-gray-400 mb-3 uppercase">VIDEOS</h3>
        <ul class="space-y-2">
          <li><a href="#" class="hover:text-gray-100">Recent update</a></li>
          <li><a href="#" class="hover:text-gray-100">New Releases</a></li>
          <li><a href="#" class="hover:text-gray-100">Uncensored leak</a></li>
          <li><a href="#" class="hover:text-gray-100">English subtitle</a></li>
        </ul>
        <?php endif; ?>
      </div>

      <!-- SEARCH -->
      <div>
        <?php if ( is_active_sidebar( 'footer-search' ) ) : ?>
        <?php dynamic_sidebar( 'footer-search' ); ?>
        <?php else : ?>
        <h3 class="text-xs font-semibold tracking-[0.15em] text-gray-400 mb-3 uppercase">SEARCH</h3>
        <ul class="space-y-2">
          <li><a href="#" class="hover:text-gray-100">Actress</a></li>
          <li><a href="#" class="hover:text-gray-100">Genre</a></li>
          <li><a href="#" class="hover:text-gray-100">Maker</a></li>
        </ul>
        <?php endif; ?>
      </div>

      <!-- LINKS -->
      <div>
        <?php if ( is_active_sidebar( 'footer-links' ) ) : ?>
        <?php dynamic_sidebar( 'footer-links' ); ?>
        <?php else : ?>
        <h3 class="text-xs font-semibold tracking-[0.15em] text-gray-400 mb-3 uppercase">LINKS</h3>
        <ul class="space-y-2">
          <li><a href="#" class="hover:text-gray-100">Contact</a></li>
          <li><a href="#" class="hover:text-gray-100">Ad enquiry</a></li>
          <li><a href="#" class="hover:text-gray-100">Terms</a></li>
          <li><a href="#" class="hover:text-gray-100">Upload video</a></li>
        </ul>
        <?php endif; ?>
      </div>

      <!-- SEE ALSO -->
      <div>
        <?php if ( is_active_sidebar( 'footer-seealso' ) ) : ?>
        <?php dynamic_sidebar( 'footer-seealso' ); ?>
        <?php else : ?>
        <h3 class="text-xs font-semibold tracking-[0.15em] text-gray-400 mb-3 uppercase">SEE ALSO</h3>
        <ul class="space-y-2">
          <li><a href="#" class="hover:text-gray-100">Live Cam Sex</a></li>
          <li><a href="#" class="hover:text-gray-100">123Av</a></li>
          <li><a href="#" class="hover:text-gray-100">Njav</a></li>
          <li><a href="#" class="hover:text-gray-100">Supjav</a></li>
          <li><a href="#" class="hover:text-gray-100">ThePornDude</a></li>
          <li><a href="#" class="hover:text-gray-100">JerkDolls</a></li>
        </ul>
        <?php endif; ?>
      </div>

    </div>

    <div class="border-t border-gray-800 mt-8 pt-4 text-[11px] text-gray-500 flex justify-between">
      <span>
        © <?php echo date( 'Y' ); ?>
        <?php bloginfo( 'name' ); ?>
      </span>
      <span>
        Powered by WordPress · MissAV Tailwind Theme
      </span>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>
