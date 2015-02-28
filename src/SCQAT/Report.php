<?php

namespace SCQAT;

/**
 * This is SCQAT report for current run
 */
class Report
{
    /**
     * List of analyzers that was ran, categorized in a "languageName" array key
     * @var array
     */
    private $analyzersNames = array();

    /**
     * List of hooks, categorized by their "hookName" array key
     * @var array
     */
    private $hooks = array();

    /**
     * The SCQAT Context instance
     * @var \SCQAT\Context
     */
    private $context;

    /**
     * Initialize report with the running context
     * @param \SCQAT\Context $context The SCQAT running context
     */
    public function __construct(\SCQAT\Context $context)
    {
        $this->context = $context;
    }

    /**
     * Report running an analyzer
     * @param string                  $fileName The filename on which the analyzer is running
     * @param \SCQAT\AnalyzerAbstract $analyzer The analyzer instance
     */
    public function analyzerRun($fileName, $analyzer)
    {
        $fileName = (string) $fileName;
        $languageName = $analyzer->getLanguageName();
        $analyzerName = $analyzer->getName();

        // Is it the first time this language is used ?
        if (! array_key_exists($languageName, $this->analyzersNames)) {
            $this->analyzersNames[$languageName] = array();
            $this->runHook("Language_First_Use", $languageName);
        }

        // Is it the first time this analyzer is used ?
        if (! in_array($analyzerName, $this->analyzersNames[$languageName])) {
            $this->analyzersNames[$languageName][] = $analyzerName;
            $this->runHook("Analyzer_First_Use", $analyzer);
        }

        $this->runHook("Analyzing_File", $fileName);
    }

    /**
     * Report an analyzer result
     * @param \SCQAT\Result $result The SCQAT run result
     */
    public function analyzerResult(\SCQAT\Result $result)
    {
        if ($result->isSuccess === false) {
            $this->context->hadError = true;
        }
        $this->runHook("Analyzer_Result", $result);
    }

    /**
     * Add a report hook
     * @param string   $hookName The hook name
     * @param \Closure $function The function to run when this hook is triggered
     */
    public function addHook($hookName, \Closure $function)
    {
        if (empty($this->hooks[$hookName])) {
            $this->hooks[$hookName] = array();
        }
        $this->hooks[$hookName][] = $function;
    }

    /**
     * Run a report hook (ensuring it has been configured)
     */
    public function runHook()
    {
        $args = func_get_args();
        $hookName = array_shift($args);
        if (!empty($this->hooks[$hookName])) {
            foreach ($this->hooks[$hookName] as $hookFunction) {
                call_user_func_array($hookFunction, $args);
            }
        }
    }
}
