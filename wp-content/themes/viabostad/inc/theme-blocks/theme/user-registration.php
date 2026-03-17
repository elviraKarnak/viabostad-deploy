</head>



        <section class="user_registration">

        <?php if (!is_user_logged_in()) : ?>
            <div class="registration-container">
                <div class="form-header">
                <h1><?php _e('User Registration', 'viabosted'); ?></h1>
                <p><?php _e('Join our network of professional real estate brokers', 'viabosted'); ?></p>
                </div>

                <div class="form-body">
                <form id="brokerRegistrationForm" method="post" enctype="multipart/form-data">

                    <div class="form-section">
                            <h2 class="section-title"><?php _e('Select User Type', 'viabosted'); ?></h2>

                            <select name="user_type" id="user_type" required>
                                <option value="" disabled selected hidden><?php _e('-- Select User Type --', 'viabosted'); ?></option>
                                <option value="agency"><?php _e('Agency', 'viabosted'); ?></option>
                                <option value="broker"><?php _e('Broker', 'viabosted'); ?></option>
                                <option value="customer"><?php _e('Customer', 'viabosted'); ?></option>
                            </select>
                        </div>
             <!-- Personal Information Section -->
                    <div class="form-section">
                    <h2 class="section-title"><?php _e('Personal Information', 'viabosted'); ?></h2>

                    <div class="form-row">
                        <div class="form-group">
                        <label for="firstName"><?php _e('First Name', 'viabosted'); ?> <span class="required">*</span></label>
                        <input type="text" id="firstName" name="first_name" required>
                        </div>

                        <div class="form-group">
                        <label for="lastName"><?php _e('Last Name', 'viabosted'); ?> <span class="required">*</span></label>
                        <input type="text" id="lastName" name="last_name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="username"><?php _e('Username', 'viabosted'); ?> <span class="required">*</span></label>
                        <input type="text" id="username" name="username" required>
                        <div class="help-text"><?php _e('Choose a unique username for your profile', 'viabosted'); ?></div>
                    </div>

                    <div class="form-group">
                        <label for="email"><?php _e('Email Address', 'viabosted'); ?> <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                        <label for="password"><?php _e('Password', 'viabosted'); ?> <span class="required">*</span></label>
                        <input type="password" id="password" name="password" required>
                        <div class="password-strength">
                            <div class="password-strength-bar"></div>
                        </div>
                        </div>

                        <div class="form-group">
                        <label for="confirmPassword"><?php _e('Confirm Password', 'viabosted'); ?> <span class="required">*</span></label>
                        <input type="password" id="confirmPassword" name="confirm_password" required>
                        </div>
                    </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="form-section">
                    <h2 class="section-title"><?php _e('Contact Information', 'viabosted'); ?></h2>

                    <div class="form-row">
                        <div class="form-group">
                        <label for="phone"><?php _e('Phone Number', 'viabosted'); ?> <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" required>
                        </div>

                        <div class="form-group">
                        <label for="website"><?php _e('Website URL', 'viabosted'); ?></label>
                        <input type="url" id="website" name="website" placeholder="https://">
                        </div>
                    </div>
                    </div>

                    <!-- Profile Information Section -->
                    <div class="form-section">
                    <h2 class="section-title"><?php _e('Profile Information', 'viabosted'); ?></h2>

                    <div class="form-group">
                        <label for="profilePicture"><?php _e('Profile Picture', 'viabosted'); ?> <span class="required">*</span></label>
                        <div class="profile-picture-upload">
                        <div class="profile-preview">
                            <i class="fas fa-user placeholder"></i>
                            <img id="profilePreview" src="" alt="Profile Preview">
                        </div>
                        <div class="upload-controls">
                            <div class="file-input-wrapper">
                            <input type="file" id="profilePicture" name="profile_picture" accept="image/*" required>
                            <label for="profilePicture">
                                <i class="fas fa-upload"></i> <?php _e('Choose Photo', 'viabosted'); ?>
                            </label>
                            </div>
                            <div class="file-info">
                            <?php _e('Accepted formats: JPG, PNG, GIF (Max 5MB)', 'viabosted'); ?>
                            </div>
                        </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bio"><?php _e('Profile Description / Bio', 'viabosted'); ?> <span class="required">*</span></label>
                        <textarea id="bio" name="bio" required  placeholder="Tell us about your experience, expertise, and what makes you a great broker..."></textarea>
                        <!-- <div class="help-text"><?php //_e('Minimum 100 characters', 'viabosted'); ?></div> -->
                    </div>
                    </div>

                    <!-- Professional Information Section -->
                    <div class="form-section agency-select-group d-none">
                    <h2 class="section-title"><?php _e('Agency Information', 'viabosted'); ?></h2>
                        <?php 

                        $agency_users = get_users([
                            'role'    => 'agency',
                            'orderby' => 'display_name',
                            'order'   => 'ASC'
                        ]);

                        if ( !empty($agency_users) ) { ?>

                            <div class="form-group">
                                <label for="agency"><?php _e('Select Agency', 'viabosted'); ?> <span class="required">*</span></label>
                                <select id="agency" name="agency">

                                    <option value="" disabled selected hidden>
                                    <?php _e('-- Select Your Agency --', 'viabosted'); ?>
                                    </option>

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

                        <!-- Professional Information Section -->
                    <div class="form-section broker-select-group d-none">
                        <h2 class="section-title"><?php _e('Broker Area', 'viabosted'); ?></h2> 

                        <div class="form-group">
                        

                                <input type="text" id="acf_address" name="address" placeholder="Search location..." autocomplete="off">

                                <!-- Hidden ACF Google Map Fields -->
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
                    </div>


                    

                    <!-- Terms and Conditions -->
                    <div class="form-section">
                        <div class="checkbox-group">
                            <input type="checkbox" id="terms" name="terms" required>
                                <label for="terms">
                                    <?php _e('I agree to the', 'viabosted'); ?>
                                    <a href="#" style="color: #3fa9db;"><?php _e('Terms and Conditions', 'viabosted'); ?></a>
                                    <?php _e('and', 'viabosted'); ?>
                                    <a href="#" style="color: #3fa9db;"><?php _e('Privacy Policy', 'viabosted'); ?></a>
                                    <span class="required">*</span>
                                </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="primary_btn icon arrow">
                    <?php _e('Register', 'viabosted'); ?>
                    </button>

                    <div class="already-registered">
                    <?php _e('Already have an account?', 'viabosted'); ?> <a href="<?php echo home_url('/log-in')?>"><?php _e('Sign In', 'viabosted'); ?></a>
                    </div>
                </form>
                </div>
            </div>
            <?php ?>
        <?php else : ?>
            <div class="registration-container">
                <div class="form-header">
                <h2><?php _e('Welcome Back!', 'viabosted'); ?></h2>
<p>
    <?php _e('You are already logged in.', 'viabosted'); ?>
    <a href="<?php echo wp_logout_url( home_url() ); ?>"><?php _e('Logout?', 'viabosted'); ?></a>
</p>
</div>
            </div>
        <?php endif; ?>
        </section>       


        <script>
    jQuery(document).ready(function ($) {

        $('#profilePicture').on('change', function(e){

            const file = this.files[0];

            if(!file) return;

            // Validate file type
            const allowedTypes = ['image/jpeg','image/png','image/gif','image/webp'];

            if(!allowedTypes.includes(file.type)){
                alert('Please upload JPG, PNG, GIF or WEBP image.');
                $(this).val('');
                return;
            }

            // Validate size (5MB)
            if(file.size > 5 * 1024 * 1024){
                alert('Image must be less than 5MB.');
                $(this).val('');
                return;
            }

            // Preview image
            const reader = new FileReader();

            reader.onload = function(event){
                $('#profilePreview')
                    .attr('src', event.target.result)
                    .show();
            };

            reader.readAsDataURL(file);

            $(".profile-preview i").hide();

        });


            // INIT GOOGLE AUTOCOMPLETE PROPERLY
            function initAutocomplete() {

                if (typeof google === 'undefined' || !google.maps.places) {
                    console.log('Google Places not loaded');
                    return;
                }

                const input = document.getElementById('acf_address');
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

jQuery(document).ready(function($){

       $('.btn-submit').prop('disabled', true);


       $("#user_type").on('change', function(){
            if($(this).val() == 'broker'){
                $('.agency-select-group').removeClass('d-none');
                $('.broker-select-group').removeClass('d-none');
                $('#acf_map_address').prop('required', true);
                $('#agency').prop('required', true);
                
            } else {
                $('.agency-select-group').addClass('d-none');
                $('#agency').removeAttr('required');
                $('#acf_map_address').removeAttr('required');
                $('.broker-select-group').addClass('d-none');
            }
       });

    $('#username').on('blur', function(){

        var username = $(this).val().trim();

        if(username.length < 3){
            return;
        }

        $.ajax({
            url:'<?php echo home_url('/wp-admin/admin-ajax.php')?>',
            type: 'POST',
            data: {
                action: 'check_username',
                username: username
            },
            beforeSend: function(){
                $('#username').next('.help-text').text('Checking...');
            },
            success: function(response){

                if(response.success){
                    $('#username').next('.help-text')
                        .css('color','green')
                        .text(response.data.message);
                      $('.btn-submit').prop('disabled', false);
                } else {
                    $('#username').next('.help-text')
                        .css('color','red')
                        .text(response.data.message);
                    $('.btn-submit').prop('disabled', true);
                }

            }
        });

    });

    $('#brokerRegistrationForm').on('submit', function(e){
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('action', 'register_broker');

        $.ajax({
            url: '<?php echo home_url('/wp-admin/admin-ajax.php')?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function(){
                $('.btn-submit').text('Registering...');
            },
            success: function(response){

                if(response.success){
                    window.location.href = response.data.redirect;
                } else {
                    alert(response.data.message);
                    $('.btn-submit').text('Register as Broker');
                }

            },
            error: function(){
                alert('Something went wrong');
                $('.btn-submit').text('Register as Broker');
            }
        });

    });

});

</script>