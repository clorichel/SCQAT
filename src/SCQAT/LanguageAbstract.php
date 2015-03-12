<?php

namespace SCQAT;

use Symfony\Component\Finder\Finder;

/**
 * The contract one has to satisfy to implement a code language
 */
abstract class LanguageAbstract
{
    /**
     * This language specific configuration part (SCQAT.Analyzers.[LanguageName])
     * @var array
     */
    public $configuration = null;

    /**
     * The name of this language (its class name)
     * @var string
     */
    protected $name = null;

    /**
     * The SCQAT Context on which to run
     * @var \SCQAT\Context
     */
    protected $context = null;

    /**
     * Initialize this language with SCQAT running context
     * @param \SCQAT\Context          $context  The SCQAT running context
     */
    public function __construct(\SCQAT\Context $context)
    {
        $this->context = $context;
        if (! empty($this->context->configuration["Analyzers"][$this->getName()])) {
            $this->configuration = $this->context->configuration["Analyzers"][$this->getName()];
        }
    }

    /**
     * Get the name of this language (its class name)
     * @return string The language name
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
     * Get the list of analyzers implemented for a specific language
     * @return array List of "raw" analyzers names (without starging namespace nor ending .php)
     */
    public static function getAnalyzersNames()
    {
        // Scanning each "languagename"/Analyzers subdirectory
        $folder = dirname(__FILE__).str_replace("\\", DIRECTORY_SEPARATOR, ltrim(get_called_class(), "SCQAT")).DIRECTORY_SEPARATOR."Analyzer";
        $analyzersNames = array();
        if (file_exists($folder)) {
            $finder = new Finder();
            $finder->files()->name('*.php')->in($folder)->depth("== 0");
            if (count($finder) > 0) {
                foreach ($finder as $file) {
                    $analyzersNames[] = $file->getBasename(".php");
                }
            }
        }

        return $analyzersNames;
    }

    /**
     * Determine if a file should be handled by the language depending on its name
     * @param  string  $filename The file name to be checked
     * @return boolean
     */
    abstract public function fileNameMatcher($filename);
}
