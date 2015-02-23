<?php

namespace SCQAT;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

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
    private $version = "0.1";

    /**
     * SCQAT root directory
     * @var string
     */
    private $rootDirectory = null;

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
     * List of files to be analyzed
     * @var array
     */
    private $files = array();

    /**
     * The timezone to apply
     * @var string
     */
    private $timezone = "Europe/Paris";

    /**
     * The long date format (as expected by PHP date function)
     * @var string
     */
    private $dateFormatLong = "d/m/Y H:i:s";

    /**
     * Initializing and building parent symfony/console application
     */
    public function __construct($rootDirectory)
    {
        $this->runner = new \SCQAT\Runner();
        date_default_timezone_set($this->timezone);
        parent::__construct($this->name, $this->version);
        $this->rootDirectory = $rootDirectory;
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

        // Introducing
        $date = new \DateTime();
        $output->writeln("<fg=white;options=bold;bg=blue>[ ".$this->name." (v".$this->version.") ]</fg=white;options=bold;bg=blue>");
        $output->writeln("<comment>".$date->format($this->dateFormatLong)." - Starting analysis</comment>");
        $output->writeln("");

        // Gathering files to analyze
        $output->write("<info>Gathering files to analyze...</info> ");
        if (! $this->gatherFiles()) {
            throw new \Exception("Unable to gather files to analyze");
        }
        $output->writeln("<comment>".count($this->files)." file(s)</comment>");

        if (count($this->files)) {
            foreach ($this->files as $file) {
                $output->writeln(" - ".$file);
            }

            // Creating SCQAT Context with files gathered
            $context = new \SCQAT\Context();
            $context->files = $this->files;
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
            $output->writeln('<info>No file to analyze...</info>');
            $output->writeln("");
            // Ending date only
            $date = new \DateTime();
            $output->writeln("<comment>".$date->format($this->dateFormatLong)." - Nothing analyzed</comment>");
        }

        // Ending
        $output->writeln("<fg=white;options=bold;bg=blue>[ ".$this->name." (v".$this->version.") ]</fg=white;options=bold;bg=blue>");

        // Exit CLI application on error if any were found
        if ($context->hadError !== false) {
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

        $context->report->addHook("Analyzer_First_Use", function ($analyzerName, $languageName, $analyzerInstance) use ($output) {
            $output->writeln("");
            $message = "<info>[".$languageName." > ".$analyzerName."] ".$analyzerInstance::$introductionMessage."...</info>";
            if (!empty($analyzerInstance->needAllFiles) && $analyzerInstance->needAllFiles === true) {
                $output->write($message." ");
            } else {
                $output->writeln($message);
            }
        });

        $context->report->addHook("Analyzing_File", function ($fileName) use ($output) {
            if (!empty($fileName)) {
                $output->write(" - ".$fileName." ");
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
     * Gathering files
     * @return boolean True if gathering went well, false on any problem
     */
    private function gatherFiles()
    {
        // User wants to analyze all modified files (staged, unstaged and untracked)
        if ($this->input->hasParameterOption("--modified")) {
            // Verifying that refs/remote/origin/master reference exists in current git repository
            $revParse = new Process("git rev-parse --verify 'refs/remotes/origin/master' 2> /dev/null");
            $revParse->run();

            if (!$revParse->isSuccessful()) {
                $this->output->writeln("<error>'refs/remotes/origin/master' reference does not exists in current folder. Is it really a git repository ? Is its remote origin correctly configured ?</error>");

                return false;
            }

            // Listing staged, unstaged and untracked files changed from local revision to 'refs/remotes/origin/master' revision
            $process = new Process("git diff-index --name-status 'refs/remotes/origin/master' | egrep '^(A|M)' | awk '{print $2;}' && git ls-files --others --exclude-standard");
            $process->run();

            if (!$process->isSuccessful()) {
                $this->output->writeln("<error>Unable to get modified files. What's going on ?</error>");

                return false;
            }

            $this->files = explode("\n", trim($process->getOutput()));

            return true;
        }

        // User wants to analyze all staged files (before commit)
        if ($this->input->hasParameterOption("--pre-commit")) {
            // Verifying that refs/remote/origin/master reference exists in current git repository
            $revParse = new Process("git rev-parse --verify 'refs/remotes/origin/master' 2> /dev/null");
            $revParse->run();

            if (!$revParse->isSuccessful()) {
                $this->output->writeln("<error>'refs/remotes/origin/master' reference does not exists in current folder. Is it really a git repository ? Is its remote origin correctly configured ?</error>");

                return false;
            }

            // Listing staged files changed from local revision to 'refs/remotes/origin/master' revision
            $process = new Process("git diff-index --cached --name-status 'refs/remotes/origin/master' | egrep '^(A|M)' | awk '{print $2;}'");
            $process->run();

            if (!$process->isSuccessful()) {
                $this->output->writeln("<error>Unable to get staged files</error>");

                return false;
            }

            $this->files = explode("\n", trim($process->getOutput()));

            return true;
        }

        // Default action, user wants to analyze all files in the git repository
        $process = new Process("git ls-files");
        $process->run();

        if (!$process->isSuccessful()) {
            $this->output->writeln("<error>Unable to 'git ls-files'. Is current folder a git repository ? Have you staged the files you want to analyze ?</error>");

            return false;
        }

        $this->files = explode("\n", trim($process->getOutput()));

        return true;
    }
}
