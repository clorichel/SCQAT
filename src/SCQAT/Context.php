<?php

namespace SCQAT;

/**
 * This is SCQAT Context of operations
 */
class Context
{
    /**
     * SCQAT vendor directory
     * @var string
     */
    public $vendorDirectory = null;

    /**
     * The directory selected for analysis
     * @var string
     */
    public $analyzedDirectory = null;

    /**
     * List of files names to analyze
     * @var array
     */
    public $files = array();

    /**
     * The report instance
     * @var \SCQAT\Report
     */
    public $report = null;

    /**
     * The processed configuration array
     * @var array
     */
    public $configuration = null;

    /**
     * Determine if the current run had an error
     * @var boolean
     */
    public $hadError = false;

    /**
     * List of errors that was seen
     * @var array
     */
    public $errors = array();

    /**
     * Initialize SCQAT Context
     * @param string $vendorDirectory   Full path to SCQAT vendor directory
     * @param string $analyzedDirectory The directory selected for analysis
     */
    public function __construct($vendorDirectory, $analyzedDirectory)
    {
        $this->vendorDirectory = $vendorDirectory;
        $this->analyzedDirectory = $analyzedDirectory;
        $this->report = new \SCQAT\Report($this);
        $this->configuration = \SCQAT\Configuration::read($analyzedDirectory);
    }

    /**
     * Add an error to the context
     * @param string $fileName     The filename that was analyzed
     * @param string $langageName  The language name used to analyze this file
     * @param string $analyzerName The analyzer that triggered the error
     */
    public function addError($fileName, $langageName, $analyzerName)
    {
        $this->hadError = true;
        if (empty($this->errors[$fileName])) {
            $this->errors[$fileName] = array();
        }
        $this->errors[$fileName][] = $langageName." > ".$analyzerName;
    }

    /**
     * Attach a set of report hooks to this context report instance
     * @param \SCQAT\Report\HooksAbstract $reportHooks The report HooksAbstract instance
     */
    public function attachReportHooks(\SCQAT\Report\HooksAbstract $reportHooks)
    {
        // We ensure the context is always attached to the report hook as we know we need it for the CLI
        $reportHooks->setContext($this);
        // We attach the reportHooks
        $this->report->attachHooks($reportHooks);
    }
}
