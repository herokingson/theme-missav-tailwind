<?php
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>

<body <?php body_class('bg-black text-gray-100'); ?>>

  <header class="border-b border-gray-800 bg-[#05060a]">
    <!-- แถวบน: โลโก้ + เมนู + ปุ่ม search -->
    <div class="max-w-6xl mx-auto flex items-center justify-between py-4 px-4 md:px-0">
      <div class="flex items-center space-x-2 [&>*_.custom-logo]:h-[35px] [&>*_.custom-logo]:w-auto">
        <?php if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) : ?>
        <?php the_custom_logo(); ?>
        <?php else : ?>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-2xl font-semibold text-pink-500">
          <?php bloginfo( 'name' ); ?>
        </a>
        <?php endif; ?>
      </div>

      <div class="flex items-center space-x-3">
        <nav class="hidden md:block">
          <?php
                    wp_nav_menu( array(
                        'theme_location' => 'primary',
                        'container'      => false,
                        'menu_class'     => 'flex space-x-6 text-sm text-gray-300',
                    ) );
                ?>
        </nav>

        <!-- ปุ่มเปิด/ปิด search bar -->
        <button type="button" id="mt-search-toggle"
          class="p-2 rounded-full hover:bg-gray-800 text-white focus:outline-none" aria-label="Toggle search">
          <!-- ไอคอนแว่นขยายแบบ SVG -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
          </svg>
        </button>

        <!-- Burger Menu Button (Mobile Only) - ใช้ inline style เพื่อความแน่ใจ -->
        <button type="button" id="mt-burger-toggle"
          class="p-2 rounded-lg hover:bg-pink-600 text-white focus:outline-none transition-colors duration-200"
          style="display: flex;" aria-label="Toggle menu">
          <svg xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div id="mt-mobile-menu" class="fixed inset-0 hidden" style="z-index: 9999;">
      <!-- Backdrop -->
      <div id="mt-mobile-backdrop"
        class="absolute inset-0 transition-opacity duration-300"
        style="background: rgba(0,0,0,0.8); opacity: 0; backdrop-filter: blur(4px);"></div>

      <!-- Menu Panel -->
      <div id="mt-mobile-panel"
        class="absolute top-0 right-0 h-full transition-transform duration-300 ease-out z-50"
        style="width: 85%; max-width: 320px; background: linear-gradient(180deg, #0f0f14 0%, #090a0e 100%); transform: translateX(100%); border-left: 1px solid #1f2937;z-index:99;">

        <!-- Header with gradient -->
        <div class="flex items-center justify-between p-5" style="background: linear-gradient(135deg, rgba(236,72,153,0.15) 0%, rgba(139,92,246,0.1) 100%); border-bottom: 1px solid #1f2937;">
          <div class="flex justify-between space-x-3">
            <span class="text-lg font-bold text-white">เมนู</span>
          </div>
          <button type="button" id="mt-mobile-close"
            class="p-2 rounded-lg transition-all duration-200 mr-0"
            style="background: rgba(255,255,255,0.05); color: #9ca3af;"
            onmouseover="this.style.background='rgba(239,68,68,0.2)'; this.style.color='#ef4444';"
            onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='#9ca3af';">
            <svg xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Mobile Navigation -->
        <nav class="p-4 overflow-y-auto" style="max-height: calc(100vh - 80px);">
       <?php
      wp_nav_menu(array(
        'theme_location' => 'primary',
        'container' => false,
        'menu_class' => 'mt-mobile-nav',
        'fallback_cb' => false,
      ));
      ?>

      <!-- Search in mobile menu -->
      <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #1f2937;">
        <form action="<?php echo esc_url(home_url('/')); ?>" method="get" class="flex items-center">
          <input type="text" name="s"
            style="flex: 1; padding: 12px 16px; background: rgba(255,255,255,0.05); border: 1px solid #374151; border-radius: 8px 0 0 8px; color: white; font-size: 14px;"
            placeholder="ค้นหาวิดีโอ...">
          <button type="submit"
            style="padding: 12px 16px; background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); border-radius: 0 8px 8px 0; color: white;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24"
              stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
            </svg>
          </button>
        </form>
      </div>
    </nav>
      </div>
    </div>

    <!-- แถวล่าง: แถบค้นหาแบบเต็มความกว้าง เปิด/ปิดได้ -->
    <div id="mt-search-bar" class="border-t border-gray-800 bg-[#05060a] hidden">
      <div class="max-w-6xl mx-auto px-4 md:px-0 py-3">
        <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="flex items-center w-full">
          <input type="text" name="s"
            class="flex-1 px-4 py-2 rounded-l-md bg-gray-900 border border-gray-700 text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-pink-500"
            placeholder="Example: Big Tit Blonde Schoolgirl">
          <button type="submit" class="flex items-center space-x-2 px-4 py-2 rounded-r-md bg-gray-700 text-sm font-semibold
            hover:opacity-80">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
            </svg>
            <span>Search</span>
          </button>
        </form>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 md:px-0 py-6">
