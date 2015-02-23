<?php

namespace SCQAT\Language\PHP\Analyzer;

use Symfony\Component\Process\ProcessBuilder;

/**
 * This is SCQAT PHP > PhpCsFixer analyzer
 *
 * It ensure application of PSR-2 standard through php-cs-fixer
 */
class PhpCsFixer extends \SCQAT\AnalyzerAbstract
{
    /**
     * {@inheritdoc}
     */
    public static $introductionMessage = "PSR-2 Standard checking through php-cs-fixer";

    /**
     * {@inheritdoc}
     */
    public static function analyze(\SCQAT\Context $context, $analyzedFileName = null)
    {
        $result = new \SCQAT\Result();

        $processBuilder = new ProcessBuilder(array("php",  $context->rootDirectory."vendor/bin/php-cs-fixer", "--level=psr2", "--dry-run", "--verbose", "fix", $analyzedFileName));
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
