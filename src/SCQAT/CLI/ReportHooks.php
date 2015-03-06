<?php

namespace SCQAT\CLI;

/**
 * SCQAT command line application report hooks
 */
class ReportHooks extends \SCQAT\Report\HooksAbstract
{
    /**
     * Underlying symfony/console output to be used to display messages
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    public $output = null;

    /**
     * Initialize with CLI console output
     * @param \Symfony\Component\Console\Output\OutputInterface $output Underlying symfony/console output
     */
    public function __construct(\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     */
    public function languageFirstUse($languageName)
    {
        $this->output->writeln("");
        $this->output->writeln("<info>Running analyzers for language</info> <comment>".$languageName."</comment>");
    }

    /**
     * {@inheritdoc}
     */
    public function analyzerFirstUse(\SCQAT\AnalyzerAbstract $analyzer)
    {
        $this->output->writeln("");
        $message = "<info>[".$analyzer->getLanguageName()." > ".$analyzer->getName()."] ".$analyzer::$introductionMessage."...</info>";
        if (!empty($analyzer->needAllFiles) && $analyzer->needAllFiles === true) {
            $this->output->write($message." ");
        } else {
            $this->output->writeln($message);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function analyzingFile($fileName)
    {
        if (!empty($fileName)) {
            $this->output->write(" - ".str_replace($this->context->analyzedDirectory, "", $fileName)." ");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function analyzerResult(\SCQAT\Result $result)
    {
        $message = "";
        if ($result->isSuccess === true) {
            $message = empty($result->value) ? "OK" : $result->value;
            $this->output->writeln("<comment>".$message."</comment>");
            if (!empty($result->description)) {
                $this->output->writeln("<comment>".$result->description."</comment>");
            }
        } else {
            $message = empty($result->value) ? "KO" : $result->value;
            $this->output->writeln("<error>".$message."</error>");
            if (!empty($result->description)) {
                $this->output->writeln("<error>".$result->description."</error>");
            }
        }
    }
}
