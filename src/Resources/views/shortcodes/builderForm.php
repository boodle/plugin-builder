<section id="plugin-builder">
    <form method="post">
        <?php 
            wp_nonce_field('plugin-builder_form-submit-action', 'plugin-builder-nonce');
            echo $this->render(
                'forms:fields/text.php',
                [
                    'id' => 'pluginName',
                    'name' => 'pluginName',
                    'fieldValue' => $formData['pluginName'],
                    'label' => 'Plugin name',
                    'helpText' => 'Something like "My Super Plugin".',
                    'required' => true,
                    'errors' => $this->getFormErrors($formErrors, 'pluginName')
                ]
            );

            echo $this->render(
                'forms:fields/url.php',
                [
                    'id' => 'pluginUri',
                    'name' => 'pluginUri',
                    'fieldValue' => $formData['pluginUri'],
                    'label' => 'Plugin URI',
                    'helpText' => 'Example "https://github.com/boodle/modern-wp-plugin-boilerplate".',
                    'required' => true,
                    'errors' => $this->getFormErrors($formErrors, 'pluginUri')
                ]
            );

            echo $this->render(
                'forms:fields/text.php',
                [
                    'id' => 'authorName',
                    'name' => 'authorName',
                    'fieldValue' => $formData['authorName'],
                    'label' => 'Author',
                    'helpText' => 'Something like "Tester McTesterson".',
                    'required' => true,
                    'errors' => $this->getFormErrors($formErrors, 'authorName')
                ]
            );

            echo $this->render(
                'forms:fields/email.php',
                [
                    'id' => 'authorEmail',
                    'name' => 'authorEmail',
                    'fieldValue' => $formData['authorEmail'],
                    'label' => 'Author Email',
                    'helpText' => 'Something like "Tester.McTesterson@testltd.com".',
                    'required' => true,
                    'errors' => $this->getFormErrors($formErrors, 'authorEmail')
                ]
            );

            echo $this->render(
                'forms:fields/url.php',
                [
                    'id' => 'authorUri',
                    'name' => 'authorUri',
                    'fieldValue' => $formData['authorUri'],
                    'label' => 'Author URI',
                    'helpText' => 'Example "https://totalonion.com".',
                    'required' => true,
                    'errors' => $this->getFormErrors($formErrors, 'authorUri')
                ]
            );
        ?>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</section>