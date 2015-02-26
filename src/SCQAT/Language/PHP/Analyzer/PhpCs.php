<?php

namespace SCQAT\Language\PHP\Analyzer;

use Symfony\Component\Process\ProcessBuilder;

/**
 * This is SCQAT PHP > PhpCs analyzer
 *
 * It ensure application of PSR-2 standard through phpcs
 */
class PhpCs extends \SCQAT\AnalyzerAbstract
{
    /**
     * {@inheritdoc}
     */
    public static $introductionMessage = "PSR-2 Standard checking through phpcs";

    /**
     * {@inheritdoc}
     */
    public static function analyze(\SCQAT\Context $context, $analyzedFileName = null)
    {
        $result = new \SCQAT\Result();

        $processBuilder = new ProcessBuilder(array("php", $context->vendorDirectory."bin/phpcs", "--standard=PSR2", "-n", $analyzedFileName));
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
