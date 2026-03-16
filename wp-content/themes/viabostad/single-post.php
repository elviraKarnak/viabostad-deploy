<?php
get_header(); 
    if(have_posts()) :
        while (have_posts() ) : the_post(); ?>

      <main>
        <div class="relative_header"></div>
        <div class="single_blog pt_50 pb_100">
        <div class="container">
          <div class="head">
            <h1 class="title"><?php the_title(); ?></h1>
            <div class="d-flex justify-content-between gap-3 align-items-center flex-wrap">
              <div>
               <p class="d-flex gap-2">
                <span><?php echo get_the_date( get_option('date_format') ); ?></span>
                  ·
                  <span>
                      <?php
                          $minutes = reading_time(get_the_content());
                          printf(
                              esc_html__( '%s min read', 'viabosted' ),
                              esc_html( $minutes )
                          );
                      ?>
                  </span>
                  </p>
              </div>
              
            </div>
          </div>
          <?php if(has_post_thumbnail()){ 
            
            
              $thumb_id = get_post_thumbnail_id();
              $img_src  = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'large') : '';
              $img_alt  = $thumb_id ? get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';
            
            ?>
          <div class="featured_wrap">
             <img src="<?php echo esc_url($img_src[0]); ?>"
                 width="<?php echo esc_attr($img_src[1]); ?>"
                 height="<?php echo esc_attr($img_src[2]); ?>"
                 alt="<?php echo esc_attr($img_alt ?: get_the_title()); ?>">
          </div>
          <?php } else { ?>
            <img src="/wp-content/uploads/2026/02/house-isolated-905x1024.webp" alt="featured" width="1920" height="800">
            <?php } ?>
          <div class="content_wrap">
           <?php the_content(); ?>
          </div>
        </div>
      </div>
      </main>

      <?php endwhile; 
    endif; 
get_footer(); ?>