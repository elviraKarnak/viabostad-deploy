<!doctype html>
<html <?php language_attributes(); ?>>

<head>
  <!-- Required meta tags -->
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport"
    content="width=device-width, minimum-scale=1, maximum-scale=1, initial-scale=1, shrink-to-fit=no">
  <!-- Page Title -->
  <title><?php wp_title('|', true, 'right');
  bloginfo('name'); ?></title>
  <!-- Stylesheets and Other Head Elements -->
  <?php wp_head(); ?>
</head>



<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>

    <header class="site_header">
      <div class="container">
        <div class="header_wrapper">
          <div class="logo_wrapper">
            <?php if(has_custom_logo()){
                the_custom_logo(); 
              }
              ?>
          </div>
          <nav>

          <?php
            wp_nav_menu(
              array(
                'container' => '',
                'container_class' => '',
                'container_id' => '',
                'items_wrap' => '<ul id="%1$s menu" class="%2$s primary_menu ">%3$s</ul>',
                'theme_location' => 'menu-1',
              )
            );
            ?>
          </nav>
          <div class="btns_wrapper">
            <a href="#"><i class="fas fa-shopping-cart"></i></a>
            <?php if(is_user_logged_in()){ ?>
              <a href="<?php echo  bp_loggedin_user_domain(); ?>"><i class="fas fa-user"></i></a>
              <a href="<?php echo wp_logout_url(); ?>" class="login_btn"><?php _e('Log Out', 'viabosted'); ?></a>
            <?php } else{ ?>
               <?php 
                  $link = get_field('log_in_button', 'option');
                    if( $link ){
                        $link_url = $link['url'];
                        $link_title = $link['title'];
                        $link_target = $link['target'] ? $link['target'] : '_self';
                        ?>
                        <a href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>" class="login_btn"><?php echo esc_html($link_title); ?></a>
            <?php } }?>

            <div class="hemburger d-xl-none">
              <span></span>
              <span></span>
              <span></span>
            </div>
          </div>
        </div>
      </div>
    </header>