<?php

namespace SCQAT;

use Symfony\Component\Finder\Finder;

/**
 * This is SCQAT Runner which actually does the analyzes
 */
class Runner
{
    /**
     * Runner "initialization date" unix timestamp with microseconds
     * @var float
     */
    private $initialized = null;

    /**
     * Runner analysis duration in s
     * @var float
     */
    public $duration = null;

    /**
     * The SCQAT Context on which to run
     * @var \SCQAT\Context
     */
    private $context = null;

    /**
     * List of SCQAT implemented (known) languages names
     * @var array
     */
    private $languagesNames = array();

    /**
     * List of SCQAT languages instances
     * @var array
     */
    private $languagesInstances = array();

    /**
     * List of SCQAT implemented (known) analyzers names (categorized by their "languageName" key)
     * @var array
     */
    private $analyzersNames = array();

    /**
     * List of SCQAT analyzers instances (categorized by their "languageName" key)
     * @var array
     */
    private $analyzersInstances = array();

    /**
     * Initializing
     */
    public function __construct()
    {
        $this->initialized = microtime(true);
    }

    /**
     * Gathering each implemented (known) languages names
     */
    private function gatherLanguages()
    {
        $folder = dirname(__FILE__).DIRECTORY_SEPARATOR."Language";
        if (file_exists($folder)) {
            $finder = new Finder();
            $finder->directories()->in($folder)->depth("== 0");
            if (count($finder) > 0) {
                foreach ($finder as $directory) {
                    $languageName = $directory->getFileName();
                    $this->languagesNames[] = $languageName;
                }
            }
        }
    }

    /**
     * Getting a language class instance, ensuring one instance per language
     * @param  string                  $languageName The wanted language name
     * @return \SCQAT\LanguageAbstract The language class instance
     */
    private function getLanguageClass($languageName)
    {
        // Make a new instance if not already made
        if (empty($this->languagesInstances[$languageName])) {
            $className = "\\SCQAT\\Language\\".$languageName;
            // Do ensure $className is a language class
            if (!is_subclass_of($className, '\\SCQAT\\LanguageAbstract')) {
                return false;
            }
            $this->languagesInstances[$languageName] = new $className();
        }

        return $this->languagesInstances[$languageName];
    }

    /**
     * Getting a language analyzer class instance, ensuring one instance per analyzer
     * @param  string                  $analyzerName The wanted analyzer name
     * @param  string                  $languageName The wanted analyzer language name
     * @return \SCQAT\LanguageAbstract The language class instance
     */
    private function getAnalyzerClass($analyzerName, $languageName)
    {
        // Make a new instance if not already made
        if (empty($this->analyzersInstances[$languageName][$analyzerName])) {
            $className = "\\SCQAT\\Language\\".$languageName."\\Analyzer\\".$analyzerName;
            // Do ensure $className is an analyzer class
            if (!is_subclass_of($className, '\\SCQAT\\AnalyzerAbstract')) {
                return false;
            }
            $this->analyzersInstances[$languageName][$analyzerName] = new $className();
        }

        return $this->analyzersInstances[$languageName][$analyzerName];
    }

    /**
     * Starting SCQAT runner
     * @param \SCQAT\Context $context The SCQAT Context on which to run
     */
    public function run(\SCQAT\Context $context)
    {
        $this->context = $context;
        $this->gatherLanguages();

        foreach ($this->languagesNames as $languageName) {
            $languageClass = $this->getLanguageClass($languageName);
            $this->analyzersNames[$languageName] = $languageClass::getAnalyzersNames();
            foreach ($this->analyzersNames[$languageName] as $analyzerName) {
                $analyzerClass = $this->getAnalyzerClass($analyzerName, $languageName);
                if (!empty($analyzerClass->needAllFiles) && $analyzerClass->needAllFiles === true) {
                    $context->report->analyzerRun("", $analyzerName, $languageName, $analyzerClass);
                    $result = $analyzerClass->analyze($context);
                    if (!$result instanceof \SCQAT\Result) {
                        $result = new \SCQAT\Result();
                        $result->isSuccess = false;
                        $result->value = "Wrong result !";
                        $result->description = "The analyzer did not returned a \\SCQAT\\Result instance";
                    }
                    $result->languageName = $languageName;
                    $result->analyzerName = $analyzerName;
                    $context->report->analyzerResult($result);
                } else {
                    foreach ($this->context->files as $fileName) {
                        if ($languageClass->fileNameMatcher($fileName) === false) {
                            continue;
                        }
                        $context->report->analyzerRun($fileName, $analyzerName, $languageName, $analyzerClass);
                        $result = $analyzerClass->analyze($context, $fileName);
                        if (!$result instanceof \SCQAT\Result) {
                            $result = new \SCQAT\Result();
                            $result->isSuccess = false;
                            $result->value = "Wrong result !";
                            $result->description = "The analyzer did not returned a \\SCQAT\\Result instance";
                        }
                        $result->languageName = $languageName;
                        $result->analyzerName = $analyzerName;
                        $result->fileName = $fileName;
                        $context->report->analyzerResult($result);
                    }
                }
            }
        }

        $this->duration = (microtime(true) - $this->initialized);
    }
}
