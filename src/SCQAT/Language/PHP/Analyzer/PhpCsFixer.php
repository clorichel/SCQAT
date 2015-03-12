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
    public function analyze($analyzedFileName = null)
    {
        $result = new \SCQAT\Result();

        $processBuilder = new ProcessBuilder(array("php",  $this->context->vendorDirectory."bin/php-cs-fixer", "--level=psr2", "--dry-run", "--verbose", "fix", $analyzedFileName));
        $process = $processBuilder->getProcess();
        $process->run();

        if (!$process->isSuccessful()) {
            $result->isSuccess = false;
            $result->value = "KO";
            $description = "";
            $outputLines = explode("\n", trim($process->getOutput()));
            foreach ($outputLines as $line) {
                // remove all output useless lines
                if (strpos($line, $analyzedFileName) !== false) {
                    // remove filename and useles chars to just keep fixers violated
                    $description = trim(substr($line, (strpos($line, $analyzedFileName) + strlen($analyzedFileName))), " ()");
                }
            }
            $result->description = "Triggered fixers : ".$description;
        } else {
            $result->isSuccess = true;
            $result->value = "OK";
        }
        return $result;
    }
}
