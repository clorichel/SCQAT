<?php

namespace SCQAT;

/**
 * The contract one has to satisfy to implement a language analyzer
 */
abstract class AnalyzerAbstract
{
    /**
     * Introduce the analyzer purpose when it's being run
     * @var string Default to "Analyzing"
     */
    public static $introductionMessage = "Analyzing";

    /**
     * Determine if the analyzer needs all files, or each file one by one
     * @var boolean Default to false
     */
    public $needAllFiles = false;

    /**
     * Determine if the analyzer has a global result, or one result per analyzed files
     * @var boolean Default to false
     */
    public $hasGlobalResult = false;

    /**
     * Run an analysis on a file given the running context
     * @param  \SCQAT\Context $context          The SCQAT running context
     * @param  string         $analyzedFileName (optional) The filename to analyze (if null, analyzer do "needs all files")
     * @return \SCQAT\Result  The result of the analysis
     */
    abstract public static function analyze(\SCQAT\Context $context, $analyzedFileName = null);
}
