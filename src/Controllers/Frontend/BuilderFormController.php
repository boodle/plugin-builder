<?php

namespace PluginBuilder\Controllers\Frontend;

use PluginBuilder\Controllers\AbstractController;
use PluginBuilder\Services\BuilderService;

class BuilderFormController extends AbstractController
{
    public function addShortcodes()
    {
        add_shortcode('plugin-builder', [$this, 'renderPluginBuilder']);
    }

    public function processPluginForm()
    {
        $formErrors = [];

        if (isset($_POST['plugin-builder-nonce'])) {
            $formErrors = $this->validate($_POST);

            if (!$formErrors) {
                $builderService = new BuilderService(
                    $_POST['pluginName'],
                    $_POST['pluginUri'],
                    $_POST['authorName'],
                    $_POST['authorEmail'],
                    $_POST['authorUri']
                );
                $builderService->build();
            }
        }
    }

    public function renderPluginBuilder()
    {
        // We only validate the form here to show errors. The actual processing is done above (which is called on an earlier hook)
        $formErrors = [];
        if (isset($_POST['plugin-builder-nonce'])) {
            $formErrors = $this->validate($_POST);
        }

        return $this->render(
            'shortcodes:builderForm.php',
            [
                'formErrors' => $formErrors,
                'formData' => $_POST
            ]
        );
    }

    public function getFormErrors(array $formErrors, string $fieldName)
    {
        $fieldErrors = [];
        foreach ($formErrors as $formError) {
            if (
                array_key_exists('id', $formError)
                && $formError['id'] == $fieldName
            ) {
                $fieldErrors[] = $formError;
            }
        }

        return $fieldErrors;
    }

    private function validate(array $formData): array
    {
        $errors = [];

        if (!wp_verify_nonce($formData['plugin-builder-nonce'], 'plugin-builder_form-submit-action')) {
            $errors[] = [
                'message' => 'You session expired. Pleas try again.'
            ];
            return $errors;
        }

        if (!array_key_exists('pluginName', $formData)) {
            $errors[] = [
                'id' => 'pluginName',
                'message' => 'No plugin Name was specified'
            ];
        }

        if (strlen($formData['pluginName']) < 3) {
            $errors[] = [
                'id' => 'pluginName',
                'message' => 'Plugin Name is too short; at least 3 letters please.'
            ];
        }

        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9 ]{3,}$/', $formData['pluginName'])) {
            $errors[] = [
                'id' => 'pluginName',
                'message' => 'Plugin Name is invalid. It must start with a letter and contain only a-Z A-Z 0-9 and spaces'
            ];
        }

        return $errors;
    }
}
