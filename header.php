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
      <div class="flex items-center space-x-2">
        <?php if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) : ?>
        <?php the_custom_logo(); ?>
        <?php else : ?>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-2xl font-semibold text-pink-500">
          <?php bloginfo( 'name' ); ?>
        </a>
        <?php endif; ?>
      </div>

      <div class="flex items-center space-x-4">
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
