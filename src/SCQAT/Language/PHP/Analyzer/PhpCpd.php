<?php

namespace SCQAT\Language\PHP\Analyzer;

use Symfony\Component\Process\ProcessBuilder;

/**
 * This is SCQAT PHP > PhpCpd analyzer
 *
 * It will detect copy/pasted (duplicated) code
 */
class PhpCpd extends \SCQAT\AnalyzerAbstract
{
    /**
     * {@inheritdoc}
     */
    public static $introductionMessage = "Detecting file by file copy/paste";

    /**
     * {@inheritdoc}
     */
    public function analyze(\SCQAT\Context $context, $analyzedFileName = null)
    {
        $result = new \SCQAT\Result();

        $processBuilder = new ProcessBuilder(array("php",  $context->vendorDirectory."bin/phpcpd", $analyzedFileName));
        $process = $processBuilder->getProcess();
        $process->run();

        if (!$process->isSuccessful()) {
            $result->isSuccess = false;
            $result->value = "KO";
            $description = "";
            $outputLines = explode("\n", trim($process->getOutput()));
            // remove first and last line
            array_shift($outputLines);
            array_pop($outputLines);
            foreach ($outputLines as $line) {
                // do not care empty lines
                if (empty(trim($line))) {
                    continue;
                }
                if (strpos($line, $analyzedFileName) !== false) {
                    // remove filename if written on line
                    $description .= str_replace($analyzedFileName, "", $line)."\n";
                } else {
                    // just write the line
                    $description .= $line."\n";
                }
            }
            $result->description = rtrim($description, "\n");
        } else {
            $result->isSuccess = true;
            $result->value = "OK";
        }

        return $result;
    }
}
