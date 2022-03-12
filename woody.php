<?php

/**
 * Plugin Name: Woody plugin
 * Plugin URI: https://github.com/woody-wordpress-pro/woody-plugin
 * Description: This plugin adds all the features of Woody Addons
 * Author: Raccourci Agency
 * Author URI: https://www.raccourci.fr
 * License: GPL2
 *
 * This program is GLP but; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of.
 */

defined('ABSPATH') or die('Cheatin&#8217; uh?');

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('PLUGIN_NAME_VERSION', '1.43.1');
define('PLUGIN_WOODY_ROOT', __FILE__);
define('PLUGIN_WOODY_DIR_ROOT', dirname(PLUGIN_WOODY_ROOT));
define('PLUGIN_WOODY_ENV', WP_ENV);

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
if (!isset($plugin_woody_kernel)) {
    $plugin_woody_kernel = new \Woody\App\Kernel(PLUGIN_WOODY_ENV);
}
