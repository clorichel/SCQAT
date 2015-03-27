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
     * Triggered as soon as a HooksAbstract is attached to SCQAT Report
     */
    abstract public function introduction();

    /**
     * Triggered as soon as the runner has run all its analysis on all files
     * @param float $duration SCQAT running duration in seconds
     */
    abstract public function ending($duration);

    /**
     * Triggered when file gathering process is initiated
     */
    abstract public function gatheringFiles();

    /**
     * Triggered when file gathering process ended with a list of files
     * @param array $filesGathered The list of files gathered
     */
    abstract public function gatheredFiles($filesGathered);

    /**
     * Triggered the first time SCQAT is using a language for an analysis
     * @param string $languageName The language name
     */
    abstract public function languageFirstUse($languageName);

    /**
     * Triggered when all language analyzers were run
     * @param string $languageName The language name
     */
    abstract public function languageEndOfUse($languageName);

    /**
     * Triggered the first time SCQAT is using an analyzer for an analysis
     * @param \SCQAT\AnalyzerAbstract $analyzer The analyzer instance
     */
    abstract public function analyzerFirstUse(\SCQAT\AnalyzerAbstract $analyzer);

    /**
     * Triggered when all files has been analyzed by an analyzer
     * @param \SCQAT\AnalyzerAbstract $analyzer The analyzer instance
     */
    abstract public function analyzerEndOfUse(\SCQAT\AnalyzerAbstract $analyzer);

    /**
     * Triggered each time a file is being analyzed (whatever analyzer is used)
     * @param string $fileName The analyzed file name
     */
    abstract public function analyzingFile($fileName);

    /**
     * Triggered at each analysis result (each file for each analyzer)
     * @param \SCQAT\Result $result The SCQAT Result instance
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
