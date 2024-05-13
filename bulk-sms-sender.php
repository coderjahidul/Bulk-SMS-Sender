<?php
/**
 * Bulk SMS Sender
 *
 * @package       BULKSMSSEN
 * @author        Jahidul islam Sabuz
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   Bulk SMS Sender
 * Plugin URI:    https://imjol.com
 * Description:   Imjol Web Design and Development Company proudly presents the Bulk SMS Sender WordPress Plugin, a powerful tool designed to streamline communication for your WordPress website. In today\'s fast-paced digital world, reaching your audience efficiently is crucial, and our plugin is tailored to meet this demand with precision and ease.  With the Bulk SMS Sender WordPress Plugin, you can effortlessly send mass text messages directly from your WordPress dashboard to your subscribers, customers, or targeted groups. Whether you\'re running a promotional campaign, sending reminders, or delivering important updates, this plugin empowers you to connect with your audience instantly.  One of the key features of our plugin is its user-friendly interface, which makes the process of composing and sending SMS messages a breeze. You can easily customize your messages, schedule them for optimal delivery times, and track their performance with insightful analytics.  Furthermore, our plugin is designed with versatility in mind, allowing you to integrate it seamlessly with your existing WordPress website and other essential plugins. It\'s compatible with various SMS gateways, ensuring reliable delivery of your messages across different networks and regions.  Security is paramount when it comes to handling sensitive customer data, and our plugin prioritizes the protection of your information. With robust encryption and privacy measures in place, you can trust that your data is safe and secure at all times.  Imjol Web Design and Development Company takes pride in delivering innovative solutions that empower businesses to succeed in the digital landscape. With the Bulk SMS Sender WordPress Plugin, you can take your communication strategy to the next level, driving engagement, fostering customer relationships, and ultimately achieving your goals with ease. Experience the power of efficient communication with our plugin today!
 * Version:       1.0.0
 * Author:        Jahidul islam Sabuz
 * Author URI:    https://imjol.com
 * Text Domain:   bulk-sms-sender
 * Domain Path:   /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Include your custom code here.
add_action('admin_post_save_sms_settings', 'handle_sms_sender_settings');
add_action('admin_post_nopriv_save_sms_settings', 'handle_sms_sender_settings');

function sms_settings_form() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('SMS API Integration', 'sms-sender');?></h1>
        <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="save_sms_settings">
            <?php wp_nonce_field('save_sms_settings_nonce', 'save_sms_settings_nonce'); ?>
            <div class="form-group">
                <label for="api_url">API URL:</label>
                <input type="text" id="api_url" name="api_url" class="form-control" value="<?php echo get_option('sms_sender_api_url'); ?>" required>
            </div>
            <div class="form-group">
                <label for="api_key">API Key:</label>
                <input type="text" id="api_key" name="api_key" class="form-control" value="<?php echo get_option('sms_sender_api_key'); ?>" required>
            </div>
            <div class="form-group">
                <label for="sender_id">Sender ID:</label>
                <input type="text" id="sender_id" name="sender_id" class="form-control" value="<?php echo get_option('sms_sender_sender_id'); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
    <?php
}

function handle_sms_sender_settings() {
    if (isset($_POST['save_sms_settings_nonce']) && wp_verify_nonce($_POST['save_sms_settings_nonce'], 'save_sms_settings_nonce')) {
        $api_url = sanitize_text_field($_POST['api_url']);
        $api_key = sanitize_text_field($_POST['api_key']);
        $sender_id = sanitize_text_field($_POST['sender_id']);

        // Update options
        update_option('sms_sender_api_url', $api_url);
        update_option('sms_sender_api_key', $api_key);
        update_option('sms_sender_sender_id', $sender_id);

        wp_redirect(admin_url('admin.php?page=sms-settings')); // Redirect after saving
        exit();
    }
}

$api_url = get_option('sms_sender_api_url');
$api_key = get_option('sms_sender_api_key');
$sender_id = get_option('sms_sender_sender_id');

define('BULKSMSBD_API_URL', $api_url);
define('BULKSMSBD_API_KEY', $api_key);
define('BULKSMSBD_SENDER_ID', $sender_id);
// Function to send SMS message using BulkSMSBD API
function send_sms($to, $message) {
    $url = BULKSMSBD_API_URL;
    $api_key = BULKSMSBD_API_KEY;
    $sender_id = BULKSMSBD_SENDER_ID;

    $url .= '?api_key=' . $api_key;
    $url .= '&type=text';
    $url .= '&number=' . urlencode($to);
    $url .= '&senderid=' . $sender_id;
    $url .= '&message=' . urlencode($message);

    // Send request
    $response = file_get_contents($url);

    // Check response
    if ($response) {
        // Check the response from BulkSMSBD API if needed
        // For example, you might parse the JSON response to check for success or error messages
        return true;
    } else {
        return false;
    }
}

// Function to display the SMS sending form
function sms_sender_form() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Send SMS', 'sms-sender');?></h1>
        <form method="post" action="">
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" class="form-control" rows="10" required></textarea>
            </div>
            <button type="submit" name="send_sms" class="btn btn-primary">Send SMS</button>
        </form>
    </div>
    <?php
}

// Function to handle form submission
function handle_sms_submission() {
    if (isset($_POST['send_sms'])) {
        $to = sanitize_text_field($_POST['phone_number']);
        $message = sanitize_text_field($_POST['message']);

        // Send SMS
        $result = send_sms($to, $message);

        if ($result) {
            echo '<div class="updated"><p>SMS sent successfully!</p></div>';
        } else {
            echo '<div class="error"><p>Failed to send SMS. Please try again later.</p></div>';
        }
    }
}




// Function to add menu to WordPress dashboard
function sms_sender_menu() {
    add_menu_page(
        'SMS Sender',
        'SMS Sender',
        'manage_options',
        'sms-sender',
        'sms_sender_form',
        'dashicons-email',
        30
    );
     
    // add submenu page for SMS Settings
    
    add_submenu_page(
        'sms-sender',
        'SMS Settings',
        'SMS Settings',
        'manage_options',
        'sms-settings',
        'sms_settings_form'
    );
}

// Hook functions to WordPress actions
add_action('admin_menu', 'sms_sender_menu');
add_action('admin_init', 'handle_sms_submission');
?>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
