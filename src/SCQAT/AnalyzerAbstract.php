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
     * The name of this analyzer (its class name)
     * @var string
     */
    protected $name = null;

    /**
     * The language instance of this analyzer
     * @var \SCQAT\LanguageAbstract
     */
    protected $language = null;

    /**
     * The SCQAT Context on which to run
     * @var \SCQAT\Context
     */
    protected $context = null;

    /**
     * This language specific configuration part (SCQAT.Analyzers.[LanguageName].[AnalyzerName])
     * @var array
     */
    protected $configuration = null;

    /**
     * Initialize this analyzer with SCQAT running context
     * @param \SCQAT\Context          $context  The SCQAT running context
     * @param \SCQAT\LanguageAbstract $language This analyzer language instance
     */
    public function __construct(\SCQAT\Context $context, \SCQAT\LanguageAbstract $language)
    {
        $this->context = $context;
        $this->language = $language;
        if (! empty($this->language->configuration[$this->getName()])) {
            $this->configuration = $this->language->configuration[$this->getName()];
        }
    }

    /**
     * Get the name of this analyzer (its class name)
     * @return string The analyzer name
     */
    public function getName()
    {
        if (empty($this->name)) {
            $explode = explode('\\', get_class($this));
            $this->name = array_pop($explode);
        }

        return $this->name;
    }

    /**
     * Get the name of the language of this analyzer (its language class name)
     * @return string The analyzer language name
     */
    public function getLanguageName()
    {
        return $this->language->getName();
    }

    /**
     * Run an analysis on a file
     * @param  string        $analyzedFileName (optional) The filename to analyze (if null, analyzer do "needs all files")
     * @return \SCQAT\Result The result of the analysis
     */
    abstract public function analyze($analyzedFileName = null);
}
