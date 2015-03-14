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
     * The name of the last language used (needed for languageEndOfUse)
     * @var string
     */
    private $lastLanguageUsed = null;

    /**
     * The last analyzer used for a specific language (needed for analyzerEndOfUse)
     * @var \SCQAT\AnalyzerAbstract
     */
    private $lastAnalyzerUsedForLanguage = null;

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
    public function analyzerRun($fileName, \SCQAT\AnalyzerAbstract $analyzer)
    {
        $fileName = (string) $fileName;
        $languageName = $analyzer->getLanguageName();
        $analyzerName = $analyzer->getName();

        // Is it the first time this language is used ?
        if (! array_key_exists($languageName, $this->analyzersNames)) {
            if ($this->lastLanguageUsed) {
                if (count($this->analyzersNames[$this->lastLanguageUsed]) == 1) {
                    $this->runHook("analyzerEndOfUse", $this->lastAnalyzerUsedForLanguage);
                }
                $this->runHook("languageEndOfUse", $this->lastLanguageUsed);
            }
            $this->analyzersNames[$languageName] = array();
            $this->runHook("languageFirstUse", $languageName);
            $this->lastLanguageUsed = $languageName;
            $this->lastAnalyzerUsedForLanguage = null;
        }

        // Is it the first time this analyzer is used ?
        if (! in_array($analyzerName, $this->analyzersNames[$languageName])) {
            // Were an analyzer run before for this language ? If yes, it has finished analyzing files.
            if ($this->lastAnalyzerUsedForLanguage) {
                $this->runHook("analyzerEndOfUse", $this->lastAnalyzerUsedForLanguage);
            }

            $this->analyzersNames[$languageName][] = $analyzerName;
            $this->runHook("analyzerFirstUse", $analyzer);
            $this->lastAnalyzerUsedForLanguage = $analyzer;
        }

        $this->runHook("analyzingFile", $fileName);
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
        $this->runHook("analyzerResult", $result);
    }

    /**
     * Run a specific report hook on each attached report hooks
     * @param string $hookName  The hook to trigger (its method name)
     * @param mixed  $arguments One or more argument to pass to final hook methods (taken by func_get_args)
     */
    public function runHook($hookName, $arguments)
    {
        $args = func_get_args();
        $hookName = array_shift($args);
        foreach ($this->hooks as $reportHooks) {
            call_user_func_array(array($reportHooks, $hookName), $args);
        }
        if ($arguments) {
            // Just do nothing, but avoid unused parameter alert
        }
    }

    /**
     * Attach a set of report hooks to this report instance
     * @param \SCQAT\Report\HooksAbstract $reportHooks The report HooksAbstract instance
     */
    public function attachHooks(\SCQAT\Report\HooksAbstract $reportHooks)
    {
        $this->hooks[] = $reportHooks;
    }
}
