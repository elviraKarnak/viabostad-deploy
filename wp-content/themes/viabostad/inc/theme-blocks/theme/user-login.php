


<section class="pt_100 pb_100 login-section">

    <div class="container">

    <?php if(!is_user_logged_in()) : ?>
    <h1><?php _e('User Login', 'viabosted'); ?></h1>

        <div class="login-form-wrapper">

            <?php
            wp_login_form( array(
                'redirect'       => get_permalink(), // redirect back after login
                'form_id'        => 'restricted-login-form',
                'label_username' => __( 'Username or Email' ),
                'label_password' => __( 'Password' ),
                'label_log_in'   => __( 'Log In' ),
                'remember'       => true
            ) );
            ?>
            <p><?php _e('Not a member?', 'viabosted'); ?> <a href="/registration/"><?php _e('Signup now', 'viabosted'); ?></a></p>
        </div>
        <?php else : ?>
            <h1><?php _e('You are already logged in', 'viabosted'); ?></h1>
            <?php endif; ?>
    </div>

</section>
