<?php

namespace SCQAT\Language\PHP\Analyzer;

use Symfony\Component\Process\ProcessBuilder;

/**
 * This is SCQAT PHP > Lint analyzer
 *
 * It runs "php -l" to check syntax of PHP files
 */
class Lint extends \SCQAT\AnalyzerAbstract
{
    /**
     * {@inheritdoc}
     */
    public static $introductionMessage = "Checking syntax";

    /**
     * {@inheritdoc}
     */
    public static function analyze(\SCQAT\Context $context, $analyzedFileName = null)
    {
        $result = new \SCQAT\Result();

        unset($context->willNeverExistFakeVar); // This is for PHPMD "unused" alert

        $processBuilder = new ProcessBuilder(array("php", "-l", $analyzedFileName));
        $process = $processBuilder->getProcess();
        $process->run();

        if (!$process->isSuccessful()) {
            $result->isSuccess = false;
            $result->value = "KO";
            $result->description = trim($process->getOutput());
        } else {
            $result->isSuccess = true;
            $result->value = "OK";
        }

        return $result;
    }
}
