<?php

namespace SCQAT;

/**
 * This is SCQAT Context of operations
 */
class Context
{
    /**
     * SCQAT root directory
     * @var string
     */
    public $rootDirectory = null;

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
     */
    public function __construct($rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
        $this->report = new \SCQAT\Report($this);
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
}
