<?php
get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();

        $video_url = get_post_meta( get_the_ID(), '_mt_video_url', true );
        ?>
        <div class="md:flex md:space-x-6">
            <div class="md:w-2/3">
                <div class="bg-black mb-4">
                    <?php if ( $video_url ) : ?>
                        <?php if ( preg_match( '/\.mp4($|\?)/', $video_url ) ) : ?>
                            <video class="w-full" controls poster="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ); ?>">
                                <source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
                            </video>
                        <?php else : ?>
                            <iframe class="w-full h-64 md:h-96"
                                    src="<?php echo esc_url( $video_url ); ?>"
                                    frameborder="0"
                                    allowfullscreen></iframe>
                        <?php endif; ?>
                    <?php else : ?>
                        <div class="w-full h-64 bg-gray-900 flex items-center justify-center text-gray-500">
                            No video URL set.
                        </div>
                    <?php endif; ?>
                </div>

                <h1 class="text-xl md:text-2xl font-semibold mb-2"><?php the_title(); ?></h1>
                <div class="text-xs text-gray-400 mb-4 flex space-x-4">
                    <span>Release date: <?php echo get_the_date(); ?></span>
                    <span>Category: <?php echo mt_get_primary_category_name(); ?></span>
                </div>

                <div class="prose prose-invert max-w-none text-sm">
                    <?php the_content(); ?>
                </div>
            </div>

            <aside class="md:w-1/3 mt-6 md:mt-0">
                <h2 class="text-lg font-semibold mb-3">Related videos</h2>
                <div class="space-y-3">
                    <?php
                    $cats = wp_get_post_categories( get_the_ID() );
                    $related = new WP_Query( array(
                        'category__in'   => $cats,
                        'post__not_in'   => array( get_the_ID() ),
                        'posts_per_page' => 6,
                    ) );

                    if ( $related->have_posts() ) :
                        while ( $related->have_posts() ) :
                            $related->the_post();
                            ?>
                            <div class="flex space-x-2">
                                <a href="<?php the_permalink(); ?>" class="w-24 flex-shrink-0">
                                    <?php if ( has_post_thumbnail() ) {
                                        the_post_thumbnail( 'thumbnail', array( 'class' => 'w-24 h-16 object-cover rounded' ) );
                                    } else { ?>
                                        <div class="w-24 h-16 bg-gray-800 rounded"></div>
                                    <?php } ?>
                                </a>
                                <div class="text-xs">
                                    <a href="<?php the_permalink(); ?>" class="font-semibold line-clamp-2 hover:text-pink-500">
                                        <?php the_title(); ?>
                                    </a>
                                    <div class="text-[10px] text-gray-400 mt-1">
                                        <?php echo get_the_date(); ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        ?>
                        <p class="text-xs text-gray-500">No related videos.</p>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
        <?php
    endwhile;
endif;

get_footer();
?>
