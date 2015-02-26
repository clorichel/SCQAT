<?php

namespace SCQAT;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
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
    private $version = "0.3";

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
     * List of files to be analyzed
     * @var array
     */
    private $files = array();

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
                $output->writeln(" - ".str_replace($this->analyzedDirectory, "", $file));
            }

            // Creating SCQAT Context with files gathered
            $context = new \SCQAT\Context($this->vendorDirectory, $this->analyzedDirectory);
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
     * Given a list of files separated by "\n", assign them to $this->files array
     * @param string $filesList List of files separated by "\n"
     */
    private function explodeFilesList($filesList)
    {
        $exploded = explode("\n", $filesList);
        foreach ($exploded as $relativeFileName) {
            if (!empty($relativeFileName)) {
                $this->files[] = $this->analyzedDirectory.$relativeFileName;
            }
        }
    }

    /**
     * Get the "cd $this->analyedDirectory && " command prefix if needed
     * @return string The "cd" to analyzed dir command
     */
    private function getCdToAnalyzedDir()
    {
        $cdToAnalyzedDir = "";
        if (!empty($this->analyzedDirectory)) {
            $cdToAnalyzedDir = "cd ".$this->analyzedDirectory." && ";
        }

        return $cdToAnalyzedDir;
    }

    /**
     * Gathering files
     * @return boolean True if gathering went well, false on any problem
     */
    private function gatherFiles()
    {
        $definition = new InputDefinition(array(
            new InputOption("file", "f", InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED),
            new InputOption("directory", "d", InputOption::VALUE_REQUIRED),
            new InputOption("modified", null, InputOption::VALUE_NONE),
            new InputOption("pre-commit", null, InputOption::VALUE_NONE),
        ));
        
        $this->input->bind($definition);

        $files = $this->input->getOption("file");
        if (!empty($files)) {
            $this->files = $files;
            return true;
        }

        $analyzedDirectory = "";
        if (!empty($this->input->getOption("directory"))) {
            $analyzedDirectory = rtrim($this->input->getOption("directory"), "/")."/";
        }
        $this->analyzedDirectory = realpath($analyzedDirectory).DIRECTORY_SEPARATOR;

        // User wants to analyze all modified files (staged, unstaged and untracked)
        if ($this->input->getOption("modified") === true) {
            return $this->gatherFilesModified();
        }

        // User wants to analyze all staged files (before commit)
        if ($this->input->getOption("pre-commit") === true) {
            return $this->gatherFilesPreCommit();
        }

        // Default action, user wants to analyze all files in the git repository
        $process = new Process($this->getCdToAnalyzedDir()."git ls-files");
        $process->run();

        if (!$process->isSuccessful()) {
            $this->output->writeln("<error>Unable to 'git ls-files'. Is current folder a git repository ? Have you staged the files you want to analyze ?</error>");

            return false;
        }

        $this->explodeFilesList(trim($process->getOutput()));

        return true;
    }

    /**
     * Gathering modified files (staged, unstaged and untracked)
     * @return boolean True if gathering went well, false on any problem
     */
    private function gatherFilesModified()
    {
        // Verifying that HEAD reference exists in current git repository
        $revParse = new Process($this->getCdToAnalyzedDir()."git rev-parse --verify HEAD 2> /dev/null");
        $revParse->run();

        if (!$revParse->isSuccessful()) {
            $this->output->writeln("<error>HEAD reference does not exists in current folder. Is it really a git repository ? Have you ever committed in it ?</error>");

            return false;
        }

        // Listing staged, unstaged and untracked files changed from local revision to HEAD revision
        $process = new Process($this->getCdToAnalyzedDir()."git diff-index --name-status HEAD | egrep '^(A|M)' | awk '{print $2;}' && git ls-files --others --exclude-standard");
        $process->run();

        if (!$process->isSuccessful()) {
            $this->output->writeln("<error>Unable to get modified files. What's going on ?</error>");

            return false;
        }

        $this->explodeFilesList(trim($process->getOutput()));

        return true;
    }

    /**
     * Gathering staged files only
     * @return boolean True if gathering went well, false on any problem
     */
    private function gatherFilesPreCommit()
    {
        // Verifying that HEAD reference exists in current git repository
        $revParse = new Process($this->getCdToAnalyzedDir()."git rev-parse --verify HEAD 2> /dev/null");
        $revParse->run();

        if (!$revParse->isSuccessful()) {
            $this->output->writeln("<error>HEAD reference does not exists in current folder. Is it really a git repository ? Is its remote origin correctly configured ?</error>");

            return false;
        }

        // Listing staged files changed from local revision to HEAD revision
        $process = new Process($this->getCdToAnalyzedDir()."git diff-index --cached --name-status HEAD | egrep '^(A|M)' | awk '{print $2;}'");
        $process->run();

        if (!$process->isSuccessful()) {
            $this->output->writeln("<error>Unable to get staged files</error>");

            return false;
        }

        $this->explodeFilesList(trim($process->getOutput()));

        return true;
    }
}
