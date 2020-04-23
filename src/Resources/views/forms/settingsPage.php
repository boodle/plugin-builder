<div class="wrap">
    <h1>My Settings</h1>
    <form method="post" action="options.php">
        <?php
            settings_fields(PLUGIN_BUILDER_NAME.'_options');
            do_settings_sections(PLUGIN_BUILDER_NAME.'settings-page');
            submit_button();
        ?>
    </form>
</div>