<?php
// Register shortcode
add_action( 'init', function () {
    add_shortcode( 'home-search-form-two', 'home_search_two_callback' );
});

// Shortcode callback
function home_search_two_callback() {
    ob_start();
    ?>

    <form id="home-search-form-two" class="home-search-form-one" method="post">
                  <div class="field search">
                    <label>
                        Area
                        <input type="text" id="filter_address_two" name="filter_address" placeholder="Search location..." autocomplete="off">
                    </label>
                    <input type="hidden" name="acf_map[address]" id="acf_map_address_2">
                    <input type="hidden" name="acf_map[lat]" id="acf_map_lat_2">
                    <input type="hidden" name="acf_map[lng]" id="acf_map_lng_2">
                    <input type="hidden" name="acf_map[zoom]" id="acf_map_zoom_2" value="14">
                    <input type="hidden" name="acf_map[name]" id="acf_map_name_2">
                    <input type="hidden" name="acf_map[street_number]" id="acf_map_street_number_2">
                    <input type="hidden" name="acf_map[street_name]" id="acf_map_street_name_2">
                    <input type="hidden" name="acf_map[city]" id="acf_map_city_2">
                    <input type="hidden" name="acf_map[state]" id="acf_map_state_2">
                    <input type="hidden" name="acf_map[post_code]" id="acf_map_post_code_2">
                    <input type="hidden" name="acf_map[country]" id="acf_map_country_2">
                    <input type="hidden" name="acf_map[country_short]" id="acf_map_country_short_2">
                  </div>
  
                  <div class="field checkboxes_wrapper">
                    <label>
                      <input type="checkbox" name="propertytype[]" value= "all">
                      <div class="field_text">
                        <img src="/wp-content/uploads/2026/02/buliding.png" alt="icon" width="20" height="20" class='icon'>
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
                    <div class="field checkboxes_wrapper without_icon">
                    <p>Sold within the last few</p>

                     <?php
                        $terms = get_terms([
                            'taxonomy'   => 'sold-period',
                            'hide_empty' => true, // set false if you want empty terms too
                        ]);
                        if (!empty($terms) && !is_wp_error($terms)) {?>
                            <?php foreach ( $terms as $term ) { ?>
                                  <label>
                                    <input type="checkbox" name="sold_period[]" value="<?php echo $term->term_id; ?>">
                                        <div class="field_text">
                                            <div class="circle"></div>
                                        <?php echo esc_html($term->name); ?>
                                        </div>
                                </label>
                  
                            <?php  } ?> 
                        <?php } ?>
                  </div>

                  <div class="filter_options text-center">
                    <button type="button" class="show_filter_option">Show search filters <i class="fas fa-chevron-right"></i></button>
                    <div class="filter_options_wrapper">
                      <div class="row_group scend_row_form">
                        <?php  $rooms_min_values = get_field('rooms_data', 'option'); ?>
                        <div class="field select">
                          <label>
                            Minimum rooms
                            <select class="form-select w-100" name="rooms_min">
                              <option>All</option>
                              <?php foreach ($rooms_min_values as $room) { ?>
                                <option><?php echo $room['room_number_sin']; ?> room<?php echo $room['room_number_sin'] > 1 ? 's' : ''; ?></option>
                              <?php } ?>
                            </select>
                          </label>
                        </div>
                        <?php  $rooms_min_values = get_field('rooms_data', 'option'); ?>
                        <div class="field select">
                          <label>
                            Maximum rooms
                            <select class="form-select w-100" name="rooms_max">
                              <option>All</option>
                              <?php foreach ($rooms_min_values as $room) { ?>
                                <option value="<?php echo $room['room_number_sin']; ?>"><?php echo $room['room_number_sin']; ?> room<?php echo $room['room_number_sin'] > 1 ? 's' : ''; ?></option>
                              <?php } ?>
                            </select>
                          </label>
                        </div>
                        <div class="field select">
                          <?php $living_area_min_values =  get_field('area_sqm' , 'option');  ?>
                          <label>
                            Minimum living area
                            <select class="form-select w-100" name="area">
                              <option>All</option>
                              <?php foreach ($living_area_min_values as $value) { ?>
                                <option value="<?php echo $value['area_sin']; ?>"><?php echo $value['area_sin']; ?> m<sup>2</sup></option>
                              <?php } ?>
                            </select>
                          </label>
                        </div>
                             <div class="field select">
                          <label>
                            Highest price
                            <?php $price_max_values = get_field('minimum_price' , 'option'); ?>
                            <select class="form-select w-100" name="price">
                              <option>Nothing</option>
                              <?php foreach ($price_max_values as $value) { ?>
                                <option value="<?php echo $value['min_price_room']; ?>"><?php echo number_format($value['min_price_room'], 0, ',', ' ') ." " . get_woocommerce_currency(); ?></option>
                              <?php } ?>
          
                            </select>
                          </label>
                        </div>
                      </div>
                
                    </div>
                  </div>
                  <input type="hidden" name="action" value="home_search_form_one">
                  <div class="submit_wrapper">
                    <input type="submit" value="Find homes for sale">
                  </div>
                  </form>

                <script>

                jQuery(document).ready(function ($) {

                    // INIT GOOGLE AUTOCOMPLETE PROPERLY
                    function initAutocomplete() {

                        if (typeof google === 'undefined' || !google.maps.places) {
                            console.log('Google Places not loaded');
                            return;
                        }

                        const input = document.getElementById('filter_address_two');
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
                                    $('#acf_map_street_number_2').val(component.long_name);
                                }

                                if (types.includes('route')) {
                                    $('#acf_map_street_name_2').val(component.long_name);
                                }

                                if (types.includes('postal_town')) {
                                    $('#acf_map_city_2').val(component.long_name);
                                }

                                if (types.includes('administrative_area_level_1')) {
                                    $('#acf_map_state_2').val(component.long_name);
                                }

                                if (types.includes('postal_code')) {
                                    $('#acf_map_post_code_2').val(component.long_name);
                                }

                                if (types.includes('country')) {
                                    $('#acf_map_country_2').val(component.long_name);
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
          jQuery(document).ready(function($) {

                $('#home-search-form-two').on('submit', function(e) {
                  // Prevent the default form submission
                  e.preventDefault();

                  // Collect form data
                  var formData = $(this).serialize();
                  
                    localStorage.setItem('formData', formData);

                    window.location.href = '/housing/?searchproperty'; // Redirect to the property listing page with form data as query parameters

              });
            });
        </script>

    <?php
    return ob_get_clean();
}
