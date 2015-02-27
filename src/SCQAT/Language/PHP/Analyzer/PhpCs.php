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

        $processBuilder = new ProcessBuilder(array("php", $context->vendorDirectory."bin/phpcs", "--standard=PSR2", "--report=xml", "-n", $analyzedFileName));
        $process = $processBuilder->getProcess();
        $process->run();

        if (!$process->isSuccessful()) {
            $result->isSuccess = false;
            $result->value = "KO";
            $description = "";
            $simpleXml = new \SimpleXMLElement(trim($process->getOutput()));
            foreach ($simpleXml->file[0] as $error) {
                // Building SCQAT file error messages (one per line)
                $description .= ($description != "" ? "\n" : "").":".$error["line"]."  (error) ".$error;
            }
            $result->description = trim($description);
        } else {
            $result->isSuccess = true;
            $result->value = "OK";
        }

        return $result;
    }
}
