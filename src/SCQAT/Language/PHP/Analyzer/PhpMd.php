<?php

namespace SCQAT\Language\PHP\Analyzer;

use Symfony\Component\Process\ProcessBuilder;

/**
 * This is SCQAT PHP > PhpMd analyzer
 *
 * It ensure that your PHP code is not in a mess regarding phpmd rules
 */
class PhpMd extends \SCQAT\AnalyzerAbstract
{
    /**
     * {@inheritdoc}
     */
    public static $introductionMessage = "PHP Mess Detector analysis";

    /**
     * {@inheritdoc}
     */
    public function analyze($analyzedFileName = null)
    {
        $result = new \SCQAT\Result();

        $processBuilder = new ProcessBuilder(array($this->language->configuration["command"], $this->context->vendorDirectory."bin/phpmd", $analyzedFileName, "text", "codesize,controversial,design,naming,unusedcode"));
        $process = $processBuilder->getProcess();
        $process->run();

        if (!$process->isSuccessful()) {
            $result->isSuccess = false;
            $result->value = "KO";
            $result->description = str_replace($analyzedFileName, "", trim($process->getOutput()));
        } else {
            $result->isSuccess = true;
            $result->value = "OK";
        }

        return $result;
    }
}
