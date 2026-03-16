<?php 

// Home Page Property Filter

add_action('wp_ajax_load_products_by_category', 'load_products_by_category');
add_action('wp_ajax_nopriv_load_products_by_category', 'load_products_by_category');

function load_products_by_category() {

    check_ajax_referer('viabostad_nonce', 'nonce');

    $slug = sanitize_text_field($_POST['category']);

    $args = [
        'post_type'      => 'property',
        'posts_per_page' => 10,
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'meta_query'     => ['relation' => 'AND'],
    ];


    $args['meta_query'][] = [
                'relation' => 'OR',
                [
                    'key'     => '_is_sold',
                    'compare' => 'NOT EXISTS'
                ],
                [
                    'key'     => '_is_sold',
                    'value'   => 'yes',
                    'compare' => '!='
                ]
            ];

    // If not "all"
    if ($slug !== 'pills-all-tab' && $slug !== 'all') {
        $args['tax_query'] = [
            [
                'taxonomy' => 'property-type',
                'field'    => 'slug',
                'terms'    => $slug,
            ]
        ];
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) :
        echo '<div class="row gy-md-4 gy-3 property-slider">';

     
               while ( $query->have_posts() ) :
                    
                    $query->the_post();

                        get_template_part( 'template-part/poperty-loop' );

                      ?>
                      

                
            <?php endwhile; 

        echo '</div>';
    else :
        echo '<p>No products found.</p>';
    endif;

    wp_reset_postdata();
    wp_die();
}


/*--------------------------------------------------------------
 AJAX PRODUCT SUBMISSION
--------------------------------------------------------------*/
add_action('wp_ajax_submit_property_form', 'handle_ajax_property_submission');
add_action('wp_ajax_nopriv_submit_property_form', 'handle_ajax_property_submission');

function handle_ajax_property_submission() {

    //check_ajax_referer('submit_property_form', 'nonce');

    if (empty($_POST['property_title']) || empty($_POST['property_description'])) {
        wp_send_json_error(['message' => 'Required fields missing.']);
    }


    $editProperty = false; 

    $property_id = $_POST['edit-property-id'] ?? false;


    if(!$property_id){

    $property_data = [
        'post_title'   => sanitize_text_field($_POST['property_title']),
        'post_content' => wp_kses_post($_POST['property_description']),
        'post_status'  => 'publish',
        'post_type'    => 'property',
        'post_author'  => get_current_user_id(),
    ];

    $property_id = wp_insert_post($property_data);

    do_action('property_created_event', $property_id);


    }else{


        $new_title = sanitize_text_field($_POST['property_title']);

            wp_update_post([
                'ID'         => $property_id,
                'post_title' => $new_title
            ]);

        $editProperty = true;

        //do_action('property_updated_event', $property_id);

    }


    if (!$property_id) {
        wp_send_json_error(['message' => 'Property creation failed.']);
    }


    if($_POST['property_description']){
        update_post_meta($property_id, 'property_description_sp', $_POST['property_description'] ?? '');
    }
    
        /*--------------------------------
        Save ACF Google Map Field
        --------------------------------*/

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
    
            update_field('address_sp', $map_data, $property_id);

            /*--------------------------------
            Save extra location meta separately
            --------------------------------*/

            // update_post_meta($property_id, 'street_number', sanitize_text_field($map['street_number'] ?? ''));
            // update_post_meta($property_id, 'street_name', sanitize_text_field($map['street_name'] ?? ''));
            // update_post_meta($property_id, 'city', sanitize_text_field($map['city'] ?? ''));
            // update_post_meta($property_id, 'state', sanitize_text_field($map['state'] ?? ''));
            // update_post_meta($property_id, 'post_code', sanitize_text_field($map['post_code'] ?? ''));
            // update_post_meta($property_id, 'country', sanitize_text_field($map['country'] ?? ''));

        update_post_meta($property_id, '_address_full', $map['address'] ?? '');
        update_post_meta($property_id, '_address_lat', $map['lat'] ?? '');
        update_post_meta($property_id, '_address_lng', $map['lng'] ?? '');
        update_post_meta($property_id, '_address_city', $map['city'] ?? '');
        update_post_meta($property_id, '_address_state', $map['state'] ?? '');
        update_post_meta($property_id, '_address_postcode', $map['post_code'] ?? '');
        update_post_meta($property_id, '_address_country', $map['country'] ?? '');
        // update_post_meta($post_id, '_address_country_short', $map['country_short'] ?? '');
        // update_post_meta($post_id, '_address_place_id', $map['place_id'] ?? '');
        }
   
    /*--------------------------------
    Property Setup
    --------------------------------*/

    //wp_set_object_terms($property_id, 'simple', 'property-type');

    update_post_meta($property_id, '_regular_price', floatval($_POST['price']));
    update_post_meta($property_id, '_price', floatval($_POST['price']));
    update_post_meta($property_id, '_stock_status', 'instock');
    update_post_meta($property_id, '_visibility', 'visible');

    /*--------------------------------
      Custom Meta
    --------------------------------*/

    update_post_meta($property_id, 'bedroom_sp', intval($_POST['bedrooms']));
    update_post_meta($property_id, 'bathroom_sp', intval($_POST['bathrooms']));
    update_post_meta($property_id, 'area', intval($_POST['area']));
    update_post_meta($property_id, '_location', sanitize_text_field($_POST['location']));

    /*--------------------------------
      Categories (ONLY existing)
    --------------------------------*/

    if (!empty($_POST['propertytype'])) {

        $term_ids = array_map('intval', $_POST['propertytype']);
        wp_set_object_terms($property_id, $term_ids, 'property-type');
    }


      if (!empty($_POST['soldperiod'])) {

        $term_ids = array_map('intval', $_POST['soldperiod']);
        wp_set_object_terms($property_id, $term_ids, 'sold-period');
    }



    if($editProperty){

            /*--------------------------------
        Image Upload - Append Gallery + Keep Existing Thumbnail
        --------------------------------*/

        if (!empty($_FILES['property_images']['name'][0])) {

            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            $gallery_attachment_ids = [];

            // Get existing gallery (ACF)
            $existing_gallery = get_field('property_images', $property_id);
            if (!is_array($existing_gallery)) {
                $existing_gallery = [];
            }

            $file_count = count($_FILES['property_images']['name']);

            for ($i = 0; $i < $file_count; $i++) {

                if ($_FILES['property_images']['error'][$i] === UPLOAD_ERR_OK) {

                    $file_array = [
                        'name'     => $_FILES['property_images']['name'][$i],
                        'type'     => $_FILES['property_images']['type'][$i],
                        'tmp_name' => $_FILES['property_images']['tmp_name'][$i],
                        'error'    => $_FILES['property_images']['error'][$i],
                        'size'     => $_FILES['property_images']['size'][$i]
                    ];

                    $original_files = $_FILES;
                    $_FILES = ['upload_file' => $file_array];

                    $attachment_id = media_handle_upload('upload_file', $property_id);

                    $_FILES = $original_files;

                    if (!is_wp_error($attachment_id)) {

                        $gallery_attachment_ids[] = $attachment_id;

                        // ✅ Only set featured image if none exists
                        if (!has_post_thumbnail($property_id)) {
                            set_post_thumbnail($property_id, $attachment_id);
                        }

                    } else {
                        error_log($attachment_id->get_error_message());
                    }
                }
            }

            // ✅ Merge old + new gallery images
            if (!empty($gallery_attachment_ids)) {

                $updated_gallery = array_merge($existing_gallery, $gallery_attachment_ids);

                // Remove duplicates (important safety)
                $updated_gallery = array_unique($updated_gallery);

                update_field('property_images', $updated_gallery, $property_id);
            }
        }

    }else{
    
        /*--------------------------------
        Image Upload - Gallery with Featured Image
        --------------------------------*/
        if (!empty($_FILES['property_images']['name'][0])) {

            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            $gallery_attachment_ids = [];

            $file_count = count($_FILES['property_images']['name']);

            for ($i = 0; $i < $file_count; $i++) {

                if ($_FILES['property_images']['error'][$i] === UPLOAD_ERR_OK) {

                    $file_array = [
                        'name'     => $_FILES['property_images']['name'][$i],
                        'type'     => $_FILES['property_images']['type'][$i],
                        'tmp_name' => $_FILES['property_images']['tmp_name'][$i],
                        'error'    => $_FILES['property_images']['error'][$i],
                        'size'     => $_FILES['property_images']['size'][$i]
                    ];

                    // Backup original $_FILES
                    $original_files = $_FILES;

                    // Replace with single file
                    $_FILES = ['upload_file' => $file_array];

                    $attachment_id = media_handle_upload('upload_file', $property_id);

                    // Restore original $_FILES
                    $_FILES = $original_files;

                    if (!is_wp_error($attachment_id)) {

                        $gallery_attachment_ids[] = $attachment_id;

                        if (count($gallery_attachment_ids) === 1) {
                            set_post_thumbnail($property_id, $attachment_id);
                        }

                    } else {
                        error_log($attachment_id->get_error_message());
                    }
                }
            }

            if (!empty($gallery_attachment_ids)) {
                update_field('property_images', $gallery_attachment_ids, $property_id);
            }


        }

    }


    do_action('save_post_property', $property_id, $post, true);

    wp_send_json_success(['message' => 'Property published successfully']);
}

 add_action('wp_ajax_toggle_property_wishlist', 'handle_property_wishlist');
    add_action('wp_ajax_nopriv_toggle_property_wishlist', 'handle_property_wishlist');

    // Handle wishlist toggle via AJAX
    function handle_property_wishlist() {

        check_ajax_referer('viabostad_nonce', 'nonce');

        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
            return;
        }
        
        $user_id = get_current_user_id();
        $property_id = intval($_POST['property_id']);
        
        // Get current wishlist
        $wishlist = get_user_meta($user_id, 'saved_properties', true);
        if (!$wishlist) {
            $wishlist = array();
        }
        
        // Toggle property in wishlist
        if (in_array($property_id, $wishlist)) {
            // Remove from wishlist
            $wishlist = array_diff($wishlist, array($property_id));
            $action = 'removed';
        } else {
            // Add to wishlist
            $wishlist[] = $property_id;
            $action = 'added';
        }
        
        // Save updated wishlist
        update_user_meta($user_id, 'saved_properties', array_values($wishlist));
        
        wp_send_json_success(array(
            'action' => $action,
            'wishlist' => array_values($wishlist)
        ));
    }


    function is_property_saved($property_id) {
        if (!is_user_logged_in()) return false;
        
        $user_id = get_current_user_id();
        $wishlist = get_user_meta($user_id, 'saved_properties', true);
        $wishlist = $wishlist ? $wishlist : array();
        
        return in_array($property_id, $wishlist);
    }


/**
 * Use Classic Editor for specific CPT only
 */
add_filter('use_block_editor_for_post_type', function ($use_block_editor, $post_type) {

    // Replace 'your_cpt_slug' with your actual post type slug
    if ($post_type === 'property') {
        return false; // Disable Gutenberg (enable Classic Editor)
    }

    return $use_block_editor; // Keep Gutenberg for others
}, 10, 2);


/**
 * Add "My Properties" tab in BuddyPress profile
 */
function viabostad_add_properties_tab() {

    bp_core_new_nav_item([
        'name'                => 'My Properties',
        'slug'                => 'my-properties',
        'screen_function'     => 'viabostad_properties_screen',
        'default_subnav_slug' => 'my-properties',
        'position'            => 40,
    ]);
}
add_action('bp_setup_nav', 'viabostad_add_properties_tab');


function viabostad_properties_screen() {

    add_action('bp_template_content', 'viabostad_properties_content');
    bp_core_load_template('members/single/plugins');
}

function viabostad_properties_content() {

    $user_id = bp_displayed_user_id();

    $args = [
        'post_type'      => 'property',
        'posts_per_page' => -1,
        'author'         => $user_id,
        'post_status'    => 'publish'
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) {

        echo '<div class="row gy-md-4 gy-3 property-slider">';

        while ($query->have_posts()) {
            $query->the_post();
                get_template_part( 'template-part/poperty-loop' );
                   
           ?>
            
            <?php }  echo '</div>';

    } else {
        echo '<p>No properties found.</p>';
    }

    wp_reset_postdata();
}


/**
 * Add "Property" tab in BuddyPress profile
 */
function viabostad_add_property_tab() {

    bp_core_new_nav_item([
        'name'                => 'Add Property',
        'slug'                => 'add-property',
        'screen_function'     => 'viabostad_property_screen',
        'default_subnav_slug' => 'property',
        'position'            => 40,
    ]);
}
add_action('bp_setup_nav', 'viabostad_add_property_tab');


function viabostad_property_screen() {

    add_action('bp_template_content', 'viabostad_property_template');
    bp_core_load_template('members/single/plugins');
}

function viabostad_property_template() {

    locate_template([
        'buddypress/members/single/add-property.php'
    ], true);

}

        // add_action('wp_ajax_home_search_form_one', 'home_search_form_one_cb');
        // add_action('wp_ajax_nopriv_home_search_form_one', 'home_search_form_one_cb');

        // function home_search_form_one_cb() {
        //     print_r($_POST);
        //     wp_die();
        // }


    add_action('wp_ajax_nopriv_filter_search_property', 'filter_search_property_cb');
    add_action('wp_ajax_filter_search_property', 'filter_search_property_cb');

    function filter_search_property_cb() {

      $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';

      $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;


      $isSoldListing = (isset($_POST['sold_property_listing']) && $_POST['sold_property_listing'] === 'yes') ? true : false;

        $args = [
            'post_type'      => 'property',
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'paged'          => $paged,
            'tax_query'      => [],
            'meta_query'     => ['relation' => 'AND'],
             'orderby' => 'id',
                'order' => 'DESC',
        ];

        /* ===================================
        PROPERTY Keyword Search (in description)
        =================================== */


        if($isSoldListing){

            $args['meta_query'][] = [
                'relation' => 'OR',

                [
                    'key'     => '_is_sold',
                    'value'   => 'yes',
                    'compare' => '='
                ]
            ];


        }else{

            $args['meta_query'][] = [
                    'relation' => 'OR',
                    [
                        'key'     => '_is_sold',
                        'compare' => 'NOT EXISTS'
                    ],
                    [
                        'key'     => '_is_sold',
                        'value'   => 'yes',
                        'compare' => '!='
                    ]
                ];

        }

        if (!empty($keyword)) {

            $args['meta_query'][] = [
                'key'     => 'property_description_sp',
                'value'   => $keyword,
                'compare' => 'LIKE'
            ];
        }

        /* ===================================
        PROPERTY TYPE (Multiple Checkbox)
        =================================== */

        if (!empty($_POST['propertytype'])) {

            $types = array_map('intval', $_POST['propertytype']);

            $args['tax_query'][] = [
                'taxonomy' => 'property-type',
                'field'    => 'term_id',
                'terms'    => $types,
                'operator' => 'IN'
            ];
        }

         /* ===================================
        PROPERTY TYPE (Multiple Checkbox)
        =================================== */

        if (!empty($_POST['sold_period'])) {

            $types = array_map('intval', $_POST['sold_period']);

            $args['tax_query'][] = [
                'taxonomy' => 'sold-period',
                'field'    => 'term_id',
                'terms'    => $types,
                'operator' => 'IN'
            ];
        }


        /* ===================================
        PRICE FILTER
        =================================== */

        if (!empty($_POST['price'])) {

            $args['meta_query'][] = [
                'key'     => '_price',
                'value'   => floatval($_POST['price']),
                'compare' => '<=',
                'type'    => 'NUMERIC'
            ];
        }

            /* ===================================
            ROOMS RANGE FILTER
            =================================== */

            $rooms_min = isset($_POST['rooms_min']) 
                ? intval(preg_replace('/[^0-9]/', '', $_POST['rooms_min'])) 
                : '';

            $rooms_max = isset($_POST['rooms_max']) 
                ? intval(preg_replace('/[^0-9]/', '', $_POST['rooms_max'])) 
                : '';

            if ($rooms_min && $rooms_max) {

                $args['meta_query'][] = [
                    'key'     => 'bedroom_sp',
                    'value'   => [$rooms_min, $rooms_max],
                    'compare' => 'BETWEEN',
                    'type'    => 'NUMERIC'
                ];

            } elseif ($rooms_min) {

                $args['meta_query'][] = [
                    'key'     => 'bedroom_sp',
                    'value'   => $rooms_min,
                    'compare' => '>=',
                    'type'    => 'NUMERIC'
                ];

            } elseif ($rooms_max) {

                $args['meta_query'][] = [
                    'key'     => 'bedroom_sp',
                    'value'   => $rooms_max,
                    'compare' => '<=',
                    'type'    => 'NUMERIC'
                ];
}
        /* ===================================
        AREA FILTER
        =================================== */

        if (!empty($_POST['area'])) {

            $args['meta_query'][] = [
                'key'     => 'area',
                'value'   => intval($_POST['area']),
                'compare' => '>=',
                'type'    => 'NUMERIC'
            ];
        }

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

        /* ===================================
        TAX RELATION
        =================================== */

        if (count($args['tax_query']) > 1) {
            $args['tax_query']['relation'] = 'AND';
        }

        /* ===================================
        RUN QUERY
        =================================== */

        $query = new WP_Query($args);

        $map_properties = [];

        ob_start();

        if ($query->have_posts()) {

            echo '<div class="row gy-md-4 gy-3 property-slider">';

            while ($query->have_posts()) {
                $query->the_post();

                get_template_part('template-part/poperty-loop');

                $location = get_field('address_sp');

                if ($location) {
                    $map_properties[] = [
                        'title' => get_the_title(),
                        'lat'   => $location['lat'],
                        'lng'   => $location['lng'],
                         'location' => $location['city'].", ". $location['country'],
                        'link'  => get_permalink(),
                        'price' => get_field('_price'),
                        'image' => get_the_post_thumbnail_url()
                    ];
                }
            }

                echo '</div>';
                $total_pages = $query->max_num_pages;

                if ( $paged < $total_pages ) : ?>
                    
                    <div class="col-12 mt-md-5 mt-3">
                        <div class="show_more_wrapper text-center">
                            <a href="javascript:void(0)" 
                            class="primary_btn icon arrow load-more-btn"
                            data-page="<?php echo esc_attr($paged + 1); ?>"
                            data-max="<?php echo esc_attr($total_pages); ?>">
                            Show More
                            </a>
                        </div>
                    </div>

           <?php endif;
									

            $html = ob_get_clean();

            wp_send_json_success([
                'html'      => $html,
                'map'       => $map_properties,
                'max_pages' => $query->max_num_pages,
                'found'     => $query->found_posts,
                'args'     => $args
            ]);

        } else {

            wp_send_json_success([
                'html'      => '<div class="row gy-md-4 gy-3 property-slider"><div class="col-12 mt-md-5 mt-3"><p>Properties coming soon.</p></div></div>',
                'message' => 'Properties coming soon.',
                'map'       => [],
                'args'     => $args
            ]);
        }

        wp_reset_postdata();
        wp_die();
    }




    add_action('save_post_property', 'sync_property_address_meta', 20, 3);

    function sync_property_address_meta($post_id, $post, $update) {

        // Avoid autosave / revision
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (wp_is_post_revision($post_id)) return;

        // Get ACF Google Map field
        $map = get_field('address_sp', $post_id);

        if (!empty($map) && is_array($map)) {

            update_post_meta($post_id, '_address_full', $map['address'] ?? '');
            update_post_meta($post_id, '_address_lat', $map['lat'] ?? '');
            update_post_meta($post_id, '_address_lng', $map['lng'] ?? '');
            update_post_meta($post_id, '_address_city', $map['city'] ?? '');
            update_post_meta($post_id, '_address_state', $map['state'] ?? '');
            update_post_meta($post_id, '_address_postcode', $map['post_code'] ?? '');
            update_post_meta($post_id, '_address_country', $map['country'] ?? '');
            update_post_meta($post_id, '_address_country_short', $map['country_short'] ?? '');
            update_post_meta($post_id, '_address_place_id', $map['place_id'] ?? '');

        } else {

            // If address removed, delete meta
            delete_post_meta($post_id, '_address_full');
            delete_post_meta($post_id, '_address_lat');
            delete_post_meta($post_id, '_address_lng');
            delete_post_meta($post_id, '_address_city');
            delete_post_meta($post_id, '_address_state');
            delete_post_meta($post_id, '_address_postcode');
            delete_post_meta($post_id, '_address_country');
            delete_post_meta($post_id, '_address_country_short');
            delete_post_meta($post_id, '_address_place_id');
        }
    }

    add_action('acf/save_post', 'sync_property_address_meta_acf', 20);

    function sync_property_address_meta_acf($post_id) {

        if (get_post_type($post_id) !== 'property') return;

        $post = get_post($post_id);
        if ($post->post_status !== 'publish') return;

        $map = get_field('address_sp', $post_id);
        if (empty($map) || !is_array($map)) return;

            update_post_meta($post_id, '_address_full', $map['address'] ?? '');
            update_post_meta($post_id, '_address_lat', $map['lat'] ?? '');
            update_post_meta($post_id, '_address_lng', $map['lng'] ?? '');
            update_post_meta($post_id, '_address_city', $map['city'] ?? '');
            update_post_meta($post_id, '_address_state', $map['state'] ?? '');
            update_post_meta($post_id, '_address_postcode', $map['post_code'] ?? '');
            update_post_meta($post_id, '_address_country', $map['country'] ?? '');
            update_post_meta($post_id, '_address_country_short', $map['country_short'] ?? '');
            update_post_meta($post_id, '_address_place_id', $map['place_id'] ?? '');
    }

    add_action('wp_ajax_delete_property_image', 'delete_property_image_callback');

        function delete_property_image_callback() {

            // Security check
            // if (!isset($_POST['nonce']) || 
            //     !wp_verify_nonce($_POST['nonce'], 'delete_property_image_nonce')) {
            //     wp_send_json_error('Security check failed');
            // }

            if (!is_user_logged_in()) {
                wp_send_json_error('Login required');
            }

            $attachment_id = intval($_POST['attachment_id']);
            $post_id       = intval($_POST['post_id']);

            if (!$attachment_id || !$post_id) {
                wp_send_json_error('Invalid data');
            }

            // Permission check
            $current_user_id = get_current_user_id();
            $post_author_id  = get_post_field('post_author', $post_id);

            if (!current_user_can('manage_options') && $current_user_id != $post_author_id) {
                wp_send_json_error('Permission denied');
            }

            /* -----------------------------
            1️⃣ Remove from ACF Gallery
            ----------------------------- */

            $gallery = get_field('property_images', $post_id);

            if (is_array($gallery)) {
                $gallery = array_diff($gallery, [$attachment_id]);
                update_field('property_images', $gallery, $post_id);
            }

            /* -----------------------------
            2️⃣ Remove Featured Image
            ----------------------------- */

            if (get_post_thumbnail_id($post_id) == $attachment_id) {
                delete_post_thumbnail($post_id);
            }

            /* -----------------------------
            3️⃣ Delete Attachment (Optional)
            ----------------------------- */

            wp_delete_attachment($attachment_id, true); // true = force delete

            wp_send_json_success('Image deleted successfully');
        }