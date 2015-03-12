<?php

namespace SCQAT;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;

/**
 * This is SCQAT configuration manager
 */
class Configuration
{
    /**
     * Read and process .scqat configuration file
     * @param  string $analyzedDirectory The directory selected for analysis
     * @return array  The processed configuration array
     */
    public static function read($analyzedDirectory)
    {
        $configFile = $analyzedDirectory.'.scqat';
        $processor = new Processor();
        $definition = new \SCQAT\Configuration\Definition();
        $parsedConfig = file_exists($configFile) ? Yaml::parse(file_get_contents($configFile)) : array();
        $configuration = $processor->processConfiguration(
            $definition,
            array($parsedConfig)
        );

        return $configuration;
    }
}
