<?php

namespace SCQAT;

use Symfony\Component\Process\Process;
use Symfony\Component\Finder\Finder;

/**
 * SCQAT file gatherer methods
 */
class FileGatherer
{
    /**
     * The directory selected for analysis
     * @var string
     */
    private $analyzedDirectory = null;

    /**
     * Initialize file gatherer
     * @param string $analyzedDirectory The directory selected for analysis
     */
    public function __construct($analyzedDirectory)
    {
        $this->analyzedDirectory = $analyzedDirectory;
    }

    /**
     * Gather all files in the analyzed directory
     * @return array List of all files names
     */
    public function all()
    {
        $finder = new Finder();
        $finder->files()->in($this->analyzedDirectory)->exclude("/vendor");
        $files = array();

        foreach ($finder as $file) {
            $files[] = $file->getRealPath();
        }

        return $files;
    }

    /**
     * Gather all git files in the analyzed directory
     * @throws \Exception If not able to "git ls-files" in the analyzed directory
     * @return array      List of all git files names
     */
    public function gitAll()
    {
        $process = new Process($this->getCdToAnalyzedDir()."git ls-files");
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception("Unable to 'git ls-files'. Is current folder a git repository ? Have you staged the files you want to analyze ?", 101);
        }

        return $this->explodeFilesList(trim($process->getOutput()));
    }

    /**
     * Gather git modified files (staged, unstaged and untracked)
     * @throws \Exception If HEAD reference is not found or if "git diff-index" does not work
     * @return array      List of all git modified files
     */
    public function gitModified()
    {
        // Verifying that HEAD reference exists in current git repository
        $revParse = new Process($this->getCdToAnalyzedDir()."git rev-parse --verify HEAD 2> /dev/null");
        $revParse->run();

        if (!$revParse->isSuccessful()) {
            throw new \Exception("HEAD reference does not exists in current folder. Is it really a git repository ? Have you ever committed in it ?");
        }

        // Listing staged, unstaged and untracked files changed from local revision to HEAD revision
        $process = new Process($this->getCdToAnalyzedDir()."git diff-index --name-status HEAD | egrep '^(A|M)' | awk '{print $2;}' && git ls-files --others --exclude-standard");
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception("Unable to get modified files. What's going on ?");
        }

        return $this->explodeFilesList(trim($process->getOutput()));
    }

    /**
     * Gather git staged files only
     * @return boolean True if gathering went well, false on any problem
     */
    public function gitPreCommit()
    {
        // Verifying that HEAD reference exists in current git repository
        $revParse = new Process($this->getCdToAnalyzedDir()."git rev-parse --verify HEAD 2> /dev/null");
        $revParse->run();

        if (!$revParse->isSuccessful()) {
            throw new \Exception("HEAD reference does not exists in current folder. Is it really a git repository ? Is its remote origin correctly configured ?");
        }

        // Listing staged files changed from local revision to HEAD revision
        $process = new Process($this->getCdToAnalyzedDir()."git diff-index --cached --name-status HEAD | egrep '^(A|M)' | awk '{print $2;}'");
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception("Unable to get staged files");
        }

        return $this->explodeFilesList(trim($process->getOutput()));
    }

    /**
     * Given a list of files separated by "\n", return an array of files names
     * @param  string $filesList List of files separated by "\n"
     * @return array  List of files names
     */
    private function explodeFilesList($filesList)
    {
        $exploded = explode("\n", $filesList);
        $files = array();
        foreach ($exploded as $relativeFileName) {
            if (!empty($relativeFileName)) {
                $files[] = $this->analyzedDirectory.$relativeFileName;
            }
        }

        return $files;
    }

    /**
     * Get the "cd $this->analyedDirectory && " command prefix if needed
     * @return string The "cd" to analyzed dir command
     */
    private function getCdToAnalyzedDir()
    {
        $cdToAnalyzedDir = "";
        if (!empty($this->analyzedDirectory)) {
            $cdToAnalyzedDir = "cd '".$this->analyzedDirectory."' && ";
        }

        return $cdToAnalyzedDir;
    }
}
