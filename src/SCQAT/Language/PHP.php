<?php

namespace SCQAT\Language;

/**
 * This is SCQAT code language PHP
 */
class PHP extends \SCQAT\LanguageAbstract
{
    /**
     * {@inheritdoc}
     */
    public function fileNameMatcher($filename)
    {
        $match = preg_match("/(\.php)|(\.inc)$/", $filename);
        if ($match == 1) {
            return true;
        }
        return false;
    }
}
