<?php

namespace SCQAT;

use Symfony\Component\Finder\Finder;

/**
 * This is SCQAT Runner which actually does the analyzes
 */
class Runner
{
    /**
     * The SCQAT Context on which to run
     * @var \SCQAT\Context
     */
    private $context = null;

    /**
     * Whitelisted languages names
     * @var array
     */
    private $languagesWhitelist = array();

    /**
     * Blacklisted languages names
     * @var array
     */
    private $languagesBlacklist = array();

    /**
     * Whitelisted analyzers names (LanguageName > AnalyzerName)
     * @var array
     */
    private $analyzersWhitelist = array();

    /**
     * Blacklisted analyzers names (LanguageName > AnalyzerName)
     * @var array
     */
    private $analyzersBlacklist = array();

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
            $this->languagesInstances[$languageName] = new $className($this->context);
        }

        return $this->languagesInstances[$languageName];
    }

    /**
     * Getting a language analyzer class instance, ensuring one instance per analyzer
     * @param  string                  $analyzerName The wanted analyzer name
     * @param  \SCQAT\LanguageAbstract $language     The language class instance
     * @return \SCQAT\AnalyzerAbstract The analyzer class instance
     */
    private function getAnalyzer($analyzerName, \SCQAT\LanguageAbstract $language)
    {
        $languageName = $language->getName();
        // Make a new instance if not already made
        if (empty($this->analyzersInstances[$languageName][$analyzerName])) {
            $className = "\\SCQAT\\Language\\".$languageName."\\Analyzer\\".$analyzerName;
            // Do ensure $className is an analyzer class
            if (!is_subclass_of($className, '\\SCQAT\\AnalyzerAbstract')) {
                return false;
            }
            $this->analyzersInstances[$languageName][$analyzerName] = new $className($this->context, $language);
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
        $result = $analyzer->analyze($fileName);
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
        $this->setWhiteAndBlacklists();

        foreach ($this->languagesNames as $languageName) {
            if (!empty($this->languagesWhitelist) && ! in_array($languageName, $this->languagesWhitelist)) {
                continue;
            }
            if (in_array($languageName, $this->languagesBlacklist) && ! in_array($languageName, $this->languagesWhitelist)) {
                continue;
            }
            $analyzer = $this->runLanguage($languageName);
        }

        if (!empty($analyzer)) {
            $this->context->report->runHook("analyzerEndOfUse", $analyzer);
        }
        if (!empty($languageName)) {
            $this->context->report->runHook("languageEndOfUse", $languageName);
        }

        $this->context->report->runHook("ending", (microtime(true) - $started));
    }

    /**
     * Run analyzers for this specific language name
     * @param  string                  $languageName The language name
     * @return \SCQAT\AnalyzerAbstract The last used analyzer instance (needed to trigger the last analyzerEndOfUse hook)
     */
    private function runLanguage($languageName)
    {
        $language = $this->getLanguage($languageName);
        $this->analyzersNames[$languageName] = $language::getAnalyzersNames();
        foreach ($this->analyzersNames[$languageName] as $analyzerName) {
            if (!empty($this->analyzersWhitelist) && ! in_array($languageName." > ".$analyzerName, $this->analyzersWhitelist)) {
                continue;
            }
            if (in_array($languageName." > ".$analyzerName, $this->analyzersBlacklist) && ! in_array($languageName." > ".$analyzerName, $this->analyzersWhitelist)) {
                continue;
            }
            $analyzer = $this->runAnalyzer($analyzerName, $language);
        }

        return empty($analyzer) ? null : $analyzer;
    }

    /**
     * Run a specific analyzer by its name
     * @param  string                  $analyzerName The analyzer name
     * @param  \SCQAT\LanguageAbstract $language     The language class instance
     * @return \SCQAT\AnalyzerAbstract The analyzer class instance
     */
    private function runAnalyzer($analyzerName, \SCQAT\LanguageAbstract $language)
    {
        $analyzer = $this->getAnalyzer($analyzerName, $language);
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

        return $analyzer;
    }

    /**
     * Popuplate languages and analyzers white and black lists
     */
    private function setWhiteAndBlacklists()
    {
        $config = $this->context->configuration;

        if (!empty($config["Analysis"]["Languages"]["except"])) {
            $this->languagesBlacklist = $config["Analysis"]["Languages"]["except"];
        }

        if (!empty($config["Analysis"]["Languages"]["only"])) {
            $this->languagesWhitelist = $config["Analysis"]["Languages"]["only"];
        }

        if (!empty($config["Analysis"]["Analyzers"]["except"])) {
            $this->analyzersBlacklist = $config["Analysis"]["Analyzers"]["except"];
        }

        if (!empty($config["Analysis"]["Analyzers"]["only"])) {
            $this->analyzersWhitelist = $config["Analysis"]["Analyzers"]["only"];
        }
    }
}
