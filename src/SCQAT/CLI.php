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
    private $version = "0.6";

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

        // Determining analyzed directory
        $analyzedDirectory = "";
        $optionDirectory = $this->input->getOption("directory");
        if (!empty($optionDirectory)) {
            $analyzedDirectory = rtrim($this->input->getOption("directory"), "/")."/";
        }
        $this->analyzedDirectory = realpath($analyzedDirectory).DIRECTORY_SEPARATOR;

        // Creating SCQAT Context
        $context = new \SCQAT\Context($this->vendorDirectory, $this->analyzedDirectory);
        
        // Attach CLI specific report hooks to the context if configuration allows it
        if (in_array("console", $context->configuration["Reports"])) {
            $context->attachReportHooks(new \SCQAT\CLI\ReportHooks($this->output, $this->input->getOption("verbose")));
        }

        // Gathering files to analyze
        $files = $this->gatherFiles($context);

        if (count($files)) {
            // Attach gathered files to the context
            $context->files = $files;

            // Run SCQAT runner on the context
            $this->runner->run($context);
        }

        // Exit CLI application on error if any were found
        if ($context->hadError !== false) {
            return 1;
        }
    }

    /**
     * Gathering files depending of CLI option passed
     * @param  \SCQAT\Context $context The SCQAT Context, used to trigger hooks
     * @return boolean        True if gathering went well, false on any problem
     */
    private function gatherFiles(\SCQAT\Context $context)
    {
        $context->report->runHook("gatheringFiles");

        $files = $this->input->getOption("file");
        if (!empty($files)) {
            return $files;
        }

        $fileGatherer = new \SCQAT\FileGatherer($this->analyzedDirectory);
        $filesGathered = array();

        if ($this->input->getOption("modified") === true) {
            // User wants to analyze all modified files (staged, unstaged and untracked)
            $filesGathered = $fileGatherer->gitModified();
        } elseif ($this->input->getOption("pre-commit") === true) {
            // User wants to analyze all staged files (before commit)
            $filesGathered = $fileGatherer->gitPreCommit();
        } elseif ($this->input->getOption("diff")) {
            $filesGathered = $fileGatherer->gitDiff($this->input->getOption("diff"));
        } else {
            // Default action, user wants to analyze all files in the git repository
            try {
                $filesGathered = $fileGatherer->gitAll();
            } catch (\Exception $e) {
                // Not a git repository ? Just grab all files
                if ($e->getCode() == 101) {
                    $filesGathered = $fileGatherer->all();
                }
            }
        }

        $context->report->runHook("gatheredFiles", $filesGathered);
        return $filesGathered;
    }
}
