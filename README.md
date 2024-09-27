# Plugin Name: Skip WP ORG API Calls
> Description: Block Matt, Eloquently
> Author:      Andrew Engstrom
> License:     GNU General Public License v3 or later
> License URI: http://www.gnu.org/licenses/gpl-3.0.html

Skip calls to wordpress.org, block Matt, eloquently.
This file is intended to be added as an mu-plugin.

It is only meant to function as a temporary block, to allow
websites to function while the ongoing feud between WPE and WP
exists, and may or may not need to be tweaked to support your
website's individual needs.

## Functions!

### disable_wp_org_endpoints
This function filters pre_http_request to block specific URLs from being hit by your WP site.

### filter_http_request_args
This function filters the request args, to essentially do the same. It is a stopgap measure
since we realized that wp_remote_post functions do not get affected by the above function that
operates on the pre_http_request hook.

### add_admin_warning
The warning function is strictly to provide a user-friendly UI response if a request is blocked
in the WordPress Administrator / backend.
