<?php

namespace PluginBuilder\Controllers\Admin;

use PluginBuilder\Controllers\AbstractController;

class Enqueue extends AbstractController
{
    public function enqueueStyles()
    {
        wp_enqueue_style(
            $this->pluginName,
            PLUGIN_BUILDER_PLUGIN_URL.'build/admin.css',
            [],
            $this->version,
            'all'
        );
    }

    public function enqueueScripts()
    {
        wp_enqueue_script(
            $this->pluginName,
            PLUGIN_BUILDER_PLUGIN_URL.'build/admin.js',
            [],
            $this->version,
            false
        );
    }
}
