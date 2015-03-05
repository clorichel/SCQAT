<?php

namespace SCQAT;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * SCQAT command line application to be ->run()
 *
 * Relying on the wonderful symfony/console
 */
class CLI extends \Symfony\Component\Console\Application
{
    /**
     * CLI Application name
     * @var string
     */
    private $name = "SCQAT - Standardized Code Quality Assurance Tool";

    /**
     * CLI Application version
     * @var string
     */
    private $version = "0.4";

    /**
     * SCQAT vendor directory
     * @var string
     */
    private $vendorDirectory = null;

    /**
     * The directory selected for analysis
     * @var string
     */
    private $analyzedDirectory = null;

    /**
     * SCQAT Runner instance
     * @var \SCQAT\Runner
     */
    private $runner = null;

    /**
     * Underlying symfony/console OutputInterface
     * @var OutputInterface
     */
    private $output;

    /**
     * Underlying symfony/console InputInterface
     * @var InputInterface
     */
    private $input;

    /**
     * The long date format (as expected by PHP date function)
     * @var string
     */
    private $dateFormatLong = "Y-m-d H:i:s";

    /**
     * Initializing and building parent symfony/console application
     * @param string $vendorDirectory Full path to SCQAT vendor directory
     */
    public function __construct($vendorDirectory)
    {
        $this->runner = new \SCQAT\Runner();
        if (!ini_get('date.timezone') && !date_default_timezone_get()) {
            date_default_timezone_set('UTC');
        }
        parent::__construct($this->name, $this->version);
        $this->vendorDirectory = $vendorDirectory;
    }

    /**
     * Implements symfony/console doRun launching method
     * @param InputInterface  $input  Underlying symfony/console InputInterface
     * @param OutputInterface $output Underlying symfony/console OutputInterface
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        // Binding input definition
        $this->input->bind(new \SCQAT\CLI\Definition());

        // Introducing
        $date = new \DateTime();
        $output->writeln("<fg=white;options=bold;bg=blue>[ ".$this->name." (v".$this->version.") ]</fg=white;options=bold;bg=blue>");
        $output->writeln("<comment>".$date->format($this->dateFormatLong)." - Starting analysis</comment>");
        $output->writeln("");

        // Gathering files to analyze
        $output->write("<info>Gathering files to analyze...</info> ");
        $files = $this->gatherFiles();
        $output->writeln("<comment>".count($files)." file(s)</comment>");

        if (count($files)) {
            foreach ($files as $file) {
                $output->writeln(" - ".str_replace($this->analyzedDirectory, "", $file));
            }

            // Creating SCQAT Context with files gathered
            $context = new \SCQAT\Context($this->vendorDirectory, $this->analyzedDirectory);
            $context->files = $files;
            // Populating context hooks
            $this->configureContextHooks($context);

            // Run SCQAT runner on the context
            $this->runner->run($context);

            // Output the result
            if ($context->hadError === false) {
                $output->writeln("");
                $output->writeln('<info>Each configured quality test was green</info>');
                $output->writeln("");
            } else {
                $output->writeln("");
                $output->writeln('<error>There were error(s)</error>');
                $output->writeln("");
            }

            // Report timing
            $date = new \DateTime();
            $output->writeln("<comment>".$date->format($this->dateFormatLong)." - Analysed in ".$this->runner->duration."s</comment>");
        } else {
            $output->writeln("");
            $output->writeln('<info>No file to analyze !</info>');
            $output->writeln("");
            // Ending date only
            $date = new \DateTime();
            $output->writeln("<comment>".$date->format($this->dateFormatLong)." - Nothing analyzed</comment>");
        }

        // Ending
        $output->writeln("<fg=white;options=bold;bg=blue>[ ".$this->name." (v".$this->version.") ]</fg=white;options=bold;bg=blue>");

        // Exit CLI application on error if any were found
        if (!empty($context) && $context->hadError !== false) {
            return 1;
        }
    }

    /**
     * Configure hooks in the context to display evenments based on the CLI paradygm
     * @param \SCQAT\Context $context The SCQAT context instance
     */
    private function configureContextHooks($context)
    {
        $output = $this->output;

        $context->report->addHook("Language_First_Use", function ($languageName) use ($output) {
            $output->writeln("");
            $output->writeln("<info>Running analyzers for language</info> <comment>".$languageName."</comment>");
        });

        $context->report->addHook("Analyzer_First_Use", function (\SCQAT\AnalyzerAbstract $analyzer) use ($output) {
            $output->writeln("");
            $message = "<info>[".$analyzer->getLanguageName()." > ".$analyzer->getName()."] ".$analyzer::$introductionMessage."...</info>";
            if (!empty($analyzer->needAllFiles) && $analyzer->needAllFiles === true) {
                $output->write($message." ");
            } else {
                $output->writeln($message);
            }
        });

        $context->report->addHook("Analyzing_File", function ($fileName) use ($output) {
            if (!empty($fileName)) {
                $output->write(" - ".str_replace($this->analyzedDirectory, "", $fileName)." ");
            }
        });

        $context->report->addHook("Analyzer_Result", function (\SCQAT\Result $result) use ($output) {
            $message = "";
            if ($result->isSuccess === true) {
                $message = empty($result->value) ? "OK" : $result->value;
                $output->writeln("<comment>".$message."</comment>");
                if (!empty($result->description)) {
                    $output->writeln("<comment>".$result->description."</comment>");
                }
            } else {
                $message = empty($result->value) ? "KO" : $result->value;
                $output->writeln("<error>".$message."</error>");
                if (!empty($result->description)) {
                    $output->writeln("<error>".$result->description."</error>");
                }
            }
        });
    }

    /**
     * Gathering files depending of CLI option passed
     * @return boolean True if gathering went well, false on any problem
     */
    private function gatherFiles()
    {
        $files = $this->input->getOption("file");
        if (!empty($files)) {
            return $files;
        }

        $analyzedDirectory = "";
        if (!empty($this->input->getOption("directory"))) {
            $analyzedDirectory = rtrim($this->input->getOption("directory"), "/")."/";
        }
        $this->analyzedDirectory = realpath($analyzedDirectory).DIRECTORY_SEPARATOR;

        $fileGatherer = new \SCQAT\FileGatherer($this->analyzedDirectory);

        if ($this->input->getOption("modified") === true) {
            // User wants to analyze all modified files (staged, unstaged and untracked)
            return $fileGatherer->gitModified();
        } elseif ($this->input->getOption("pre-commit") === true) {
            // User wants to analyze all staged files (before commit)
            return $fileGatherer->gitPreCommit();
        } else {
            // Default action, user wants to analyze all files in the git repository
            try {
                return $fileGatherer->gitAll();
            } catch (\Exception $e) {
                // Not a git repository ? Just grab all files
                if ($e->getCode() == 101) {
                    return $fileGatherer->all();
                }
            }
        }
    }
}
