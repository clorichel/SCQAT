<?php

namespace SCQAT;

use Symfony\Component\Finder\Finder;

/**
 * This is SCQAT Runner which actually does the analyzes
 */
class Runner
{
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
    private function getLanguage($languageName)
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
    private function getAnalyzer($analyzerName, $languageName)
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
     * Do analyze a file with a specific analyzer
     * @param \SCQAT\AnalyzerAbstract $analyzer The analyzer instance to use
     * @param string                  $fileName The file name to analyze
     */
    private function analyze(\SCQAT\AnalyzerAbstract $analyzer, $fileName = null)
    {
        $this->context->report->analyzerRun($fileName, $analyzer);
        $result = $analyzer->analyze($this->context, $fileName);
        if (!$result instanceof \SCQAT\Result) {
            $result = new \SCQAT\Result();
            $result->isSuccess = false;
            $result->value = "Wrong result !";
            $result->description = "The analyzer did not returned a \\SCQAT\\Result instance";
        }
        $result->languageName = $analyzer->getLanguageName();
        $result->analyzerName = $analyzer->getName();
        if (!empty($fileName)) {
            $result->fileName = $fileName;
        }
        $this->context->report->analyzerResult($result);
    }

    /**
     * Starting SCQAT runner
     * @param \SCQAT\Context $context The SCQAT Context on which to run
     */
    public function run(\SCQAT\Context $context)
    {
        $started = microtime(true);
        $this->context = $context;
        $this->gatherLanguages();

        foreach ($this->languagesNames as $languageName) {
            $language = $this->getLanguage($languageName);
            $this->analyzersNames[$languageName] = $language::getAnalyzersNames();
            foreach ($this->analyzersNames[$languageName] as $analyzerName) {
                $analyzer = $this->getAnalyzer($analyzerName, $languageName);
                if (!empty($analyzer->needAllFiles) && $analyzer->needAllFiles === true) {
                    $this->analyze($analyzer);
                } else {
                    foreach ($this->context->files as $fileName) {
                        if ($language->fileNameMatcher($fileName) === false) {
                            continue;
                        }
                        $this->analyze($analyzer, $fileName);
                    }
                }
            }
        }

        $this->duration = (microtime(true) - $started);
    }
}
