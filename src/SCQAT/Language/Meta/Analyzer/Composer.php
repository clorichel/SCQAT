<?php

namespace SCQAT\Language\Meta\Analyzer;

/**
 * This is SCQAT Meta > Composer analyzer
 *
 * It detects if composer.json and composer.lock are always both modified
 */
class Composer extends \SCQAT\AnalyzerAbstract
{
    /**
     * {@inheritdoc}
     */
    public static $introductionMessage = "Checking Composer configuration";

    /**
     * {@inheritdoc}
     */
    public $hasGlobalResult = true;

    /**
     * {@inheritdoc}
     */
    public $needAllFiles = true;

    /**
     * {@inheritdoc}
     */
    public static function analyze(\SCQAT\Context $context, $analyzedFileName = null)
    {
        $result = new \SCQAT\Result();

        unset($analyzedFileName); // This is for PHPMD "unused" alert
        $composerJsonDetected = false;
        $composerLockDetected = false;

        foreach ($context->files as $file) {
            if ($file === 'composer.json') {
                $composerJsonDetected = true;
            }

            if ($file === 'composer.lock') {
                $composerLockDetected = true;
            }
        }

        if ($composerJsonDetected === false && $composerLockDetected === false) {
            $result->isSuccess = true;
            $result->value = "Useless, no change";

            return $result;
        }

        if ($composerJsonDetected && !$composerLockDetected) {
            $result->isSuccess = false;
            $result->value = "Conflict";
            $result->description = "The 'composer.lock' file MUST be updated if 'composer.json' file has been";

            return $result;
        }
    }
}
