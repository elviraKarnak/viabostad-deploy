<?php
// Register shortcode
add_action( 'init', function () {
    add_shortcode( 'home-search-form-three', 'home_search_three_callback' );
});

// Shortcode callback
function home_search_three_callback() {
    ob_start();
    ?>

    <form id="home-search-form-three">
                    <div class="field search">
                    <label>
                        Area
                        <input type="text" id="filter_address_3" name="filter_address" placeholder="Search location..." autocomplete="off">
                    </label>
                    <input type="hidden" name="acf_map[address]" id="acf_map_address_3">
                    <input type="hidden" name="acf_map[lat]" id="acf_map_lat_3">
                    <input type="hidden" name="acf_map[lng]" id="acf_map_lng_3">
                    <input type="hidden" name="acf_map[zoom]" id="acf_map_zoom_3" value="14">
                    <input type="hidden" name="acf_map[name]" id="acf_map_name_3">
                    <input type="hidden" name="acf_map[street_number]" id="acf_map_street_number_3">
                    <input type="hidden" name="acf_map[street_name]" id="acf_map_street_name_3">
                    <input type="hidden" name="acf_map[city]" id="acf_map_city_3">
                    <input type="hidden" name="acf_map[state]" id="acf_map_state_3">
                    <input type="hidden" name="acf_map[post_code]" id="acf_map_post_code_3">
                    <input type="hidden" name="acf_map[country]" id="acf_map_country_3">
                    <input type="hidden" name="acf_map[country_short]" id="acf_map_country_short_3">
                  </div>
                  <div class="submit_wrapper">
                    <input type="submit" value="Find Brokers for Home">
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

                        const input = document.getElementById('filter_address_3');
                        if (!input) return;

                        const autocomplete = new google.maps.places.Autocomplete(input, {
                            types: ['geocode'], // show full address suggestions
                            fields: ['formatted_address', 'geometry', 'address_components']
                        });

                        autocomplete.addListener('place_changed', function () {

                            const place = autocomplete.getPlace();
                            if (!place.geometry) return;

                            // Basic data
                            $('#acf_map_address_3').val(place.formatted_address);
                            $('#acf_map_lat_3').val(place.geometry.location.lat());
                            $('#acf_map_lng_3').val(place.geometry.location.lng());
                            $('#acf_map_zoom_3').val(14);

                            // Clear old values first
                            $('#acf_map_street_number_3, #acf_map_street_name_3, #acf_map_city_3, #acf_map_state_3, #acf_map_post_code_3, #acf_map_country_3').val('');

                            
                            //console.log('Selected place:', place);
                            
                            
                            // Fill address components
                            place.address_components.forEach(function(component) {

                                const types = component.types;

                            
                                if (types.includes('street_number')) {
                                    $('#acf_map_street_number_3').val(component.long_name);
                                }

                                if (types.includes('route')) {
                                    $('#acf_map_street_name_3').val(component.long_name);
                                }

                                if (types.includes('postal_town')) {
                                    $('#acf_map_city_3').val(component.long_name);
                                }

                                if (types.includes('administrative_area_level_1')) {
                                    $('#acf_map_state_3').val(component.long_name);
                                }

                                if (types.includes('postal_code')) {
                                    $('#acf_map_post_code_3').val(component.long_name);
                                }

                                if (types.includes('country')) {
                                    $('#acf_map_country_3').val(component.long_name);
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

                    $('#home-search-form-three').on('submit', function(e) {
                    // Prevent the default form submission
                    e.preventDefault();

                    // Collect form data
                    var formData = $(this).serialize();
                    
                        localStorage.setItem('formData', formData);

                        window.location.href = '/broker/?searchbroker'; // Redirect to the property listing page with form data as query parameters

                    });

 



                });
        </script>

     
    <?php
    return ob_get_clean();
}
