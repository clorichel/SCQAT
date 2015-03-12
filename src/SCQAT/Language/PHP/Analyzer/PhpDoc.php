<?php

namespace SCQAT\Language\PHP\Analyzer;

use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This is SCQAT PHP > PhpDoc analyzer
 *
 * It ensure that your PHP code is well documented
 */
class PhpDoc extends \SCQAT\AnalyzerAbstract
{
    /**
     * {@inheritdoc}
     */
    public static $introductionMessage = "Checking for documentation completeness";

    /**
     * {@inheritdoc}
     */
    public function analyze($analyzedFileName = null)
    {
        $result = new \SCQAT\Result();
        // phpDoc will parse file and write structure.xml in this directory
        $targetDirectory = $this->context->analyzedDirectory.".scqat-phpdoc-temp".uniqid().DIRECTORY_SEPARATOR;

        $processBuilder = new ProcessBuilder(array($this->language->configuration["command"], $this->context->vendorDirectory."bin/phpdoc", "-f", $analyzedFileName, "-i", "vendor/", "-t", $targetDirectory, "--template=", "xml"));
        $process = $processBuilder->getProcess();
        $process->run();

        if (!$process->isSuccessful()) {
            $result->isSuccess = false;
            $result->value = "Unable to run phpdoc !";
            $result->description = str_replace($analyzedFileName, "", trim($process->getOutput()));
        } else {
            $structureFile = $targetDirectory."structure.xml";
            if (file_exists($structureFile)) {
                // Parsing structure.xml generated by phpDoc
                $xmlContent = file_get_contents($structureFile);
                $simpleXml = new \SimpleXMLElement($xmlContent);
                $errorString = "";
                foreach ($simpleXml->file[0]->parse_markers->error as $error) {
                    // Ignoring "no file summary"
                    if ($error != "No summary was found for this file") {
                        // Extracting final "filename.php" from $analyzedFilename
                        $phpDocFilePath = substr($analyzedFileName, strrpos($analyzedFileName, '/') + 1);
                        // Using xpath to find docblock for current file at the specific line error
                        $relativeDocBlocks = $simpleXml->xpath("//file[@path='".$phpDocFilePath."']/*//docblock[@line='".$error["line"]."']");
                        // Ignoring error if docblock description is "{@inheritdoc}"
                        if (strpos($relativeDocBlocks[0]->description, "{@inheritdoc}") === false) {
                            // Building SCQAT file error messages (one per line)
                            $errorString .= ($errorString != "" ? "\n" : "").":".$error["line"]."  (error) ".$error;
                        }
                    }
                }
                if ($errorString != "") {
                    // Found any phpDoc error ?
                    $result->isSuccess = false;
                    $result->value = "KO";
                    $result->description = $errorString;
                } else {
                    // No error detected
                    $result->isSuccess = true;
                    $result->value = "OK";
                }
            } else {
                $result->isSuccess = false;
                $result->value = "KO";
                $result->description = "Cannot generate structure file !";
            }
        }

        // Remove phpDoc target directory
        $symfonyFs = new Filesystem();
        $symfonyFs->remove($targetDirectory);

        return $result;
    }
}
