<?php

spl_autoload_register(function (string $className) {
    if (strpos($className, PLUGIN_BUILDER_NAMESPACE) !== 0) {
        return;
    }

    $pathParts = explode('\\', $className);
    $pathParts[0] = 'src';
    
    include PLUGIN_BUILDER_PLUGIN_FOLDER . '/' . implode('/', $pathParts) . '.php';
});
