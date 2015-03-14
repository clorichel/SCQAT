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
        $this->output->writeln("<fg=white;options=bold;bg=blue>[ ".$this->name." (v".$this->version.") ]</fg=white;options=bold;bg=blue>");
        $this->output->writeln("<comment>".$date->format($this->dateFormatLong)." - Starting analysis</comment>");
        $this->output->writeln("");

        // Determining analyzed directory
        $analyzedDirectory = "";
        if (!empty($this->input->getOption("directory"))) {
            $analyzedDirectory = rtrim($this->input->getOption("directory"), "/")."/";
        }
        $this->analyzedDirectory = realpath($analyzedDirectory).DIRECTORY_SEPARATOR;

        // Creating SCQAT Context
        $context = new \SCQAT\Context($this->vendorDirectory, $this->analyzedDirectory);

        // Gathering files to analyze
        $this->output->write("<info>Gathering files to analyze...</info> ");
        $files = $this->gatherFiles();
        $this->output->writeln("<comment>".count($files)." file(s)</comment>");

        $filesCount = count($files);
        if ($filesCount) {
            if ($filesCount <= 10 || $this->input->getOption("verbose")) {
                foreach ($files as $file) {
                    $this->output->writeln(" - ".str_replace($this->analyzedDirectory, "", $file));
                }
            } else {
                $this->output->writeln(" - too many gathered files to show them here, use -v for verbose output");
            }

            // Attach gathered files to the context
            $context->files = $files;
            // Attach CLI specific report hooks to the context
            $context->attachReportHooks(new \SCQAT\CLI\ReportHooks($this->output, ($filesCount <= 10 || $this->input->getOption("verbose"))));

            // Run SCQAT runner on the context
            $this->runner->run($context);

            // Output the result
            if ($context->hadError === false) {
                $this->output->writeln("");
                $this->output->writeln('<info>Each configured quality test was green</info>');
                $this->output->writeln("");
            } else {
                $this->output->writeln("");
                $this->output->writeln('<error>There were error(s)</error>');
                $this->output->writeln("");
            }

            // Report timing
            $date = new \DateTime();
            $this->output->writeln("<comment>".$date->format($this->dateFormatLong)." - Analysed in ".$this->runner->duration."s</comment>");
        } else {
            $this->output->writeln("");
            $this->output->writeln('<info>No file to analyze !</info>');
            $this->output->writeln("");
            // Ending date only
            $date = new \DateTime();
            $this->output->writeln("<comment>".$date->format($this->dateFormatLong)." - Nothing analyzed</comment>");
        }

        // Ending
        $this->output->writeln("<fg=white;options=bold;bg=blue>[ ".$this->name." (v".$this->version.") ]</fg=white;options=bold;bg=blue>");

        // Exit CLI application on error if any were found
        if ($context->hadError !== false) {
            return 1;
        }
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
