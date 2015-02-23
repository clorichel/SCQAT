<?php

namespace SCQAT;

/**
 * The contract one has to satisfy to implement a code language
 */
abstract class LanguageAbstract
{
    /**
     * Determine if a file should be handled by the language depending on its name
     * @param  string  $filename The file name to be checked
     * @return boolean
     */
    abstract public function fileNameMatcher($filename);

    /**
     * Get the list of analyzers implemented for a specific language
     * @return array List of "raw" analyzers names (without starging namespace nor ending .php)
     */
    public static function getAnalyzersNames()
    {
        // TODO this is funny and works but not that smart... change that
        // Scanning each "languagename"/Analyzers subdirectory
        $folder = dirname(__FILE__).str_replace("\\", DIRECTORY_SEPARATOR, ltrim(get_called_class(), "SCQAT")).DIRECTORY_SEPARATOR."Analyzer";
        $analyzersNames = array();
        if (file_exists($folder)) {
            $finder = new \Symfony\Component\Finder\Finder();
            $finder->files()->name('*.php')->in($folder)->depth("== 0");
            if (count($finder) > 0) {
                foreach ($finder as $file) {
                    $analyzersNames[] = $file->getBasename(".php");
                }
            }
        }

        return $analyzersNames;
    }
}
