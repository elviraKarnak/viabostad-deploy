<?php
// Register shortcode
add_action( 'init', function () {
    add_shortcode( 'property-listing', 'property_listing_callback' );
});

// Shortcode callback
function property_listing_callback() {
    ob_start();
    ?>
    
	<?php 
        $dummyImg =  get_stylesheet_directory_uri() .'/assets/images/contact-banner.webp';
        $thumb_id = get_post_thumbnail_id();
        $img_src  = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'large') : '';
        $img_alt  = $thumb_id ? get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';


        if($innerBanner){
            $imgUrl   = $img_src[0];
            $alt      = $img_alt;
            $height   = $img_src[2];
            $width    = $img_src[1];
        }else{
            $imgUrl   = $dummyImg;
            $alt      = 'Inner banner';
            $height   = '390';
            $width    = '1920';
        }
    ?>
    
 
	  <section class="sticky_map_locations">
        <div class="container-fluid">
          <div class="outer_wrapper">
            <div class="row">       
              <div class="col-lg-8 col-xl-7">
				<div class="left_wrapper">
					<div class="sec_head">
                        <form id="filter-properties">
                            <div class="shop_filter">
                                <div class="find_property_wrapper">
                                    <div class="row align-items-center gy-md-4 gy-3">
                                        <div class="col-md-6">
                                            <div class="form_wrapper">
                                                <div class="input_wrap">
                                                    <a href="javascript:void(0)" class="filter-open-btn ">
                                                    <img decoding="async" src="https://viabostad.elvirainfotech.live/wp-content/themes/viabostad/assets/images/filter-btn-blue.svg" alt="filter-btn-blue" width="62" height="47"/>
                                                    </a>
                                                </div>
                                                <div class="filter_fields listing_page_filter">
                                                    <div class="find_property_wrapper">
                                                    <div class="form_wrap">
                                                      <div class="field search">
                                                            <label>
                                                                Area
                                                                <input type="text" id="filter_address" name="filter_address" placeholder="Search location..." autocomplete="off">
                                                            </label>
                                                                    <input type="hidden" name="acf_map[address]" id="acf_map_address">
                                                                    <input type="hidden" name="acf_map[lat]" id="acf_map_lat">
                                                                    <input type="hidden" name="acf_map[lng]" id="acf_map_lng">
                                                                    <input type="hidden" name="acf_map[zoom]" id="acf_map_zoom" value="14">
                                                                    <input type="hidden" name="acf_map[name]" id="acf_map_name">
                                                                    <input type="hidden" name="acf_map[street_number]" id="acf_map_street_number">
                                                                    <input type="hidden" name="acf_map[street_name]" id="acf_map_street_name">
                                                                    <input type="hidden" name="acf_map[city]" id="acf_map_city">
                                                                    <input type="hidden" name="acf_map[state]" id="acf_map_state">
                                                                    <input type="hidden" name="acf_map[post_code]" id="acf_map_post_code">
                                                                    <input type="hidden" name="acf_map[country]" id="acf_map_country">
                                                                    <input type="hidden" name="acf_map[country_short]" id="acf_map_country_short">
                                                                </div>
                                                     <div class="field checkboxes_wrapper">
                                                            <p>Housing type</p>
                                                                <label>
                                                                    <input type="checkbox" name="house_type"/>
                                                                    <div class="field_text">
                                                                    <img src="/wp-content/uploads/2026/02/buliding.png" alt="icon" width="20" height="20">
                                                                    All types
                                                                    </div>
                                                                </label>
                                                                    <?php
                                                                    $terms = get_terms([
                                                                        'taxonomy'   => 'property-type',
                                                                        'hide_empty' => true, // set false if you want empty terms too
                                                                    ]);
                                                                    if (!empty($terms) && !is_wp_error($terms)) {?>
                                                                        <?php foreach ( $terms as $term ) { ?>
                                                                        <label>
                                                                        <input type="checkbox" id="<?php echo $term->slug; ?>" name="propertytype[]" value="<?php echo $term->term_id; ?>">
                                                                            <div class="field_text">
                                                                            <img src="/wp-content/uploads/2026/02/buliding.png" alt="icon" width="20" height="20" class='icon'>
                                                                            <?php echo esc_html($term->name); ?>
                                                                            </div>
                                                                        </label>
                                                                        <?php  } ?> 
                                                                    <?php } ?>
                                                                                                
                                                                </div>
                                                                <div class="field">
                                                                <p>Min Room</p>
                                                                    <?php $rooms_data = get_field('rooms_data', 'option');  ?>
                                                                
                                                                
                                                                    <select class="form-select" name="rooms_min">
                                                                    <option disabled selected>Select Room</option>
                                                                    <?php foreach ($rooms_data as $room) { ?>
                                                                        <option value="<?php echo esc_attr($room['room_number_sin']); ?>" ><?php echo esc_html($room['room_number_sin'] . ($room['room_number_sin'] > 1 ? ' rooms' : ' room')); ?></option>
                                                                    <?php } ?>
                                                                    </select>
                                                            
                                                                </div>
                                                                <div class="field">
                                                                <p>Max Room</p>
                                                                    <?php $rooms_data = get_field('rooms_data', 'option');  ?>
                                                                
                                                                
                                                                    <select class="form-select" name="rooms_max">
                                                                    <option disabled selected>Select Room</option>
                                                                    <?php foreach ($rooms_data as $room) { ?>
                                                                        <option value="<?php echo esc_attr($room['room_number_sin']); ?>" ><?php echo esc_html($room['room_number_sin'] . ($room['room_number_sin'] > 1 ? ' rooms' : ' room')); ?></option>
                                                                    <?php } ?>
                                                                    </select>
                                                            
                                                                </div>
                                                                <div class="field">
                                                                <p>Living area</p>
                                                                <?php $area = get_field('area_sqm' , 'option');  ?>
                                                                    <select class="form-select" name="area">
                                                                    <option  disabled selected>Select Living Area</option>
                                                                    <?php foreach ($area as $area) { ?>
                                                                        <option value="<?php echo esc_attr($area['area_sin']); ?>" ><?php echo esc_html($area['area_sin']); ?>m<sup>2</sup></option>
                                                                    <?php } ?>
                                                                    </select>
                                                                
                                                                </div>
                                                                <div class="field">
                                                                <p>Final price</p>
                                                                        <?php $price = get_field('minimum_price' , 'option');  ?>
                                                                    <select class="form-select" name="price">
                                                                    <option disabled selected>Select Final Price</option>
                                                                    <?php foreach ($price as $price) { ?>
                                                                        <option value="<?php echo esc_attr($price['min_price_room']); ?>" ><?php echo esc_html($price['min_price_room'])." ".get_woocommerce_currency(); ?> </option>
                                                                    <?php } ?>
                                                                    </select>
                                                                
                                                                </div>
                                                                <hr>
                                                            
                                                                <div class="field">
                                                                <label>
                                                                    Keyword
                                                                    <input
                                                                    type="text"
                                                                    placeholder="Pool, tiled stoved, etc "
                                                                    name="keyword"
                                                                    />
                                                                </label>
                                                                </div>
                                                                    
                                                        
                                                                <div class="submit_wrapper">
                                                                <input
                                                                    type="submit"
                                                                    value="Find homes for sale"
                                                                />
                                                                </div>
                                                               <input type="hidden" name="action" value="filter_search_property">
                                                            </div>
                                                            <button type="button" class="cross"><i class="fas fa-times"></i></button>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </form>
                          </div>

						<div id="all_properties">

							 <?php 

                             $map_properties = [];

                             $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
                             
                               $product_query = new WP_Query([
                                    'post_type'      => 'property',
                                    'posts_per_page' => 10,
                                    'paged'          => $paged,
                                    'orderby'        => 'ID',
                                    'order'          => 'DESC',
                                    'meta_query'     => [
                                        'relation' => 'AND',
                                        [
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
                                        ]
                                    ]
                                ]);


                            if ( $product_query->have_posts() ) { ?>

							<div class="total_found_products">
								<?php
			
								
									$total        = $product_query->found_posts;
									$per_page     = $product_query->get( 'posts_per_page' );
									$current      = max( 1, get_query_var( 'paged' ) );

									$first = ( $per_page * $current ) - $per_page + 1;
									$last  = min( $total, $per_page * $current );
									?>

									<!-- <p class="search_result">
										Showing Newest Results <?php echo esc_html( $first ); ?>–<?php echo esc_html( $last ); ?>
										of <?php echo esc_html( $total ); ?>
									</p> -->

							</div>
					 
						
                                                                
                                            <div class="row gy-md-4 gy-3 property-slider">
                        
                                                <?php while ( $product_query->have_posts() ) :
                            
                                                    $product_query->the_post();

                                                    get_template_part( 'template-part/poperty-loop' );

                                                    $location = get_field('address_sp', get_the_ID());

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
                                                
                                                endwhile; ?>

                                            </div>

                                            <?php

                                

                                            // /* PAGINATION */
                                            // $big = 999999999;

                                            // $pagination = paginate_links(array(
                                            //     'base'      => str_replace($big, '%#%', esc_url('?paged=' . $big)),
                                            //     'format'    => '?paged=%#%',
                                            //     'current'   => max(1, $paged),
                                            //     'total'     => $product_query->max_num_pages,
                                            //     'type'      => 'array',
                                            // ));

                                            // if ($pagination) {
                                            //     echo '<div class="ajax-pagination"><ul>';
                                            //     foreach ($pagination as $page) {
                                            //         echo '<li>' . $page . '</li>';
                                            //     }
                                            //     echo '</ul></div>';
                                            // }

                                       
                                        ?>


                                            <?php } else { ?>
                                                <p class="no-result">No properties found.</p>
                                                <?php } wp_reset_postdata(); ?>


                                                <?php
                                                    $total_pages = $product_query->max_num_pages;

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

                                                    <?php endif; ?>
										
				</div>
        </div>
              </div>
			
              <div class="col-lg-4 col-xl-5">
               <div class="map_wrapper">
                    <div id="property-map"></div>
                </div>
              </div>
            </div>
            </div>
          </div>
      </section>


	<script>


    jQuery(document).ready(function ($) {

            // INIT GOOGLE AUTOCOMPLETE PROPERLY
            function initAutocomplete() {

                if (typeof google === 'undefined' || !google.maps.places) {
                    console.log('Google Places not loaded');
                    return;
                }

                const input = document.getElementById('filter_address');
                if (!input) return;

                const autocomplete = new google.maps.places.Autocomplete(input, {
                    types: ['geocode'], // show full address suggestions
                    fields: ['formatted_address', 'geometry', 'address_components']
                });

                autocomplete.addListener('place_changed', function () {

                    const place = autocomplete.getPlace();
                    if (!place.geometry) return;

                    // Basic data
                    $('#acf_map_address').val(place.formatted_address);
                    $('#acf_map_lat').val(place.geometry.location.lat());
                    $('#acf_map_lng').val(place.geometry.location.lng());
                    $('#acf_map_zoom').val(14);

                    // Clear old values first
                    $('#acf_map_street_number, #acf_map_street_name, #acf_map_city, #acf_map_state, #acf_map_post_code, #acf_map_country').val('');

                    
                    //console.log('Selected place:', place);
                    
                    
                    // Fill address components
                    place.address_components.forEach(function(component) {

                        const types = component.types;

                    
                        if (types.includes('street_number')) {
                            $('#acf_map_street_number').val(component.long_name);
                        }

                        if (types.includes('route')) {
                            $('#acf_map_street_name').val(component.long_name);
                        }

                        if (types.includes('postal_town')) {
                            $('#acf_map_city').val(component.long_name);
                        }

                        if (types.includes('administrative_area_level_1')) {
                            $('#acf_map_state').val(component.long_name);
                        }

                        if (types.includes('postal_code')) {
                            $('#acf_map_post_code').val(component.long_name);
                        }

                        if (types.includes('country')) {
                            $('#acf_map_country').val(component.long_name);
                        }

                    });

                });
            }

            // Run after window fully loads (important!)
            jQuery(window).on('load', function () {
                initAutocomplete();
            });

    });
</script>


   <script>


        let map;
        let markers = [];
        let infoWindow;

        var propertyData = <?php echo json_encode($map_properties); ?>;

        function initPropertyMap(propertyData) {

            const mapContainer = document.getElementById('property-map');
            if (!mapContainer) return;

            const defaultCenter = propertyData.length
                ? { lat: parseFloat(propertyData[0].lat), lng: parseFloat(propertyData[0].lng) }
                : { lat: 59.3293, lng: 18.0686 };

            map = new google.maps.Map(mapContainer, {
                zoom: 8,
                center: defaultCenter,

                // ✅ SAFE light gray modern style
                styles: [
                    {
                        featureType: "poi",
                        stylers: [{ visibility: "off" }]
                    },
                    {
                        featureType: "transit",
                        stylers: [{ visibility: "off" }]
                    },
                    {
                        featureType: "road",
                        elementType: "labels.icon",
                        stylers: [{ visibility: "off" }]
                    },
                    {
                        featureType: "water",
                        elementType: "geometry",
                        stylers: [{ color: "#e9ecef" }]
                    },
                    {
                        featureType: "landscape",
                        elementType: "geometry",
                        stylers: [{ color: "#f8f9fa" }]
                    }
                ]
            });

            infoWindow = new google.maps.InfoWindow();
            const bounds = new google.maps.LatLngBounds();

            propertyData.forEach((property) => {

                const position = new google.maps.LatLng(
                    parseFloat(property.lat),
                    parseFloat(property.lng)
                );

                createCustomMarker(position, property);
                bounds.extend(position);

            });

                        if (propertyData.length > 1) {
                map.fitBounds(bounds);
                
            } else {
                map.setZoom(14);
            }
        }

        function createCustomMarker(position, property) {

            const overlay = new google.maps.OverlayView();

            overlay.onAdd = function () {

                const div = document.createElement("div");
                div.className = "custom-marker";

                div.innerHTML = `
                    <div class="marker-card">
                        <img src="${property.image}" />
                    </div>
                    <div class="marker-dot"></div>
                `;

                div.addEventListener("click", () => {
                    infoWindow.setContent(`
                        <div class="map-popup">
                            <div class="image-wrapper">
                                <img src="${property.image}" width="100%">
                            </div>
                            <div class="descrip-wrapper">
                                <h4 class="title">${property.title}</h4>
                                <p class="location">${property.location}</p>
                                <p class="price">${property.price}</p>
                            </div>
                        </div>
                    `);
                    infoWindow.setPosition(position);
                    infoWindow.open(map);
                });

                overlay.div = div;
                overlay.getPanes().overlayMouseTarget.appendChild(div);
            };

            overlay.draw = function () {
                const projection = this.getProjection();
                const point = projection.fromLatLngToDivPixel(position);

                if (point) {
                    this.div.style.position = "absolute";
                    this.div.style.left = point.x + "px";
                    this.div.style.top = point.y + "px";
                }
            };

            overlay.setMap(map);
            markers.push(overlay); 
        }


        function updateMapMarkers(newData) {

            // Clear old markers
            if (markers.length) {
                markers.forEach(marker => {
                    if(marker.setMap){
                        marker.setMap(null);
                    }
                });
                markers = [];
            }

            propertyData = newData;

            const bounds = new google.maps.LatLngBounds();

            propertyData.forEach((property) => {

                const position = new google.maps.LatLng(
                    parseFloat(property.lat),
                    parseFloat(property.lng)
                );

                createCustomMarker(position, property);
                bounds.extend(position);
            });

            if(propertyData.length){
                map.fitBounds(bounds);
            }
        }


    
        jQuery(document).ready(function ($) {


                                
            initPropertyMap(propertyData);

            let currentPage = 1;

            // ==============================
            // FILTER SUBMIT
            // ==============================
            $("#filter-properties").on("submit", function (e) {
                e.preventDefault();
                currentPage = 1;
                load_properties(currentPage, false);
            });

            // ==============================
            // LOAD MORE CLICK
            // ==============================
            $(document).on("click", ".load-more-btn", function (e) {
                e.preventDefault();

                var button = $(this);
                var nextPage = button.data("page");
                var maxPage = button.data("max");

                if (nextPage <= maxPage) {
                    load_properties(nextPage, true);
                    currentPage = nextPage;
                }

                if (nextPage >= maxPage) {
                    button.closest(".show_more_wrapper").remove();
                }
            });

            // ==============================
            // MAIN LOAD FUNCTION
            // ==============================

            function load_properties(paged = 1, append = false) {

                var formData = new FormData($('#filter-properties')[0]);
                formData.append("action", "filter_search_property");
                formData.append("paged", paged);

                if (!append) {
                    $("#all_properties").html('<span class="loader-property"></span>');
                } else {
                    $(".load-more-btn").text("Loading...");
                }

                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {

                       
                 

                        if (!response.success) return;

                        var html    = response.data.html;
                        var mapData = response.data.map;
                        var count   = response.data.count;
                        var maxPage = response.data.max_page;

                        if (append) {

                            var temp = $("<div>").html(html);
                            var newItems = temp.find(".property-slider").html();
                            var newButton = temp.find(".show_more_wrapper");

                            if (newItems) {
                                $(".property-slider").append(newItems);
                            }

                            $(".show_more_wrapper").remove();

                            if (newButton.length) {
                                $("#all_properties").append(newButton);
                            }

                             if (mapData) {
                                 propertyData = [...propertyData, ...mapData];
  
                                initPropertyMap(propertyData);
                            }

                        } else {
                            $("#all_properties").html(html);
                                if (mapData) {
                                initPropertyMap(mapData);
                            }
                        }

                        if (count) {
                            $(".total_found_products").html(count);
                        }

                        // ✅ Update Map
                        
                    },
                     error: function(xhr, status, error) {
                        console.log("Error:", error);
                        alert('Network Error Please Try Again.');
                    },

                    complete: function(xhr, status) {
                         $(".listing_page_filter").removeClass('active');
                        console.log("Request Finished");
                        // This runs ALWAYS (after success or error)
                    }
                });
            }


         // =========================================
            // RESTORE FILTER FROM LOCAL STORAGE
        // =========================================

            var queryString = window.location.search;

            if (!queryString.includes('searchproperty')) return;

            const savedData = localStorage.getItem("formData");
            if (!savedData) return;

            const params = new URLSearchParams(savedData);

            $("#all_properties").html('<span class="loader-property"></span>');

            // Delay a little to ensure selects are fully rendered
            setTimeout(function () {

                params.forEach(function (value, key) {

                    let field = $("[name='" + key + "']");

                    if (!field.length) return;

                    // Checkbox arrays
                    if (key.includes("[]")) {
                        $("input[name='" + key + "'][value='" + value + "']")
                            .prop("checked", true);
                    }

                    // Select dropdown
                    else if (field.is("select")) {

                         let cleanValue = value.replace(/[^\d.]/g, '');

                        field.val(cleanValue).trigger("change");
                    }

                    // Normal input
                    else {
                        field.val(value);
                    }

                });

                // Visible address
                if (params.get("acf_map[address]")) {
                    $("#filter_address").val(params.get("acf_map[address]"));
                }

                // Auto submit
                $("#filter-properties").trigger("submit");

            }, 100);

        });




    </script>

    <?php
    return ob_get_clean();
}
