<?php

namespace PluginBuilder\Services;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class BuilderService
{
    /**
     * What to search and replace in the files; this gets updated in the generateNames method
     * @var array
     */
    private $searchAndReplace = [
        'Ben Broadhurst' => 'AUTHOR NAME HERE',
        'ben@totalonion.com' => 'AUTHOR EMAIL HERE',
        'https://totalonion.com' => 'AUTHOR URI HERE',
        'https://github.com/boodle/modern-wp-plugin-boilerplate' => 'PLUGIN URI HERE',
        'ModernWpPluginBoilerplate' => 'ModernWpPluginBoilerplate',
        'modern-wp-plugin-boilerplate' => 'modern-wp-plugin-boilerplate',
        'MODERN_WP_PLUGIN_BOILERPLATE' => 'MODERN_WP_PLUGIN_BOILERPLATE',
        'modernWpPluginBoilerplate' => 'modernWpPluginBoilerplate',
        'Modern WP Plugin Boilerplate' => 'Modern WP Plugin Boilerplate'
    ];

    public function __construct(
        string $pluginName,
        string $pluginUri,
        string $authorName,
        string $authorEmail,
        string $authorUri
    )
    {
        $this->pluginName = $pluginName;
        $this->pluginUri = $pluginUri;
        $this->authorName = $authorName;
        $this->authorEmail = $authorEmail;
        $this->authorUri = $authorUri;
    }

    public function downloadSource()
    {
        $curlHandle = curl_init('https://codeload.github.com/boodle/modern-wp-plugin-boilerplate/zip/master');
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curlHandle);
        file_put_contents(
            PLUGIN_BUILDER_PLUGIN_FOLDER.'/buildSrc/master.zip',
            $response
        );
    }

    public function build() {
        try {
            $names = $this->generateNames();
            $buildId = md5(openssl_random_pseudo_bytes(20));

            // Extract a new copy
            if (!$this->extractPluginCopy($buildId)) {
                // TODO: stop here and report error
                echo 'FAILED TO EXTRACT';
                return;
            }

            // Rename the files that have to be
            if (!$this->renamePluginFiles($buildId, $names)) {
                // TODO: stop here and report error
                echo 'FAILED TO RENAME FILES';
                return;
            }

            // Start in the top folder and recurse
            $this->updateAllFiles(PLUGIN_BUILDER_PLUGIN_FOLDER.'/buildDst/'.$buildId.'/'.$names['textDomain']);

            // zip it and download it
            if (!$this->returnZipFile($buildId, $names['textDomain'])) {
                // TODO: stop here and report error
                echo ' FAILED TO ZIP RESULTING PLUGIN';
                return;
            }

            $this->returnZipToUser(PLUGIN_BUILDER_PLUGIN_FOLDER.'/buildDst/'.$buildId.'/'.$names['textDomain'].'.zip');
        } catch (\Exception $e) {
            // TODO; currently just re-throw it
            throw $e;
        } finally {
            // delete the work files
        }
    }

    private function cleanup($buildId)
    {
        // TODO: delete the work files
    }

    private function updateAllFiles(string $directoryName)
    {
        if (!is_dir($directoryName)) {
            throw new \Exception(sprintf('Directory "%s" not found, or is not a directory.', $directoryName));
        }

        $directoryHandle = opendir($directoryName);
        if (!$directoryHandle) {
            throw new \Exception(sprintf('Directory "%s" could not be open, but exists, check permissions.', $directoryName));
        }

        while (($file = readdir($directoryHandle)) !== false) {
            if (in_array($file, ['.','..'])) {
                continue;
            }
            
            $fullFilename = $directoryName.'/'.$file;
            if (filetype($fullFilename) == 'dir') {
                $this->updateAllFiles($fullFilename);
            } else {
                $this->updateSingleFile($fullFilename);
            }
        }
        
        closedir($directoryHandle);
    }

    private function updateSingleFile(string $filename): bool
    {
        $contents = file_get_contents($filename);
        if (!$contents) {
            return false;
        }

        foreach ($this->searchAndReplace as $search => $replace) {
            $contents = str_replace($search, $replace, $contents);
        }

        if (!file_put_contents($filename, $contents)) {
            return false;
        }

        return true;
    }

    private function extractPluginCopy(string $buildId): bool
    {
        $zip = new \ZipArchive;
        if ($zip->open(PLUGIN_BUILDER_PLUGIN_FOLDER.'/buildSrc/master.zip') === true) {
            $zip->extractTo(PLUGIN_BUILDER_PLUGIN_FOLDER.'/buildDst/'.$buildId.'/');
            $zip->close();
            return true;
        }

        return false;
    }

    private function renamePluginFiles(string $buildId, array $names): bool
    {
        $renameFiles = [
            [
                'from' => '/modern-wp-plugin-boilerplate-master/src/ModernWpPluginBoilerplate.php',
                'to'   => '/modern-wp-plugin-boilerplate-master/src/'.$names['namespace'].'.php'
            ],
            [
                'from' => '/modern-wp-plugin-boilerplate-master/modern-wp-plugin-boilerplate.php',
                'to'   => '/modern-wp-plugin-boilerplate-master/'.$names['textDomain'].'.php'
            ],
            [
                'from' => '/modern-wp-plugin-boilerplate-master/languages/modern-wp-plugin-boilerplate.pot',
                'to'   => '/modern-wp-plugin-boilerplate-master/languages/'.$names['textDomain'].'.pot'
            ],
            [
                'from' => '/modern-wp-plugin-boilerplate-master',
                'to' => '/'.$names['textDomain']
            ],
        ];

        foreach ($renameFiles as $renameFile) {
            if (
                !rename(
                    PLUGIN_BUILDER_PLUGIN_FOLDER.'/buildDst/'.$buildId.$renameFile['from'],
                    PLUGIN_BUILDER_PLUGIN_FOLDER.'/buildDst/'.$buildId.$renameFile['to']
                )
            ) {
                return false;
            }
        }

        return true;
    }

    private function generateNames(): array
    {
        $lowerElements = preg_split('/[ ]{1,}/', strtolower($this->pluginName));
        $upperElements = preg_split('/[ ]{1,}/', strtoupper($this->pluginName));
        $titleElements = preg_split('/[ ]{1,}/', strtolower($this->pluginName));

        array_walk(
            $titleElements,
            function(&$element) {
                $element = ucfirst($element);
            }
        );
        
        $names = [
            'name' => $this->pluginName,
            'pluginUri' => $this->pluginUri,
            'authorName' => $this->authorName,
            'authorEmail' => $this->authorEmail,
            'authorUri' => $this->authorUri,
            'namespace' => implode('', $titleElements),
            'textDomain' => implode('-', $lowerElements),
            'constants' => implode('_', $upperElements),
            'functionNames' => preg_replace_callback(
                '/^([A-Z]{1})/',
                function ($matches) {
                    return strtolower($matches[1]);
                },
                implode('', $titleElements)
            )
        ];

        $this->searchAndReplace = [
            'Ben Broadhurst' => $names['authorName'],
            'ben@totalonion.com' => $names['authorEmail'],
            'https://totalonion.com' => $names['authorUri'],
            'https://github.com/boodle/modern-wp-plugin-boilerplate' => $names['pluginUri'],
            'ModernWpPluginBoilerplate' => $names['namespace'],
            'modern-wp-plugin-boilerplate' => $names['textDomain'],
            'MODERN_WP_PLUGIN_BOILERPLATE' => $names['constants'],
            'modernWpPluginBoilerplate' => $names['functionNames'],
            'Modern WP Plugin Boilerplate' => $names['name'],

        ];

        return $names;
    }

    private function returnZipFile(string $buildId, string $pluginFilename)
    {
        $zipFilename = PLUGIN_BUILDER_PLUGIN_FOLDER.'/buildDst/'.$buildId.'/'.$pluginFilename.'.zip';
        $folderName = PLUGIN_BUILDER_PLUGIN_FOLDER.'/buildDst/'.$buildId.'/'.$pluginFilename;

        return $this->zipData($folderName, $zipFilename);
    }

    private function zipData(string $source, string $destination): bool
    {
        if (
            extension_loaded('zip') === true
            && file_exists($source) === true
        ) {
            $zip = new ZipArchive();

            if ($zip->open($destination, ZIPARCHIVE::CREATE) === true) {
                $source = realpath($source);

                if (is_dir($source) === true) {
                    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

                    foreach ($files as $file) {
                        $file = realpath($file);

                        if (is_dir($file) === true) {
                            $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                        } else if (is_file($file) === true) {
                            $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                        }
                    }
                } else if (is_file($source) === true) {
                    $zip->addFromString(basename($source), file_get_contents($source));
                }
            }

            return $zip->close();
        }

        return false;
    }

    private function returnZipToUser(string $zipUri)
    {
        $file_name = basename($zipUri);

        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Content-Length: " . filesize($zipUri));

        readfile($zipUri);
        exit();
    }
}
