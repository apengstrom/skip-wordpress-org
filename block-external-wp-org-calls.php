<?php
/**
 * Plugin Name: Skip WP ORG API Calls
 * Plugin URI: https://github.com/apengstrom/skip-wordpress-org
 * Description: Block Matt, Eloquently
 * Version: 1.0.0
 * Author: Andrew Engstrom
 * Author URI: https://www.curiousm.com/
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

/*
 * Basic security, prevents file from being loaded directly.
 */
defined( 'ABSPATH' ) or die( 'Not today!' );

/*
 * This function is strictly to provide a friendly user warning in the WP Administrator.
 */
function add_admin_warning(){
    ?>

    <div class="error">
        <h1>Skip WP ORG API Calls</h1>
        <p>
            <strong>WARNING:</strong> This message means a call to WordPress.org related services was blocked, and some functionality might not work on your site whilst this plugin is enabled. <strong>This plugin is only meant as a temporary workaround.</strong> If you have any problems using this plugin, or have any feedback, please create an issue on our GitHub repository <a href="https://github.com/apengstrom/skip-wordpress-org" target="_blank">here</a>. We will try to reply as soon as possible.
        </p>
    </div>

    <?php
}

/*
 * This function will block via pre_http_request, the majority of wordpress.org
 * related functionality, as well as woocommerce.com
 */
function disable_wp_org_endpoints($pre, $args, $url) {
    // Block woocommerce.com calls
    if (strpos($url, 'woocommerce.com') !== false) {
        return array(
            'body' => json_encode(array()),
            'response' => array('code' => 200),
        );
    }

    // Block api.wordpress.org Version Check calls
    if (strpos($url, 'api.wordpress.org/core/version-check') !== false) {
        return array(
            'body' => json_encode(array(
                'offers' => array(
                    array('version' => get_bloginfo('version')) // Return the current WordPress version
                )
            )),
            'response' => array('code' => 200),
        );
    }

    // Block api.wordpress.org Calls if not Version Checking
    if (strpos($url, 'api.wordpress.org') !== false) {
        return array(
            'body' => json_encode(array()),
            'response' => array('code' => 200),
        );
    }

    // We don't want to "feed" the beast
    if (strpos($url, 'wordpress.org') !== false) {
        return array(
            'body' => '<rss></rss>',
            'response' => array('code' => 200),
        );
    }

    return $pre;

}
add_filter('pre_http_request', 'disable_wp_org_endpoints', 10, 3);

/*
 * Function to filter HTTP Request args for plugins making a wp_remote_post request
 * Basically, we can't hook or filter wp_remote_* functions, so we filter the args
 * in the HTTP object instead with this filter.
 */
function filter_http_request_args($args, $url) {

    // Filter the URL
    if (strpos($url, 'wordpress.org') != false || strpos($url, 'woocommerce.com') != false) {

        $current_screen = '';
        if ( function_exists( 'get_current_screen' ) ) {
            $current_screen = get_current_screen();
        }

        // James bond this request if it's ajax
        if ((defined('DOING_AJAX') && DOING_AJAX)
            || $current_screen->id != 'plugins'
            || $current_screen->id != 'update-core') {
            die("Another day!");
        } else {
            // We are likely hitting this on something like plugins.php, so let's show a friendly message.
            add_action('admin_notices','add_admin_warning');
            return false;
        }
    }

    return $args;
}
add_filter('http_request_args', 'filter_http_request_args', 10, 2);
