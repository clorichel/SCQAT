<?php

namespace SCQAT\Report;

/**
 * This is SCQAT report for current run
 */
abstract class HooksAbstract
{
    /**
     * The SCQAT Context of operations
     * @var \SCQAT\Context
     */
    public $context = null;

    /**
     * Triggered the first time SCQAT is using a language for an analysis
     * @param  string $languageName The language name
     */
    abstract public function languageFirstUse($languageName);

    /**
     * Triggered the first time SCQAT is using an analyzer for an analysis
     * @param  \SCQAT\AnalyzerAbstract $analyzer The analyzer instance
     */
    abstract public function analyzerFirstUse(\SCQAT\AnalyzerAbstract $analyzer);

    /**
     * Triggered each time a file is being analyzed (whatever analyzer is used)
     * @param  string $fileName The analyzed file name
     */
    abstract public function analyzingFile($fileName);

    /**
     * Triggered at each analysis result (each file for each analyzer)
     * @param  \SCQAT\Result $result The SCQAT Result instance
     */
    abstract public function analyzerResult(\SCQAT\Result $result);

    /**
     * Context could be needed by hooks
     * @param \SCQAT\Context $context The SCQAT context of operations
     */
    final public function setContext(\SCQAT\Context $context)
    {
        $this->context = $context;
    }
}
