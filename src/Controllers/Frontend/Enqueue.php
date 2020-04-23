<?php

namespace PluginBuilder\Controllers\Frontend;

use PluginBuilder\Controllers\AbstractController;

class Enqueue extends AbstractController
{
    public function enqueueStyles()
    {
        wp_enqueue_style(
            $this->pluginName,
            PLUGIN_BUILDER_PLUGIN_URL.'build/public.css',
            [],
            $this->version,
            'all'
        );
    }

    public function enqueueScripts()
    {
        wp_enqueue_script(
            $this->pluginName,
            PLUGIN_BUILDER_PLUGIN_URL.'build/public.js',
            [],
            $this->version,
            false
        );
    }
}
