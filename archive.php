<?php
get_header();
?>

<header class="mb-4">
    <h1 class="text-2xl font-semibold">
        <?php
        if ( is_category() ) {
            single_cat_title();
        } elseif ( is_search() ) {
            printf( 'Search result: "%s"', get_search_query() );
        } else {
            the_archive_title();
        }
        ?>
    </h1>
</header>

<?php if ( have_posts() ) : ?>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
        <?php while ( have_posts() ) : the_post();
            get_template_part( 'template-parts/content', 'card' );
        endwhile; ?>
    </div>

    <div class="mt-6">
        <?php the_posts_pagination( array(
            'mid_size'  => 1,
            'prev_text' => '« Prev',
            'next_text' => 'Next »',
        ) ); ?>
    </div>
<?php else : ?>
    <p>No videos found.</p>
<?php endif; ?>

<?php
get_footer();
?>
