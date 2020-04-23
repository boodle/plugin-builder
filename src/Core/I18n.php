<?php

namespace PluginBuilder\Core;

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://totalonion.com
 * @since      1.0.0
 *
 * @package    PluginBuilder
 * @subpackage PluginBuilder/Core
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    PluginBuilder
 * @subpackage PluginBuilder/Core
 * @author     Ben Broadhurst <ben@totalonion.com>
 */
class I18n
{
    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function loadPluginTextdomain()
    {
        load_plugin_textdomain(
            'plugin-builder',
            false,
            dirname(PLUGIN_BUILDER_PLUGIN_FOLDER . '/languages/')
        );
    }
}
