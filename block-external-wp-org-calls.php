<?php
/**
 * Plugin Name: Skip WP ORG API Calls
 * Description: Block Matt, Eloquently
 * Author:      Andrew Engstrom
 * License:     GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Basic security, prevents file from being loaded directly.
defined( 'ABSPATH' ) or die( 'Not today!' );

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

/* Function to filter HTTP Request args for plugins making a wp_remote_post request
 * Basically, we can't hook or filter wp_remote_* functions, so we filter the args
 * in the HTTP object instead with this filter.
 */
function filter_http_request_args($args, $url) {

    // James bond this request
    if (strpos($url, 'wordpress.org') != false || strpos($url, 'woocommerce.com') != false) {
        die("Another day!");
    }

    return $args;
}
add_filter('http_request_args', 'filter_http_request_args', 10, 2);
