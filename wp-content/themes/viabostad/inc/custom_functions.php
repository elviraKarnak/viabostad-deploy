<?php 

//acf theme page

if( function_exists('acf_add_options_page') ) {

	acf_add_options_page(array(
		'page_title' 	=> 'Theme General Settings',
		'menu_title'	=> 'Theme Settings',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> 'false'
	));

	
	acf_add_options_sub_page(array(
		'page_title' 	=> 'Theme Footer Settings',
		'menu_title'	=> 'Footer',
		'parent_slug'	=> 'theme-general-settings',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Theme Footer Settings',
		'menu_title'	=> 'Footer',
		'parent_slug'	=> 'theme-general-settings',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Testimonials',
		'menu_title'	=> 'Testimonials',
		'parent_slug'	=> 'theme-general-settings',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Theme Additional Settings',
		'menu_title'	=> 'Additional Fields',
		'parent_slug'	=> 'theme-general-settings',
	));

    acf_add_options_sub_page(array(
		'page_title' 	=> 'Property Forms Settings',
		'menu_title'	=> 'Property Forms',
		'parent_slug'	=> 'theme-general-settings',
	));

}

    add_action('init', 'add_author_support_to_products');

    function add_author_support_to_products() {
        add_post_type_support('product', 'author');
    }

    add_action('template_redirect', 'redirect_shop_page');

    function redirect_shop_page() {
        if (is_shop()) {
                wp_redirect(home_url());
            exit;
        }
    }


    add_filter('manage_edit-product_columns', 'add_product_author_column');

    function add_product_author_column($columns) {
        $columns['author'] = 'Author';
        return $columns;
    }


    // Broker Registration 
    add_action('init', 'create_broker_role');

    function create_broker_role() {

        //remove_role('broker'); // reset if already created

        add_role(
            'broker',
            'Broker',
            [

                // Basic
                'read' => true,
                'upload_files' => true,

                // Required to appear in Author dropdown
                'edit_posts' => true,

                // WooCommerce product capabilities
                'edit_products' => true,
                'publish_products' => true,
                'delete_products' => true,
                'edit_published_products' => true,

            ]
        );

        add_role(
            'agency',
            'Agency',
            [

                // Basic
                'read' => true,
                'upload_files' => true,

                // Required to appear in Author dropdown
                'edit_posts' => true,

                // WooCommerce product capabilities
                'edit_products' => true,
                'publish_products' => true,
                'delete_products' => true,
                'edit_published_products' => true,

            ]
        );
    }

    add_action('wp_ajax_nopriv_register_broker', 'handle_broker_registration');
    add_action('wp_ajax_register_broker', 'handle_broker_registration');

    function handle_broker_registration() {

        if (!isset($_POST['email']) || !is_email($_POST['email'])) {
            wp_send_json_error(['message' => 'Invalid email address']);
        }

        $user_type = isset($_POST['user_type']) ? sanitize_text_field($_POST['user_type']) : '';

        $email      = sanitize_email($_POST['email']);
        $username   = sanitize_user($_POST['username']);
        $password   = $_POST['password'];
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name  = sanitize_text_field($_POST['last_name']);
        $phone      = sanitize_text_field($_POST['phone']);
        $bio        = sanitize_textarea_field($_POST['bio']);
        $agency     = $_POST['agency'];

        $attachment_id = false;

        if (username_exists($username) || email_exists($email)) {
            wp_send_json_error(['message' => 'Username or Email already exists']);
        }

        // Create user
        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            wp_send_json_error(['message' => $user_id->get_error_message()]);
        }


        wp_update_user([
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'role'       => $user_type
        ]);

        update_user_meta($user_id, 'phone', $phone);
        update_user_meta($user_id, 'description', $bio);


        if (!empty($_FILES['profile_picture']['name'])) {

            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            $attachment_id = media_handle_upload('profile_picture', $post_id);

            // if (!is_wp_error($attachment_id)) {
            //     update_user_meta($user_id, 'profile_picture', $attachment_id);
            // } 

            if (!is_wp_error($attachment_id)) {

                $file = get_attached_file($attachment_id);

                if ($file && file_exists($file)) {

                    $avatar_dir = bp_core_avatar_upload_path() . '/avatars/' . $user_id;

                    if (!file_exists($avatar_dir)) {
                        wp_mkdir_p($avatar_dir);
                    }

                    $full  = $avatar_dir . '/avatar-bpfull.jpg';
                    $thumb = $avatar_dir . '/avatar-bpthumb.jpg';

                    copy($file, $full);
                    copy($file, $thumb);
                }

            
            }

        }

        if($user_type == 'broker'){

            update_user_meta($user_id, 'agency_id_viabostad', $agency);
    

        // Create Broker Post
        $post_id = wp_insert_post([
            'post_type'   => 'broker',
            'post_title'  => $first_name . ' ' . $last_name,
            'post_status' => 'publish',
            'post_content'=> $bio,
            'post_author' => $user_id,
        ]);

        if (is_wp_error($post_id)) {
            wp_send_json_error(['message' => 'Failed to create broker profile']);
        }

        
        if (!empty($_POST['acf_map']) && is_array($_POST['acf_map'])) {

            $map = $_POST['acf_map'];

                $map_data = array(
                'address' => sanitize_text_field($map['address'] ?? ''),
                'lat' => floatval($map['lat'] ?? 0),
                'lng' => floatval($map['lng'] ?? 0),
                'zoom' => intval($map['zoom'] ?? 14),
                'name' => sanitize_text_field($map['name'] ?? ''),
                'street_number' => sanitize_text_field($map['street_number'] ?? ''),
                'street_name' => sanitize_text_field($map['street_name'] ?? ''),
                'city' => sanitize_text_field($map['city'] ?? ''),
                'state' => sanitize_text_field($map['state'] ?? ''),
                'post_code' => sanitize_text_field($map['post_code'] ?? ''),
                'country' => sanitize_text_field($map['country'] ?? ''),
                'country_short' => sanitize_text_field($map['country_short'] ?? '')
                );
    
            update_field('broker_address', $map_data, $property_id);

            /*--------------------------------
            Save extra location meta separately
            --------------------------------*/

            // update_post_meta($property_id, 'street_number', sanitize_text_field($map['street_number'] ?? ''));
            // update_post_meta($property_id, 'street_name', sanitize_text_field($map['street_name'] ?? ''));
            // update_post_meta($property_id, 'city', sanitize_text_field($map['city'] ?? ''));
            // update_post_meta($property_id, 'state', sanitize_text_field($map['state'] ?? ''));
            // update_post_meta($property_id, 'post_code', sanitize_text_field($map['post_code'] ?? ''));
            // update_post_meta($property_id, 'country', sanitize_text_field($map['country'] ?? ''));

        update_post_meta($post_id, '_address_full', $map['address'] ?? '');
        update_post_meta($post_id, '_address_lat', $map['lat'] ?? '');
        update_post_meta($post_id, '_address_lng', $map['lng'] ?? '');
        update_post_meta($post_id, '_address_city', $map['city'] ?? '');
        update_post_meta($post_id, '_address_state', $map['state'] ?? '');
        update_post_meta($post_id, '_address_postcode', $map['post_code'] ?? '');
        update_post_meta($post_id, '_address_country', $map['country'] ?? '');
        // update_post_meta($post_id, '_address_country_short', $map['country_short'] ?? '');
        // update_post_meta($post_id, '_address_place_id', $map['place_id'] ?? '');
        }
   

        update_post_meta($post_id, 'bio', $bio);
        update_post_meta($post_id, '_agency_id', $agency);
        // wp_set_object_terms($post_id, $agency, 'agency');
        update_user_meta($user_id, 'broker_post_id', $post_id);
        
            if (!is_wp_error($attachment_id)) {

                // Set as featured image
                set_post_thumbnail($post_id, $attachment_id);
            } else {
                wp_send_json_error(['message' => 'Image upload failed']);
            }
        }

        // Auto login
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        //$user_id = 123; // your user ID

        if ( function_exists( 'bp_core_get_user_domain' ) ) {
            $profile_url = bp_core_get_user_domain( $user_id );
            //echo $profile_url;
        }

        wp_send_json_success([
            'redirect' => $profile_url
        ]);

        wp_die();
    }

    add_action('wp_ajax_nopriv_check_username', 'check_username_ajax');
    add_action('wp_ajax_check_username', 'check_username_ajax');

    function check_username_ajax() {

        if (empty($_POST['username'])) {
            wp_send_json_error(['message' => 'Username is required']);
        }

        $username = sanitize_user($_POST['username']);

        if (username_exists($username)) {
            wp_send_json_error(['message' => 'Username already taken']);
        } else {
            wp_send_json_success(['message' => 'Username available']);
        }

        wp_die();
    }


    add_action('wp_ajax_nopriv_broker_search', 'broker_search_ajax');
    add_action('wp_ajax_broker_search', 'broker_search_ajax');

    function broker_search_ajax() {

        $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';
        $agency  = isset($_POST['agency']) ? sanitize_text_field($_POST['agency']) : '';




                $args = [
                    'post_type'      => 'broker',
                    'posts_per_page' => -1,
                    'meta_query'     => ['relation' => 'AND'],
                ];


            /* ===================================
            LOCATION FILTER (City / State)
            ACF Google Map Field: address_sp
            =================================== */

            if (!empty($_POST['acf_map']['city'])) {

                $city = sanitize_text_field($_POST['acf_map']['city']);

                $args['meta_query'][] = [
                    'key'     => '_address_city',
                    'value'   => $city,
                    'compare' => '='
                ];
            }

            if (!empty($_POST['acf_map']['state'])) {

                $state = sanitize_text_field($_POST['acf_map']['state']);

                $args['meta_query'][] = [
                    'key'     => '_address_state',
                    'value'   => $state,
                    'compare' => '='
                ];
            }

            if (!empty($_POST['filter']['country'])) {

                $state = sanitize_text_field($_POST['filter']['country']);

                $args['meta_query'][] = [
                    'key'     => '_address_country',
                    'value'   => $state,
                    'compare' => '='
                ];
            }


                if (!empty($agency)) {
                    $args['meta_query'] = [
                        [
                            'key'     => '_agency_id',
                            'value'   => $agency,
                            'compare' => '=',
                        ]
                    ];
                }

        $query = new WP_Query($args);

        if ($query->have_posts()){   
        
                $totalPosts = $query->found_posts;
                
                if($totalPosts > 1){
                    $mess = $totalPosts. ' Results Found';
                }else{
                $mess = $totalPosts. ' Result Found';
                }
                
            ?>
    

                <!-- Results -->
                <div class="row mb-3">
                    <div class="col-12">
                    <p class="results-text"><?php echo $mess; ?> </p>
                    </div>
                </div>

                <!-- Cards -->
                <div class="row g-4">

                    <?php while ($query->have_posts()){ 
                            
                        $query->the_post();

                        
                    $thumb_id = get_post_thumbnail_id();
                    $img_src  = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'full') : '';
                    $img_alt  = $thumb_id ? get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';

                    ?>
            

                    <div class="col-lg-4 col-md-6">
                    <a href="<?php the_permalink(); ?>" class="d-block">
                        <div class="agent-card">
                        <?php if ( $img_src ) : ?>
                            <img src="<?php echo esc_url($img_src[0]); ?>"
                            width="<?php echo esc_attr($img_src[1]); ?>"
                            height="<?php echo esc_attr($img_src[2]); ?>"
                            alt="<?php echo esc_attr($img_alt ?: get_the_title()); ?>"
                            class="img-fluid"
                            >
                        <?php endif; 

                             $post_id  = get_the_ID(); // Your post ID
                
                        $author = get_the_author_ID();
                        $agency_id = get_user_meta($author, 'agency_id_viabostad', true);
                        $get_agency = get_user_by('id', $agency_id);
                        $full_name = trim( $get_agency->first_name . ' ' . $get_agency->last_name );
                    
                      ?>
                      <div class="agent-info">
                        <h5><?php the_title(); ?></h5>
                        <p><?php echo $full_name; ?></p>
                      </div>
                        </div>
                    </a>
                    </div>

                    <?php } ?>

                </div>

                <?php }else{?>

                <div class="row g-4">
                    <div class="col-md-12">
                    <h6>No Broker Found.</h6>
                    </div>
                </div>

                <?php } wp_reset_query(); 

        wp_die();
    }




   add_action('wp_ajax_viabosted_add_dynamic_product', 'viabosted_add_dynamic_product');
   add_action('wp_ajax_nopriv_viabosted_add_dynamic_product', 'viabosted_add_dynamic_product');

function viabosted_add_dynamic_product(){

    check_ajax_referer('viabostad_nonce', 'nonce');

    $property_id = intval($_POST['property-id']);
    $product_id  = intval($_POST['product-id']);

    if(!$property_id || !$product_id){
        wp_send_json_error();
    }

    // Empty cart (only 1 property allowed)
    WC()->cart->empty_cart();

    // Get property data (from CPT)
    $property_name  = get_the_title($property_id);
    $property_price = get_post_meta($property_id, '_price', true);
    $location       = get_post_meta($property_id, '_address_full', true);
    $image          = get_the_post_thumbnail_url($property_id, 'full');

    WC()->cart->add_to_cart(
        $product_id,
        1,
        0,
        [],
        [
            'custom_price'  => $property_price,
            'property_data' => [
                'id'       => $property_id,
                'name'     => $property_name,
                'location' => $location,
                'image'    => $image,
                'price'    => $property_price
            ],
            'unique_key' => md5(microtime().rand())
        ]
    );

    wp_send_json_success();
}

    // set custom price for cart

    add_action('woocommerce_before_calculate_totals', function($cart){

        if (is_admin() && !defined('DOING_AJAX')) return;

        foreach ($cart->get_cart() as $cart_item) {
            if (isset($cart_item['custom_price'])) {
                $cart_item['data']->set_price($cart_item['custom_price']);
            }
        }

    }, 20);

    // overwrite product name
    add_filter('woocommerce_cart_item_name', function($name, $cart_item, $cart_item_key){

        if (isset($cart_item['property_data']['name'])) {

            $property_id = $cart_item['property_data']['id'];
            $property_name = $cart_item['property_data']['name'];

            // Make title clickable to property page
            $name = '<a href="'.get_permalink($property_id).'">'.$property_name.'</a>';
        }

        return $name;

    }, 10, 3);


    // add meta data 

    add_filter('woocommerce_get_item_data', function($item_data, $cart_item){

        if(isset($cart_item['property_data'])){

            $item_data[] = [
                'name'  => 'Property',
                'value' => $cart_item['property_data']['name']
            ];

            $item_data[] = [
                'name'  => 'Location',
                'value' => $cart_item['property_data']['location']
            ];
        }

        return $item_data;

    }, 10, 2);

    // replace images

    add_filter('woocommerce_cart_item_thumbnail', function($thumbnail, $cart_item){

        if(isset($cart_item['property_data']['image'])){
            $thumbnail = '<img src="'.$cart_item['property_data']['image'].'" width="100">';
        }

        return $thumbnail;

    }, 10, 2);



        // add in order meta

        add_action('woocommerce_checkout_create_order_line_item', function($item, $cart_item_key, $values){

        if (isset($values['property_data'])) {

            $property = $values['property_data'];

            // Replace product title
            $item->set_name($property['name']);

            // Save clean meta
            $item->add_meta_data('Location', $property['location'], true);
            $item->add_meta_data('Property-ID', $property['id'], true);
            $item->add_meta_data('Image', $property['image'], true);
        }

    }, 20, 4);


    add_action('woocommerce_after_order_itemmeta', function($item_id, $item, $product){

    $image = $item->get_meta('Image');

    if ($image) {
        echo '<p><img src="'.$image.'" width="80"></p>';
    }

}, 10, 3);


        add_filter('woocommerce_email_order_items_args', function($args){
            $args['show_image'] = true;
            $args['image_size'] = [80, 80];
            return $args;
        });

        add_filter('woocommerce_order_item_thumbnail', function($image, $item){

        $property_image = $item->get_meta('Image');

        if ($property_image) {
            $image = '<img src="'.$property_image.'" width="80">';
        }

        return $image;

    }, 10, 2);


    add_action('woocommerce_thankyou', function($order_id){

        $order = wc_get_order($order_id);

        foreach ($order->get_items() as $item) {

            $image = $item->get_meta('Image');
            $location = $item->get_meta('Location');

            echo '<div style="border:1px solid #ddd;padding:15px;margin-top:20px;">';
            echo '<img src="'.$image.'" width="200">';
            echo '<h3>'.$item->get_name().'</h3>';
            echo '<p><strong>Location:</strong> '.$location.'</p>';
            echo '</div>';
        }

    });


        add_action('woocommerce_order_status_processing', 'update_property_sold_status_from_order');
        add_action('woocommerce_order_status_completed', 'update_property_sold_status_from_order');

        function update_property_sold_status_from_order($order_id) {
            if (!$order_id) {
                return;
            }

            $order = wc_get_order($order_id);

            if (!$order) {
                return;
            }

            foreach ($order->get_items() as $item) {
                $property_id = $item->get_meta('Property-ID');

                if (!empty($property_id)) {
                    update_post_meta((int) $property_id, '_is_sold', 'yes');
                    update_post_meta((int) $property_id, '_sold_date', date('Y-m-d'));
                }
            }
        }


    add_action('wp_logout', function() {
    wp_safe_redirect( home_url('/log-in') ); // change /login/ if needed
    exit;
});


    add_filter( 'login_redirect', function( $redirect_to, $request, $user ) {

        // If no user object, return default
        if ( ! isset( $user->roles ) ) {
            return $redirect_to;
        }

        // Admin → Dashboard
        if ( in_array( 'administrator', (array) $user->roles ) ) {
            return admin_url();
        }

        // Other users → BuddyPress profile
        if ( function_exists( 'bp_core_get_user_domain' ) ) {
            return bp_core_get_user_domain( $user->ID );
        }

        return home_url();

    }, 10, 3 );