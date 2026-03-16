<?php 

$name = get_field('title_broker');
$description = get_field('description_broker');

?>

    <section class="agents-section space-mr">
      <div class="container">

        <!-- Header -->
        <div class="row mb-md-5 mb-4 ">
          <div class="col-lg-5">
            <?php if($name) { ?>
              <h2 class="sec_hdng"><?php echo $name; ?></h2>
            <?php } ?>
            <?php if($name) { ?>
              <p><?php echo $description; ?></p>
            <?php } ?>
          </div>

          <div class="col-lg-7">
            <form id="brokerSearchForm" class="search-outer-wp">
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="search-wp position-relative">
                  
                        <input type="text" id="filter_address" class="form-control" name="filter_address" placeholder="Search location..." autocomplete="off">

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
                    <button id="search-broker">
                      <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/search.svg" alt="Search" width="20px" height="20px">
                    </button>
                  </div>
                </div>

                   <?php 

                        $agency_users = get_users([
                            'role'    => 'agency',
                            'orderby' => 'display_name',
                            'order'   => 'ASC'
                        ]);

                        if ( ! empty( $agency_users ) ) { ?>

                        <div class="col-md-6">
                        <select class="form-control broker-agencies" name="agency">
                            <option selected disabled><?php _e('Select Agency', 'viabosted'); ?></option>
                            <?php foreach ( $agency_users as $user ) : 
                                        
                                        $full_name = trim( $user->first_name . ' ' . $user->last_name );

                                        // fallback if no first/last name
                                        if ( empty( $full_name ) ) {
                                            $full_name = $user->display_name;
                                        }
                                    ?>

                                        <option value="<?php echo esc_attr( $user->ID ); ?>">
                                            <?php echo esc_html( $full_name ); ?>
                                        </option>

                                    <?php endforeach; ?>
                        </select>
                        </div>
                 <?php } ?>
              </div>
            </form>
          </div>
        </div>

            <?php 

            $broker_query = new WP_Query([

                'post_type'      => 'broker',
                'posts_per_page' => -1,
                'orderby' => 'id',
                'order' => 'ASC',
            ]);

            if ($broker_query->have_posts()) { 
              


              $totalPosts = $broker_query->found_posts;

              if ($totalPosts > 1) {
                  $mess = $totalPosts . ' ' . __('Results Found', 'viabosted');
              } else {
                  $mess = $totalPosts . ' ' . __('Result Found', 'viabosted');
              }
                            
              ?>

          <div id="broker-results">      

              <!-- Results -->
              <div class="row mb-3">
                <div class="col-12">
                  <p class="results-text"><?php echo $mess; ?> </p>
                </div>
              </div>

              <!-- Cards -->
              <div class="row g-4">

                <?php while ($broker_query->have_posts()){ 
                          
                      $broker_query->the_post();

                    
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

            <?php } wp_reset_query(); ?>

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
      jQuery(document).ready(function ($) {


        $("#search-broker").on('click', function(e) {
           e.preventDefault();
          $('#brokerSearchForm').submit();
        })

          $(".broker-agencies").on('change', function(e) {
           $('#brokerSearchForm').submit();
        })

        $('#brokerSearchForm').on('submit', function (e) {
          e.preventDefault();

          var formData = new FormData(this);

          formData.append('action', 'broker_search');
             
           $('#broker-results').html('<span class="loader-property"></span>');

            $.ajax({
              url: '<?php echo home_url('/wp-admin/admin-ajax.php')?>', // WP default
              type: 'POST',
              data:formData,
              processData: false,
              contentType: false,
              success: function (response) {
                $('#broker-results').html(response);
              }
            });

          });

        // =========================================
            // RESTORE FILTER FROM LOCAL STORAGE
        // =========================================

            var queryString = window.location.search;

            if (!queryString.includes('searchbroker')) return;

            const savedData = localStorage.getItem("formData");
            if (!savedData) return;

            const params = new URLSearchParams(savedData);

             $('#broker-results').html('<span class="loader-property"></span>');

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
                $("#brokerSearchForm").trigger("submit");

            }, 1);




      });
  
    </script>