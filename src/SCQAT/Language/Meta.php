<?php

namespace SCQAT\Language;

/**
 * This is SCQAT code language Meta (matches all files whatever their code language)
 */
class Meta extends \SCQAT\LanguageAbstract
{
    /**
     * {@inheritdoc}
     */
    public function fileNameMatcher($filename)
    {
        return true;
    }
}
