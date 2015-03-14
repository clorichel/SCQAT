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
     * Determine if CLI has been called in verbose mode
     * @var boolean True if in verbose mode, false if not
     */
    public $inVerboseMode = true;

    /**
     * If in non verbose mode, we need to know errors encountered by each analyzer to report them
     * @var array
     */
    public $analyzerErrors = array();

    /**
     * The progress bar of the running process, if defined
     * @var \Symfony\Component\Console\Helper\ProgressBar|null
     */
    private $runningProgress = null;

    /**
     * The current language files count
     * @var integer
     */
    private $languageFilesCount = 0;

    /**
     * Initialize with CLI console output
     * @param \Symfony\Component\Console\Output\OutputInterface $output        Underlying symfony/console output
     * @param boolean                                           $inVerboseMode Determine if CLI has been called in verbose mode or not
     */
    public function __construct(\Symfony\Component\Console\Output\OutputInterface $output, $inVerboseMode = true)
    {
        $this->output = $output;
        $this->inVerboseMode = $inVerboseMode;
    }

    /**
     * {@inheritdoc}
     */
    public function languageFirstUse($languageName)
    {
        $this->output->writeln("");
        if ($this->inVerboseMode) {
            $this->output->writeln("<info>> Running analyzers for language</info> <comment>".$languageName."</comment>");
        } else {
            $this->output->write("<info>> Running analyzers for language</info> <comment>".$languageName."</comment>");
        }
        // First time we use this language, we initialize the files count
        $this->languageFilesCount = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function languageEndOfUse($languageName)
    {
        if (! $this->inVerboseMode) {
            $this->output->writeln("");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function analyzerFirstUse(\SCQAT\AnalyzerAbstract $analyzer)
    {
        $this->analyzerErrors = array();
        $this->output->writeln("");
        $message = "<info>[".$analyzer->getLanguageName()." > ".$analyzer->getName()."] ".$analyzer::$introductionMessage."...</info>";
        if (!empty($analyzer->needAllFiles) && $analyzer->needAllFiles === true) {
            $this->output->write($message." ");
        } else {
            if ($this->inVerboseMode) {
                $this->output->writeln($message);
            } else {
                $this->runningProgress = new \Symfony\Component\Console\Helper\ProgressBar($this->output, $this->languageFilesCount);
                $this->runningProgress->setMessage($message);
                $this->runningProgress->setMessage("", "result");
                $this->runningProgress->start();
                $this->runningProgress->setFormat("%message% %current%/%max% %result%");
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function analyzerEndOfUse(\SCQAT\AnalyzerAbstract $analyzer)
    {
        if (! $this->inVerboseMode) {
            if ($this->runningProgress) {
                // End of an analyzer ? We just grab the current step to get the file count !
                $this->languageFilesCount = $this->runningProgress->getStep();
                if (empty($this->analyzerErrors)) {
                    $this->runningProgress->setMessage("<comment>OK</comment>", "result");
                    $this->runningProgress->finish();
                    $this->runningProgress = null;
                } else {
                    $this->runningProgress->setMessage("<error>KO</error>", "result");
                    $this->runningProgress->finish();
                    $this->runningProgress = null;
                    $this->output->writeln("");
                    foreach ($this->analyzerErrors as $outputMessage) {
                        $this->output->writeln($outputMessage);
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function analyzingFile($fileName)
    {
        if (!empty($fileName)) {
            if ($this->inVerboseMode) {
                $this->output->write(" - ".str_replace($this->context->analyzedDirectory, "", $fileName)." ");
            } else {
                $this->runningProgress->advance();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function analyzerResult(\SCQAT\Result $result)
    {
        $message = "";
        if ($result->isSuccess === true) {
            if ($this->inVerboseMode) {
                $message = empty($result->value) ? "OK" : $result->value;
                $this->output->writeln("<comment>".$message."</comment>");
                if (!empty($result->description)) {
                    $this->output->writeln("<comment>".$result->description."</comment>");
                }
            }
        } else {
            $message = empty($result->value) ? "KO" : $result->value;
            if ($this->inVerboseMode) {
                $this->output->writeln("<error>".$message."</error>");
                if (!empty($result->description)) {
                    $this->output->writeln("<error>".$result->description."</error>");
                }
            }
            $this->analyzerErrors[] = " - ".str_replace($this->context->analyzedDirectory, "", $result->fileName)." <error>".$message."</error>";
            if (!empty($result->description)) {
                $this->analyzerErrors[] = "<error>".$result->description."</error>";
            }
        }
    }
}
