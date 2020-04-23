<?php

namespace PluginBuilder;

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://totalonion.com
 * @since             1.0.0
 * @package           PluginBuilder
 *
 * @wordpress-plugin
 * Plugin Name:       Plugin Builder
 * Plugin URI:        https://github.com/TotalOnion/plugin-builder
 * Description:       Connect to the multiverse
 * Version:           1.0.0
 * Author:            Ben Broadhurst
 * Author URI:        https://totalonion.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plugin-builder
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('PLUGIN_BUILDER_VERSION', '1.0.0');
define('PLUGIN_BUILDER_NAME', 'plugin-builder');
define('PLUGIN_BUILDER_NAMESPACE', 'PluginBuilder');
define('PLUGIN_BUILDER_PLUGIN_FOLDER', __DIR__);
define('PLUGIN_BUILDER_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoloaders
require_once PLUGIN_BUILDER_PLUGIN_FOLDER . '/autoload.php';
require_once PLUGIN_BUILDER_PLUGIN_FOLDER . '/vendor/autoload.php';

// Activate and deactivation hooks
register_activation_hook(__FILE__, ['\PluginBuilder\Core\Activator', 'activate']);
register_deactivation_hook(__FILE__, ['\PluginBuilder\Core\Deactivator', 'deactivate']);

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function pluginBuilderStart() {
    try {
        $plugin = new PluginBuilder();
        $plugin->run();
    } catch (\Exception $e) {
        print_r($e->getTrace());
    }
}
pluginBuilderStart();
