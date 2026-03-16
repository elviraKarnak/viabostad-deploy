<?php
/*
|--------------------------------------------------------------------------
| PROPERTY NOTIFICATIONS — User (Author) Alert System
| Expandable: add more $type cases as needed
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| 0. Debug Logger
|--------------------------------------------------------------------------
*/

function property_log($message) {
    $log_file = WP_CONTENT_DIR . '/property-notifications.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[{$timestamp}] {$message}\n", FILE_APPEND);
}


/*
|--------------------------------------------------------------------------
| 1. Register BuddyPress Property Component
|--------------------------------------------------------------------------
*/

add_action('bp_setup_components', function () {

    if (!class_exists('BP_Component')) {
        property_log('ERROR: BP_Component not found. BuddyPress inactive.');
        return;
    }

    if (!class_exists('BP_Property_Component')) {

        class BP_Property_Component extends BP_Component {

            public function __construct() {
                parent::start(
                    'property',
                    'Property',
                    plugin_dir_path(__FILE__)
                );
            }
        }
    }

    buddypress()->property = new BP_Property_Component();
    property_log('BP_Property_Component registered.');
});


/*
|--------------------------------------------------------------------------
| 2. Register Component for BP Notifications
|--------------------------------------------------------------------------
*/

add_filter('bp_notifications_get_registered_components', function ($components) {
    $components[] = 'property';
    return $components;
});


/*
|--------------------------------------------------------------------------
| 3. Format What the User Sees in Their BP Notification Bell
|--------------------------------------------------------------------------
|  Expandable: add new $action cases here for future notification types
*/

add_filter('bp_notifications_get_notifications_for_user', 'format_property_notifications', 10, 7);

function format_property_notifications(
    $content,
    $item_id,
    $secondary_item_id,
    $total_items,
    $format,
    $action,
    $component
) {
    if ($component !== 'property') {
        return $content;
    }

    $post = get_post($item_id);
    if (!$post) {
        return $content;
    }

    /*
    |------------------------------------------------------------------
    | Notification Text — expandable, add new action types below
    |------------------------------------------------------------------
    */
    switch ($action) {

        case 'property_published':
            $text = 'Your property "' . $post->post_title . '" has been published.';
            break;

        case 'property_updated':
            $text = 'Your property "' . $post->post_title . '" was updated successfully.';
            break;

        // Future types — uncomment and extend as needed:
        // case 'property_approved':
        //     $text = 'Your property "' . $post->post_title . '" has been approved.';
        //     break;

        // case 'property_rejected':
        //     $text = 'Your property "' . $post->post_title . '" was rejected. Please review.';
        //     break;

        // case 'property_enquiry':
        //     $text = 'Someone enquired about your property "' . $post->post_title . '".';
        //     break;

        default:
            return $content;
    }

    if ($format === 'string') {
        return $text;
    }

    return [
        'text' => $text,
        'link' => get_permalink($item_id),
    ];
}


/*
|--------------------------------------------------------------------------
| 4. Core Notification Function — Sends to the AUTHOR (User)
|--------------------------------------------------------------------------
|  $post_id : the property post ID
|  $type    : 'created' | 'updated'  (expandable — add more types)
*/

function property_send_notifications($post_id, $type = 'updated') {

    property_log("property_send_notifications() — post_id={$post_id}, type={$type}");

    // --- Validate post ---
    $post = get_post($post_id);
    if (!$post) {
        property_log("ERROR: Post not found for post_id={$post_id}");
        return;
    }
    if ($post->post_type !== 'property') {
        property_log("ERROR: post_type='{$post->post_type}' — expected 'property'. Aborting.");
        return;
    }

    // --- Validate BuddyPress ---
    if (!function_exists('bp_notifications_add_notification')) {
        property_log("ERROR: bp_notifications_add_notification() not found. BP Notifications inactive.");
        return;
    }

    // --- The USER (author) who owns the property ---
    $user_id = (int) $post->post_author;
    $user    = get_userdata($user_id);

    if (!$user) {
        property_log("ERROR: Could not get user data for user_id={$user_id}");
        return;
    }

    property_log("Notifying user: {$user->display_name} (ID={$user_id}, email={$user->user_email})");

    /*
    |------------------------------------------------------------------
    | Map type → BP component_action
    | Expandable: add new types here
    |------------------------------------------------------------------
    */
    $bp_action_map = [
        'created' => 'property_published',
        'updated' => 'property_updated',
        // 'approved'  => 'property_approved',
        // 'rejected'  => 'property_rejected',
        // 'enquiry'   => 'property_enquiry',
    ];

    if (!isset($bp_action_map[$type])) {
        property_log("ERROR: Unknown notification type='{$type}'");
        return;
    }

    $bp_action = $bp_action_map[$type];

    /*
    |------------------------------------------------------------------
    | BuddyPress Notification → goes to user's notification bell
    |------------------------------------------------------------------
    */

    // Remove previous notification for same post+action to avoid stacking
    bp_notifications_delete_notifications_by_item_id(
        $user_id,
        $post_id,
        'property',
        $bp_action
    );

    $notif_id = bp_notifications_add_notification([
        'user_id'           => $user_id,        // ← The author receives it
        'item_id'           => $post_id,
        'secondary_item_id' => $user_id,        // ← Who triggered (same user here)
        'component_name'    => 'property',
        'component_action'  => $bp_action,
        'date_notified'     => bp_core_current_time(),
        'is_new'            => 1,
    ]);

    if ($notif_id) {
        property_log("BP Notification sent. notif_id={$notif_id} → user_id={$user_id}");
    } else {
        property_log("ERROR: bp_notifications_add_notification() failed for user_id={$user_id}");
    }

    /*
    |------------------------------------------------------------------
    | Email Notification → goes to user's email
    | Expandable: add new $type cases to this map
    |------------------------------------------------------------------
    */

    $email_templates = [

        'created' => [
            'subject' => 'Your Property Has Been Published',
            'body'    =>
                "Hello {user_name},\n\n" .
                "Your property has been successfully published.\n\n" .
                "Title: {post_title}\n" .
                "View:  {post_url}\n\n" .
                "Regards,\nYour Website"
        ],

        'updated' => [
            'subject' => 'Your Property Has Been Updated',
            'body'    =>
                "Hello {user_name},\n\n" .
                "Your property has been updated successfully.\n\n" .
                "Title: {post_title}\n" .
                "View:  {post_url}\n\n" .
                "Regards,\nYour Website"
        ],

        // Future types:
        // 'approved' => [ 'subject' => '...', 'body' => '...' ],
        // 'rejected' => [ 'subject' => '...', 'body' => '...' ],
    ];

    if (!isset($email_templates[$type])) {
        property_log("WARNING: No email template for type='{$type}'");
        return;
    }

    $template = $email_templates[$type];

    // Replace placeholders
    $placeholders = [
        '{user_name}'  => $user->display_name,
        '{post_title}' => $post->post_title,
        '{post_url}'   => get_permalink($post_id),
    ];

    $subject = str_replace(array_keys($placeholders), array_values($placeholders), $template['subject']);
    $body    = str_replace(array_keys($placeholders), array_values($placeholders), $template['body']);

    $mail_sent = wp_mail($user->user_email, $subject, $body);

    if ($mail_sent) {
        property_log("Email sent to {$user->user_email}");
    } else {
        property_log("ERROR: wp_mail() failed for {$user->user_email}");

        // Capture PHPMailer error if available
        global $phpmailer;
        if (!empty($phpmailer->ErrorInfo)) {
            property_log("PHPMailer Error: " . $phpmailer->ErrorInfo);
        }
    }

    property_log("Done — post_id={$post_id}, type={$type}");
}


/*
|--------------------------------------------------------------------------
| 5. WP Backend / Admin Save Trigger (non-AJAX)
|--------------------------------------------------------------------------
*/

add_action('save_post_property', function ($post_id, $post, $update) {

    property_log("save_post_property fired — post_id={$post_id}, update=" . ($update ? 'true' : 'false'));

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        property_log("Skipping: DOING_AUTOSAVE");
        return;
    }

    if (wp_is_post_revision($post_id)) {
        property_log("Skipping: is revision");
        return;
    }

    if ($post->post_status !== 'publish') {
        property_log("Skipping: post_status='{$post->post_status}'");
        return;
    }

    // Prevent duplicate when AJAX handler already fired
    if (defined('DOING_PROPERTY_AJAX') && DOING_PROPERTY_AJAX) {
        property_log("Skipping: DOING_PROPERTY_AJAX defined");
        return;
    }

    $type = $update ? 'updated' : 'created';
    property_log("save_post_property proceeding — type={$type}");
    property_send_notifications($post_id, $type);

}, 20, 3);


/*
|--------------------------------------------------------------------------
| 6. AJAX Event Hooks (called from your AJAX handler)
|--------------------------------------------------------------------------
*/

add_action('property_created_event', function ($post_id) {
    property_log("property_created_event fired — post_id={$post_id}");
    property_send_notifications($post_id, 'created');
});

add_action('property_updated_event', function ($post_id) {
    property_log("property_updated_event fired — post_id={$post_id}");
    property_send_notifications($post_id, 'updated');
});